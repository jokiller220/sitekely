<div class="app-shell admin-shell">
    <?php $active='users'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-content admin-page">
            <header class="admin-page-header">
                <div>
                    <span>Communauté</span>
                    <h1>Gestion des utilisateurs</h1>
                    <p>Supervisez les comptes étudiants, enseignants et administrateurs, modifiez leurs rôles et gérez leurs accès.</p>
                </div>
                <div class="header-stat">
                    <strong><?=count($rows)?></strong>
                    <span>comptes</span>
                </div>
            </header>

            <form class="admin-filterbar" method="get">
                <input type="hidden" name="page" value="users">
                <div style="flex:1;min-width:220px;"><?=icon('search',17)?>
                    <input name="search" value="<?=e($search)?>" placeholder="Rechercher par nom, prénom ou email…">
                </div>
                <select name="role">
                    <option value="">Tous les rôles</option>
                    <option value="student" <?=$role==='student'?'selected':''?>>Étudiants</option>
                    <option value="teacher" <?=$role==='teacher'?'selected':''?>>Enseignants</option>
                    <option value="admin" <?=$role==='admin'?'selected':''?>>Administrateurs</option>
                </select>
                <select name="status">
                    <option value="">Tous les statuts</option>
                    <option value="active" <?=$status==='active'?'selected':''?>>Actifs</option>
                    <option value="suspended" <?=$status==='suspended'?'selected':''?>>Suspendus</option>
                </select>
                <button class="button secondary">Filtrer</button>
                <?php if($search!==''||$role!==''||$status!==''): ?>
                    <a href="<?=url('users')?>" class="button subtle">Réinitialiser</a>
                <?php endif ?>
            </form>

            <section class="admin-table-card">
                <div class="admin-table-head">
                    <div>
                        <strong>Répertoire des utilisateurs</strong>
                        <span><?=count($rows)?> utilisateur<?=count($rows)>1?'s':''?> trouvé<?=count($rows)>1?'s':''?></span>
                    </div>
                </div>

                <div class="table-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Adresse e-mail</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Dernière connexion</th>
                                <th>Inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($rows as $row): 
                                $isSuspended = ($row['status'] ?? 'active') === 'suspended';
                            ?>
                                <tr>
                                    <td>
                                        <div class="admin-user-cell">
                                            <?php if(!empty($row['avatar_path'])): ?>
                                                <img src="<?=e($row['avatar_path'])?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                            <?php else: ?>
                                                <span><?=e(strtoupper(substr($row['first_name'],0,1).substr($row['last_name'],0,1)))?></span>
                                            <?php endif ?>
                                            <div>
                                                <strong><?=e($row['first_name'].' '.$row['last_name'])?></strong>
                                                <small>#<?=str_pad((string)$row['id'],4,'0',STR_PAD_LEFT)?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?=e($row['email'])?></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                            <input type="hidden" name="form" value="admin_user_role">
                                            <input type="hidden" name="user_id" value="<?=$row['id']?>">
                                            <select name="role" onchange="this.form.submit()" style="padding:2px 6px;font-size:0.8rem;border-radius:4px;border:1px solid var(--border);background:var(--surface);">
                                                <option value="student" <?=$row['role']==='student'?'selected':''?>>Étudiant</option>
                                                <option value="teacher" <?=$row['role']==='teacher'?'selected':''?>>Enseignant</option>
                                                <option value="admin" <?=$row['role']==='admin'?'selected':''?>>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                            <input type="hidden" name="form" value="admin_user_status">
                                            <input type="hidden" name="user_id" value="<?=$row['id']?>">
                                            <input type="hidden" name="status" value="<?=$isSuspended?'active':'suspended'?>">
                                            <button type="submit" class="button table-button" style="border:none;background:transparent;cursor:pointer;padding:0;" title="Cliquer pour suspendre/réactiver">
                                                <?php if($isSuspended): ?>
                                                    <span style="color:#ef4444;font-size:0.8rem;font-weight:600;">Suspendu</span>
                                                <?php else: ?>
                                                    <span class="active-status"><i></i> Actif</span>
                                                <?php endif ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);">
                                        <?=!empty($row['last_login_at']) ? date('d/m/Y H:i', strtotime($row['last_login_at'])) : '—'?>
                                    </td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;">
                                        <?=date('d/m/Y',strtotime($row['created_at']))?>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:6px;align-items:center;">
                                            <button type="button" class="button table-button secondary" onclick="openResetModal(<?=$row['id']?>, '<?=addslashes($row['first_name'].' '.$row['last_name'])?>')" title="Réinitialiser le mot de passe">
                                                <?=icon('lock',14)?>
                                            </button>
                                            
                                            <?php if($row['id'] !== currentUser()['id']): ?>
                                                <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer définitivement l\'utilisateur <?=addslashes($row['first_name'].' '.$row['last_name'])?> et tous ses contenus ?');">
                                                    <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                    <input type="hidden" name="form" value="admin_user_delete">
                                                    <input type="hidden" name="user_id" value="<?=$row['id']?>">
                                                    <button class="button table-button danger" title="Supprimer le compte">
                                                        <?=icon('trash',14)?>
                                                    </button>
                                                </form>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if(!$rows): ?>
                                <tr>
                                    <td colspan="7" class="empty-state">Aucun utilisateur correspondant à votre recherche.</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- Modal Reset Password Admin -->
<div id="resetPwdModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div class="panel" style="max-width:440px;width:90%;margin:auto;padding:24px;border-radius:12px;background:var(--surface);">
        <h3 style="margin-top:0;">Réinitialiser le mot de passe</h3>
        <p style="color:var(--text-muted);font-size:0.875rem;">Pour <strong id="modalUserName"></strong></p>
        <form method="post">
            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
            <input type="hidden" name="form" value="admin_user_reset_password">
            <input type="hidden" name="user_id" id="modalUserId">

            <div class="field">
                <label>Nouveau mot de passe temporaire</label>
                <input required minlength="10" type="password" name="new_password" placeholder="Minimum 10 caractères">
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <button type="button" class="button secondary" onclick="document.getElementById('resetPwdModal').style.display='none'">Annuler</button>
                <button type="submit" class="button primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openResetModal(id, name) {
    document.getElementById('modalUserId').value = id;
    document.getElementById('modalUserName').innerText = name;
    document.getElementById('resetPwdModal').style.display = 'flex';
}
</script>
