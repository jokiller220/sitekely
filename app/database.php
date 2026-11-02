<?php
declare(strict_types=1);

function connectDatabase(array $config): PDO
{
    if (($config['database_driver'] ?? 'sqlite') === 'supabase') {
        if (!extension_loaded('pdo_pgsql')) {
            throw new RuntimeException('L’extension PHP pdo_pgsql doit être activée pour utiliser Supabase.');
        }

        $usesTransactionPooler = str_contains($config['supabase_dsn'], 'port=6543');

        return new PDO($config['supabase_dsn'], $config['supabase_user'], $config['supabase_password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Le pooler transactionnel Supabase ne conserve pas les
            // prepared statements entre deux requêtes.
            PDO::ATTR_EMULATE_PREPARES => $usesTransactionPooler,
        ]);
    }

    $directory = dirname($config['database']);
    if (!is_dir($directory)) mkdir($directory, 0755, true);
    if (!is_dir($config['uploads'])) mkdir($config['uploads'], 0755, true);

    $db = new PDO('sqlite:' . $config['database'], null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $db->exec('PRAGMA foreign_keys = ON');
    migrate($db);
    seedAdministrator($db);
    return $db;
}

function addColumnIfNotExists(PDO $db, string $table, string $column, string $typeDef): void
{
    $stmt = $db->query("PRAGMA table_info({$table})");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array($column, $cols, true)) {
        $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$typeDef}");
    }
}

function migrate(PDO $db): void
{
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT, first_name TEXT NOT NULL, last_name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL, role TEXT NOT NULL DEFAULT 'student',
        bio TEXT DEFAULT '', status TEXT NOT NULL DEFAULT 'active', avatar_path TEXT DEFAULT NULL,
        last_login_at TEXT DEFAULT NULL, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS media (
        id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL,
        type TEXT NOT NULL CHECK(type IN ('video','podcast')), title TEXT NOT NULL,
        description TEXT DEFAULT '', category TEXT NOT NULL, visibility TEXT NOT NULL DEFAULT 'public',
        status TEXT NOT NULL DEFAULT 'published', file_path TEXT, original_name TEXT, mime_type TEXT, size INTEGER,
        views_count INTEGER NOT NULL DEFAULT 0, thumbnail_path TEXT DEFAULT NULL, duration_seconds INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS favorites (
        user_id INTEGER NOT NULL, media_id INTEGER NOT NULL, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(user_id, media_id), FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(media_id) REFERENCES media(id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS shares (
        id INTEGER PRIMARY KEY AUTOINCREMENT, media_id INTEGER NOT NULL, token TEXT NOT NULL UNIQUE,
        expires_at TEXT, revoked_at TEXT, views_count INTEGER NOT NULL DEFAULT 0, created_by INTEGER NOT NULL, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(media_id) REFERENCES media(id) ON DELETE CASCADE,
        FOREIGN KEY(created_by) REFERENCES users(id)
    );
    CREATE TABLE IF NOT EXISTS notifications (
        id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL,
        type TEXT NOT NULL DEFAULT 'info', title TEXT NOT NULL, message TEXT NOT NULL,
        link TEXT, read_at TEXT, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL UNIQUE,
        description TEXT DEFAULT '', icon TEXT NOT NULL DEFAULT 'library',
        is_active INTEGER NOT NULL DEFAULT 1, sort_order INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS reports (
        id INTEGER PRIMARY KEY AUTOINCREMENT, reporter_id INTEGER, media_id INTEGER NOT NULL,
        reason TEXT NOT NULL, details TEXT DEFAULT '',
        status TEXT NOT NULL DEFAULT 'pending', action_taken TEXT DEFAULT '',
        resolved_at TEXT DEFAULT NULL, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(reporter_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY(media_id) REFERENCES media(id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS password_resets (
        id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL,
        token TEXT NOT NULL UNIQUE, expires_at TEXT NOT NULL, used_at TEXT DEFAULT NULL,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS contact_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL,
        email TEXT NOT NULL, subject TEXT NOT NULL DEFAULT 'Général',
        message TEXT NOT NULL, is_read INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    );");

    addColumnIfNotExists($db, 'users', 'status', "TEXT NOT NULL DEFAULT 'active'");
    addColumnIfNotExists($db, 'users', 'avatar_path', "TEXT DEFAULT NULL");
    addColumnIfNotExists($db, 'users', 'last_login_at', "TEXT DEFAULT NULL");
    addColumnIfNotExists($db, 'media', 'views_count', "INTEGER NOT NULL DEFAULT 0");
    addColumnIfNotExists($db, 'media', 'thumbnail_path', "TEXT DEFAULT NULL");
    addColumnIfNotExists($db, 'media', 'duration_seconds', "INTEGER NOT NULL DEFAULT 0");
    addColumnIfNotExists($db, 'shares', 'views_count', "INTEGER NOT NULL DEFAULT 0");
    addColumnIfNotExists($db, 'categories', 'position', "INTEGER NOT NULL DEFAULT 0");
    addColumnIfNotExists($db, 'reports', 'user_id', "INTEGER");

    if ((int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn() === 0) {
        $defaults = [
            ['Technologie', 'Innovation, IA et numérique', 'video', 1],
            ['Informatique', 'Code, web et cybersécurité', 'library', 2],
            ['Sciences', 'Comprendre le monde et la recherche', 'chart', 3],
            ['Management', 'Entreprendre, gérer et collaborer', 'users', 4],
            ['Langues', 'S’ouvrir à de nouvelles voix et cultures', 'podcast', 5],
            ['Culture', 'Art, créativité et société', 'heart', 6],
            ['Projets étudiants', 'Productions et mémoires académiques', 'upload', 7],
        ];
        $catStmt = $db->prepare('INSERT INTO categories(name, description, icon, sort_order) VALUES(?,?,?,?)');
        foreach ($defaults as $d) $catStmt->execute($d);
    }

    $db->exec("INSERT INTO notifications(user_id,type,title,message,link)
        SELECT u.id,'welcome','Bienvenue sur Media Spotlight','Votre espace personnel est prêt. Découvrez vos contenus, favoris et options de profil.','?page=profile'
        FROM users u WHERE NOT EXISTS (SELECT 1 FROM notifications n WHERE n.user_id=u.id AND n.type='welcome')");
}

function seedAdministrator(PDO $db): void
{
    if ((int) $db->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn() > 0) return;
    $stmt = $db->prepare('INSERT INTO users(first_name,last_name,email,password_hash,role) VALUES(?,?,?,?,?)');
    $stmt->execute(['Administrateur', 'Media Spotlight', 'admin@mediaspotlight.local', password_hash('ChangeMe!2026', PASSWORD_DEFAULT), 'admin']);
}
