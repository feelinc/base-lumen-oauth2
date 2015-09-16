<?php namespace App\OAuth2\Grant;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Grant\AuthCodeGrant;

/**
 * Auth code grant class
 */
class AuthCode extends AuthCodeGrant
{
  /**
   * Complete the auth code grant
   *
   * @return array
   *
   * @throws
   */
  public function completeFlow()
  {
    parent::completeFlow();

    $accessToken = $this->server->getTokenType()->getParam('access_token');
    $accessToken = $this->server->getAccessTokenStorage()->get($accessToken);

    $this->server->getTokenType()->setParam('expires', (int) $accessToken->getExpireTime());

    return $this->server->getTokenType()->generateResponse();
  }
}
