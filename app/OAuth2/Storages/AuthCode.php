<?php namespace App\OAuth2\Storages;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\AuthCodeInterface;

class AuthCode extends AbstractStorage implements AuthCodeInterface
{
  /**
   * {@inheritdoc}
   */
  public function get($code)
  {
    $result = app('db')->table('oauth_auth_code')
              ->where('auth_code', $code)
              ->where('expire_time', '>=', time())
              ->first();

    if (is_object($result)) {
      $token = new AuthCodeEntity($this->server);
      $token->setId($result->auth_code);
      $token->setRedirectUri($result->client_redirect_uri);
      $token->setExpireTime($result->expire_time);

      return $token;
    }

    return;
  }

  public function create($token, $expireTime, $sessionId, $redirectUri)
  {
    app('db')->table('oauth_auth_code')
          ->insert([
            'auth_code'           => $token,
            'client_redirect_uri' => $redirectUri,
            'session_id'          => $sessionId,
            'expire_time'         => $expireTime,
          ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getScopes(AuthCodeEntity $token)
  {
    $result = app('db')->table('oauth_auth_code_scope')
                  ->select(['oauth_scope.id', 'oauth_scope.description'])
                  ->join('oauth_scope', 'oauth_auth_code_scope.scope', '=', 'oauth_scope.id')
                  ->where('auth_code', $token->getId())
                  ->get();

    $response = [];

    if (count($result) > 0) {
      foreach ($result as $row) {
        $scope = (new ScopeEntity($this->server))->hydrate([
          'id'          => $row->id,
          'description' => $row->description,
        ]);
        $response[] = $scope;
      }
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
  {
    app('db')->table('oauth_auth_code_scope')
          ->insert([
            'auth_code' => $token->getId(),
            'scope'     => $scope->getId(),
          ]);
  }

  /**
   * {@inheritdoc}
   */
  public function delete(AuthCodeEntity $token)
  {
    app('db')->table('oauth_auth_code')
          ->where('auth_code', $token->getId())
          ->delete();
  }
}
