<div class="app-shell admin-shell">
    <?php $active='admin-content'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-content admin-page">
            <header class="admin-page-header">
                <div>
                    <span>Gestion des médias</span>
                    <h1>Tous les contenus</h1>
                    <p>Consultez, modérez et supervisez l'ensemble des publications de la plateforme.</p>
                </div>
                <a class="button primary" href="<?=url('upload')?>"><?=icon('plus',16)?> Nouveau contenu</a>
            </header>

            <form class="admin-filterbar" method="get">
                <input type="hidden" name="page" value="admin-content">
                <div style="flex:1;min-width:200px;">
                    <?=icon('search',17)?>
                    <input name="search" value="<?=e($search)?>" placeholder="Rechercher par titre ou créateur…">
                </div>
                <select name="type">
                    <option value="">Tous les formats</option>
                    <option value="video" <?=$type==='video'?'selected':''?>>Vidéos</option>
                    <option value="podcast" <?=$type==='podcast'?'selected':''?>>Podcasts</option>
                </select>
                <select name="category">
                    <option value="">Toutes les catégories</option>
                    <?php foreach($categories as $cat): $catName = is_array($cat) ? $cat['name'] : $cat; ?>
                        <option value="<?=e($catName)?>" <?=$category===$catName?'selected':''?>>
                            <?=e($catName)?>
                        </option>
                    <?php endforeach ?>
                </select>
                <select name="status">
                    <option value="">Tous les statuts</option>
                    <option value="published" <?=$status==='published'?'selected':''?>>Publié</option>
                    <option value="draft" <?=$status==='draft'?'selected':''?>>Brouillon</option>
                    <option value="archived" <?=$status==='archived'?'selected':''?>>Archivé</option>
                    <option value="hidden" <?=$status==='hidden'?'selected':''?>>Masqué</option>
                </select>
                <button class="button secondary">Filtrer</button>
                <?php if($search!==''||$type!==''||$category!==''||$status!==''): ?>
                    <a href="<?=url('admin-content')?>" class="button subtle">Effacer</a>
                <?php endif ?>
            </form>

            <section class="admin-table-card">
                <div class="admin-table-head">
                    <div>
                        <strong><?=count($rows)?> contenu<?=count($rows)>1?'s':''?></strong>
                        <span>Résultats actuels</span>
                    </div>
                </div>

                <div class="table-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Contenu</th>
                                <th>Créateur</th>
                                <th>Format</th>
                                <th>Vues</th>
                                <th>Visibilité</th>
                                <th>Statut</th>
                                <th>Publication</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($rows as $row): ?>
                                <tr>
                                    <td>
                                        <div class="admin-content-cell">
                                            <?php if(!empty($row['thumbnail_path'])): ?>
                                                <img src="<?=e($row['thumbnail_path'])?>" style="width:36px;height:24px;object-fit:cover;border-radius:4px;">
                                            <?php else: ?>
                                                <span><?=icon($row['type']==='video'?'video':'podcast',16)?></span>
                                            <?php endif ?>
                                            <div>
                                                <strong>
                                                    <a href="<?=url('media',['id'=>$row['id']])?>" target="_blank" class="link">
                                                        <?=e($row['title'])?>
                                                    </a>
                                                </strong>
                                                <small><?=e($row['category'])?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?=e($row['first_name'].' '.$row['last_name'])?></td>
                                    <td><span class="format-badge"><?=e($row['type'])?></span></td>
                                    <td><?=number_format((int)$row['views_count'],0,',',' ')?></td>
                                    <td>
                                        <span class="visibility-badge <?=e($row['visibility'])?>"><?=e($row['visibility'])?></span>
                                    </td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                            <input type="hidden" name="form" value="admin_toggle_media_status">
                                            <input type="hidden" name="media_id" value="<?=$row['id']?>">
                                            <select name="status" onchange="this.form.submit()" style="padding:2px 6px;font-size:0.8rem;border-radius:4px;border:1px solid var(--border);background:var(--surface);">
                                                <option value="published" <?=$row['status']==='published'?'selected':''?>>Publié</option>
                                                <option value="draft" <?=$row['status']==='draft'?'selected':''?>>Brouillon</option>
                                                <option value="archived" <?=$row['status']==='archived'?'selected':''?>>Archivé</option>
                                                <option value="hidden" <?=$row['status']==='hidden'?'selected':''?>>Masqué</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;">
                                        <?=date('d/m/Y',strtotime($row['created_at']))?>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:6px;align-items:center;">
                                            <a class="button table-button secondary" href="<?=url('edit-media',['id'=>$row['id']])?>" title="Modifier le média">
                                                <?=icon('edit',14)?> <span>Modifier</span>
                                            </a>
                                            <a class="icon-button row-action" href="<?=url('media',['id'=>$row['id']])?>" target="_blank" title="Voir le lecteur">
                                                <?=icon('eye',15)?>
                                            </a>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer définitivement ce contenu ?');">
                                                <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                <input type="hidden" name="form" value="delete">
                                                <input type="hidden" name="media_id" value="<?=$row['id']?>">
                                                <button class="button table-button danger" title="Supprimer">
                                                    <?=icon('trash',14)?>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if(!$rows): ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="guided-empty">
                                            <div class="empty-illustration"><?=icon('library',24)?></div>
                                            <h3>Aucun contenu</h3>
                                            <p>Aucun résultat ne correspond à vos filtres.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
