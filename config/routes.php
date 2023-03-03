<?php

use App\Action\Home\HomeAction;
use App\Action\Synchronization\StartSynchronizationAction;
use Slim\App;

return function (App $app) {
    $app->get('/', HomeAction::class);

    $app->post('/synchronization', StartSynchronizationAction::class);
};
