<main class="container">
    <article class="panel media-detail">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
            <div>
                <a href="<?=url($media['type']==='video'?'videos':'podcasts')?>" class="link" style="font-size:0.875rem;display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <?=icon('arrow',14)?> Retour aux <?=$media['type']==='video'?'vidéos':'podcasts'?>
                </a>
                <h1 class="page-title" style="margin:0;"><?=e($media['title'])?></h1>
            </div>

            <div style="display:flex;gap:10px;align-items:center;">
                <?php if(currentUser() && (currentUser()['id']==$media['user_id'] || currentUser()['role']==='admin')): ?>
                    <a href="<?=url('edit-media',['id'=>$media['id']])?>" class="button secondary">
                        <?=icon('edit',15)?> <span>Modifier</span>
                    </a>
                <?php endif ?>
                <?php if(currentUser()): ?>
                    <button type="button" class="button secondary" onclick="document.getElementById('reportModal').style.display='flex'">
                        <?=icon('flag',15)?> <span>Signaler</span>
                    </button>
                <?php endif ?>
            </div>
        </div>

        <?php if($media['file_path']): ?>
            <?php if($media['type']==='video'): ?>
                <video id="mediaPlayer" class="player" controls controlsList="nodownload" poster="<?=e($media['thumbnail_path'] ?? '')?>">
                    <source src="<?=url('file',['id'=>$media['id'],'token'=>$shareToken])?>" type="<?=e($media['mime_type'])?>">
                </video>
            <?php else: ?>
                <div style="padding:28px 24px;background:var(--surface-light);border-radius:12px;margin-bottom:20px;border:1px solid var(--border);">
                    <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;margin-bottom:20px;">
                        <?php if(!empty($media['thumbnail_path'])): ?>
                            <img src="<?=e($media['thumbnail_path'])?>" alt="Couverture du podcast" style="width:120px;height:120px;border-radius:12px;object-fit:cover;box-shadow:0 8px 24px rgba(0,0,0,0.15);">
                        <?php else: ?>
                            <div style="width:120px;height:120px;border-radius:12px;background:var(--accent-dim);color:var(--accent);display:flex;align-items:center;justify-content:center;">
                                <?=icon('podcast',48)?>
                            </div>
                        <?php endif ?>
                        <div>
                            <span class="badge" style="background:var(--accent-dim);color:var(--accent);font-weight:600;padding:4px 10px;border-radius:8px;font-size:0.8rem;">
                                Épisode Podcast
                            </span>
                            <h2 style="margin:8px 0 4px;"><?=e($media['title'])?></h2>
                            <p style="color:var(--text-muted);margin:0;font-size:0.9rem;">Par <?=e($media['first_name'].' '.$media['last_name'])?> · <?=e($media['category'])?></p>
                        </div>
                    </div>

                    <audio id="mediaPlayer" class="player" controls controlsList="nodownload" style="width:100%;">
                        <source src="<?=url('file',['id'=>$media['id'],'token'=>$shareToken])?>" type="<?=e($media['mime_type'])?>">
                    </audio>

                    <div style="display:flex;gap:10px;justify-content:center;margin-top:14px;">
                        <button type="button" class="button secondary" onclick="skipAudio(-15)">
                            <?=icon('refresh',14)?> <span>-15s</span>
                        </button>
                        <button type="button" class="button secondary" onclick="skipAudio(15)">
                            <span>+15s</span> <?=icon('refresh',14)?>
                        </button>
                    </div>
                </div>
            <?php endif ?>
        <?php else: ?>
            <div class="player empty-state">
                <?=icon($media['type']==='video'?'video':'podcast',30)?> 
                <span>Média en attente de téléversement</span>
            </div>
        <?php endif ?>

        <div style="margin:20px 0;line-height:1.6;font-size:1.05rem;color:var(--text);">
            <?=nl2br(e($media['description']))?>
        </div>

        <div class="media-meta" style="display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin:20px 0;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <span>Par <strong><?=e($media['first_name'].' '.$media['last_name'])?></strong></span>
                <span>•</span>
                <span><?=e($media['category'])?></span>
                <span>•</span>
                <span style="display:inline-flex;align-items:center;gap:4px;">
                    <?=icon('eye',15)?> <?=number_format((int)$media['views_count'], 0, ',', ' ')?> vue<?=(int)$media['views_count']>1?'s':''?>
                </span>
                <span>•</span>
                <span style="color:var(--text-muted);font-size:0.875rem;"><?=date('d/m/Y', strtotime($media['created_at']))?></span>
            </div>
            <div>
                <span class="badge" style="background:var(--surface-light);border:1px solid var(--border);padding:4px 10px;border-radius:6px;font-size:0.8rem;text-transform:capitalize;">
                    <?=e($media['visibility'])?>
                </span>
            </div>
        </div>

        <?php if(currentUser()): ?>
            <div class="action-row" style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap;">
                <form method="post" style="display:inline;">
                    <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                    <input type="hidden" name="form" value="favorite">
                    <input type="hidden" name="media_id" value="<?=$media['id']?>">
                    <button class="button <?=$isFavorite?'primary':'secondary'?>">
                        <?=icon('heart',16)?> 
                        <span><?=$isFavorite?'Retirer des favoris':'Ajouter aux favoris'?></span>
                    </button>
                </form>

                <?php if(currentUser()['id']==$media['user_id'] || currentUser()['role']==='admin'): ?>
                    <button type="button" class="button secondary" onclick="document.getElementById('shareModal').style.display='flex'">
                        <?=icon('share',16)?> <span>Créer un lien de partage sécurisé</span>
                    </button>
                <?php endif ?>
            </div>
        <?php endif ?>

        <?php if(!empty($similar)): ?>
            <div style="margin-top:40px;border-top:1px solid var(--border);padding-top:28px;">
                <h3 style="margin-bottom:18px;">Contenus similaires dans <?=e($media['category'])?></h3>
                <div class="cards-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">
                    <?php foreach($similar as $sim): ?>
                        <a href="<?=url('media',['id'=>$sim['id']])?>" class="panel" style="padding:16px;text-decoration:none;display:flex;flex-direction:column;gap:10px;border-radius:10px;transition:transform 0.2s,box-shadow 0.2s;">
                            <div style="display:flex;align-items:center;gap:8px;font-size:0.8rem;color:var(--text-muted);">
                                <?=icon($sim['type']==='video'?'video':'podcast',15)?>
                                <span><?=strtoupper($sim['type'])?></span>
                                <span>•</span>
                                <span><?=number_format((int)$sim['views_count'],0,',',' ')?> vues</span>
                            </div>
                            <strong style="color:var(--text);font-size:1rem;"><?=e($sim['title'])?></strong>
                            <small style="color:var(--text-muted);"><?=e($sim['first_name'].' '.$sim['last_name'])?></small>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
    </article>
</main>

<!-- Modal Signaler -->
<div id="reportModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div class="panel" style="max-width:480px;width:90%;margin:auto;padding:24px;border-radius:12px;background:var(--surface);">
        <h3 style="margin-top:0;display:flex;align-items:center;gap:8px;color:#ef4444;">
            <?=icon('flag',18)?> <span>Signaler ce contenu</span>
        </h3>
        <p style="color:var(--text-muted);font-size:0.875rem;">
            Votre signalement sera examiné par l'équipe d'administration sous 24 à 48 heures.
        </p>

        <form method="post">
            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
            <input type="hidden" name="form" value="report_media">
            <input type="hidden" name="media_id" value="<?=$media['id']?>">

            <div class="field">
                <label>Motif du signalement</label>
                <select name="reason" required>
                    <option value="Contenu inapproprié">Contenu inapproprié ou offensant</option>
                    <option value="Droits d'auteur">Violation de droits d'auteur</option>
                    <option value="Diffamation">Diffamation ou harcèlement</option>
                    <option value="Informations trompeuses">Informations trompeuses ou fausses</option>
                    <option value="Autre">Autre motif</option>
                </select>
            </div>

            <div class="field">
                <label>Précisions complémentaires (facultatif)</label>
                <textarea name="details" rows="3" placeholder="Expliquez brièvement le problème…"></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <button type="button" class="button secondary" onclick="document.getElementById('reportModal').style.display='none'">Annuler</button>
                <button type="submit" class="button danger">Envoyer le signalement</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Partage Sécurisé -->
<div id="shareModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div class="panel" style="max-width:480px;width:90%;margin:auto;padding:24px;border-radius:12px;background:var(--surface);">
        <h3 style="margin-top:0;display:flex;align-items:center;gap:8px;">
            <?=icon('share',18)?> <span>Créer un lien de partage sécurisé</span>
        </h3>
        <p style="color:var(--text-muted);font-size:0.875rem;">
            Générez une URL unique pour autoriser la consultation de ce média même s'il est privé ou non répertorié.
        </p>

        <form method="post">
            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
            <input type="hidden" name="form" value="share">
            <input type="hidden" name="media_id" value="<?=$media['id']?>">

            <div class="field">
                <label>Durée de validité du lien</label>
                <select name="expires">
                    <option value="never">Permanent (sans expiration)</option>
                    <option value="24h">24 heures</option>
                    <option value="7d">7 jours</option>
                    <option value="30d">30 jours</option>
                </select>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <button type="button" class="button secondary" onclick="document.getElementById('shareModal').style.display='none'">Annuler</button>
                <button type="submit" class="button primary">Générer le lien</button>
            </div>
        </form>
    </div>
</div>

<script>
function skipAudio(seconds) {
    const player = document.getElementById('mediaPlayer');
    if (player) {
        player.currentTime = Math.max(0, player.currentTime + seconds);
    }
}
</script>

<?php require dirname(__DIR__).'/partials/footer.php'; ?>
