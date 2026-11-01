<?php
declare(strict_types=1);

/**
 * Charge un fichier .env local sans remplacer les variables définies par le
 * serveur web. Les identifiants Supabase restent donc hors du dépôt.
 */
$envFile = dirname(__DIR__) . '/.env.local';
if (!is_file($envFile)) {
    $envFile = dirname(__DIR__) . '/.env';
}
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (strlen($value) >= 2 && $value[0] === $value[strlen($value) - 1] && in_array($value[0], ["'", '"'], true)) {
            $value = substr($value, 1, -1);
        }
        if ($key !== '' && getenv($key) === false) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

$supabaseDsn = getenv('SUPABASE_DB_DSN') ?: '';
$supabaseUser = getenv('SUPABASE_DB_USER') ?: '';
$supabasePassword = getenv('SUPABASE_DB_PASSWORD') ?: '';

return [
    'name' => 'Media Spotlight',
    // SQLite reste le mode local par défaut. Ajoutez les trois variables
    // SUPABASE_DB_* dans .env.local pour employer PostgreSQL/Supabase.
    'database_driver' => $supabaseDsn !== '' && $supabaseUser !== '' && $supabasePassword !== '' ? 'supabase' : 'sqlite',
    'database' => dirname(__DIR__) . '/data/media_spotlight.sqlite',
    'supabase_dsn' => $supabaseDsn,
    'supabase_user' => $supabaseUser,
    'supabase_password' => $supabasePassword,
    'uploads' => dirname(__DIR__) . '/data/uploads',
    'max_upload_bytes' => 524_288_000,
    'categories' => ['Technologie', 'Informatique', 'Sciences', 'Management', 'Langues', 'Culture', 'Projets étudiants'],
    'allowed_mimes' => [
        'video' => ['video/mp4', 'video/webm', 'video/ogg'],
        'podcast' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'],
    ],
];
