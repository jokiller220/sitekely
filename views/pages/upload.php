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
                <div class="field" style="margin-top:14px;"><label for="mediaThumbnail">Miniature / Couverture personnalisée (facultatif)</label><input id="mediaThumbnail" name="thumbnail" type="file" accept="image/png,image/jpeg,image/webp"><small>Format JPG, PNG ou WebP pour illustrer la vidéo ou l'audio.</small></div>
                <div class="form-section-title spaced"><span>02</span><div><h2>Fichier multimédia</h2><p>MP4, WebM, MP3, WAV ou OGG · 500 Mo maximum.</p></div></div>
                <div class="drop-zone" id="dropZone" tabindex="0" role="button" aria-label="Sélectionner un fichier"><input id="mediaFile" name="media" type="file" accept="video/mp4,video/webm,video/ogg,audio/mpeg,audio/wav,audio/ogg,audio/mp4"><span class="drop-icon"><?=icon('upload',26)?></span><strong>Glissez votre fichier ici</strong><p>ou <u>parcourez votre ordinateur</u></p><small>Votre fichier reste privé jusqu’à la publication.</small></div>
                <div class="file-preview" id="filePreview" hidden><span class="file-kind"><?=icon('file',21)?></span><div><strong id="fileName"></strong><small id="fileMeta"></small><div class="fake-progress"><span></span></div></div><button type="button" id="removeFile" aria-label="Retirer le fichier"><?=icon('x',16)?></button></div>
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
