<?php

namespace App\Http\Controllers\v1;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Http\Controllers\Controller;
use App\Http\Controllers\OAuth2;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Hashing\BcryptHasher;

use App\Http\Validators\UserValidator;
use App\Models\User;

class UserController extends Controller {

  use OAuth2;

  /**
   * Do user authentication.
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function postLogin(Request $request)
  {
    // Authenticate user credentials
    $credentials = $request->only('email', 'password');

    $user = $this->getUserProvider()->retrieveByCredentials($credentials);

    if ( ! $this->hasValidCredentials($user, $credentials)) {
      return new Response([
        'error'   => 'invalid_credentials', 
        'message' => 'The email or password were incorrect.'
      ], 401);
    }

    // Register authenticated user into OAuth
    $response = $this->completeAuthorizationFlow($request, $user);
    
    return $response;
  }

  /**
   * Return a user.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  integer $userId
   * @return \Illuminate\Http\Response
   */
  public function getUser(Request $request, $userId)
  {
    $identifiedOAuth = $request->get('identified_oauth');

    echo '<pre>';
    print_r($identifiedOAuth['user']);
    echo '</pre>';
  }

  /**
   * Determine if the user matches the credentials.
   *
   * @param  mixed  $user
   * @param  array  $credentials
   * @return bool
   */
  protected function hasValidCredentials($user, $credentials)
  {
    return ! is_null($user) && $this->getUserProvider()->validateCredentials($user, $credentials);
  }

  /**
   * Get the user provider instance.
   *
   * @return \Illuminate\Contracts\Auth\UserProvider
   */
  protected function getUserProvider()
  {
    return new EloquentUserProvider(new BcryptHasher, '\App\Models\User');
  }

}
