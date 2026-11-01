<?php $isAdmin = currentUser()['role']==='admin'; ?>
<div class="app-shell <?=$isAdmin?'admin-shell':''?>">
    <?php $active='settings'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-content profile-page">
            <header class="profile-hero">
                <div class="profile-avatar-xl">
                    <?=icon('lock',32)?>
                </div>
                <div>
                    <span class="profile-role">Compte & Sécurité</span>
                    <h1>Paramètres du compte</h1>
                    <p>Gérez vos préférences, vos notifications et la sécurité de votre compte.</p>
                </div>
            </header>

            <div class="profile-grid">
                <section class="profile-card">
                    <div class="profile-card-heading">
                        <span><?=icon('shield',20)?></span>
                        <div>
                            <h2>Préférences & Notifications</h2>
                            <p>Configurez vos alertes et l'affichage de l'interface.</p>
                        </div>
                    </div>

                    <form method="post">
                        <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                        <input type="hidden" name="form" value="update_settings">

                        <div class="field">
                            <label>Thème d'affichage</label>
                            <select name="theme_preference">
                                <option value="system">Automatique (selon le système)</option>
                                <option value="dark">Mode sombre permanent</option>
                                <option value="light">Mode clair permanent</option>
                            </select>
                        </div>

                        <div style="margin:20px 0;display:flex;flex-direction:column;gap:14px;">
                            <label style="font-weight:600;font-size:0.9rem;">Notifications par e-mail</label>
                            
                            <label style="display:flex;align-items:center;gap:10px;font-size:0.875rem;cursor:pointer;">
                                <input type="checkbox" name="notify_shares" checked>
                                <span>M'alerter lors de la création d'un lien de partage sécurisé</span>
                            </label>

                            <label style="display:flex;align-items:center;gap:10px;font-size:0.875rem;cursor:pointer;">
                                <input type="checkbox" name="notify_activity" checked>
                                <span>Recevoir un résumé des consultations de mes contenus</span>
                            </label>

                            <label style="display:flex;align-items:center;gap:10px;font-size:0.875rem;cursor:pointer;">
                                <input type="checkbox" name="notify_updates" checked>
                                <span>Informations sur les nouveautés de la plateforme</span>
                            </label>
                        </div>

                        <div class="profile-form-actions" style="margin-top:20px;">
                            <button class="button primary">
                                <?=icon('check',16)?> <span>Enregistrer mes préférences</span>
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="profile-side">
                    <section class="profile-card">
                        <div class="profile-card-heading">
                            <span><?=icon('clock',20)?></span>
                            <div>
                                <h2>Session active</h2>
                                <p>Informations de connexion courante.</p>
                            </div>
                        </div>

                        <div style="font-size:0.875rem;display:flex;flex-direction:column;gap:10px;">
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-muted);">Adresse IP</span>
                                <code><?=e($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')?></code>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-muted);">Navigateur</span>
                                <span style="max-width:180px;text-align:right;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?=e($_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu')?>
                                </span>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-muted);">Dernière connexion</span>
                                <span><?=date('d/m/Y H:i')?></span>
                            </div>
                        </div>

                        <div style="margin-top:16px;">
                            <a href="?action=logout" class="button secondary" style="width:100%;justify-content:center;">
                                <?=icon('logout',16)?> <span>Déconnecter ma session</span>
                            </a>
                        </div>
                    </section>

                    <section class="profile-card" style="border-color:rgba(239, 68, 68, 0.25);">
                        <div class="profile-card-heading">
                            <span style="color:#ef4444;"><?=icon('trash',20)?></span>
                            <div>
                                <h2 style="color:#ef4444;">Supprimer le compte</h2>
                                <p>Suppression irréversible de votre compte et données associées.</p>
                            </div>
                        </div>

                        <form method="post" onsubmit="return confirm('Attention : cette action supprimera définitivement votre compte et vos médias. Confirmer ?');">
                            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                            <input type="hidden" name="form" value="delete_account">

                            <div class="field">
                                <label>Confirmez votre mot de passe</label>
                                <input type="password" name="password_confirm" required placeholder="Votre mot de passe actuel">
                            </div>

                            <button class="button danger" style="width:100%;justify-content:center;margin-top:10px;">
                                <?=icon('trash',16)?> <span>Supprimer définitivement</span>
                            </button>
                        </form>
                    </section>
                </aside>
            </div>
        </div>
    </main>
</div>
