<?php

// bootstrap setup
require __DIR__ . '/../app/bootstrap.php';

if (PHP_SAPI == 'cli-server') {
  // to help the built-in PHP dev server, check if the request was actually for
  // something which should probably be served as a static file
  $file = __DIR__ . $_SERVER['REQUEST_URI'];

  if (is_file($file)) {
    return false;
  }
}

require __DIR__ . '/../vendor/autoload.php';

$container = new Slim\Container();

// container config
require __DIR__ . '/../app/config.php';

$app = new Slim\App($container);

// routes
require __DIR__ . '/../app/routes.php';

// run app
$app->run();
