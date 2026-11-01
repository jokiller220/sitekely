<div class="app-shell">
    <?php require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-bar">
            <?=icon('edit',16)?> <span>Modification de contenu</span>
        </div>
        <div class="workspace-content">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
                <div>
                    <a href="<?=url('my-media')?>" class="link" style="font-size:0.875rem;display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;">
                        <?=icon('arrow',14)?> Retour à mes contenus
                    </a>
                    <h1 class="page-title" style="margin:0;">Modifier : <?=e($media['title'])?></h1>
                </div>
                <div style="display:flex;gap:10px;">
                    <a href="<?=url('media',['id'=>$media['id']])?>" class="button secondary" target="_blank">
                        <?=icon('eye',15)?> <span>Prévisualiser</span>
                    </a>
                </div>
            </div>

            <div class="profile-grid">
                <section class="profile-card">
                    <div class="profile-card-heading">
                        <span><?=icon('edit',20)?></span>
                        <div>
                            <h2>Informations du média</h2>
                            <p>Modifiez le titre, la description, la catégorie et les paramètres de diffusion.</p>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                        <input type="hidden" name="form" value="edit_media">
                        <input type="hidden" name="media_id" value="<?=$media['id']?>">

                        <div class="field">
                            <label>Titre du contenu</label>
                            <input required maxlength="150" name="title" value="<?=e($media['title'])?>" placeholder="Titre percutant…">
                        </div>

                        <div class="form-row">
                            <div class="field">
                                <label>Catégorie</label>
                                <select name="category" required>
                                    <?php foreach($categories as $cat): $catName = is_array($cat) ? $cat['name'] : $cat; ?>
                                        <option value="<?=e($catName)?>" <?=$media['category']===$catName?'selected':''?>>
                                            <?=e($catName)?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="field">
                                <label>Visibilité</label>
                                <select name="visibility">
                                    <option value="public" <?=$media['visibility']==='public'?'selected':''?>>Public (visible dans le catalogue)</option>
                                    <option value="unlisted" <?=$media['visibility']==='unlisted'?'selected':''?>>Non répertorié (accessible par lien uniquement)</option>
                                    <option value="private" <?=$media['visibility']==='private'?'selected':''?>>Privé (visible seulement par vous)</option>
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label>Statut de publication</label>
                            <select name="status">
                                <option value="published" <?=$media['status']==='published'?'selected':''?>>Publié (en ligne)</option>
                                <option value="draft" <?=$media['status']==='draft'?'selected':''?>>Brouillon (non accessible publiquement)</option>
                                <option value="archived" <?=$media['status']==='archived'?'selected':''?>>Archivé</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Description détaillée</label>
                            <textarea rows="5" name="description" placeholder="Présentation du contenu…"><?=e($media['description'])?></textarea>
                        </div>

                        <div class="form-row" style="margin-top:20px;">
                            <div class="field">
                                <label>Miniature / Couverture personnalisée</label>
                                <input type="file" name="thumbnail" accept="image/png,image/jpeg,image/webp">
                                <small>Format JPG, PNG ou WebP (max 5 Mo). Laissez vide pour conserver l'actuelle.</small>
                                <?php if(!empty($media['thumbnail_path'])): ?>
                                    <div style="margin-top:8px;display:flex;align-items:center;gap:10px;">
                                        <img src="<?=e($media['thumbnail_path'])?>" alt="Miniature actuelle" style="width:70px;height:45px;object-fit:cover;border-radius:6px;border:1px solid var(--border);">
                                        <span style="font-size:0.8rem;color:var(--text-muted);">Miniature actuelle</span>
                                    </div>
                                <?php endif ?>
                            </div>

                            <div class="field">
                                <label>Remplacer le fichier média (optionnel)</label>
                                <input type="file" name="media_file" accept="<?=$media['type']==='video'?'video/mp4,video/webm':'audio/mpeg,audio/mp3,audio/wav'?>">
                                <small>Remplacera le fichier existant (actuellement : <?=basename($media['file_path'])?>).</small>
                            </div>
                        </div>

                        <div class="profile-form-actions" style="margin-top:24px;">
                            <button class="button primary">
                                <?=icon('check',17)?> <span>Enregistrer les modifications</span>
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="profile-side">
                    <section class="profile-card">
                        <div class="profile-card-heading">
                            <span><?=icon('chart',20)?></span>
                            <div>
                                <h2>Statistiques</h2>
                                <p>Informations sur les consultations.</p>
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:12px;font-size:0.9rem;">
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-muted);">Format</span>
                                <strong><?=strtoupper($media['type'])?></strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-muted);">Nombre de vues</span>
                                <strong><?=number_format((int)$media['views_count'], 0, ',', ' ')?></strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-muted);">Date de création</span>
                                <span><?=date('d/m/Y H:i', strtotime($media['created_at']))?></span>
                            </div>
                        </div>
                    </section>

                    <section class="profile-card" style="border-color: rgba(239, 68, 68, 0.25);">
                        <div class="profile-card-heading">
                            <span style="color:#ef4444;"><?=icon('trash',20)?></span>
                            <div>
                                <h2 style="color:#ef4444;">Zone de danger</h2>
                                <p>Suppression irréversible du contenu.</p>
                            </div>
                        </div>
                        <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce média ? Cette action est irréversible.');">
                            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                            <input type="hidden" name="form" value="delete">
                            <input type="hidden" name="media_id" value="<?=$media['id']?>">
                            <button class="button danger" style="width:100%;justify-content:center;">
                                <?=icon('trash',16)?> <span>Supprimer ce média</span>
                            </button>
                        </form>
                    </section>
                </aside>
            </div>
        </div>
    </main>
</div>
