<section class="catalog-hero">
    <div>
        <span class="section-pill"><?= $type==='video'?'Catalogue vidéo':'Univers audio' ?></span>
        <h1><?=e($heading)?></h1>
        <p><?= $type==='video'?'Apprenez autrement grâce aux productions vidéo de notre communauté.':'Des voix, des histoires et des connaissances à écouter partout.' ?></p>
    </div>
    <div class="catalog-art"><?=icon($type==='video'?'video':'podcast',50)?></div>
</section>

<main class="home-section catalog-main">
    <form class="catalog-toolbar" method="get">
        <input type="hidden" name="page" value="<?=$type==='video'?'videos':'podcasts'?>">
        <div class="search-field">
            <?=icon('search',17)?>
            <input name="search" value="<?=e($search)?>" placeholder="Rechercher un titre ou un sujet…">
        </div>
        <select name="category">
            <option value="">Toutes les catégories</option>
            <?php foreach($categories as $cat): 
                $catName = is_array($cat) ? $cat['name'] : $cat;
            ?>
                <option value="<?=e($catName)?>" <?=$category===$catName?'selected':''?>>
                    <?=e($catName)?>
                </option>
            <?php endforeach ?>
        </select>
        <select name="sort">
            <option value="recent" <?=$sort==='recent'?'selected':''?>>Plus récents</option>
            <option value="popular" <?=$sort==='popular'?'selected':''?>>Plus populaires (vues)</option>
        </select>
        <button class="button primary">Rechercher</button>
        <?php if($search!==''||$category!==''||$sort!=='recent'): ?>
            <a href="<?=url($type==='video'?'videos':'podcasts')?>" class="button subtle">Effacer</a>
        <?php endif ?>
    </form>

    <div class="results-heading">
        <div>
            <h2><?=count($items)?> résultat<?=count($items)>1?'s':''?></h2>
            <p>Contenus publics disponibles</p>
        </div>
    </div>

    <?php if($items): ?>
        <div class="media-grid home-media-grid">
            <?php foreach($items as $media) require dirname(__DIR__).'/partials/media-card.php'; ?>
        </div>
    <?php else: ?>
        <div class="catalog-empty">
            <span><?=icon($type==='video'?'video':'podcast',34)?></span>
            <h3>Aucun contenu trouvé</h3>
            <p>Essayez une autre recherche ou publiez le premier contenu de cette catégorie.</p>
            <a class="button primary" href="<?=url('upload')?>"><?=icon('plus',16)?> Publier</a>
        </div>
    <?php endif ?>
</main>

<?php require dirname(__DIR__).'/partials/footer.php'; ?>
