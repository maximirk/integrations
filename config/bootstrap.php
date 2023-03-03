<?php

use App\Factory\ContainerFactory;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

$container = (new ContainerFactory())->createInstance();

return $container->get(App::class);
