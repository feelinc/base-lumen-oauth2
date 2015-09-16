<?php namespace App\OAuth2\Storages;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\AbstractStorage;
use League\OAuth2\Server\Storage\RefreshTokenInterface;

class RefreshToken extends AbstractStorage implements RefreshTokenInterface
{
  /**
   * {@inheritdoc}
   */
  public function get($token)
  {
    $result = app('db')->table('oauth_refresh_token')
              ->where('refresh_token', $token)
              ->first();

    if (is_object($result)) {
      $token = (new RefreshTokenEntity($this->server))
            ->setId($result->refresh_token)
            ->setExpireTime($result->expire_time)
            ->setAccessTokenId($result->access_token);

      return $token;
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function create($token, $expireTime, $accessToken)
  {
    app('db')->table('oauth_refresh_token')
          ->insert([
            'refresh_token' => $token,
            'access_token'  => $accessToken,
            'expire_time'   => $expireTime,
          ]);
  }

  /**
   * {@inheritdoc}
   */
  public function delete(RefreshTokenEntity $token)
  {
    app('db')->table('oauth_refresh_token')
              ->where('refresh_token', $token->getId())
              ->delete();
  }
}
