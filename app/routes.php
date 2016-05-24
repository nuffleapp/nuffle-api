<?php

/**
 * Calculate the results of a given dice equation
 *
 * @param  String $equation Dice equation 
 * @return Object           Response object with equation results
 */
$app->get('/v1/roll', function ($request, $response, $args) {
  // dice equation 
  $equation = strtolower($request->getQueryParam('equation', ''));

  // roll 'em!
  $data = \Nuffle\Nuffle::roll($equation);

  // format the response
  $response->withJson($data, 200);
  return $response;
});

/**
 * Calculate the results of a given dice equation submitted via Slack
 *
 * @param  String $token Authorization token
 * @param  String $text  Dice equation
 * @return Object        Response object with equation results
 */
$app->post('/v1/roll/slack', function($request, $response, $args) {
  $token = $request->getParam('token', NULL);
  $equation = strtolower($request->getParam('text', ''));

  if ( $equation == 'help' ) {
    // help me!
    $data = array(
      'response_type' => 'ephemeral',
      'text' => "How to use /roll",
      'attachments' => array(
          array(
              'color' => '#FA2F96',
              'text' => "Nuffle is a dice calculator, allowing you to perform complex dice rolls and calculate their result. To do so, simply use the `/roll <equation>` command.\nEquations must use the standard rpg dice notation format (1d6, 2d20, etc), but otherwise operate much the same as any other calculator.\nAccepted operators are `+`, `-`, `/`, `*`, `(`, and `)`."
            ),
          array(
              'text' => "Example: `/roll 5d6 + 1d20 / (1d6 - 2)`"
            )
        )
    );
  } else {
    // roll the equation as usual
    $results = \Nuffle\Nuffle::roll($equation);

    // display it all pretty-like
    $data = array(
      'response_type' => 'ephemeral',
      'text' => "You rolled a $results->result.",
      'attachments' => array(
          array(
              'color' => '#FA2F96',
              'text' => "Breakdown: $results->equation"
            )
        )
    );
  }

  // format the response
  $response->withJson($data, 200);
  return $response;
});