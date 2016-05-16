<?php

// beautify the 404 handler
$container['notFoundHandler'] = function ($c) {
  return function ($request, $response) use ($c) {
    return $c['response']->withJson(array('error' => 'Not found.'), 404);
  };
};

// beautify the exception handler
$container['errorHandler'] = function ($c) {
  return function ($request, $response, $exception) use ($c) {
    return $c['response']->withJson(array('error' => $exception->getMessage()), 500);
  };
};