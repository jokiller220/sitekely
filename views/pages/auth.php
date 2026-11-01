<main class="auth-page">
    <section class="panel auth-card">
        <div class="auth-brand">
            <a href="<?=url()?>" aria-label="Media Spotlight">
                <img class="logo-img logo-light" src="public/assets/logo.png" alt="Media Spotlight" height="42" width="118">
                <img class="logo-img logo-dark" src="public/assets/logo-dark.png" alt="Media Spotlight" height="42" width="118">
            </a>
        </div>
        <h1 class="page-title"><?= $mode==='register'?'Créer votre compte':'Connexion' ?></h1>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
            <input type="hidden" name="form" value="<?=$mode?>">

            <?php if($mode==='register'): ?>
                <div class="form-row">
                    <div class="field">
                        <label>Prénom</label>
                        <input required name="first_name" autocomplete="given-name" placeholder="Ex: Lucas">
                    </div>
                    <div class="field">
                        <label>Nom</label>
                        <input required name="last_name" autocomplete="family-name" placeholder="Ex: Martin">
                    </div>
                </div>
                <div class="field">
                    <label>Type de compte</label>
                    <select name="role">
                        <option value="student">Étudiant</option>
                        <option value="teacher">Enseignant</option>
                    </select>
                </div>
            <?php endif ?>

            <div class="field">
                <label>Adresse e-mail</label>
                <input required type="email" name="email" autocomplete="email" placeholder="nom@exemple.com">
            </div>

            <div class="field">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <label style="margin-bottom:0;">Mot de passe</label>
                    <?php if($mode==='login'): ?>
                        <a href="<?=url('forgot-password')?>" class="link" style="font-size:0.8rem;">Mot de passe oublié ?</a>
                    <?php endif ?>
                </div>
                <div class="password-field-wrap" style="position:relative;display:flex;align-items:center;width:100%;">
                    <input required minlength="10" type="password" name="password" id="authPassword" autocomplete="<?=$mode==='login'?'current-password':'new-password'?>" placeholder="Minimum 10 caractères" style="padding-right:42px;width:100%;">
                    <button type="button" class="password-toggle-btn" aria-label="Afficher ou masquer le mot de passe" title="Afficher ou masquer le mot de passe" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);width:32px;height:32px;display:grid;place-items:center;border:0;background:transparent;cursor:pointer;padding:0;color:var(--muted);">
                        <span class="eye-show"><?=icon('eye', 18)?></span>
                        <span class="eye-hide" hidden><?=icon('eye-off', 18)?></span>
                    </button>
                </div>
            </div>

            <?php if($mode==='register'): ?>
                <div class="field">
                    <label>Confirmez votre mot de passe</label>
                    <div class="password-field-wrap" style="position:relative;display:flex;align-items:center;width:100%;">
                        <input required minlength="10" type="password" name="password_confirm" id="authPasswordConfirm" autocomplete="new-password" placeholder="Retapez votre mot de passe" style="padding-right:42px;width:100%;">
                        <button type="button" class="password-toggle-btn" aria-label="Afficher ou masquer le mot de passe" title="Afficher ou masquer le mot de passe" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);width:32px;height:32px;display:grid;place-items:center;border:0;background:transparent;cursor:pointer;padding:0;color:var(--muted);">
                            <span class="eye-show"><?=icon('eye', 18)?></span>
                            <span class="eye-hide" hidden><?=icon('eye-off', 18)?></span>
                        </button>
                    </div>
                </div>

                <div style="margin:16px 0;">
                    <label style="display:flex;align-items:flex-start;gap:10px;font-size:0.85rem;cursor:pointer;line-height:1.4;">
                        <input type="checkbox" name="terms" value="1" required style="margin-top:3px;">
                        <span>
                            J’ai lu et j’accepte les <a href="<?=url('terms')?>" target="_blank" class="link">Conditions Générales d'Utilisation</a> et la <a href="<?=url('privacy')?>" target="_blank" class="link">Politique de Confidentialité</a>.
                        </span>
                    </label>
                </div>
            <?php endif ?>

            <button class="button primary" style="width:100%;margin-top:10px;">
                <?=icon($mode==='register'?'arrow':'user',16)?> 
                <span><?=$mode==='register'?'Créer mon compte':'Se connecter'?></span>
            </button>
        </form>

        <p style="margin-top:20px;">
            <?=$mode==='register'?'Déjà inscrit ?':'Pas encore de compte ?'?> 
            <a class="link" href="<?=url($mode==='register'?'login':'register')?>">
                <?=$mode==='register'?'Se connecter':'S’inscrire'?>
            </a>
        </p>
    </section>
</main>
