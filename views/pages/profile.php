<?php $isAdmin=$profile['role']==='admin'; ?>
<div class="app-shell <?=$isAdmin?'admin-shell':''?>">
    <?php $active='profile'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-content profile-page">
            <header class="profile-hero">
                <div class="profile-avatar-xl" style="position:relative;overflow:hidden;padding:0;">
                    <?php if(!empty($profile['avatar_path'])): ?>
                        <img src="<?=e($profile['avatar_path'])?>" alt="Avatar de profil" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                    <?php else: ?>
                        <span style="font-size:2rem;font-weight:700;">
                            <?=e(strtoupper(substr($profile['first_name'],0,1).substr($profile['last_name'],0,1)))?>
                        </span>
                    <?php endif ?>
                </div>
                <div>
                    <span class="profile-role">
                        <?=e($profile['role']==='admin'?'Administrateur':($profile['role']==='teacher'?'Enseignant':'Étudiant'))?>
                    </span>
                    <h1><?=e($profile['first_name'].' '.$profile['last_name'])?></h1>
                    <p><?=e($profile['email'])?></p>
                </div>
                <div class="profile-summary">
                    <strong><?=$contentCount?></strong>
                    <span>contenu<?=$contentCount>1?'s':''?> publié<?=$contentCount>1?'s':''?></span>
                    <small>Membre depuis <?=date('m/Y',strtotime($profile['created_at']))?></small>
                </div>
            </header>

            <div class="profile-grid">
                <section class="profile-card">
                    <div class="profile-card-heading">
                        <span><?=icon('user',20)?></span>
                        <div>
                            <h2>Informations personnelles</h2>
                            <p>Mettez à jour les informations visibles sur votre compte.</p>
                        </div>
                    </div>

                    <form method="post">
                        <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                        <input type="hidden" name="form" value="profile_details">

                        <div class="form-row">
                            <div class="field">
                                <label>Prénom</label>
                                <input required maxlength="60" name="first_name" autocomplete="given-name" value="<?=e($profile['first_name'])?>">
                            </div>
                            <div class="field">
                                <label>Nom</label>
                                <input required maxlength="60" name="last_name" autocomplete="family-name" value="<?=e($profile['last_name'])?>">
                            </div>
                        </div>

                        <div class="field">
                            <label>Adresse e-mail</label>
                            <input required type="email" name="email" autocomplete="email" value="<?=e($profile['email'])?>">
                        </div>

                        <div class="field">
                            <label>Présentation <span>facultatif</span></label>
                            <textarea maxlength="500" name="bio" placeholder="Quelques mots à propos de vous…"><?=e($profile['bio'])?></textarea>
                            <small>500 caractères maximum</small>
                        </div>

                        <div class="profile-form-actions">
                            <button class="button primary">
                                <?=icon('check',17)?> <span>Enregistrer les modifications</span>
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="profile-side">
                    <!-- Photo de profil -->
                    <section class="profile-card">
                        <div class="profile-card-heading">
                            <span><?=icon('user',20)?></span>
                            <div>
                                <h2>Photo de profil</h2>
                                <p>Personnalisez votre avatar public.</p>
                            </div>
                        </div>

                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                            <input type="hidden" name="form" value="profile_avatar">

                            <div class="field">
                                <label>Téléverser une image</label>
                                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required>
                                <small>JPG, PNG ou WebP (max 5 Mo)</small>
                            </div>

                            <button class="button secondary" style="width:100%;margin-top:10px;justify-content:center;">
                                <?=icon('upload',16)?> <span>Mettre à jour la photo</span>
                            </button>
                        </form>

                        <?php if(!empty($profile['avatar_path'])): ?>
                            <form method="post" style="margin-top:10px;">
                                <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                <input type="hidden" name="form" value="remove_avatar">
                                <button class="button danger" style="width:100%;justify-content:center;">
                                    <?=icon('trash',16)?> <span>Supprimer la photo</span>
                                </button>
                            </form>
                        <?php endif ?>
                    </section>

                    <!-- Sécurité mot de passe -->
                    <section class="profile-card">
                        <div class="profile-card-heading">
                            <span><?=icon('shield',20)?></span>
                            <div>
                                <h2>Sécurité</h2>
                                <p>Modifiez votre mot de passe.</p>
                            </div>
                        </div>

                        <form method="post">
                            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                            <input type="hidden" name="form" value="profile_password">

                            <div class="field">
                                <label>Mot de passe actuel</label>
                                <input required type="password" name="current_password" autocomplete="current-password">
                            </div>

                            <div class="field">
                                <label>Nouveau mot de passe</label>
                                <input required minlength="10" type="password" name="new_password" autocomplete="new-password">
                                <small>10 caractères minimum</small>
                            </div>

                            <div class="field">
                                <label>Confirmer le mot de passe</label>
                                <input required minlength="10" type="password" name="password_confirmation" autocomplete="new-password">
                            </div>

                            <button class="button profile-security-button">
                                <?=icon('shield',17)?> <span>Modifier le mot de passe</span>
                            </button>
                        </form>
                    </section>

                    <section class="profile-tip">
                        <?=icon('lock',18)?>
                        <div>
                            <strong>Préférences & Confidentialité</strong>
                            <p>Accédez aux <a href="<?=url('settings')?>" class="link">Paramètres avancés</a> pour gérer vos notifications et vos sessions.</p>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </main>
</div>
