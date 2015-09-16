<?php namespace App\OAuth2\Storages;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\SessionInterface;

class Session extends AbstractStorage implements SessionInterface
{
  /**
   * {@inheritdoc}
   */
  public function getByAccessToken(AccessTokenEntity $accessToken)
  {
    $result = app('db')->table('oauth_session')
              ->select(['oauth_session.id', 'oauth_session.owner_type', 'oauth_session.owner_id', 'oauth_session.client_id', 'oauth_session.client_redirect_uri'])
              ->join('oauth_access_token', 'oauth_access_token.session_id', '=', 'oauth_session.id')
              ->where('oauth_access_token.access_token', $accessToken->getId())
              ->first();

    if (is_object($result)) {
      $session = new SessionEntity($this->server);
      $session->setId($result->id);
      $session->setOwner($result->owner_type, $result->owner_id);

      return $session;
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function getByAuthCode(AuthCodeEntity $authCode)
  {
    $result = app('db')->table('oauth_session')
              ->select(['oauth_session.id', 'oauth_session.owner_type', 'oauth_session.owner_id', 'oauth_session.client_id', 'oauth_session.client_redirect_uri'])
              ->join('oauth_auth_code', 'oauth_auth_code.session_id', '=', 'oauth_session.id')
              ->where('oauth_auth_code.auth_code', $authCode->getId())
              ->first();

    if (is_object($result)) {
      $session = new SessionEntity($this->server);
      $session->setId($result->id);
      $session->setOwner($result->owner_type, $result->owner_id);

      return $session;
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function getScopes(SessionEntity $session)
  {
    $result = app('db')->table('oauth_session')
              ->select('oauth_scope.*')
              ->join('oauth_session_scope', 'oauth_session.id', '=', 'oauth_session_scope.session_id')
              ->join('oauth_scope', 'oauth_scope.id', '=', 'oauth_session_scope.scope')
              ->where('oauth_session.id', $session->getId())
              ->get();

    $scopes = [];

    foreach ($result as $scope) {
      $scopes[] = (new ScopeEntity($this->server))->hydrate([
        'id'          => $scope->id,
        'description' => $scope->description,
      ]);
    }

    return $scopes;
  }

  /**
   * {@inheritdoc}
   */
  public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
  {
    $id = app('db')->table('oauth_session')
            ->insertGetId([
              'owner_type' => $ownerType,
              'owner_id'   => $ownerId,
              'client_id'  => $clientId,
            ]);

    return $id;
  }

  /**
   * {@inheritdoc}
   */
  public function associateScope(SessionEntity $session, ScopeEntity $scope)
  {
    app('db')->table('oauth_session_scope')
              ->insert([
                'session_id' => $session->getId(),
                'scope'      => $scope->getId(),
              ]);
  }
}
