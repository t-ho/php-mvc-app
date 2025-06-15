<?php

session_start();

$config = require_once __DIR__ . '/../config/config.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', $config['app']['base_url']);
}

require_once __DIR__ . '/helpers.php';

spl_autoload_register(function ($class) {
    $paths = [
      __DIR__ . '/controllers/',
      __DIR__ . '/models/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
