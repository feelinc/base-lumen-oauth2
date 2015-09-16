<?php namespace App\OAuth2\Storages;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\MacTokenInterface;

class MAC extends AbstractStorage implements MacTokenInterface
{
  /**
   * {@inheritdoc}
   */
  public function create($macKey, $accessToken)
  {
    app('db')->table('oauth_mac_key')
          ->insert([
            'key'          => $macKey, 
            'access_token' => $accessToken
          ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getByAccessToken($accessToken)
  {
    $result = app('db')->table('oauth_mac_key')
              ->where('access_token', $accessToken)
              ->first();

    if (is_object($result)) {
      return $result->key;
    }

    return;
  }
}
