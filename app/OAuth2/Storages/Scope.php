<?php namespace App\OAuth2\Storages;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\ScopeInterface;

class Scope extends AbstractStorage implements ScopeInterface
{
  /**
   * {@inheritdoc}
   */
  public function get($scope, $grantType = null, $clientId = null)
  {
    $result = app('db')->table('oauth_scope')
                ->where('id', $scope)
                ->first();

    if (is_null($result)) {
      return;
    }

    return (new ScopeEntity($this->server))->hydrate([
      'id'          => $result->id,
      'description' => $result->description,
    ]);
  }
}
