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
use App\OAuth2\Storages\AuthCode as AuthCodeStorage;
use App\OAuth2\Storages\RefreshToken as RefreshTokenStorage;

use App\OAuth2\Grant\AuthCode;
use App\OAuth2\Grant\RefreshToken;
use App\OAuth2\TokenType\Bearer;

use App\Models\User;

trait OAuth2 {

  /**
   * Do client authorization based on user login.
   *
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  protected function completeAuthorizationFlow(Request $request, User $user)
  {
    // First create OAuth Auth Code
    $server = new AuthorizationServer;

    $server->setSessionStorage(new SessionStorage);
    $server->setAccessTokenStorage(new AccessTokenStorage);
    $server->setClientStorage(new ClientStorage);
    $server->setScopeStorage(new ScopeStorage);
    $server->setAuthCodeStorage(new AuthCodeStorage);
    $server->setRefreshTokenStorage(new RefreshTokenStorage);

    $server->addGrantType(new AuthCode);
    $server->addGrantType(new RefreshToken);
    $server->setTokenType(new Bearer);

    $identifiedOAuth = $request->get('identified_oauth');

    $authParams = [
      'client'       => $identifiedOAuth['client'], 
      'redirect_uri' => $identifiedOAuth['client']->getRedirectUri(), 
      'scopes'       => $identifiedOAuth['scopes'], 
      'state'        => time()
    ];

    $redirectUri = $server->getGrantType('authorization_code')
                          ->newAuthorizeRequest('user', $user->id, $authParams);

    parse_str(parse_url($redirectUri, PHP_URL_QUERY), $queryStr);

    // Complete the OAuth Auth flow
    $server->getRequest()->request->set('grant_type', 'authorization_code');
    $server->getRequest()->request->set('client_id', $identifiedOAuth['client']->getId());
    $server->getRequest()->request->set('client_secret', $identifiedOAuth['client']->getSecret());
    $server->getRequest()->request->set('redirect_uri', $identifiedOAuth['client']->getRedirectUri());
    $server->getRequest()->request->set('code', $queryStr['code']);

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
