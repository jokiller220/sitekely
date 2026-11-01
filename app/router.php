<?php
declare(strict_types=1);

function route(PDO $db, array $config): void
{
    if (isset($_GET['share'])) resolveShare($db);
    $page = $_GET['page'] ?? 'home';

    switch ($page) {
        case 'home': showHome($db); break;
        case 'videos': showCatalog($db, 'video'); break;
        case 'podcasts': showCatalog($db, 'podcast'); break;
        case 'media': showMedia($db); break;
        case 'edit-media': showEditMedia($db); break;
        case 'file': streamMedia($db); break;
        case 'login': render('pages/auth', ['title'=>'Connexion','mode'=>'login']); break;
        case 'register': render('pages/auth', ['title'=>'Inscription','mode'=>'register']); break;
        case 'forgot-password': render('pages/forgot-password', ['title'=>'Mot de passe oublié']); break;
        case 'reset-password': showResetPassword($db); break;
        case 'dashboard': if(currentUser()&&currentUser()['role']==='admin') redirect('admin'); showDashboard($db, false); break;
        case 'admin': showDashboard($db, true); break;
        case 'upload': requireLogin(); render('pages/upload', ['title'=>'Publication','categories'=>getActiveCategories($db)]); break;
        case 'my-media': showLibrary($db, 'media'); break;
        case 'favorites': showLibrary($db, 'favorites'); break;
        case 'shares': showLibrary($db, 'shares'); break;
        case 'users': showUsers($db); break;
        case 'admin-content': showAdminContent($db); break;
        case 'categories': showAdminCategories($db); break;
        case 'reports': showAdminReports($db); break;
        case 'admin-messages': showAdminMessages($db); break;
        case 'teacher': showTeacherPanel($db); break;
        case 'settings': showSettings($db); break;
        case 'profile': showProfile($db); break;
        case 'notifications': showNotifications($db); break;
        case 'about': render('pages/about', ['title'=>'À propos']); break;
        case 'contact': render('pages/contact', ['title'=>'Contact']); break;
        case 'legal':
        case 'privacy':
        case 'terms':
        case 'cookies': showLegalPage($page); break;
        default: http_response_code(404); render('errors/404', ['title'=>'Page introuvable']);
    }
}

function showNotifications(PDO $db): void
{
    $user=requireLogin();
    $stmt=$db->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 100');
    $stmt->execute([$user['id']]);
    render('pages/notifications',['title'=>'Notifications','notifications'=>$stmt->fetchAll()]);
}

function showProfile(PDO $db): void
{
    $user = requireLogin();
    $stmt = $db->prepare('SELECT id,first_name,last_name,email,role,bio,avatar_path,status,created_at FROM users WHERE id=?');
    $stmt->execute([$user['id']]);
    $profile = $stmt->fetch();
    $count = $db->prepare('SELECT COUNT(*) FROM media WHERE user_id=?');
    $count->execute([$user['id']]);
    render('pages/profile', ['title'=>'Mon profil','profile'=>$profile,'contentCount'=>(int)$count->fetchColumn()]);
}

function showSettings(PDO $db): void
{
    $user = requireLogin();
    $stmt = $db->prepare('SELECT * FROM users WHERE id=?');
    $stmt->execute([$user['id']]);
    $userData = $stmt->fetch();
    render('pages/settings', ['title'=>'Paramètres du compte','user'=>$userData]);
}

function showHome(PDO $db): void
{
    $stats=[
        'contents'=>(int)$db->query("SELECT COUNT(*) FROM media WHERE status='published'")->fetchColumn(),
        'creators'=>(int)$db->query("SELECT COUNT(*) FROM users WHERE role IN ('student','teacher')")->fetchColumn(),
        'categories'=>(int)$db->query("SELECT COUNT(DISTINCT category) FROM media")->fetchColumn(),
    ];
    render('pages/home',['title'=>'Accueil','items'=>mediaCards($db),'stats'=>$stats]);
}

function showCatalog(PDO $db, string $type): void
{
    $search=trim($_GET['search']??'');$category=trim($_GET['category']??'');$sort=trim($_GET['sort']??'recent');
    $sql="SELECT m.*,u.first_name,u.last_name FROM media m JOIN users u ON u.id=m.user_id WHERE m.status='published' AND m.visibility='public' AND m.type=?";
    $args=[$type];
    if($search!==''){$sql.=' AND (m.title LIKE ? OR m.description LIKE ?)';$args[]='%'.$search.'%';$args[]='%'.$search.'%';}
    if($category!==''){$sql.=' AND m.category=?';$args[]=$category;}
    if($sort==='popular') {
        $sql.=' ORDER BY m.views_count DESC, m.created_at DESC';
    } else {
        $sql.=' ORDER BY m.created_at DESC';
    }
    $stmt=$db->prepare($sql);$stmt->execute($args);
    render('pages/catalog',[
        'title'=>$type==='video'?'Vidéos':'Podcasts',
        'heading'=>$type==='video'?'Toutes les vidéos':'Tous les podcasts',
        'type'=>$type,
        'items'=>$stmt->fetchAll(),
        'search'=>$search,
        'category'=>$category,
        'sort'=>$sort,
        'categories'=>getActiveCategories($db)
    ]);
}

function showMedia(PDO $db): void
{
    $id = (int)($_GET['id']??0);
    $stmt=$db->prepare('SELECT m.*,u.first_name,u.last_name FROM media m JOIN users u ON u.id=m.user_id WHERE m.id=?');
    $stmt->execute([$id]); $media=$stmt->fetch(); $shareToken=(string)($_GET['share']??'');
    if (!$media || (!canAccessMedia($media) && !$shareToken)) { 
        http_response_code(404); 
        render('errors/404',['title'=>'Contenu indisponible']); 
        return; 
    }

    // Increment views count
    $db->prepare("UPDATE media SET views_count = views_count + 1 WHERE id = ?")->execute([$id]);
    $media['views_count'] = ((int)($media['views_count'] ?? 0)) + 1;

    // Check favorite status for user
    $isFavorite = false;
    $user = currentUser();
    if ($user) {
        $fStmt = $db->prepare('SELECT 1 FROM favorites WHERE user_id=? AND media_id=?');
        $fStmt->execute([$user['id'], $id]);
        $isFavorite = (bool)$fStmt->fetchColumn();
    }

    // Get similar media
    $simStmt = $db->prepare("SELECT m.*,u.first_name,u.last_name FROM media m JOIN users u ON u.id=m.user_id WHERE m.id != ? AND m.category = ? AND m.status='published' AND m.visibility='public' ORDER BY m.views_count DESC LIMIT 3");
    $simStmt->execute([$id, $media['category']]);
    $similar = $simStmt->fetchAll();

    render('pages/media', [
        'title'=>$media['title'],
        'media'=>$media,
        'shareToken'=>$shareToken,
        'isFavorite'=>$isFavorite,
        'similar'=>$similar
    ]);
}

function showEditMedia(PDO $db): void
{
    $user = requireLogin();
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM media WHERE id=?');
    $stmt->execute([$id]);
    $media = $stmt->fetch();

    if (!$media) {
        setFlash('error', 'Contenu introuvable.');
        redirect('my-media');
    }
    if ($media['user_id'] !== $user['id'] && $user['role'] !== 'admin') {
        setFlash('error', 'Vous n\'êtes pas autorisé à modifier ce média.');
        redirect('my-media');
    }

    render('pages/edit-media', [
        'title' => 'Modifier : ' . $media['title'],
        'media' => $media,
        'categories' => getActiveCategories($db)
    ]);
}

function streamMedia(PDO $db): never
{
    $stmt=$db->prepare('SELECT * FROM media WHERE id=?'); $stmt->execute([(int)($_GET['id']??0)]); $media=$stmt->fetch();
    $token=(string)($_GET['token']??''); $shared=false;
    if ($media && $token) { 
        $q=$db->prepare("SELECT id FROM shares WHERE media_id=? AND token=? AND revoked_at IS NULL AND (expires_at IS NULL OR expires_at > CURRENT_TIMESTAMP)"); 
        $q->execute([$media['id'],$token]); 
        $shareId = $q->fetchColumn();
        if ($shareId) {
            $shared = true;
            $db->prepare("UPDATE shares SET views_count = views_count + 1 WHERE id = ?")->execute([$shareId]);
        }
    }
    $file=$media && $media['file_path'] ? dirname(__DIR__).'/'.$media['file_path'] : '';
    if (!$media || (!$shared && !canAccessMedia($media)) || !$file || !is_file($file)) { 
        http_response_code(403); 
        exit('Accès au fichier refusé.'); 
    }
    header('Content-Type: '.$media['mime_type']); 
    header('Content-Length: '.filesize($file)); 
    header('X-Content-Type-Options: nosniff'); 
    readfile($file); 
    exit;
}

function resolveShare(PDO $db): void
{
    $stmt=$db->prepare("SELECT id, media_id FROM shares WHERE token=? AND revoked_at IS NULL AND (expires_at IS NULL OR expires_at > CURRENT_TIMESTAMP)");
    $stmt->execute([$_GET['share']]); 
    $share=$stmt->fetch();
    if (!$share) { 
        http_response_code(404); 
        render('errors/404',['title'=>'Lien expiré ou révoqué']); 
        exit; 
    }
    $db->prepare("UPDATE shares SET views_count = views_count + 1 WHERE id = ?")->execute([$share['id']]);
    $_GET['page']='media'; 
    $_GET['id']=$share['media_id'];
}

function showDashboard(PDO $db, bool $admin): void
{
    $user=$admin?requireAdmin():requireLogin();
    if ($admin) {
        $totalViews = (int)$db->query('SELECT SUM(views_count) FROM media')->fetchColumn();
        $stats = [
            ['label'=>'Utilisateurs','value'=>(int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn(),'icon'=>'users','tone'=>'purple','note'=>'Comptes inscrits'],
            ['label'=>'Vidéos','value'=>(int)$db->query("SELECT COUNT(*) FROM media WHERE type='video'")->fetchColumn(),'icon'=>'video','tone'=>'blue','note'=>'Toutes publications'],
            ['label'=>'Podcasts','value'=>(int)$db->query("SELECT COUNT(*) FROM media WHERE type='podcast'")->fetchColumn(),'icon'=>'podcast','tone'=>'green','note'=>'Tous les épisodes'],
            ['label'=>'Vues totales','value'=>$totalViews,'icon'=>'eye','tone'=>'orange','note'=>'Consultations cumulées'],
        ];
        $recentUsers=$db->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 5')->fetchAll();
        $recent=$db->query('SELECT m.*,u.first_name,u.last_name FROM media m JOIN users u ON u.id=m.user_id ORDER BY m.created_at DESC LIMIT 5')->fetchAll();
    } else {
        $count=function(string $sql) use($db,$user): int {$stmt=$db->prepare($sql);$stmt->execute([$user['id']]);return (int)$stmt->fetchColumn();};
        $userViews = (int)$db->query("SELECT SUM(views_count) FROM media WHERE user_id=".$user['id'])->fetchColumn();
        $stats = [
            ['label'=>'Mes vidéos','value'=>$count("SELECT COUNT(*) FROM media WHERE user_id=? AND type='video'"),'icon'=>'video','tone'=>'purple','note'=>'Contenus vidéo'],
            ['label'=>'Mes podcasts','value'=>$count("SELECT COUNT(*) FROM media WHERE user_id=? AND type='podcast'"),'icon'=>'podcast','tone'=>'blue','note'=>'Épisodes audio'],
            ['label'=>'Favoris','value'=>$count('SELECT COUNT(*) FROM favorites WHERE user_id=?'),'icon'=>'heart','tone'=>'green','note'=>'Contenus enregistrés'],
            ['label'=>'Vues cumulées','value'=>$userViews,'icon'=>'eye','tone'=>'orange','note'=>'Sur tous mes médias'],
        ];
        $stmt=$db->prepare('SELECT m.*,u.first_name,u.last_name FROM media m JOIN users u ON u.id=m.user_id WHERE m.user_id=? ORDER BY m.created_at DESC LIMIT 5');
        $stmt->execute([$user['id']]);
        $recent=$stmt->fetchAll();
        $recentUsers=[];
    }
    $chart=[];
    for($days=6;$days>=0;$days--){
        $date=date('Y-m-d',strtotime("-{$days} days"));
        $stmt=$db->prepare('SELECT COUNT(*) FROM media WHERE date(created_at)=?'.($admin?'':' AND user_id=?'));
        $args=[$date];
        if(!$admin)$args[]=$user['id'];
        $stmt->execute($args);
        $chart[]=['label'=>date('d/m',strtotime($date)),'value'=>(int)$stmt->fetchColumn()];
    }
    $maxChart=max(1,...array_column($chart,'value'));
    render('pages/dashboard',['title'=>$admin?'Administration':'Tableau de bord','admin'=>$admin,'user'=>$user,'stats'=>$stats,'recent'=>$recent,'recentUsers'=>$recentUsers,'chart'=>$chart,'maxChart'=>$maxChart]);
}

function showLibrary(PDO $db, string $mode): void
{
    $user=requireLogin();
    $filterType = trim($_GET['type'] ?? '');
    if ($mode==='favorites') { 
        $stmt=$db->prepare('SELECT m.* FROM favorites f JOIN media m ON m.id=f.media_id WHERE f.user_id=? ORDER BY f.created_at DESC'); 
        $stmt->execute([$user['id']]);
        $heading='Mes favoris'; $active='favorites'; 
    }
    elseif ($mode==='shares') { 
        $stmt=$db->prepare('SELECT sh.*,m.title,m.type FROM shares sh JOIN media m ON m.id=sh.media_id WHERE sh.created_by=? ORDER BY sh.created_at DESC'); 
        $stmt->execute([$user['id']]);
        $heading='Mes liens partagés'; $active='shares'; 
    }
    else { 
        $sql = 'SELECT * FROM media WHERE user_id=?';
        $params = [$user['id']];
        if ($filterType === 'video' || $filterType === 'podcast') {
            $sql .= ' AND type=?';
            $params[] = $filterType;
        } elseif ($filterType === 'draft') {
            $sql .= " AND status='draft'";
        }
        $sql .= ' ORDER BY created_at DESC';
        $stmt=$db->prepare($sql); 
        $stmt->execute($params);
        $heading='Mes contenus'; $active='media'; 
    }
    
    $scheme=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http'; 
    $baseUrl=$scheme.'://'.($_SERVER['HTTP_HOST']??'localhost').($_SERVER['PHP_SELF']??'/');
    render('pages/library',[
        'title'=>$heading,
        'heading'=>$heading,
        'rows'=>$stmt->fetchAll(),
        'mode'=>$mode,
        'active'=>$active,
        'baseUrl'=>$baseUrl,
        'filterType'=>$filterType
    ]);
}

function showUsers(PDO $db): void
{
    requireAdmin();
    $search = trim($_GET['search'] ?? '');
    $role = trim($_GET['role'] ?? '');
    $status = trim($_GET['status'] ?? '');

    $sql = 'SELECT * FROM users WHERE 1=1';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
        $term = '%' . $search . '%';
        $params[] = $term; $params[] = $term; $params[] = $term;
    }
    if ($role !== '') {
        $sql .= ' AND role = ?';
        $params[] = $role;
    }
    if ($status !== '') {
        $sql .= ' AND status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY created_at DESC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    render('pages/users', [
        'title'=>'Utilisateurs',
        'rows'=>$stmt->fetchAll(),
        'search'=>$search,
        'role'=>$role,
        'status'=>$status
    ]);
}

function showAdminContent(PDO $db): void
{
    requireAdmin();
    $search=trim($_GET['search']??'');$type=trim($_GET['type']??'');$category=trim($_GET['category']??'');$status=trim($_GET['status']??'');
    $sql='SELECT m.*,u.first_name,u.last_name FROM media m JOIN users u ON u.id=m.user_id WHERE 1=1';$args=[];
    if($search!==''){$sql.=' AND (m.title LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)';$term='%'.$search.'%';$args=[$term,$term,$term];}
    if(in_array($type,['video','podcast'],true)){$sql.=' AND m.type=?';$args[]=$type;}
    if($category!==''){$sql.=' AND m.category=?';$args[]=$category;}
    if($status!==''){$sql.=' AND m.status=?';$args[]=$status;}
    $sql.=' ORDER BY m.created_at DESC';
    $stmt=$db->prepare($sql);$stmt->execute($args);
    render('pages/admin-content',[
        'title'=>'Gestion des contenus',
        'rows'=>$stmt->fetchAll(),
        'search'=>$search,
        'type'=>$type,
        'category'=>$category,
        'status'=>$status,
        'categories'=>getActiveCategories($db)
    ]);
}

function showAdminCategories(PDO $db): void
{
    requireAdmin();
    $categories = $db->query('SELECT c.*, (SELECT COUNT(*) FROM media m WHERE m.category = c.name) AS media_count FROM categories c ORDER BY position ASC, id ASC')->fetchAll();
    render('pages/admin-categories', [
        'title' => 'Gestion des catégories',
        'categories' => $categories
    ]);
}

function showAdminReports(PDO $db): void
{
    requireAdmin();
    $status = trim($_GET['status'] ?? '');
    $sql = 'SELECT r.*, m.title AS media_title, m.type AS media_type, u.first_name, u.last_name, u.email 
            FROM reports r 
            JOIN media m ON m.id = r.media_id 
            LEFT JOIN users u ON (u.id = r.user_id OR u.id = r.reporter_id) 
            WHERE 1=1';
    $params = [];
    if ($status !== '') {
        $sql .= ' AND r.status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY r.created_at DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    render('pages/admin-reports', [
        'title' => 'Signalements des contenus',
        'reports' => $stmt->fetchAll(),
        'status' => $status
    ]);
}

function showAdminMessages(PDO $db): void
{
    requireAdmin();
    $messages = $db->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
    render('pages/admin-messages', [
        'title' => 'Messages de contact reçus',
        'messages' => $messages
    ]);
}

function showTeacherPanel(PDO $db): void
{
    $teacher = requireTeacher();
    // Student submissions: media created by students
    $submissions = $db->query("SELECT m.*, u.first_name, u.last_name, u.email 
                               FROM media m 
                               JOIN users u ON u.id = m.user_id 
                               WHERE u.role = 'student' 
                               ORDER BY m.created_at DESC LIMIT 50")->fetchAll();
    // Distinct students
    $students = $db->query("SELECT u.id, u.first_name, u.last_name, u.email, COUNT(m.id) AS count_works 
                            FROM users u 
                            LEFT JOIN media m ON m.user_id = u.id 
                            WHERE u.role = 'student' 
                            GROUP BY u.id 
                            ORDER BY u.last_name ASC")->fetchAll();

    render('pages/teacher', [
        'title' => 'Espace Enseignant',
        'teacher' => $teacher,
        'submissions' => $submissions,
        'students' => $students
    ]);
}

function showResetPassword(PDO $db): void
{
    $token = trim($_GET['token'] ?? '');
    if (!$token) {
        setFlash('error', 'Lien de réinitialisation invalide.');
        redirect('login');
    }
    $stmt = $db->prepare("SELECT pr.*, u.email FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = ? AND pr.used_at IS NULL AND pr.expires_at > CURRENT_TIMESTAMP");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();
    if (!$reset) {
        setFlash('error', 'Ce lien de réinitialisation a expiré ou a déjà été utilisé.');
        redirect('forgot-password');
    }
    render('pages/reset-password', [
        'title' => 'Nouveau mot de passe',
        'token' => $token,
        'email' => $reset['email']
    ]);
}

function showLegalPage(string $page): void
{
    $titles = [
        'legal' => 'Mentions Légales',
        'privacy' => 'Politique de Confidentialité',
        'terms' => 'Conditions Générales d’Utilisation',
        'cookies' => 'Politique des Cookies',
    ];
    render('pages/legal', [
        'title' => $titles[$page] ?? 'Informations Légales',
        'section' => $page
    ]);
}
