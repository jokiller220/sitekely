<div class="app-shell">
    <?php $active='teacher'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-content admin-page">
            <header class="admin-page-header">
                <div>
                    <span>Espace Pédagogique</span>
                    <h1>Espace Enseignant</h1>
                    <p>Consultez les travaux et médias produits par vos étudiants, suivez leurs publications et examinez leurs projets.</p>
                </div>
                <div style="display:flex;gap:12px;">
                    <div class="header-stat">
                        <strong><?=count($students)?></strong>
                        <span>étudiants</span>
                    </div>
                    <div class="header-stat">
                        <strong><?=count($submissions)?></strong>
                        <span>travaux reçus</span>
                    </div>
                </div>
            </header>

            <div class="profile-grid">
                <section class="admin-table-card" style="margin:0;">
                    <div class="admin-table-head">
                        <div>
                            <strong>Travaux récents des étudiants</strong>
                            <span>Vidéos et podcasts téléversés par les étudiants inscrits</span>
                        </div>
                    </div>

                    <div class="table-scroll">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Titre du travail</th>
                                    <th>Étudiant</th>
                                    <th>Format</th>
                                    <th>Catégorie</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($submissions as $sub): ?>
                                    <tr>
                                        <td>
                                            <div class="admin-content-cell">
                                                <span><?=icon($sub['type']==='video'?'video':'podcast',16)?></span>
                                                <div>
                                                    <strong><?=e($sub['title'])?></strong>
                                                    <small><?=e($sub['visibility'])?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?=e($sub['first_name'].' '.$sub['last_name'])?></strong>
                                            <br><small style="color:var(--text-muted);"><?=e($sub['email'])?></small>
                                        </td>
                                        <td>
                                            <span class="format-badge"><?=e($sub['type'])?></span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background:var(--surface-light);border:1px solid var(--border);padding:2px 8px;border-radius:6px;font-size:0.8rem;">
                                                <?=e($sub['category'])?>
                                            </span>
                                        </td>
                                        <td style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;">
                                            <?=date('d/m/Y', strtotime($sub['created_at']))?>
                                        </td>
                                        <td>
                                            <a href="<?=url('media',['id'=>$sub['id']])?>" target="_blank" class="button table-button primary">
                                                <?=icon('eye',14)?> <span>Consulter</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                                <?php if(!$submissions): ?>
                                    <tr>
                                        <td colspan="6" class="empty-state">Aucun travail d'étudiant pour le moment.</td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <aside class="profile-side">
                    <section class="profile-card">
                        <div class="profile-card-heading">
                            <span><?=icon('users',20)?></span>
                            <div>
                                <h2>Répertoire des étudiants</h2>
                                <p>Volume de contenus par étudiant.</p>
                            </div>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <?php foreach($students as $st): ?>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                                    <div>
                                        <strong><?=e($st['first_name'].' '.$st['last_name'])?></strong>
                                        <br><small style="color:var(--text-muted);"><?=e($st['email'])?></small>
                                    </div>
                                    <span class="badge" style="background:var(--accent-dim);color:var(--accent);font-weight:600;padding:3px 8px;border-radius:12px;font-size:0.8rem;">
                                        <?=(int)$st['count_works']?> projet<?=(int)$st['count_works']>1?'s':''?>
                                    </span>
                                </div>
                            <?php endforeach ?>
                            <?php if(!$students): ?>
                                <p class="empty-state">Aucun étudiant inscrit.</p>
                            <?php endif ?>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </main>
</div>
