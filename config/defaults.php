<?php

// Application default settings

// Error reporting
error_reporting(1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Timezone
date_default_timezone_set('Europe/Moscow');

$settings = [];

// Environment
$settings['env'] = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'dev';

// Error handler
$settings['error'] = [
    // Should be set to false for the production environment
    'display_error_details' => false,
    // Should be set to false for the test environment
    'log_errors' => true,
    // Display error details in error log
    'log_error_details' => false,
];

// Logger settings
$settings['logger'] = [
    // Log file location
    'path' => __DIR__ . '/../logs',
    // Default log level
    'level' => \Monolog\Logger::INFO,
];

$settings['logger_file'] = 'error.log';

//Redis
$settings['redis'] = [
    'host'   => '127.0.0.1',
    'port'   => 6379
];

// Console commands
$settings['commands'] = [
    \App\Console\StartSynchronizationCommand::class,
    \App\Console\WorkerForSyncStackCommand::class
];

//base settings
$settings['document_root'] = __DIR__ . '/../';

return $settings;
