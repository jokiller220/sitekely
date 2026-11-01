<div class="app-shell admin-shell">
    <?php $active='categories'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-content admin-page">
            <header class="admin-page-header">
                <div>
                    <span>Taxonomie & Organisation</span>
                    <h1>Gestion des catégories</h1>
                    <p>Créez, modifiez et organisez les catégories de contenus proposées sur la plateforme.</p>
                </div>
                <div class="header-stat">
                    <strong><?=count($categories)?></strong>
                    <span>catégories</span>
                </div>
            </header>

            <div class="profile-grid">
                <section class="admin-table-card" style="margin:0;">
                    <div class="admin-table-head">
                        <div>
                            <strong>Liste des catégories</strong>
                            <span>Catégories actives et visibles pour les utilisateurs</span>
                        </div>
                    </div>

                    <div class="table-scroll">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Position</th>
                                    <th>Médias</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($categories as $cat): ?>
                                    <tr>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <span style="display:inline-flex;padding:6px;background:var(--accent-dim);color:var(--accent);border-radius:6px;">
                                                    <?=icon('tag',16)?>
                                                </span>
                                                <strong><?=e($cat['name'])?></strong>
                                            </div>
                                        </td>
                                        <td style="color:var(--text-muted);font-size:0.875rem;max-width:250px;">
                                            <?=e($cat['description'] ?: '—')?>
                                        </td>
                                        <td>
                                            <span style="font-size:0.85rem;color:var(--text-muted);"><?=(int)$cat['position']?></span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background:var(--surface-light);border:1px solid var(--border);padding:2px 8px;border-radius:12px;font-size:0.8rem;">
                                                <?=(int)$cat['media_count']?> contenu<?=(int)$cat['media_count']>1?'s':''?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                <input type="hidden" name="form" value="admin_category_edit">
                                                <input type="hidden" name="category_id" value="<?=$cat['id']?>">
                                                <input type="hidden" name="name" value="<?=e($cat['name'])?>">
                                                <input type="hidden" name="description" value="<?=e($cat['description'])?>">
                                                <input type="hidden" name="position" value="<?=$cat['position']?>">
                                                <input type="hidden" name="is_active" value="<?=$cat['is_active'] ? 0 : 1?>">
                                                <button type="submit" class="button table-button" style="border:none;background:transparent;cursor:pointer;padding:0;" title="Cliquer pour changer le statut">
                                                    <?php if($cat['is_active']): ?>
                                                        <span class="active-status"><i></i> Actif</span>
                                                    <?php else: ?>
                                                        <span style="color:var(--text-muted);font-size:0.8rem;display:inline-flex;align-items:center;gap:4px;">Inactif</span>
                                                    <?php endif ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:6px;align-items:center;">
                                                <button class="button table-button secondary" onclick="openEditModal(<?=$cat['id']?>, '<?=addslashes($cat['name'])?>', '<?=addslashes($cat['description'])?>', <?=$cat['position']?>, <?=$cat['is_active']?>)">
                                                    <?=icon('edit',14)?> <span>Modifier</span>
                                                </button>
                                                <?php if((int)$cat['media_count'] === 0): ?>
                                                    <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer cette catégorie ?');">
                                                        <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                        <input type="hidden" name="form" value="admin_category_delete">
                                                        <input type="hidden" name="category_id" value="<?=$cat['id']?>">
                                                        <button class="button table-button danger">
                                                            <?=icon('trash',14)?>
                                                        </button>
                                                    </form>
                                                <?php endif ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                                <?php if(!$categories): ?>
                                    <tr>
                                        <td colspan="6" class="empty-state">Aucune catégorie pour le moment.</td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <aside class="profile-side">
                    <section class="profile-card">
                        <div class="profile-card-heading">
                            <span><?=icon('plus',20)?></span>
                            <div>
                                <h2>Ajouter une catégorie</h2>
                                <p>Définissez une nouvelle catégorie thématique.</p>
                            </div>
                        </div>

                        <form method="post">
                            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                            <input type="hidden" name="form" value="admin_category_add">

                            <div class="field">
                                <label>Nom de la catégorie</label>
                                <input required maxlength="60" name="name" placeholder="Ex: Audiovisuel, IA…">
                            </div>

                            <div class="field">
                                <label>Description (optionnelle)</label>
                                <textarea name="description" rows="3" placeholder="Brève description de la thématique…"></textarea>
                            </div>

                            <div class="field">
                                <label>Ordre d'affichage (position)</label>
                                <input type="number" name="position" value="<?=count($categories) + 1?>" min="0">
                            </div>

                            <div class="profile-form-actions" style="margin-top:16px;">
                                <button class="button primary" style="width:100%;justify-content:center;">
                                    <?=icon('plus',16)?> <span>Créer la catégorie</span>
                                </button>
                            </div>
                        </form>
                    </section>
                </aside>
            </div>
        </div>
    </main>
</div>

<!-- Modal Edition Catégorie -->
<div id="editCatModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div class="panel" style="max-width:480px;width:90%;margin:auto;padding:24px;border-radius:12px;background:var(--surface);">
        <h3 style="margin-top:0;">Modifier la catégorie</h3>
        <form method="post" id="editCatForm">
            <input type="hidden" name="csrf" value="<?=csrfToken()?>">
            <input type="hidden" name="form" value="admin_category_edit">
            <input type="hidden" name="category_id" id="modalCatId">

            <div class="field">
                <label>Nom</label>
                <input required maxlength="60" name="name" id="modalCatName">
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description" id="modalCatDesc" rows="3"></textarea>
            </div>

            <div class="field">
                <label>Position</label>
                <input type="number" name="position" id="modalCatPos" min="0">
            </div>

            <div class="field">
                <label>Visibilité</label>
                <select name="is_active" id="modalCatActive">
                    <option value="1">Actif (visible)</option>
                    <option value="0">Inactif (masqué)</option>
                </select>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <button type="button" class="button secondary" onclick="closeEditModal()">Annuler</button>
                <button type="submit" class="button primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, desc, pos, active) {
    document.getElementById('modalCatId').value = id;
    document.getElementById('modalCatName').value = name;
    document.getElementById('modalCatDesc').value = desc;
    document.getElementById('modalCatPos').value = pos;
    document.getElementById('modalCatActive').value = active;
    const modal = document.getElementById('editCatModal');
    modal.style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editCatModal').style.display = 'none';
}
</script>
