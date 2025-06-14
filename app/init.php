<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

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
