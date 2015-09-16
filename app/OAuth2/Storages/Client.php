<?php namespace App\OAuth2\Storages;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\OAuth2\Entity\ClientEntity;

use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\ClientInterface;

class Client extends AbstractStorage implements ClientInterface
{
  /**
   * {@inheritdoc}
   */
  public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
  {
    $query = app('db')->table('oauth_client')
              ->select('oauth_client.*')
              ->where('oauth_client.id', $clientId);

    if ($clientSecret !== null) {
      $query->where('oauth_client.secret', $clientSecret);
    }

    if ($redirectUri) {
      $query->join('oauth_client_redirect_uri', 'oauth_client.id', '=', 'oauth_client_redirect_uri.client_id')
          ->select(['oauth_client.*', 'oauth_client_redirect_uri.*'])
          ->where('oauth_client_redirect_uri.redirect_uri', $redirectUri);
    }

    $result = $query->first();

    if (is_object($result)) {
      $client = new ClientEntity($this->server);
      $client->hydrate([
        'id'   => $result->id,
        'name' => $result->name,
      ]);

      return $client;
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function getBySession(SessionEntity $session)
  {
    $result = app('db')->table('oauth_client')
              ->select([
                'oauth_client.id', 'oauth_client.name'
              ])
              ->join('oauth_session', 'oauth_client.id', '=', 'oauth_session.client_id')
              ->where('oauth_session.id', $session->getId())
              ->first();

    if (is_object($result)) {
      $client = new ClientEntity($this->server);
      $client->hydrate([
        'id'   => $result->id,
        'name' => $result->name,
      ]);

      return $client;
    }

    return;
  }

  /**
   * Get the complete client data associated with a session
   *
   * @param \League\OAuth2\Server\Entity\SessionEntity $session The session
   *
   * @return \League\OAuth2\Server\Entity\ClientEntity | null
   */
  public function getCompleteBySession(SessionEntity $session)
  {
    $result = app('db')->table('oauth_client')
              ->select([
                'oauth_client.id', 
                'oauth_client.secret', 
                'oauth_client.name', 
                'oauth_client_redirect_uri.redirect_uri', 
                'oauth_client.request_limit', 
                'oauth_client.current_total_request', 
                'oauth_client.request_limit_until', 
                'oauth_client.last_request_at'
              ])
              ->join('oauth_session', 'oauth_client.id', '=', 'oauth_session.client_id')
              ->join('oauth_client_redirect_uri', 'oauth_client.id', '=', 'oauth_client_redirect_uri.client_id')
              ->where('oauth_session.id', $session->getId())
              ->first();

    if (is_object($result)) {
      $client = new ClientEntity($this->server);
      $client->hydrate([
        'id'                  => $result->id, 
        'secret'              => $result->secret, 
        'name'                => $result->name, 
        'redirectUri'         => $result->redirect_uri, 
        'requestLimit'        => $result->request_limit, 
        'currentTotalRequest' => $result->current_total_request, 
        'requestLimitUntil'   => $result->request_limit_until, 
        'lastRequestAt'       => $result->last_request_at
      ]);

      return $client;
    }

    return;
  }
}
