<main class="auth-page">
    <section class="panel auth-card">
        <div class="auth-brand">
            <a href="<?=url()?>" aria-label="Media Spotlight">
                <img class="logo-img logo-light" src="public/assets/logo.png" alt="Media Spotlight" height="42" width="118">
                <img class="logo-img logo-dark" src="public/assets/logo-dark.png" alt="Media Spotlight" height="42" width="118">
            </a>
        </div>
        
        <h1 class="page-title">Mot de passe oublié</h1>
        <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:20px;">
            Saisissez l'adresse e-mail associée à votre compte. Nous générerons un lien sécurisé temporaire pour réinitialiser votre mot de passe.
        </p>

        <form method="post">
            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
            <input type="hidden" name="form" value="forgot_password">

            <div class="field">
                <label>Adresse e-mail</label>
                <input required type="email" name="email" autocomplete="email" placeholder="nom@exemple.com">
            </div>

            <button class="button primary" style="width:100%;margin-top:12px;">
                <?=icon('mail',16)?> <span>Envoyer le lien de réinitialisation</span>
            </button>
        </form>

        <p style="margin-top:20px;">
            <a class="link" href="<?=url('login')?>">← Retour à la connexion</a>
        </p>
    </section>
</main>
