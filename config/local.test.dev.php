<?php

// Phpunit test environment
return function (array $settings): array {
    $settings['error']['display_error_details'] = true;
    $settings['error']['log_errors'] = true;

    // Mocked Logger settings
    $settings['logger'] = [
        'path' => '',
        'level' => 0,
        'test' => new \Psr\Log\NullLogger(),
    ];

    //Redis
    $settings['redis'] = [
        'host'   => 'host.docker.internal',
        'port'   => 6380
    ];

    return $settings;
};
