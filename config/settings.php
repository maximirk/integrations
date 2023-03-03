<?php

// Load default settings
$settings = require __DIR__ . '/defaults.php';

// Overwrite default settings with environment specific local settings
$configFiles = sprintf('%s/{local.%s,env,../../env}.php', __DIR__, $settings['env']);

$files = glob($configFiles, GLOB_BRACE);
if (is_iterable($files)) {
    foreach ($files as $file) {
        $local = require $file;
        if (is_callable($local)) {
            $settings = $local($settings);
        }
    }
}


return $settings;
