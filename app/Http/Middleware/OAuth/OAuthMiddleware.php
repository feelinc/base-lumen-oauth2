<?php

namespace App\Http\Middleware\OAuth;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Closure;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse as Response;

use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;

use App\OAuth2\Storages\Session as SessionStorage;
use App\OAuth2\Storages\AccessToken as AccessTokenStorage;
use App\OAuth2\Storages\Client as ClientStorage;
use App\OAuth2\Storages\Scope as ScopeStorage;

use App\Models\User;

use League\OAuth2\Server\Exception\OAuthException;

class OAuthMiddleware
{

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string  $scope
   * @return mixed
   */
  public function handle(Request $request, Closure $next, $scope = '')
  {
    // Set up the OAuth 2.0 resource server
    $server = new ResourceServer(
      new SessionStorage,
      new AccessTokenStorage,
      new ClientStorage,
      new ScopeStorage
    );

    $isError = false;

    try {
      
      // Check that access token is present
      $server->isValidRequest();
      
    } catch (OAuthException $e) {

      // Catch an OAuth exception
      $response = new Response([
        'error'   => $e->errorType,
        'message' => $e->getMessage()
      ], $e->httpStatusCode, $e->getHttpHeaders());

      $isError = true;

    } catch (\Exception $e) {

      $response = new Response([
        'error'   => $e->getCode(),
        'message' => $e->getMessage()
        ], 500, []);

      $isError = true;

    }

    if ( ! $isError) {
      // Get session info
      $session = $server->getSessionStorage()->getByAccessToken(
        $server->getAccessToken()
      );

      if ( ! $session instanceOf SessionEntity) {
        $isError = true;
      }
    }

    if ( ! $isError) {
      // Get user info
      $user = null;

      if ($session->getOwnerType() === 'user') {
        $user = User::find($session->getOwnerId());

        if ( ! $user instanceOf User) {
          $isError = true;
        }
      }
    }

    if ( ! $isError) {
      // Get client info
      $client = $server->getClientStorage()->getCompleteBySession($session);

      if ( ! $client instanceOf ClientEntity) {
        $isError = true;
      }
    }

    if ( ! $isError) {
      // Get scopes info
      $scopes = $session->getScopes();

      if (! empty($scope)) {
        $isScopeFound = false;

        if ( ! is_null($scopes) && is_array($scopes)) {
          foreach ($scopes as $scopeEntity) {
            if ($scopeEntity->getId() === $scope) {
              $isScopeFound = true;
              break;
            }
          }
        }

        if ( ! $isScopeFound) {
          $response = new Response([
            'error'   => 'invalid_client', 
            'message' => 'Client authentication failed.'
          ], 401);

          $isError = true;
        }
      }
    }

    if ($isError) {
      $response->headers->set('Content-type', 'application/json');
      return $response;
    } else {
      // Put the identified client & scopes into request
      // for further app process
      $request->merge([
        'identified_oauth' => [
          'client' => $client, 
          'user'   => $user, 
          'scopes' => $scopes
        ]
      ]);
    }

    return $next($request);
  }
}
