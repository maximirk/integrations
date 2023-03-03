<?php

use App\Factory\ContainerFactory;
use App\Handler\ConsoleErrorHandler;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

require_once __DIR__ . '/../vendor/autoload.php';

$env = (new ArgvInput())->getParameterOption(['--env', '-e'], 'dev');

if ($env) {
    $_ENV['APP_ENV'] = $env;
}

$container = (new ContainerFactory())->createInstance();

try {
    $application = $container->get(Application::class);
    exit($application->run());
} catch (Throwable $exception) {
    $settings = $container->get("settings");
    $handler = $container->get(ConsoleErrorHandler::class);

    call_user_func(
        $handler,
        $exception,
        (bool)$settings["error"]["display_error_details"],
        (bool)$settings["error"]["log_errors"],
        (bool)$settings["error"]["log_error_details"]
    );

    exit(1);
}
