<?php

// Dev environment
return function (array $settings): array {
    $settings['error']['display_error_details'] = true;
    $settings['logger']['level'] = \Monolog\Logger::DEBUG;

    //Redis
    $settings['redis'] = [
        'host'   => 'host.docker.internal',
        'port'   => 6380
    ];

    return $settings;
};
