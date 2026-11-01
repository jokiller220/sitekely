<div class="app-shell admin-shell">
    <?php $active='reports'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-content admin-page">
            <header class="admin-page-header">
                <div>
                    <span>Modération de la plateforme</span>
                    <h1>Signalements des contenus</h1>
                    <p>Traitez les signalements soumis par les utilisateurs et prenez les décisions appropriées.</p>
                </div>
                <div class="header-stat">
                    <strong><?=count($reports)?></strong>
                    <span>signalement<?=count($reports)>1?'s':''?></span>
                </div>
            </header>

            <form class="admin-filterbar" method="get">
                <input type="hidden" name="page" value="reports">
                <div style="display:flex;align-items:center;gap:10px;">
                    <label style="font-size:0.875rem;color:var(--text-muted);">Filtrer par statut :</label>
                    <select name="status" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="pending" <?=$status==='pending'?'selected':''?>>En attente</option>
                        <option value="resolved" <?=$status==='resolved'?'selected':''?>>Résolu</option>
                        <option value="dismissed" <?=$status==='dismissed'?'selected':''?>>Classé sans suite</option>
                    </select>
                </div>
            </form>

            <section class="admin-table-card">
                <div class="admin-table-head">
                    <div>
                        <strong>Historique des signalements</strong>
                        <span>Motifs, détails et actions de modération</span>
                    </div>
                </div>

                <div class="table-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Contenu signalé</th>
                                <th>Motif</th>
                                <th>Détails</th>
                                <th>Signalé par</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Décision</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($reports as $rep): ?>
                                <tr>
                                    <td>
                                        <div class="admin-content-cell">
                                            <span><?=icon($rep['media_type']==='video'?'video':'podcast',16)?></span>
                                            <div>
                                                <strong>
                                                    <a href="<?=url('media',['id'=>$rep['media_id']])?>" target="_blank" class="link">
                                                        <?=e($rep['media_title'])?>
                                                    </a>
                                                </strong>
                                                <small>ID média #<?=$rep['media_id']?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:rgba(239, 68, 68, 0.15);color:#ef4444;font-weight:600;padding:3px 8px;border-radius:6px;font-size:0.8rem;">
                                            <?=e($rep['reason'])?>
                                        </span>
                                    </td>
                                    <td style="max-width:240px;font-size:0.85rem;color:var(--text-muted);">
                                        <?=e($rep['details'] ?: 'Aucun détail fourni.')?>
                                    </td>
                                    <td>
                                        <strong><?=e($rep['first_name'].' '.$rep['last_name'])?></strong>
                                        <br><small style="color:var(--text-muted);"><?=e($rep['email'])?></small>
                                    </td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;">
                                        <?=date('d/m/Y H:i', strtotime($rep['created_at']))?>
                                    </td>
                                    <td>
                                        <?php if($rep['status']==='pending'): ?>
                                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:12px;background:rgba(234, 179, 8, 0.15);color:#eab308;font-size:0.8rem;font-weight:600;">
                                                En attente
                                            </span>
                                        <?php elseif($rep['status']==='resolved'): ?>
                                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:12px;background:rgba(34, 197, 94, 0.15);color:#22c55e;font-size:0.8rem;font-weight:600;">
                                                Résolu
                                            </span>
                                        <?php else: ?>
                                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:12px;background:var(--surface-light);color:var(--text-muted);font-size:0.8rem;">
                                                Classé
                                            </span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php if($rep['status']==='pending'): ?>
                                            <div style="display:flex;gap:6px;">
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                    <input type="hidden" name="form" value="admin_resolve_report">
                                                    <input type="hidden" name="report_id" value="<?=$rep['id']?>">
                                                    <input type="hidden" name="action" value="dismiss">
                                                    <button class="button table-button secondary" title="Classer sans suite">
                                                        <?=icon('check',14)?> <span>Rejeter</span>
                                                    </button>
                                                </form>

                                                <form method="post" style="display:inline;" onsubmit="return confirm('Masquer ce contenu ? Il ne sera plus visible sur le site.');">
                                                    <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                    <input type="hidden" name="form" value="admin_resolve_report">
                                                    <input type="hidden" name="report_id" value="<?=$rep['id']?>">
                                                    <input type="hidden" name="action" value="hide">
                                                    <button class="button table-button danger" title="Masquer le contenu">
                                                        <?=icon('eye',14)?> <span>Masquer</span>
                                                    </button>
                                                </form>

                                                <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer définitivement ce contenu ?');">
                                                    <input type="hidden" name="csrf" value="<?=csrfToken()?>">
                                                    <input type="hidden" name="form" value="admin_resolve_report">
                                                    <input type="hidden" name="report_id" value="<?=$rep['id']?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button class="button table-button danger" title="Supprimer définitivement">
                                                        <?=icon('trash',14)?>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <small style="color:var(--text-muted);">Traité</small>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if(!$reports): ?>
                                <tr>
                                    <td colspan="7" class="empty-state">Aucun signalement à traiter.</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
