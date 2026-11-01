<main class="auth-page">
    <section class="panel auth-card">
        <div class="auth-brand">
            <a href="<?=url()?>" aria-label="Media Spotlight">
                <img class="logo-img logo-light" src="public/assets/logo.png" alt="Media Spotlight" height="42" width="118">
                <img class="logo-img logo-dark" src="public/assets/logo-dark.png" alt="Media Spotlight" height="42" width="118">
            </a>
        </div>
        
        <h1 class="page-title">Nouveau mot de passe</h1>
        <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:20px;">
            Choisissez un nouveau mot de passe sécurisé pour le compte <strong><?=e($email)?></strong>.
        </p>

        <form method="post">
            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
            <input type="hidden" name="form" value="reset_password">
            <input type="hidden" name="token" value="<?=e($token)?>">

            <div class="field">
                <label>Nouveau mot de passe</label>
                <input required minlength="10" type="password" name="password" autocomplete="new-password" placeholder="Au moins 10 caractères">
            </div>

            <div class="field">
                <label>Confirmez le mot de passe</label>
                <input required minlength="10" type="password" name="password_confirm" autocomplete="new-password" placeholder="Retapez le mot de passe">
            </div>

            <button class="button primary" style="width:100%;margin-top:12px;">
                <?=icon('check',16)?> <span>Mettre à jour mon mot de passe</span>
            </button>
        </form>

        <p style="margin-top:20px;">
            <a class="link" href="<?=url('login')?>">← Annuler et retourner à la connexion</a>
        </p>
    </section>
</main>
