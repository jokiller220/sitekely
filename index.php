<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/actions.php';
require __DIR__ . '/app/router.php';

handleAction($db, $config);
route($db, $config);
