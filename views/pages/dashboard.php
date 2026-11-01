<div class="app-shell dashboard-shell">
<?php $active=$admin?'admin':'dashboard'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
<main class="workspace">
    <div class="workspace-bar dashboard-topline">
        <div><span class="welcome-kicker"><?= $admin?'Console d’administration':'Espace créateur' ?></span><strong>Bonjour, <?=e($user['first_name'])?> 👋</strong></div>
            <div class="dashboard-date"><?=icon('calendar',14)?> <span><?=date('d M Y')?></span></div>
    </div>
    <div class="workspace-content dashboard-content">
        <header class="dashboard-header">
            <div><div class="eyebrow"><?= $admin?'Vue globale de la plateforme':'Votre activité' ?></div><h1><?= $admin?'Tableau de bord administrateur':'Tableau de bord' ?></h1><p><?= $admin?'Suivez les utilisateurs, les publications et les partages depuis un seul espace.':'Gérez vos publications et suivez votre progression.' ?></p></div>
            <div class="dashboard-actions"><a class="button subtle action-outline" href="<?=url($admin?'users':'my-media')?>"><?=icon($admin?'users':'library',16)?> <span><?= $admin?'Gérer les utilisateurs':'Voir mes contenus' ?></span></a><a class="button primary" href="<?=url('upload')?>"><?=icon('plus',16)?> <span>Publier</span></a></div>
        </header>

        <section class="dashboard-metrics">
            <?php foreach($stats as $stat): ?>
            <article class="stat-card <?=$stat['tone']?>"><div class="stat-icon"><?=icon($stat['icon'],18)?></div><div class="stat-value"><?=number_format($stat['value'],0,',',' ')?></div><div class="stat-label"><?=e($stat['label'])?></div><div class="stat-note"><?=e($stat['note'])?></div></article>
            <?php endforeach ?>
        </section>

        <section class="dashboard-grid">
            <article class="dashboard-panel activity-panel">
                <div class="panel-heading"><div><h2>Activité des publications</h2><p>Les 7 derniers jours</p></div><span class="trend-badge">Données réelles</span></div>
                <div class="bar-chart" role="img" aria-label="Publications des sept derniers jours">
                    <?php foreach($chart as $point): ?><div class="chart-column"><div class="bar-value"><?=$point['value']?></div><div class="chart-track"><div class="chart-bar" style="height:<?=max(6,round($point['value']/$maxChart*100))?>%"></div></div><span><?=e($point['label'])?></span></div><?php endforeach ?>
                </div>
            </article>
            <aside class="dashboard-panel quick-panel">
                <div class="panel-heading"><div><h2>Actions rapides</h2><p>Accès aux tâches fréquentes</p></div></div>
                <a href="<?=url('upload')?>"><span class="quick-icon purple"><?=icon('upload',17)?></span><div><strong>Publier un contenu</strong><small>Ajouter une vidéo ou un podcast</small></div><?=icon('chevron',16)?></a>
                <a href="<?=url('my-media')?>"><span class="quick-icon blue"><?=icon('library',17)?></span><div><strong>Gérer les contenus</strong><small>Modifier, partager ou supprimer</small></div><?=icon('chevron',16)?></a>
                <a href="<?=url($admin?'users':'favorites')?>"><span class="quick-icon green"><?=icon($admin?'users':'heart',17)?></span><div><strong><?= $admin?'Utilisateurs':'Mes favoris' ?></strong><small><?= $admin?'Consulter les comptes':'Retrouver les contenus sauvegardés' ?></small></div><?=icon('chevron',16)?></a>
            </aside>
        </section>

        <section class="dashboard-lower <?= $admin?'with-users':'' ?>">
            <article class="dashboard-panel recent-panel">
                <div class="panel-heading"><div><h2>Contenus récents</h2><p><?= $admin?'Dernières publications de la plateforme':'Vos dernières publications' ?></p></div><a class="link" href="<?=url('my-media')?>">Voir tout →</a></div>
                <?php if($recent): ?><div class="dashboard-table"><table><thead><tr><th>Contenu</th><th>Type</th><th>Visibilité</th><th>Date</th><th></th></tr></thead><tbody><?php foreach($recent as $item): ?><tr><td><div class="content-cell"><span class="content-symbol <?=$item['type']==='podcast'?'audio':''?>"><?=icon($item['type']==='podcast'?'podcast':'video',15)?></span><div><strong><?=e($item['title'])?></strong><small><?=e(($item['first_name']??$user['first_name']).' '.($item['last_name']??$user['last_name']))?></small></div></div></td><td><?=e($item['type']==='podcast'?'Podcast':'Vidéo')?></td><td><span class="visibility-badge <?=e($item['visibility'])?>"><?=e($item['visibility'])?></span></td><td><?=date('d/m/Y',strtotime($item['created_at']))?></td><td><a class="row-action icon-button" href="<?=url('media',['id'=>$item['id']])?>" aria-label="Ouvrir le contenu"><?=icon('more',17)?></a></td></tr><?php endforeach ?></tbody></table></div>
                <?php else: ?><div class="guided-empty"><div class="empty-illustration"><?=icon('video',25)?></div><h3>Aucun contenu publié</h3><p>Commencez par ajouter votre première vidéo ou votre premier podcast.</p><a class="button primary" href="<?=url('upload')?>"><?=icon('plus',16)?> <span>Créer un contenu</span></a></div><?php endif ?>
            </article>
            <?php if($admin): ?><aside class="dashboard-panel users-panel"><div class="panel-heading"><div><h2>Nouveaux utilisateurs</h2><p>Dernières inscriptions</p></div><a class="link" href="<?=url('users')?>">Voir tout</a></div><?php foreach($recentUsers as $member): ?><div class="user-row"><span class="user-avatar"><?=e(strtoupper(substr($member['first_name'],0,1).substr($member['last_name'],0,1)))?></span><div><strong><?=e($member['first_name'].' '.$member['last_name'])?></strong><small><?=e(ucfirst($member['role']))?> · <?=date('d/m/Y',strtotime($member['created_at']))?></small></div><span class="online-dot"></span></div><?php endforeach ?></aside><?php endif ?>
        </section>
    </div>
</main>
</div>
