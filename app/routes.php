<?php

/**
 * Roll any number of x-sided dice
 *
 * @param  Integer  $count Number of dice to roll
 * @param  Integer  $sides Number of sides per dice
 * @return Object          Response object with individual and total dice values
 */
$app->get('/v1/roll', function ($request, $response, $args) {
  $count = (int)$request->getQueryParam('count', 1);
  $sides = (int)$request->getQueryParam('sides', NULL);

  // can't roll less than 1 dice
  if ( !is_numeric($count) || $count < 1 ) {
    throw new \Exception("Invalid number of dice.");
  }

  $results = array();

  // roll the specified number of dice
  for ( $i = 0; $i < $count ; $i++ ) {
    $results[] = \Nuffle\Nuffle::roll($sides);
  }

  // sum up the results
  $total = array_sum($results);

  // format the response
  $data = array(
      'results' => $results,
      'total' => $total
    );

  // respond with json
  $response->withJson($data, 200);

  return $response;
});