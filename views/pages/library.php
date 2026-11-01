<div class="app-shell">
    <?php require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-bar">
            <?=icon($mode==='favorites'?'heart':($mode==='shares'?'share':'library'),16)?> 
            <span><?=e($heading)?></span>
        </div>
        <div class="workspace-content">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                <h1 class="page-title" style="margin:0;"><?=e($heading)?></h1>
                <?php if($mode==='media'): ?>
                    <a href="<?=url('upload')?>" class="button primary">
                        <?=icon('plus',16)?> <span>Nouveau contenu</span>
                    </a>
                <?php endif ?>
            </div>

            <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;border-bottom:1px solid var(--border);padding-bottom:12px;">
                <a href="<?=url('my-media')?>" class="button <?=$mode==='media'&&$filterType===''?'primary':'secondary'?>">Tous les contenus</a>
                <a href="<?=url('my-media',['type'=>'video'])?>" class="button <?=$mode==='media'&&$filterType==='video'?'primary':'secondary'?>"><?=icon('video',15)?> Vidéos</a>
                <a href="<?=url('my-media',['type'=>'podcast'])?>" class="button <?=$mode==='media'&&$filterType==='podcast'?'primary':'secondary'?>"><?=icon('podcast',15)?> Podcasts</a>
                <a href="<?=url('my-media',['type'=>'draft'])?>" class="button <?=$mode==='media'&&$filterType==='draft'?'primary':'secondary'?>">Brouillons</a>
                <a href="<?=url('favorites')?>" class="button <?=$mode==='favorites'?'primary':'secondary'?>"><?=icon('heart',15)?> Favoris</a>
                <a href="<?=url('shares')?>" class="button <?=$mode==='shares'?'primary':'secondary'?>"><?=icon('share',15)?> Liens partagés</a>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <?php if($mode==='shares'): ?>
                            <tr>
                                <th>Média associé</th>
                                <th>Lien de partage</th>
                                <th>Expiration</th>
                                <th>Consultations</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        <?php elseif($mode==='favorites'): ?>
                            <tr>
                                <th>Titre du média</th>
                                <th>Type</th>
                                <th>Catégorie</th>
                                <th>Vues</th>
                                <th>Date d'ajout</th>
                                <th>Actions</th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Titre</th>
                                <th>Format</th>
                                <th>Catégorie</th>
                                <th>Vues</th>
                                <th>Visibilité</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        <?php endif ?>
                    </thead>
                    <tbody>
                        <?php if($mode==='shares'): ?>
                            <?php foreach($rows as $row): 
                                $shareUrl = $baseUrl . '?share=' . $row['token'];
                                $isExpired = $row['expires_at'] && strtotime($row['expires_at']) < time();
                                $isRevoked = !empty($row['revoked_at']);
                            ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="<?=url('media',['id'=>$row['media_id']])?>" class="link">
                                                <?=e($row['title'])?>
                                            </a>
                                        </strong>
                                    </td>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <code class="share-link" style="max-width:280px;overflow:hidden;text-overflow:ellipsis;"><?=e($shareUrl)?></code>
                                            <button type="button" class="button table-button secondary copy-btn" data-url="<?=e($shareUrl)?>" title="Copier l'URL">
                                                <?=icon('copy',14)?> <span>Copier</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);">
                                        <?=$row['expires_at'] ? date('d/m/Y H:i', strtotime($row['expires_at'])) : 'Permanent'?>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:var(--surface-light);border:1px solid var(--border);padding:2px 8px;border-radius:12px;font-size:0.8rem;">
                                            <?=(int)$row['views_count']?> vue<?=(int)$row['views_count']>1?'s':''?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($isRevoked): ?>
                                            <span style="color:#ef4444;font-size:0.8rem;font-weight:600;">Révoqué</span>
                                        <?php elseif($isExpired): ?>
                                            <span style="color:#eab308;font-size:0.8rem;font-weight:600;">Expiré</span>
                                        <?php else: ?>
                                            <span class="active-status"><i></i> Actif</span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php if(!$isRevoked): ?>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('Révoquer ce lien de partage ? Les personnes disposant du lien ne pourront plus y accéder.');">
                                                <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                <input type="hidden" name="form" value="revoke_share">
                                                <input type="hidden" name="share_id" value="<?=$row['id']?>">
                                                <button class="button table-button danger">
                                                    <?=icon('ban',14)?> <span>Révoquer</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color:var(--text-muted);font-size:0.85rem;">Inactif</span>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php elseif($mode==='favorites'): ?>
                            <?php foreach($rows as $row): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <span><?=icon($row['type']==='video'?'video':'podcast',16)?></span>
                                            <strong>
                                                <a href="<?=url('media',['id'=>$row['id']])?>" class="link">
                                                    <?=e($row['title'])?>
                                                </a>
                                            </strong>
                                        </div>
                                    </td>
                                    <td><span class="format-badge"><?=e($row['type'])?></span></td>
                                    <td><?=e($row['category'])?></td>
                                    <td><?=number_format((int)$row['views_count'],0,',',' ')?></td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);"><?=date('d/m/Y', strtotime($row['created_at']))?></td>
                                    <td>
                                        <div style="display:flex;gap:6px;">
                                            <a class="button table-button primary" href="<?=url('media',['id'=>$row['id']])?>">
                                                <?=icon('eye',14)?> <span>Voir</span>
                                            </a>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                <input type="hidden" name="form" value="favorite">
                                                <input type="hidden" name="media_id" value="<?=$row['id']?>">
                                                <button class="button table-button danger" title="Retirer des favoris">
                                                    <?=icon('heart',14)?>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <?php foreach($rows as $row): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <?php if(!empty($row['thumbnail_path'])): ?>
                                                <img src="<?=e($row['thumbnail_path'])?>" style="width:40px;height:28px;object-fit:cover;border-radius:4px;">
                                            <?php else: ?>
                                                <span><?=icon($row['type']==='video'?'video':'podcast',16)?></span>
                                            <?php endif ?>
                                            <strong>
                                                <a href="<?=url('media',['id'=>$row['id']])?>" class="link">
                                                    <?=e($row['title'])?>
                                                </a>
                                            </strong>
                                        </div>
                                    </td>
                                    <td><span class="format-badge"><?=e($row['type'])?></span></td>
                                    <td><?=e($row['category'])?></td>
                                    <td><?=number_format((int)$row['views_count'],0,',',' ')?></td>
                                    <td>
                                        <span class="badge" style="background:var(--surface-light);border:1px solid var(--border);padding:2px 8px;border-radius:6px;font-size:0.8rem;text-transform:capitalize;">
                                            <?=e($row['visibility'])?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($row['status']==='published'): ?>
                                            <span class="active-status"><i></i> Publié</span>
                                        <?php elseif($row['status']==='draft'): ?>
                                            <span style="color:var(--text-muted);font-size:0.8rem;">Brouillon</span>
                                        <?php else: ?>
                                            <span style="color:#eab308;font-size:0.8rem;">Archivé</span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:6px;align-items:center;">
                                            <a class="button table-button secondary" href="<?=url('edit-media',['id'=>$row['id']])?>">
                                                <?=icon('edit',14)?> <span>Modifier</span>
                                            </a>
                                            <a class="button table-button" href="<?=url('media',['id'=>$row['id']])?>">
                                                <?=icon('eye',14)?>
                                            </a>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer définitivement ce contenu ?');">
                                                <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                <input type="hidden" name="form" value="delete">
                                                <input type="hidden" name="media_id" value="<?=$row['id']?>">
                                                <button class="button table-button danger">
                                                    <?=icon('trash',14)?>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                        <?php if(!$rows): ?>
                            <tr>
                                <td colspan="7" class="empty-state">Aucun élément pour le moment.</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const url = btn.getAttribute('data-url');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span>Copié !</span>';
                setTimeout(() => { btn.innerHTML = originalText; }, 2000);
            });
        }
    });
});
</script>
