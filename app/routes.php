<?php

use \IndefiniteArticle\IndefiniteArticle;
use \Nuffle\Nuffle;
use \Curl\Curl;

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
  $data = Nuffle::roll($equation);

  // format the response
  return $response->withJson($data, 200);
});

/**
 * Calculate the results of a given dice equation (submitted via Slack)
 *
 * @param  String $token Authorization token
 * @param  String $text  Dice equation
 * @return Object        Response object with equation results
 */
$app->post('/slack/roll', function($request, $response, $args) {
  $token = $request->getParam('token', NULL);

  if ( $token != getenv('SLACK_VERIFICATION_TOKEN') ) {
    return $response->withJson(array('error' => 'Not authorized.'), 401);
  }

  $equation = strtolower($request->getParam('text', ''));

  if ( $equation == 'help' ) {
    // help me!
    $data = array(
        'response_type' => 'ephemeral',
        'text' => "How to use /roll",
        'attachments' => array(
            array(
                'color' => '#FA2F96',
                'text' => "Nuffle is a dice calculator, allowing you to perform complex dice rolls and calculate their result. To do so, simply use the /roll <equation> command.\nEquations must use the standard RPG dice notation format (1d6, 2d20, etc), but otherwise operate much the same as any other calculator.\nAccepted operators are +, -, /, *, (, and )."
              ),
            array(
                'text' => "Example: /roll 5d6 + 1d20 / (1d6 - 2)"
              )
          )
      );
  } else {
    try {
      // roll the equation as usual
      $results = Nuffle::roll($equation);

      // display it all pretty-like
      $data = array(
          'response_type' => 'ephemeral',
          'text' => "You rolled " . IndefiniteArticle::A($results->result) . "!",
          'attachments' => array(
              array(
                  'color' => '#FA2F96',
                  'text' => "Breakdown: " . $results->equation
                )
            )
        );

      // format the response
      return $response->withJson($data, 200);
    } catch ( \Exception $e ) {
      // catch exceptions separately herer so we can format them for slack
      $data = array(
          'response_type' => 'ephemeral',
          'text' => $e->getMessage()
        );

      // format the response
      return $response->withJson($data, 500);
    }
  }
});

/**
 * Enable Slack application authentication - this is a formality, as the Slack
 * slash command webhooks don't actually require authentication to work
 *
 * @param  String   $code OAuth code
 * @return Redirect       Redirect to a different location
 */
$app->get('/slack/oauth', function($request, $response, $args) {
  $curl = new Curl();
  $code = $request->getParam('code', NULL);

  $payload = array(
      'client_id' => getenv('SLACK_CLIENT_ID'),
      'client_secret' => getenv('SLACK_CLIENT_SECRET'),
      'code' => $code
    );

  // we don't actually need the oauth token to handle slack commands, so just
  // discard the response and move on
  $curl->get('https://slack.com/api/oauth.access', $payload);

  // TODO redirect request to a different location
});
