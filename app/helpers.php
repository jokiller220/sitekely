<?php
declare(strict_types=1);

function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function icon(string $name, int $size = 18, string $class = ''): string
{
    $paths = [
        'home'=>'<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
        'plus'=>'<path d="M12 5v14M5 12h14"/>','upload'=>'<path d="M12 16V4m0 0L7 9m5-5 5 5"/><path d="M5 20h14"/>',
        'library'=>'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h7M7 16h5"/>',
        'heart'=>'<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"/>',
        'share'=>'<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 10.5 6.8-4M8.6 13.5l6.8 4"/>',
        'chart'=>'<path d="M3 3v18h18"/><path d="m7 16 4-5 4 3 5-7"/>','users'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'video'=>'<rect x="3" y="5" width="15" height="14" rx="2"/><path d="m18 10 4-2v8l-4-2z"/>','podcast'=>'<path d="M12 2a7 7 0 0 0-7 7v3a7 7 0 0 0 14 0V9a7 7 0 0 0-7-7Z"/><path d="M8 21h8M12 18v3"/>',
        'play'=>'<circle cx="12" cy="12" r="10"/><path d="m10 8 6 4-6 4Z"/>','eye'=>'<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'trash'=>'<path d="M3 6h18M8 6V4h8v2M19 6l-1 15H6L5 6M10 11v6M14 11v6"/>','more'=>'<circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>',
        'arrow'=>'<path d="M5 12h14M13 6l6 6-6 6"/>','chevron'=>'<path d="m9 18 6-6-6-6"/>','calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        'moon'=>'<path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/>','logout'=>'<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        'user'=>'<path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/>','send'=>'<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>','check'=>'<path d="m5 12 4 4L19 6"/>',
        'menu'=>'<path d="M4 7h16M4 12h16M4 17h16"/>','x'=>'<path d="m6 6 12 12M18 6 6 18"/>','file'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/>',
        'search'=>'<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>','bell'=>'<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>','settings'=>'<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"/>','shield'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
        'sun'=>'<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"/>',
        'copy'=>'<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
        'edit'=>'<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
        'flag'=>'<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>',
        'lock'=>'<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'filter'=>'<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>',
        'tag'=>'<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
        'mail'=>'<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
        'clock'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'alert'=>'<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        'refresh'=>'<path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>',
        'ban'=>'<circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>',
        'folder'=>'<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
    ];
    $path=$paths[$name]??$paths['more'];
    return '<svg class="ui-icon '.e($class).'" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$path.'</svg>';
}
function currentUser(): ?array { return $_SESSION['user'] ?? null; }
function url(string $page = 'home', array $parameters = []): string { return '?' . http_build_query(['page' => $page] + $parameters); }
function redirect(string $page, array $parameters = []): never { header('Location: ' . url($page, $parameters)); exit; }
function csrfToken(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function verifyCsrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) abortWith('Session expirée. Veuillez recommencer.'); }
function flash(string $message, string $type = 'success'): void { $_SESSION['flash'] = compact('message', 'type'); }
function abortWith(string $message): never {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        http_response_code(422);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
    flash($message, 'error');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url()));
    exit;
}
function requireLogin(): array
{
    if (!$user = currentUser()) { flash('Connectez-vous pour continuer.', 'error'); redirect('login'); }
    if (($user['status'] ?? 'active') === 'suspended') {
        session_destroy(); session_start(); flash('Votre compte a été suspendu par l’administration.', 'error'); redirect('login');
    }
    return $user;
}
function requireAdmin(): array { $user = requireLogin(); if ($user['role'] !== 'admin') { http_response_code(403); render('errors/403', ['title' => 'Accès refusé']); exit; } return $user; }
function requireTeacher(): array { $user = requireLogin(); if (!in_array($user['role'], ['teacher', 'admin'], true)) { http_response_code(403); render('errors/403', ['title' => 'Accès réservé aux enseignants']); exit; } return $user; }
function canAccessMedia(array $media): bool
{
    $user = currentUser();
    $isOwner = $user && ($user['id'] == $media['user_id']);
    $isAdmin = $user && ($user['role'] === 'admin');
    if ($isOwner || $isAdmin) return true;
    if (($media['status'] ?? 'published') !== 'published') return false;
    return ($media['visibility'] ?? 'public') === 'public';
}
function getActiveCategories(PDO $db): array
{
    try {
        $stmt = $db->query("SELECT name FROM categories WHERE is_active = TRUE ORDER BY sort_order ASC, name ASC");
        $cats = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($cats)) return $cats;
    } catch (PDOException) {}
    return $GLOBALS['config']['categories'] ?? ['Technologie', 'Informatique', 'Sciences', 'Management', 'Langues'];
}
function createNotification(PDO $db, int $userId, string $type, string $title, string $message, ?string $link = null): void
{
    $db->prepare('INSERT INTO notifications(user_id,type,title,message,link) VALUES(?,?,?,?,?)')->execute([$userId,$type,$title,$message,$link]);
}

function unreadNotificationCount(): int
{
    $user=currentUser(); $db=$GLOBALS['db']??null;
    if(!$user || !$db instanceof PDO) return 0;
    $stmt=$db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND read_at IS NULL');$stmt->execute([$user['id']]);return (int)$stmt->fetchColumn();
}

function render(string $view, array $data = []): void
{
    $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';
    if (!is_file($viewFile)) throw new RuntimeException("Vue introuvable: {$view}");
    extract($data, EXTR_SKIP);
    ob_start();
    require $viewFile;
    $content = ob_get_clean();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    require dirname(__DIR__) . '/views/layout.php';
}

function mediaCards(PDO $db, ?string $type = null, int $limit = 6): array
{
    $sql = "SELECT m.*,u.first_name,u.last_name FROM media m JOIN users u ON u.id=m.user_id WHERE m.status='published' AND m.visibility='public'";
    $args = [];
    if ($type) { $sql .= ' AND m.type=?'; $args[] = $type; }
    $sql .= ' ORDER BY m.created_at DESC LIMIT ' . (int) $limit;
    $stmt = $db->prepare($sql); $stmt->execute($args);
    return $stmt->fetchAll();
}
