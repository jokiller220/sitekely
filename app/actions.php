<?php
declare(strict_types=1);

function handleAction(PDO $db, array $config): void
{
    if (($_GET['action'] ?? '') === 'logout') {
        session_destroy(); session_start(); flash('Vous êtes déconnecté.'); redirect('home');
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    verifyCsrf();

    match ($_POST['form'] ?? '') {
        'register' => registerUser($db),
        'login' => loginUser($db),
        'upload' => publishMedia($db, $config),
        'edit_media' => editMedia($db, $config),
        'favorite' => toggleFavorite($db),
        'share' => createShare($db),
        'revoke_share' => revokeShare($db),
        'delete' => deleteMedia($db, $config),
        'contact' => contactMessage($db),
        'report_media' => reportMedia($db),
        'profile_details' => updateProfileDetails($db),
        'profile_password' => updateProfilePassword($db),
        'profile_avatar' => updateProfileAvatar($db, $config),
        'profile_avatar_remove' => removeProfileAvatar($db),
        'user_settings' => updateUserSettings($db),
        'user_delete_account' => deleteUserAccount($db),
        'forgot_password' => forgotPassword($db),
        'reset_password' => resetPassword($db),
        'notification_read' => markNotificationRead($db),
        'notifications_read_all' => markAllNotificationsRead($db),
        'admin_user_status' => adminUserStatus($db),
        'admin_user_role' => adminUserRole($db),
        'admin_user_delete' => adminUserDelete($db),
        'admin_user_reset_password' => adminUserResetPassword($db),
        'admin_category_add' => adminCategoryAdd($db),
        'admin_category_edit' => adminCategoryEdit($db),
        'admin_category_delete' => adminCategoryDelete($db),
        'admin_resolve_report' => adminResolveReport($db),
        'admin_toggle_media_status' => adminToggleMediaStatus($db),
        default => abortWith('Action inconnue.'),
    };
}

function updateProfileDetails(PDO $db): never
{
    $user = requireLogin();
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $bio = trim($_POST['bio'] ?? '');
    if ($first === '' || $last === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) abortWith('Vérifiez votre nom et votre adresse e-mail.');
    if (mb_strlen($first) > 60 || mb_strlen($last) > 60 || mb_strlen($bio) > 500) abortWith('Certaines informations sont trop longues.');
    try {
        $db->prepare('UPDATE users SET first_name=?,last_name=?,email=?,bio=? WHERE id=?')->execute([$first,$last,$email,$bio,$user['id']]);
    } catch (PDOException) { abortWith('Cette adresse e-mail est déjà utilisée.'); }
    $_SESSION['user'] = array_merge($user, ['first_name'=>$first,'last_name'=>$last,'email'=>$email,'bio'=>$bio]);
    flash('Votre profil a été mis à jour.'); redirect('profile');
}

function updateProfilePassword(PDO $db): never
{
    $user = requireLogin();
    $current = $_POST['current_password'] ?? '';
    $password = $_POST['new_password'] ?? '';
    $confirmation = $_POST['password_confirmation'] ?? '';
    $stmt = $db->prepare('SELECT password_hash FROM users WHERE id=?');
    $stmt->execute([$user['id']]);
    $hash = $stmt->fetchColumn();
    if (!$hash || !password_verify($current, (string)$hash)) abortWith('Le mot de passe actuel est incorrect.');
    if (strlen($password) < 10) abortWith('Le nouveau mot de passe doit contenir au moins 10 caractères.');
    if ($password !== $confirmation) abortWith('La confirmation du mot de passe ne correspond pas.');
    $db->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($password, PASSWORD_DEFAULT),$user['id']]);
    session_regenerate_id(true); flash('Votre mot de passe a été modifié.'); redirect('profile');
}

function updateProfileAvatar(PDO $db, array $config): never
{
    $user = requireLogin();
    if (empty($_FILES['avatar']['name']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        abortWith('Fichier image invalide.');
    }
    $file = $_FILES['avatar'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
        abortWith('Format d’image non supporté (JPEG, PNG ou WEBP uniquement).');
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $avatarName = 'avatar_' . $user['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $config['uploads'] . '/' . $avatarName)) {
        abortWith('Échec du téléversement.');
    }
    $path = 'data/uploads/' . $avatarName;
    $db->prepare('UPDATE users SET avatar_path=? WHERE id=?')->execute([$path, $user['id']]);
    $_SESSION['user']['avatar_path'] = $path;
    flash('Photo de profil mise à jour.'); redirect('profile');
}

function removeProfileAvatar(PDO $db): never
{
    $user = requireLogin();
    if (!empty($user['avatar_path'])) {
        $file = dirname(__DIR__) . '/' . $user['avatar_path'];
        if (is_file($file)) unlink($file);
    }
    $db->prepare('UPDATE users SET avatar_path=NULL WHERE id=?')->execute([$user['id']]);
    $_SESSION['user']['avatar_path'] = null;
    flash('Photo de profil supprimée.'); redirect('profile');
}

function updateUserSettings(PDO $db): never
{
    requireLogin();
    flash('Vos paramètres ont été enregistrés.'); redirect('settings');
}

function deleteUserAccount(PDO $db): never
{
    $user = requireLogin();
    if ($user['role'] === 'admin') abortWith('Le compte administrateur ne peut être supprimé ici.');
    $password = $_POST['password'] ?? '';
    $stmt = $db->prepare('SELECT password_hash FROM users WHERE id=?'); $stmt->execute([$user['id']]);
    $hash = $stmt->fetchColumn();
    if (!$hash || !password_verify($password, (string)$hash)) abortWith('Mot de passe incorrect.');
    $db->prepare('DELETE FROM users WHERE id=?')->execute([$user['id']]);
    session_destroy(); session_start(); flash('Votre compte a été définitivement supprimé.'); redirect('home');
}

function registerUser(PDO $db): never
{
    $first = trim($_POST['first_name'] ?? ''); $last = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? '')); $password = $_POST['password'] ?? '';
    $confirmation = $_POST['password_confirmation'] ?? ($_POST['password_confirm'] ?? $password);
    $role = $_POST['role'] ?? 'student';
    $terms = !empty($_POST['terms']) || !empty($_POST['accept_terms']);

    if (!$terms) abortWith('Vous devez accepter les conditions d’utilisation.');
    if ($password !== $confirmation) abortWith('Les deux mots de passe ne correspondent pas.');
    if (!$first || !$last || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 10) {
        abortWith('Vérifiez les champs. Le mot de passe doit comporter au moins 10 caractères.');
    }
    if (!in_array($role, ['student', 'teacher'], true)) abortWith('Rôle invalide.');
    try {
        $stmt = $db->prepare('INSERT INTO users(first_name,last_name,email,password_hash,role) VALUES(?,?,?,?,?)');
        $stmt->execute([$first, $last, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        $_SESSION['user'] = ['id' => (int) $db->lastInsertId(), 'first_name' => $first, 'last_name' => $last, 'email' => $email, 'role' => $role, 'status' => 'active'];
        createNotification($db, (int)$_SESSION['user']['id'], 'welcome', 'Bienvenue sur Media Spotlight', 'Votre compte a bien été créé. Complétez maintenant votre profil.', '?page=profile');
        flash('Votre compte est créé. Bienvenue !'); redirect('dashboard');
    } catch (PDOException) { abortWith('Cette adresse e-mail est déjà utilisée.'); }
}

function loginUser(PDO $db): never
{
    $stmt = $db->prepare('SELECT * FROM users WHERE email=?');
    $stmt->execute([strtolower(trim($_POST['email'] ?? ''))]); $user = $stmt->fetch();
    if (!$user || !password_verify($_POST['password'] ?? '', $user['password_hash'])) abortWith('E-mail ou mot de passe incorrect.');
    if (($user['status'] ?? 'active') === 'suspended') abortWith('Ce compte a été suspendu par l’administration.');
    $db->prepare('UPDATE users SET last_login_at=CURRENT_TIMESTAMP WHERE id=?')->execute([$user['id']]);
    $_SESSION['user'] = $user; session_regenerate_id(true); flash('Connexion réussie.');
    redirect($user['role'] === 'admin' ? 'admin' : ($user['role'] === 'teacher' ? 'teacher' : 'dashboard'));
}

function publishMedia(PDO $db, array $config): never
{
    $user = requireLogin(); $title = trim($_POST['title'] ?? ''); $type = $_POST['type'] ?? '';
    $category = trim($_POST['category'] ?? ''); $visibility = $_POST['visibility'] ?? 'public';
    $status = $_POST['status'] ?? 'published';
    if (!in_array($status, ['published', 'draft'], true)) $status = 'published';

    $activeCategories = getActiveCategories($db);
    if (!$title || !isset($config['allowed_mimes'][$type]) || !in_array($category, $activeCategories, true) || !in_array($visibility, ['public','unlisted','private'], true)) {
        abortWith('Informations de publication invalides.');
    }

    [$path, $original, $mime, $size] = [null, null, null, null];
    if (!empty($_FILES['media']['name'])) {
        $file = $_FILES['media'];
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > $config['max_upload_bytes']) abortWith('Le fichier est invalide ou dépasse 500 Mo.');
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!in_array($mime, $config['allowed_mimes'][$type], true)) abortWith('Format de fichier non autorisé.');
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $storedName = bin2hex(random_bytes(20)) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $config['uploads'] . '/' . $storedName)) abortWith('Impossible d’enregistrer le fichier.');
        $path = 'data/uploads/' . $storedName; $original = basename($file['name']); $size = $file['size'];
    }

    $thumbnailPath = null;
    if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumb = $_FILES['thumbnail'];
        $thumbMime = (new finfo(FILEINFO_MIME_TYPE))->file($thumb['tmp_name']);
        if (in_array($thumbMime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            $ext = strtolower(pathinfo($thumb['name'], PATHINFO_EXTENSION));
            $thumbName = 'thumb_' . bin2hex(random_bytes(16)) . '.' . $ext;
            if (move_uploaded_file($thumb['tmp_name'], $config['uploads'] . '/' . $thumbName)) {
                $thumbnailPath = 'data/uploads/' . $thumbName;
            }
        }
    }

    $stmt = $db->prepare('INSERT INTO media(user_id,type,title,description,category,visibility,status,file_path,original_name,mime_type,size,thumbnail_path) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$user['id'],$type,$title,trim($_POST['description'] ?? ''),$category,$visibility,$status,$path,$original,$mime,$size,$thumbnailPath]);
    $mediaId = (int)$db->lastInsertId();
    createNotification($db, (int)$user['id'], 'publication', 'Contenu créé', '« '.$title.' » est maintenant enregistré dans votre bibliothèque.', '?page=my-media');
    flash($status === 'draft' ? 'Brouillon enregistré avec succès.' : 'Contenu publié avec succès.');
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'id' => $mediaId, 'redirect' => url('my-media')]);
        exit;
    }
    redirect('my-media');
}

function editMedia(PDO $db, array $config): never
{
    $user = requireLogin();
    $id = (int) ($_POST['media_id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM media WHERE id=?'); $stmt->execute([$id]); $media = $stmt->fetch();
    if (!$media || ($media['user_id'] != $user['id'] && $user['role'] !== 'admin')) abortWith('Action non autorisée.');

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $visibility = $_POST['visibility'] ?? 'public';
    $status = $_POST['status'] ?? 'published';

    if (!$title) abortWith('Le titre est requis.');
    if (!in_array($visibility, ['public', 'unlisted', 'private'], true)) abortWith('Visibilité invalide.');
    if (!in_array($status, ['published', 'draft', 'archived', 'hidden'], true)) abortWith('Statut invalide.');

    $thumbnailPath = $media['thumbnail_path'];
    if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $thumbFile = $_FILES['thumbnail'];
        $thumbMime = (new finfo(FILEINFO_MIME_TYPE))->file($thumbFile['tmp_name']);
        if (in_array($thumbMime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            $ext = strtolower(pathinfo($thumbFile['name'], PATHINFO_EXTENSION));
            $thumbName = 'thumb_' . bin2hex(random_bytes(16)) . '.' . $ext;
            if (move_uploaded_file($thumbFile['tmp_name'], $config['uploads'] . '/' . $thumbName)) {
                $thumbnailPath = 'data/uploads/' . $thumbName;
            }
        }
    }

    $filePath = $media['file_path'];
    $originalName = $media['original_name'];
    $mimeType = $media['mime_type'];
    $size = $media['size'];

    if (!empty($_FILES['media']['name']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['media'];
        if ($file['size'] <= $config['max_upload_bytes']) {
            $newMime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
            if (in_array($newMime, $config['allowed_mimes'][$media['type']], true)) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $storedName = bin2hex(random_bytes(20)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $config['uploads'] . '/' . $storedName)) {
                    if ($media['file_path']) {
                        $oldFile = dirname(__DIR__) . '/' . $media['file_path'];
                        if (is_file($oldFile)) @unlink($oldFile);
                    }
                    $filePath = 'data/uploads/' . $storedName;
                    $originalName = basename($file['name']);
                    $mimeType = $newMime;
                    $size = $file['size'];
                }
            }
        }
    }

    $db->prepare('UPDATE media SET title=?, description=?, category=?, visibility=?, status=?, thumbnail_path=?, file_path=?, original_name=?, mime_type=?, size=? WHERE id=?')
       ->execute([$title, $description, $category, $visibility, $status, $thumbnailPath, $filePath, $originalName, $mimeType, $size, $id]);

    flash('Contenu mis à jour avec succès.'); redirect('media', ['id' => $id]);
}

function toggleFavorite(PDO $db): never
{
    $user = requireLogin(); $id = (int) ($_POST['media_id'] ?? 0);
    $check = $db->prepare('SELECT 1 FROM favorites WHERE user_id=? AND media_id=?');
    $check->execute([$user['id'], $id]);
    if ($check->fetchColumn()) {
        $db->prepare('DELETE FROM favorites WHERE user_id=? AND media_id=?')->execute([$user['id'], $id]);
        flash('Contenu retiré de vos favoris.');
    } else {
        $db->prepare('INSERT INTO favorites(user_id,media_id) VALUES(?,?)')->execute([$user['id'], $id]);
        flash('Contenu ajouté à vos favoris.');
    }
    redirect('media', ['id' => $id]);
}

function createShare(PDO $db): never
{
    $user = requireLogin(); $id = (int) ($_POST['media_id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM media WHERE id=?'); $stmt->execute([$id]); $media = $stmt->fetch();
    if (!$media || ($media['user_id'] != $user['id'] && $user['role'] !== 'admin')) abortWith('Action non autorisée.');
    $token = bin2hex(random_bytes(18));
    $duration = $_POST['duration'] ?? 'never';
    $expiresAt = match($duration) {
        '24h' => date('Y-m-d H:i:s', strtotime('+24 hours')),
        '7d' => date('Y-m-d H:i:s', strtotime('+7 days')),
        '30d' => date('Y-m-d H:i:s', strtotime('+30 days')),
        default => null,
    };
    $db->prepare('INSERT INTO shares(media_id,token,expires_at,created_by) VALUES(?,?,?,?)')->execute([$id,$token,$expiresAt,$user['id']]);
    flash('Lien sécurisé créé. Retrouvez-le dans « Liens partagés ».'); redirect('shares');
}

function revokeShare(PDO $db): never
{
    $user = requireLogin();
    $shareId = (int) ($_POST['share_id'] ?? 0);
    $stmt = $db->prepare('SELECT s.*, m.user_id as media_owner FROM shares s JOIN media m ON m.id=s.media_id WHERE s.id=?');
    $stmt->execute([$shareId]); $share = $stmt->fetch();
    if (!$share || ($share['created_by'] != $user['id'] && $share['media_owner'] != $user['id'] && $user['role'] !== 'admin')) {
        abortWith('Action non autorisée.');
    }
    $db->prepare('UPDATE shares SET revoked_at=CURRENT_TIMESTAMP WHERE id=?')->execute([$shareId]);
    flash('Le lien partagé a été révoqué.'); redirect('shares');
}

function deleteMedia(PDO $db, array $config): never
{
    $user = requireLogin(); $id = (int) ($_POST['media_id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM media WHERE id=?'); $stmt->execute([$id]); $media = $stmt->fetch();
    if (!$media || ($media['user_id'] != $user['id'] && $user['role'] !== 'admin')) abortWith('Action non autorisée.');
    if ($media['file_path']) { $file = dirname(__DIR__) . '/' . $media['file_path']; if (is_file($file)) @unlink($file); }
    if ($media['thumbnail_path']) { $thumb = dirname(__DIR__) . '/' . $media['thumbnail_path']; if (is_file($thumb)) @unlink($thumb); }
    $db->prepare('DELETE FROM media WHERE id=?')->execute([$id]); flash('Contenu supprimé.'); redirect('my-media');
}

function contactMessage(PDO $db): never
{
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $subject = trim($_POST['subject'] ?? 'Message de contact');
    $message = trim($_POST['message'] ?? '');
    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
        abortWith('Veuillez remplir correctement tous les champs.');
    }
    $db->prepare('INSERT INTO contact_messages(name, email, subject, message) VALUES(?,?,?,?)')
       ->execute([$name, $email, $subject, $message]);
    flash('Votre message a bien été envoyé. Notre équipe vous répondra dans les plus brefs délais.');
    redirect('contact');
}

function reportMedia(PDO $db): never
{
    $user = currentUser();
    $mediaId = (int) ($_POST['media_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    $details = trim($_POST['details'] ?? '');
    if (!$reason) abortWith('Veuillez indiquer le motif du signalement.');
    $stmt = $db->prepare('INSERT INTO reports(reporter_id, media_id, reason, details) VALUES(?,?,?,?)');
    $stmt->execute([$user['id'] ?? null, $mediaId, $reason, $details]);
    flash('Votre signalement a bien été transmis aux modérateurs.'); redirect('media', ['id' => $mediaId]);
}

function forgotPassword(PDO $db): never
{
    $email = strtolower(trim($_POST['email'] ?? ''));
    $stmt = $db->prepare('SELECT id FROM users WHERE email=?'); $stmt->execute([$email]);
    $userId = $stmt->fetchColumn();
    if ($userId) {
        $token = bin2hex(random_bytes(24));
        $expires = date('Y-m-d H:i:s', strtotime('+2 hours'));
        $db->prepare('INSERT INTO password_resets(user_id, token, expires_at) VALUES(?,?,?)')->execute([(int)$userId, $token, $expires]);
        flash('Lien de réinitialisation généré : <a href="?page=reset-password&token='.$token.'"><b>Cliquez ici pour réinitialiser votre mot de passe</b></a> (Valable 2h).');
    } else {
        flash('Si cette adresse existe, un lien vous a été envoyé.');
    }
    redirect('forgot-password');
}

function resetPassword(PDO $db): never
{
    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmation = $_POST['password_confirmation'] ?? '';
    if (strlen($password) < 10) abortWith('Le mot de passe doit comporter au moins 10 caractères.');
    if ($password !== $confirmation) abortWith('La confirmation du mot de passe ne correspond pas.');

    $stmt = $db->prepare('SELECT * FROM password_resets WHERE token=? AND used_at IS NULL AND expires_at > CURRENT_TIMESTAMP');
    $stmt->execute([$token]); $reset = $stmt->fetch();
    if (!$reset) abortWith('Ce jeton de réinitialisation est invalide ou a expiré.');

    $db->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($password, PASSWORD_DEFAULT), $reset['user_id']]);
    $db->prepare('UPDATE password_resets SET used_at=CURRENT_TIMESTAMP WHERE id=?')->execute([$reset['id']]);
    flash('Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    redirect('login');
}

function markNotificationRead(PDO $db): never
{
    $user=requireLogin();$id=(int)($_POST['notification_id']??0);
    $stmt=$db->prepare("UPDATE notifications SET read_at=COALESCE(read_at,CURRENT_TIMESTAMP) WHERE id=? AND user_id=?");$stmt->execute([$id,$user['id']]);
    $link=trim($_POST['notification_link']??'');
    if($link!=='' && str_starts_with($link,'?page=')){header('Location: '.$link);exit;}
    redirect('notifications');
}

function markAllNotificationsRead(PDO $db): never
{
    $user=requireLogin();$db->prepare('UPDATE notifications SET read_at=CURRENT_TIMESTAMP WHERE user_id=? AND read_at IS NULL')->execute([$user['id']]);
    flash('Toutes les notifications ont été marquées comme lues.');redirect('notifications');
}

function adminUserStatus(PDO $db): never
{
    requireAdmin();
    $userId = (int) ($_POST['user_id'] ?? 0);
    $status = ($_POST['status'] ?? '') === 'suspended' ? 'suspended' : 'active';
    $db->prepare('UPDATE users SET status=? WHERE id=? AND role != "admin"')->execute([$status, $userId]);
    createNotification($db, $userId, 'admin', 'Statut de compte', $status==='suspended'?'Votre compte a été suspendu par l’administration.':'Votre compte a été réactivé par l’administration.');
    flash('Statut de l’utilisateur mis à jour.'); redirect('users');
}

function adminUserRole(PDO $db): never
{
    requireAdmin();
    $userId = (int) ($_POST['user_id'] ?? 0);
    $role = in_array($_POST['role'] ?? '', ['student', 'teacher', 'admin'], true) ? $_POST['role'] : 'student';
    $db->prepare('UPDATE users SET role=? WHERE id=?')->execute([$role, $userId]);
    flash('Rôle de l’utilisateur modifié avec succès.'); redirect('users');
}

function adminUserDelete(PDO $db): never
{
    requireAdmin();
    $userId = (int) ($_POST['user_id'] ?? 0);
    if ($userId === currentUser()['id']) abortWith('Impossible de supprimer votre propre compte.');
    $db->prepare('DELETE FROM users WHERE id=? AND role != "admin"')->execute([$userId]);
    flash('Utilisateur et ses contenus associés supprimés définitivement.'); redirect('users');
}

function adminUserResetPassword(PDO $db): never
{
    requireAdmin();
    $userId = (int) ($_POST['user_id'] ?? 0);
    $newPass = $_POST['new_password'] ?? 'Media2026!';
    if (strlen($newPass) < 8) abortWith('Mot de passe trop court.');
    $db->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($newPass, PASSWORD_DEFAULT), $userId]);
    flash('Mot de passe réinitialisé pour cet utilisateur.'); redirect('users');
}

function adminCategoryAdd(PDO $db): never
{
    requireAdmin();
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'library');
    if (!$name) abortWith('Le nom de la catégorie est obligatoire.');
    try {
        $db->prepare('INSERT INTO categories(name, description, icon) VALUES(?,?,?)')->execute([$name, $desc, $icon]);
        flash('Catégorie ajoutée.');
    } catch (PDOException) { abortWith('Cette catégorie existe déjà.'); }
    redirect('categories');
}

function adminCategoryEdit(PDO $db): never
{
    requireAdmin();
    $id = (int) ($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'library');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    if (!$name) abortWith('Le nom de la catégorie est obligatoire.');
    $db->prepare('UPDATE categories SET name=?, description=?, icon=?, is_active=? WHERE id=?')->execute([$name, $desc, $icon, $isActive, $id]);
    flash('Catégorie mise à jour.'); redirect('categories');
}

function adminCategoryDelete(PDO $db): never
{
    requireAdmin();
    $id = (int) ($_POST['category_id'] ?? 0);
    $cat = $db->query("SELECT name FROM categories WHERE id=".(int)$id)->fetchColumn();
    if ($cat) {
        $count = $db->prepare('SELECT COUNT(*) FROM media WHERE category=?');
        $count->execute([$cat]);
        if ($count->fetchColumn() > 0) abortWith('Impossible de supprimer une catégorie liée à des contenus existants.');
        $db->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
        flash('Catégorie supprimée.');
    }
    redirect('categories');
}

function adminResolveReport(PDO $db): never
{
    requireAdmin();
    $reportId = (int) ($_POST['report_id'] ?? 0);
    $action = $_POST['action'] ?? 'dismiss';
    $status = $action === 'dismiss' ? 'dismissed' : 'resolved';
    
    $stmt = $db->prepare('SELECT r.*, m.id as media_id, m.user_id as author_id FROM reports r JOIN media m ON m.id=r.media_id WHERE r.id=?');
    $stmt->execute([$reportId]); $report = $stmt->fetch();
    
    if ($report) {
        if ($action === 'hide') {
            $db->prepare('UPDATE media SET status="hidden" WHERE id=?')->execute([$report['media_id']]);
            createNotification($db, (int)$report['author_id'], 'alert', 'Contenu masqué', 'Votre contenu a été masqué suite à une modération.');
        } elseif ($action === 'publish') {
            $db->prepare('UPDATE media SET status="published" WHERE id=?')->execute([$report['media_id']]);
        } elseif ($action === 'delete') {
            $db->prepare('DELETE FROM media WHERE id=?')->execute([$report['media_id']]);
            createNotification($db, (int)$report['author_id'], 'alert', 'Contenu supprimé', 'Un de vos contenus a été supprimé pour non-respect des règles.');
        }
        $db->prepare('UPDATE reports SET status=?, action_taken=?, resolved_at=CURRENT_TIMESTAMP WHERE id=?')->execute([$status, $action, $reportId]);
        flash('Décision de modération enregistrée.');
    }
    redirect('reports');
}

function adminToggleMediaStatus(PDO $db): never
{
    requireAdmin();
    $mediaId = (int) ($_POST['media_id'] ?? 0);
    $status = in_array($_POST['status'] ?? '', ['published', 'pending', 'hidden', 'archived', 'draft'], true) ? $_POST['status'] : 'published';
    $db->prepare('UPDATE media SET status=? WHERE id=?')->execute([$status, $mediaId]);
    flash('Statut de publication mis à jour : ' . $status); redirect('admin-content');
}
