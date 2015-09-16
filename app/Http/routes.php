<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->welcome();
});

$app->post('/authorizations', [
  'uses' => 'AuthorizationController@postIndex'
]);

$app->post('/v1/authenticate', [
  'middleware' => 'oauth', 
  'uses' => 'v1\UserController@postLogin'
]);

/*
 * -----------------------------------------------------------------------------
 * Users endpoints
 * -----------------------------------------------------------------------------
 */
$app->post('/v1/users', [
  'middleware' => 'oauth', 
  'uses' => 'v1\UserController@postIndex'
]);
$app->get('/v1/users/{id}', [
  'middleware' => ['oauth', 'auth'], 
  'uses' => 'v1\UserController@getUser'
]);