<div class="app-shell">
<?php $active='upload'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
<main class="workspace">
    <div class="workspace-bar"><?=icon('upload',16)?> <span>Publier un média</span></div>
    <div class="workspace-content upload-workspace">
        <header class="upload-header"><div><span class="eyebrow">Nouvelle publication</span><h1 class="page-title">Ajouter un contenu</h1><p>Renseignez les informations et vérifiez votre fichier avant de publier.</p></div><div class="upload-steps"><span class="active">1</span><i></i><span>2</span><i></i><span>3</span></div></header>
        <form class="upload-layout" method="post" enctype="multipart/form-data" id="uploadForm">
            <input type="hidden" name="csrf" value="<?=csrfToken()?>"><input type="hidden" name="form" value="upload">
            <section class="panel upload-main">
                <div class="form-section-title"><span>01</span><div><h2>Informations principales</h2><p>Présentez clairement votre contenu.</p></div></div>
                <div class="field"><label for="mediaTitle">Titre du contenu <b>*</b></label><input id="mediaTitle" name="title" required maxlength="160" placeholder="Ex. Introduction à l’intelligence artificielle"><small><span id="titleCount">0</span>/160 caractères</small></div>
                <div class="field"><label for="mediaDescription">Description</label><textarea id="mediaDescription" name="description" placeholder="Expliquez ce que les utilisateurs vont découvrir…"></textarea></div>
                <div class="form-row"><div class="field"><label for="mediaType">Format <b>*</b></label><select id="mediaType" name="type"><option value="video">Vidéo</option><option value="podcast">Podcast</option></select></div><div class="field"><label for="mediaCategory">Catégorie <b>*</b></label><select id="mediaCategory" name="category"><?php foreach($categories as $cat): $cName=is_array($cat)?$cat['name']:$cat; ?><option value="<?=e($cName)?>"><?=e($cName)?></option><?php endforeach ?></select></div></div>
                <div class="field" style="margin-top:14px;">
                    <label for="mediaThumbnail">Miniature / Couverture personnalisée (facultatif)</label>
                    <input id="mediaThumbnail" name="thumbnail" type="file" accept="image/png,image/jpeg,image/webp">
                    <small>Format JPG, PNG ou WebP pour illustrer la vidéo ou l'audio.</small>
                    <div id="thumbPreviewWrap" class="thumb-preview-wrap" hidden>
                        <img id="thumbPreviewImg" src="" alt="Aperçu miniature">
                        <button type="button" id="removeThumb" class="thumb-remove-btn" title="Supprimer la miniature"><?=icon('x',14)?></button>
                    </div>
                </div>

                <div class="form-section-title spaced"><span>02</span><div><h2>Fichier multimédia</h2><p>MP4, WebM, MP3, WAV ou OGG · 500 Mo maximum.</p></div></div>
                <div class="drop-zone" id="dropZone" tabindex="0" role="button" aria-label="Sélectionner un fichier">
                    <input id="mediaFile" name="media" type="file" accept="video/mp4,video/webm,video/ogg,audio/mpeg,audio/wav,audio/ogg,audio/mp4" required>
                    <span class="drop-icon"><?=icon('upload',26)?></span>
                    <strong>Glissez votre fichier vidéo ou podcast ici</strong>
                    <p>ou <u>parcourez votre ordinateur</u></p>
                    <small>Formats acceptés : Vidéos MP4/WebM ou Podcasts MP3/WAV/OGG jusqu’à 500 Mo</small>
                </div>

                <div class="file-preview" id="filePreview" hidden>
                    <span class="file-kind" id="fileKindIcon"><?=icon('file',21)?></span>
                    <div class="file-preview-body">
                        <div class="file-preview-header">
                            <strong id="fileName"></strong>
                            <div class="file-badges">
                                <span class="preview-badge" id="previewFormatBadge">Format</span>
                                <span class="preview-badge duration-badge" id="previewDurationBadge" hidden>⏱ 00:00</span>
                            </div>
                        </div>
                        <small id="fileMeta"></small>
                        
                        <!-- Lecteur interactif pour prévisualiser le fichier avant la mise en ligne -->
                        <div class="live-player-container" id="livePlayerWrap" hidden>
                            <video id="previewVideo" controls playsinline preload="metadata" hidden></video>
                            <div id="previewAudioWrap" class="preview-audio-box" hidden>
                                <div class="audio-wave-anim"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
                                <audio id="previewAudio" controls preload="metadata"></audio>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="removeFile" aria-label="Retirer le fichier" title="Retirer ce fichier"><?=icon('x',16)?></button>
                </div>
            </section>
            <aside class="upload-side">
                <section class="panel"><div class="form-section-title"><span>03</span><div><h2>Publication</h2><p>Contrôlez qui peut accéder au contenu.</p></div></div><div class="visibility-options"><label><input type="radio" name="visibility" value="public" checked><span><?=icon('users',18)?><b>Public</b><small>Visible dans le catalogue</small></span></label><label><input type="radio" name="visibility" value="unlisted"><span><?=icon('share',18)?><b>Non répertorié</b><small>Accessible uniquement par lien</small></span></label><label><input type="radio" name="visibility" value="private"><span><?=icon('eye',18)?><b>Privé</b><small>Réservé aux personnes autorisées</small></span></label></div>
                <div class="field" style="margin-top:16px;"><label>Statut</label><select name="status"><option value="published">Publier immédiatement</option><option value="draft">Enregistrer comme brouillon</option></select></div>
                </section>
                <div class="publish-summary"><div><?=icon('check',17)?><span>Vos données sont protégées</span></div><p>Vous pourrez modifier ou supprimer cette publication depuis votre tableau de bord.</p><button class="button primary publish-button" id="publishButton"><?=icon('upload',17)?><span>Publier le contenu</span></button><a href="<?=url('dashboard')?>">Annuler et revenir</a></div>
            </aside>
        </form>
    </div>
</main>
</div>

<!-- Modal UI de progression et délai d'encodage / mise en ligne -->
<div class="upload-modal-overlay" id="uploadModal" hidden aria-hidden="true" role="dialog" aria-labelledby="modalHeading">
    <div class="upload-modal-backdrop"></div>
    <div class="upload-modal-card">
        <div class="modal-format-pill" id="modalFormatPill">
            <span class="pulsing-dot"></span>
            <span id="modalFormatLabel">Mise en ligne en cours</span>
        </div>

        <h3 id="modalHeading" class="upload-modal-title">Traitement et mise en ligne</h3>
        <p class="upload-modal-subtitle" id="modalMediaTitle">Votre média est en cours de publication…</p>

        <!-- Barre de chargement moderne -->
        <div class="upload-progress-section">
            <div class="upload-bar-track">
                <div class="upload-bar-fill" id="uploadBarFill" style="width: 0%;">
                    <span class="bar-shine"></span>
                </div>
            </div>
            <div class="upload-bar-metrics">
                <span class="upload-percentage" id="uploadPercentage">0%</span>
                <span class="upload-size-info" id="uploadSizeInfo">Préparation…</span>
            </div>
        </div>

        <!-- Stepper interactif de progression par étapes -->
        <div class="upload-stepper">
            <div class="stepper-step active" id="stepUpload">
                <div class="step-bullet"><?=icon('upload', 14)?></div>
                <div class="step-details">
                    <span class="step-title">Téléversement du fichier</span>
                    <span class="step-caption" id="stepUploadCaption">Transfert sécurisé vers le serveur…</span>
                </div>
                <span class="step-status-tag" id="stepUploadTag">En cours</span>
            </div>

            <div class="stepper-step" id="stepProcess">
                <div class="step-bullet"><?=icon('refresh', 14)?></div>
                <div class="step-details">
                    <span class="step-title">Traitement & Optimisation</span>
                    <span class="step-caption" id="stepProcessCaption">Contrôle des flux audio/vidéo et encodage…</span>
                </div>
                <span class="step-status-tag" id="stepProcessTag">En attente</span>
            </div>

            <div class="stepper-step" id="stepFinalize">
                <div class="step-bullet"><?=icon('shield', 14)?></div>
                <div class="step-details">
                    <span class="step-title">Indexation & Publication</span>
                    <span class="step-caption" id="stepFinalizeCaption">Attribution des permissions et mise en ligne…</span>
                </div>
                <span class="step-status-tag" id="stepFinalizeTag">En attente</span>
            </div>
        </div>

        <div class="modal-footer-notice" id="modalFooterNotice">
            <p>⚡ <b>Patience :</b> Le média est optimisé pour garantir une lecture fluide sur mobile et ordinateur.</p>
        </div>

        <div class="modal-error-box" id="modalErrorBox" hidden>
            <p id="modalErrorMessage"></p>
            <button type="button" class="button secondary" id="modalRetryBtn">Fermer et modifier</button>
        </div>
    </div>
</div>
