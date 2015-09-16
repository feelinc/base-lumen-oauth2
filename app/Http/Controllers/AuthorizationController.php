<?php

namespace App\Http\Controllers;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse as Response;

use League\OAuth2\Server\AuthorizationServer;

use App\OAuth2\Storages\Session as SessionStorage;
use App\OAuth2\Storages\AccessToken as AccessTokenStorage;
use App\OAuth2\Storages\Client as ClientStorage;
use App\OAuth2\Storages\Scope as ScopeStorage;
use App\OAuth2\Storages\RefreshToken as RefreshTokenStorage;

use App\OAuth2\Grant\ClientCredentials;
use App\OAuth2\Grant\RefreshToken;
use App\OAuth2\TokenType\Bearer;

use League\OAuth2\Server\Exception\OAuthException;

class AuthorizationController extends Controller {

  /**
   * Do authorization.
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function postIndex(Request $request)
  {
    $server = new AuthorizationServer;

    $server->setSessionStorage(new SessionStorage);
    $server->setAccessTokenStorage(new AccessTokenStorage);
    $server->setClientStorage(new ClientStorage);
    $server->setScopeStorage(new ScopeStorage);
    $server->setRefreshTokenStorage(new RefreshTokenStorage);

    $server->addGrantType(new ClientCredentials);
    $server->addGrantType(new RefreshToken);
    $server->setTokenType(new Bearer);

    try {

      $accessToken = $server->issueAccessToken();

      $response = new Response($accessToken, 200, [
        'Cache-Control' => 'no-store',
        'Pragma'        => 'no-store'
      ]);

    } catch (OAuthException $e) {

      $response = new Response([
        'error'   => $e->errorType,
        'message' => $e->getMessage()
      ], $e->httpStatusCode, $e->getHttpHeaders());

    } catch (\Exception $e) {

      $response = new Response([
        'error'   => $e->getCode(),
        'message' => $e->getMessage()
      ], 500);

    } finally {

      // Return the response
      $response->headers->set('Content-type', 'application/json');
      return $response;

    }

    // TO DO: Remove previous active access token for current client
    
  }

}
