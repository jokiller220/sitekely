<?php 
$active = $active ?? '';
$user = currentUser();
$isAdmin = $user && $user['role'] === 'admin';
$isTeacher = $user && $user['role'] === 'teacher';
$db = $GLOBALS['db'] ?? null;
$pendingReports = 0;
$unreadMessages = 0;
if ($isAdmin && $db instanceof PDO) {
    try {
        $pendingReports = (int)$db->query("SELECT COUNT(*) FROM reports WHERE status='pending'")->fetchColumn();
        $unreadMessages = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
    } catch (PDOException) {}
}
?>
<aside class="sidebar <?=$isAdmin?'admin-sidebar':''?>">
<?php if($isAdmin): ?>
    <div class="admin-sidebar-heading">
        <span><?=icon('shield',18)?></span>
        <div><strong>Administration</strong><small>Console de gestion</small></div>
    </div>

    <p class="sidebar-label">Supervision</p>
    <a class="<?=$active==='admin'?'active':''?>" href="<?=url('admin')?>">
        <?=icon('chart')?> <span>Tableau de bord</span>
    </a>
    <a class="<?=$active==='admin-content'?'active':''?>" href="<?=url('admin-content')?>">
        <?=icon('shield')?> <span>Contenus</span><em>›</em>
    </a>
    <a class="<?=$active==='users'?'active':''?>" href="<?=url('users')?>">
        <?=icon('users')?> <span>Utilisateurs</span><em>›</em>
    </a>
    <a class="<?=$active==='categories'?'active':''?>" href="<?=url('categories')?>">
        <?=icon('tag',17)?> <span>Catégories</span><em>›</em>
    </a>
    <a class="<?=$active==='reports'?'active':''?>" href="<?=url('reports')?>">
        <?=icon('flag',17)?> <span>Signalements</span>
        <?php if($pendingReports > 0): ?>
            <b style="margin-left:auto;background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:9px;line-height:1.4;"><?=$pendingReports?></b>
        <?php else: ?>
            <em>›</em>
        <?php endif ?>
    </a>
    <a class="<?=$active==='admin-messages'?'active':''?>" href="<?=url('admin-messages')?>">
        <?=icon('mail',17)?> <span>Messages</span>
        <?php if($unreadMessages > 0): ?>
            <b style="margin-left:auto;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:9px;line-height:1.4;"><?=$unreadMessages?></b>
        <?php else: ?>
            <em>›</em>
        <?php endif ?>
    </a>

    <p class="sidebar-label">Personnel</p>
    <a class="<?=in_array($active, ['media','favorites','shares','upload'], true)?'active':''?>" href="<?=url('my-media')?>">
        <?=icon('library',17)?> <span>Mes médias</span>
    </a>
    <a class="<?=$active==='settings'?'active':''?>" href="<?=url('settings')?>">
        <?=icon('lock',17)?> <span>Paramètres</span>
    </a>

<?php else: ?>
    <?php if($isTeacher): ?>
        <p class="sidebar-label">Enseignement</p>
        <a class="<?=$active==='teacher'?'active':''?>" href="<?=url('teacher')?>">
            <?=icon('users',17)?> <span>Espace Enseignant</span>
        </a>
    <?php endif ?>

    <p class="sidebar-label">Mon espace</p>
    <a class="<?=$active==='dashboard'?'active':''?>" href="<?=url('dashboard')?>">
        <?=icon('home')?> <span>Tableau de bord</span>
    </a>
    <a class="<?=$active==='upload'?'active':''?>" href="<?=url('upload')?>">
        <?=icon('upload')?> <span>Publier un contenu</span>
    </a>
    <a class="<?=$active==='media'?'active':''?>" href="<?=url('my-media')?>">
        <?=icon('library')?> <span>Mes contenus</span>
    </a>
    <a class="<?=$active==='favorites'?'active':''?>" href="<?=url('favorites')?>">
        <?=icon('heart')?> <span>Mes favoris</span>
    </a>
    <a class="<?=$active==='shares'?'active':''?>" href="<?=url('shares')?>">
        <?=icon('share')?> <span>Liens partagés</span>
    </a>
    <a class="<?=$active==='settings'?'active':''?>" href="<?=url('settings')?>">
        <?=icon('lock',17)?> <span>Paramètres</span>
    </a>
<?php endif ?>
</aside>
