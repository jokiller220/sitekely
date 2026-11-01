<div class="app-shell admin-shell">
    <?php $active='admin-messages'; require dirname(__DIR__).'/partials/sidebar.php'; ?>
    <main class="workspace">
        <div class="workspace-content admin-page">
            <header class="admin-page-header">
                <div>
                    <span>Boîte de réception</span>
                    <h1>Messages de contact</h1>
                    <p>Consultez les messages envoyés via le formulaire public du site.</p>
                </div>
                <div class="header-stat">
                    <strong><?=count($messages)?></strong>
                    <span>message<?=count($messages)>1?'s':''?></span>
                </div>
            </header>

            <section class="admin-table-card">
                <div class="admin-table-head">
                    <div>
                        <strong>Messages reçus</strong>
                        <span>Demandes, questions et prises de contact</span>
                    </div>
                </div>

                <div class="table-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Expéditeur</th>
                                <th>Objet</th>
                                <th>Message</th>
                                <th>Date d'envoi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($messages as $msg): ?>
                                <tr>
                                    <td>
                                        <strong><?=e($msg['name'])?></strong>
                                        <br><a href="mailto:<?=e($msg['email'])?>" class="link" style="font-size:0.85rem;"><?=e($msg['email'])?></a>
                                    </td>
                                    <td>
                                        <strong><?=e($msg['subject'] ?: 'Sans objet')?></strong>
                                    </td>
                                    <td style="max-width:350px;font-size:0.875rem;color:var(--text);white-space:pre-wrap;line-height:1.4;">
                                        <?=nl2br(e($msg['message']))?>
                                    </td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;">
                                        <?=date('d/m/Y H:i', strtotime($msg['created_at']))?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?=e($msg['email'])?>?subject=<?=rawurlencode('Re: '.($msg['subject']?:'Votre message sur Media Spotlight'))?>" class="button table-button primary">
                                            <?=icon('mail',14)?> <span>Répondre</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php if(!$messages): ?>
                                <tr>
                                    <td colspan="5" class="empty-state">Aucun message pour le moment.</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
