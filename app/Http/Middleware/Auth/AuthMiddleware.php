<?php

namespace App\Http\Middleware\Auth;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Closure;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse as Response;

use App\Models\User;

class AuthMiddleware
{

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string  $scope
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $identifiedOAuth = $request->get('identified_oauth');

    $isError = false;

    if ( ! isset($identifiedOAuth['user'])) {
      $isError = true;
    }

    if ( ! $isError) {
      if ( ! isset($identifiedOAuth['user'])) {
        $isError = true;
      }
    }

    if ( ! $isError) {
      if ( ! $identifiedOAuth['user'] instanceOf User) {
        $isError = true;
      }
    }

    if ($isError) {
      return new Response([
        'error'   => 'invalid_user', 
        'message' => 'User authentication failed.'
      ], 401);
    }

    return $next($request);
  }
}
