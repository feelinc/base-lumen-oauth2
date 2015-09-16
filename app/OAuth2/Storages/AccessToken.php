<?php namespace App\OAuth2\Storages;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\AccessTokenInterface;

class AccessToken extends AbstractStorage implements AccessTokenInterface
{
  /**
   * {@inheritdoc}
   */
  public function get($token)
  {
    $result = app('db')->table('oauth_access_token')
              ->where('access_token', $token)
              ->first();

    if (is_object($result)) {
      $token = (new AccessTokenEntity($this->server))
            ->setId($result->access_token)
            ->setExpireTime($result->expire_time);

      return $token;
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function getScopes(AccessTokenEntity $token)
  {
    $result = app('db')->table('oauth_access_token_scope')
                  ->select(['oauth_scope.id', 'oauth_scope.description'])
                  ->join('oauth_scope', 'oauth_access_token_scope.scope', '=', 'oauth_scope.id')
                  ->where('access_token', $token->getId())
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
  public function create($token, $expireTime, $sessionId)
  {
    app('db')->table('oauth_access_token')
          ->insert([
            'access_token' => $token,
            'session_id'   => $sessionId,
            'expire_time'  => $expireTime,
          ]);
  }

  /**
   * {@inheritdoc}
   */
  public function associateScope(AccessTokenEntity $token, ScopeEntity $scope)
  {
    app('db')->table('oauth_access_token_scope')
          ->insert([
            'access_token' => $token->getId(),
            'scope'        => $scope->getId(),
          ]);
  }

  /**
   * {@inheritdoc}
   */
  public function delete(AccessTokenEntity $token)
  {
    app('db')->table('oauth_access_token')
          ->where('access_token', $token->getId())
          ->delete();
  }
}
