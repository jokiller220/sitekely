<?php $user = currentUser(); $currentPage = $_GET['page'] ?? 'home'; $notificationCount=unreadNotificationCount(); $adminArea = $user && $user['role']==='admin' && in_array($currentPage,['admin','users','admin-content','categories','reports','admin-messages','settings','upload','my-media','favorites','shares','profile','notifications'],true); ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title ?? 'Media Spotlight') ?> · Media Spotlight</title>
    <link rel="icon" type="image/png" sizes="32x32" href="public/assets/favicon.png">
    <link rel="icon" type="image/x-icon" href="public/assets/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="public/assets/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/assets/app.css?v=6">
    <link rel="stylesheet" href="public/assets/dashboard.css?v=5">
    <link rel="stylesheet" href="public/assets/components.css?v=6">
    <link rel="stylesheet" href="public/assets/branding.css?v=5">
    <link rel="stylesheet" href="public/assets/public.css?v=5">
    <link rel="stylesheet" href="public/assets/typography.css?v=5">
    <link rel="stylesheet" href="public/assets/experience.css?v=4">
    <link rel="stylesheet" href="public/assets/admin-panel.css?v=2">
    <link rel="stylesheet" href="public/assets/admin-typography.css?v=2">
    <link rel="stylesheet" href="public/assets/admin-theme.css?v=2">
    <link rel="stylesheet" href="public/assets/theme-toggle.css?v=2">
    <link rel="stylesheet" href="public/assets/admin-header.css?v=4">
    <link rel="stylesheet" href="public/assets/profile.css?v=2">
    <link rel="stylesheet" href="public/assets/connected-header.css?v=2">
    <link rel="stylesheet" href="public/assets/notifications.css?v=2">
    <link rel="stylesheet" href="public/assets/landing-experience.css?v=2">
    <link rel="stylesheet" href="public/assets/landing-motion.css?v=1">

</head>
<body class="<?=$adminArea?'admin-area':''?>">
<header class="topbar <?=$adminArea?'admin-topbar':''?>">
    <a class="logo" href="<?= url() ?>" aria-label="Media Spotlight">
        <img class="logo-img logo-light" src="public/assets/logo.png" alt="Media Spotlight" height="38" width="107">
        <img class="logo-img logo-dark" src="public/assets/logo-dark.png" alt="Media Spotlight" height="38" width="107">
    </a>
    <?php if($adminArea): ?><span class="admin-console-label">Administration</span><div class="admin-header-search"><?=icon('search',17)?><input placeholder="Rechercher dans l’administration…" aria-label="Rechercher"></div><div class="admin-header-actions"><button class="admin-theme-toggle" id="themeToggle" type="button" aria-label="Activer le mode sombre" aria-pressed="false"><span class="admin-action-icon theme-moon"><?=icon('moon',18)?></span><span class="admin-action-icon theme-sun"><?=icon('sun',18)?></span><span class="admin-theme-label">Mode sombre</span></button><a class="admin-notification" href="<?=url('notifications')?>" aria-label="Afficher les notifications"><span><?=icon('bell',18)?></span><?php if($notificationCount):?><b><?=$notificationCount>9?'9+':$notificationCount?></b><?php endif?></a><a class="admin-identity" href="<?=url('profile')?>" title="Ouvrir mon profil"><span class="admin-avatar"><?=e(strtoupper(substr($user['first_name'],0,1).substr($user['last_name'],0,1)))?></span><div><strong><?=e($user['first_name'].' '.$user['last_name'])?></strong><small>Mon profil</small></div></a><a class="admin-logout" href="?action=logout" aria-label="Se déconnecter"><?=icon('logout',18)?><span>Déconnexion</span></a></div><?php else: ?>
    <nav class="main-nav" id="mainNav" aria-label="Navigation principale">
        <a class="<?=$currentPage==='home'?'active':''?>" href="<?= url() ?>">Accueil</a><a class="<?=$currentPage==='videos'?'active':''?>" href="<?= url('videos') ?>">Vidéos</a>
        <a class="<?=$currentPage==='podcasts'?'active':''?>" href="<?= url('podcasts') ?>">Podcasts</a><a class="<?=$currentPage==='about'?'active':''?>" href="<?= url('about') ?>">À propos</a>
        <a class="<?=$currentPage==='contact'?'active':''?>" href="<?= url('contact') ?>">Contact</a>
        <div class="mobile-nav-actions"><?php if($user): ?><a href="<?=url($user['role']==='admin'?'admin':'dashboard')?>"><?=icon('home',17)?> Mon espace</a><a href="<?=url('profile')?>"><?=icon('user',17)?> Mon profil</a><a href="?action=logout"><?=icon('logout',17)?> Déconnexion</a><?php else: ?><a href="<?=url('login')?>"><?=icon('user',17)?> Connexion</a><a href="<?=url('register')?>"><?=icon('arrow',17)?> S’inscrire</a><?php endif ?></div>
    </nav>
    <button class="mobile-menu-button" id="mobileMenu" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mainNav"><span class="menu-open"><?=icon('menu',21)?></span><span class="menu-close"><?=icon('x',21)?></span></button>
    <button class="theme-toggle icon-button" id="themeToggle" aria-label="Activer le mode sombre" title="Changer de thème"><span class="theme-moon"><?=icon('moon',17)?></span><span class="theme-sun"><?=icon('sun',17)?></span></button>
    <?php if ($user): ?>
        <a class="connected-notification" href="<?=url('notifications')?>" aria-label="Notifications"><?=icon('bell',17)?><?php if($notificationCount):?><b><?=$notificationCount>9?'9+':$notificationCount?></b><?php endif?></a>
        <a class="connected-space" href="<?=url($user['role']==='admin'?'admin':'dashboard')?>"><?=icon('home',17)?><span>Mon espace</span></a>
        <a class="connected-profile" href="<?=url('profile')?>" aria-label="Ouvrir mon profil"><span class="connected-avatar"><?=e(strtoupper(substr($user['first_name'],0,1).substr($user['last_name'],0,1)))?></span><span class="connected-profile-copy"><strong><?=e($user['first_name'].' '.$user['last_name'])?></strong><small><?=e($user['role']==='admin'?'Administrateur':($user['role']==='teacher'?'Enseignant':'Étudiant'))?></small></span><?=icon('chevron',15)?></a>
        <a class="connected-logout" href="?action=logout" aria-label="Se déconnecter" title="Déconnexion"><?=icon('logout',17)?></a>
    <?php else: ?>
        <a class="button subtle" href="<?= url('login') ?>"><?=icon('user',16)?> <span>Connexion</span></a>
        <a class="button primary" href="<?= url('register') ?>"><?=icon('arrow',16)?> <span>S’inscrire</span></a>
    <?php endif ?><?php endif ?>
</header>
<?php if ($flash): ?><div class="flash-toast <?= e($flash['type']) ?>" role="status"><span class="toast-icon"><?=icon($flash['type']==='error'?'x':'check',17)?></span><span><?= e($flash['message']) ?></span><button class="toast-close" aria-label="Fermer"><?=icon('x',15)?></button><span class="toast-progress"></span></div><?php endif ?>
<?= $content ?>
<script src="public/assets/app.js?v=10"></script>
</body>
</html>
