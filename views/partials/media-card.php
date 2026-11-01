<a class="media-card" href="<?= url('media', ['id' => $media['id']]) ?>">
    <div class="media-thumb <?= $media['type']==='podcast'?'podcast':'' ?>" style="<?=!empty($media['thumbnail_path']) ? 'background-image:url('.e($media['thumbnail_path']).');background-size:cover;background-position:center;' : ''?>">
        <span class="play"><?=icon($media['type']==='podcast'?'podcast':'play',19)?></span>
        <span class="media-type"><?= $media['type']==='podcast'?'Audio':'Vidéo' ?></span>
        <?php if(!empty($media['views_count'])): ?>
            <span class="views-badge" style="position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,0.7);color:#fff;font-size:0.75rem;padding:2px 7px;border-radius:4px;backdrop-filter:blur(4px);display:inline-flex;align-items:center;gap:4px;">
                <?=icon('eye',12)?> <?=number_format((int)$media['views_count'],0,',',' ')?>
            </span>
        <?php endif ?>
    </div>
    <div class="media-body">
        <span class="tag"><?= e(strtoupper($media['category'])) ?></span>
        <h3><?= e($media['title']) ?></h3>
        <p>
            <?= e($media['first_name'].' '.$media['last_name']) ?>
            <span><?= date('d/m/Y', strtotime($media['created_at'])) ?></span>
        </p>
    </div>
</a>
