<?php

// set default timezone
date_default_timezone_set('America/Denver');

// setup error logging in the production environment
if ( isset($_ENV['environment']) && $_ENV['environment'] == 'production' ) {
  // hide all errors
  ini_set('display_errors', false);

  // send data up to rollbar (if configured)
  if ( isset($_ENV['ROLLBAR_ACCESS_TOKEN']) ) {
    Rollbar::init(array('access_token' => $_ENV['ROLLBAR_ACCESS_TOKEN']));
  }

  // beautify fatal errors
  register_shutdown_function(function() {
    $error = error_get_last();

    if ($error['type'] === E_ERROR) {
      http_response_code(500);
      header('Content-Type: application/json');
      echo(json_encode(array('error' => 'An unknown error occurred.')));
      return;
    }
  });
}