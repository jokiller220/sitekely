<main class="container">
    <div style="max-width:920px;margin:32px auto;">
        <nav style="display:flex;gap:12px;margin-bottom:28px;flex-wrap:wrap;border-bottom:1px solid var(--border);padding-bottom:12px;">
            <a href="<?=url('legal')?>" class="button <?=$section==='legal'?'primary':'secondary'?>">Mentions légales</a>
            <a href="<?=url('privacy')?>" class="button <?=$section==='privacy'?'primary':'secondary'?>">Politique de confidentialité</a>
            <a href="<?=url('terms')?>" class="button <?=$section==='terms'?'primary':'secondary'?>">Conditions d’utilisation</a>
            <a href="<?=url('cookies')?>" class="button <?=$section==='cookies'?'primary':'secondary'?>">Politique des cookies</a>
        </nav>

        <article class="panel" style="padding:36px;line-height:1.7;">
            <?php if($section==='legal'): ?>
                <h1 class="page-title" style="margin-top:0;">Mentions Légales</h1>
                <p>Conformément aux dispositions de la loi n° 2004-575 du 21 juin 2004 pour la confiance en l'économie numérique, voici les informations légales relatives à la plateforme <strong>Media Spotlight</strong>.</p>
                
                <h2>1. Éditeur de la plateforme</h2>
                <p>La plateforme <strong>Media Spotlight</strong> est éditée par le département Médias & Numérique académique.<br>
                Adresse de contact : <a href="mailto:contact@mediaspotlight.local" class="link">contact@mediaspotlight.local</a>.<br>
                Directeur de publication : Responsable éditorial Media Spotlight.</p>

                <h2>2. Hébergement</h2>
                <p>La plateforme est hébergée sur une infrastructure locale sécurisée conforme aux normes de protection des données scolaires et universitaires.</p>

                <h2>3. Propriété intellectuelle</h2>
                <p>L'ensemble des contenus multimédias (vidéos, podcasts, textes et visuels) publiés sur Media Spotlight demeurent la propriété respective de leurs auteurs étudiants et enseignants. Toute reproduction ou rediffusion publique non autorisée est interdite sans l'accord préalable de l'auteur.</p>

            <?php elseif($section==='privacy'): ?>
                <h1 class="page-title" style="margin-top:0;">Politique de Confidentialité (RGPD)</h1>
                <p>Media Spotlight attache une importance primordiale à la protection de la vie privée et des données à caractère personnel de ses membres.</p>

                <h2>1. Données collectées</h2>
                <p>Dans le cadre du fonctionnement du service, nous collectons les données strictement nécessaires : prénom, nom, adresse e-mail académique, rôle (étudiant, enseignant, administrateur), avatar, et logs de connexion.</p>

                <h2>2. Finalité des traitements</h2>
                <p>Ces données sont utilisées uniquement pour : l'authentification sécurisée, la gestion des contenus publiés, le partage sécurisé entre étudiants et enseignants, et les notifications relatives à l'activité de votre compte.</p>

                <h2>3. Vos droits</h2>
                <p>Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, de rectification et de suppression totale de vos données. Vous pouvez à tout moment supprimer votre compte directement depuis vos <a href="<?=url('settings')?>" class="link">Paramètres</a>.</p>

            <?php elseif($section==='terms'): ?>
                <h1 class="page-title" style="margin-top:0;">Conditions Générales d’Utilisation (CGU)</h1>
                <p>En créant un compte sur Media Spotlight, chaque utilisateur accepte de respecter les présentes conditions générales d'utilisation.</p>

                <h2>1. Accès au service</h2>
                <p>Media Spotlight est réservé aux étudiants, enseignants et membres habilités. Chaque utilisateur s'engage à fournir des informations véridiques et à maintenir la confidentialité de ses identifiants de connexion.</p>

                <h2>2. Règles de publication et modération</h2>
                <p>Tout contenu contenant des propos diffamatoires, discriminatoires, haineux, ou portant atteinte aux droits de propriété intellectuelle fera l'objet d'un masquage immédiat et d'éventuelles sanctions disciplinaires ou légales.</p>

                <h2>3. Partages de liens sécurisés</h2>
                <p>Les liens de partage temporaires et sécurisés sont émis sous la responsabilité du créateur du contenu. Ils peuvent être révoqués à tout moment par l'auteur ou par un administrateur.</p>

            <?php elseif($section==='cookies'): ?>
                <h1 class="page-title" style="margin-top:0;">Politique relative aux Cookies</h1>
                <p>Media Spotlight utilise un nombre minimal de cookies strictement indispensables au bon fonctionnement technique de la plateforme.</p>

                <h2>1. Cookies de session</h2>
                <p>Ces cookies permettent de maintenir votre authentification sécurisée tout au long de votre navigation (cookie de session PHP). Ils sont détruits dès la fermeture du navigateur ou lors de la déconnexion.</p>

                <h2>2. Cookies de préférences</h2>
                <p>Un cookie ou stockage local peut être utilisé pour mémoriser votre préférence d'affichage (mode sombre ou clair).</p>

                <h2>3. Cookies tiers et traceurs publicitaires</h2>
                <p>Media Spotlight ne dépose aucun cookie publicitaire ni aucun traceur commercial ou d'analyse invasive tierce.</p>
            <?php endif ?>
        </article>
    </div>
</main>
<?php require dirname(__DIR__).'/partials/footer.php'; ?>
