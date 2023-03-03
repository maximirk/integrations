<?php require_once __DIR__.'/vendor/autoload.php';

use GO\Scheduler;

$scheduler = new Scheduler();

//tasks
$scheduler
    ->raw('/usr/local/bin/php /var/www/html/bin/console.php synchronization StockMainMoySkladToWildberries')
    ->everyMinute()->onlyOne();
$scheduler
    ->raw('/usr/local/bin/php /var/www/html/bin/console.php synchronization ProductsMoySklad')
    ->hourly()->onlyOne();

//workers
$scheduler
    ->raw('/usr/local/bin/php /var/www/html/bin/console.php sync_worker_stack StockMainMoySkladToWildberries')
    ->everyMinute();
$scheduler
    ->raw('/usr/local/bin/php /var/www/html/bin/console.php sync_worker_stack ProductsMoySklad')
    ->everyMinute()->onlyOne();

$scheduler->run();
