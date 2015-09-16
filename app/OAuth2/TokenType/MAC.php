<?php namespace App\OAuth2\TokenType;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\TokenType\MAC as MACStrategy;
use League\OAuth2\Server\Util\SecureKey;

/**
 * MAC Token Type
 */
class MAC extends MACStrategy
{
  /**
   * {@inheritdoc}
   */
  public function generateResponse()
  {
    $macKey = SecureKey::generate();
    $this->server->getMacStorage()->create($macKey, $this->getParam('access_token'));

    $response = [
      'access_token'  => $this->getParam('access_token'),
      'token_type'    => 'mac',
      'expires'       => $this->getParam('expires'),
      'expires_in'    => $this->getParam('expires_in'),
      'mac_key'       => $macKey,
      'mac_algorithm' => 'hmac-sha-256',
    ];

    return $response;
  }
}
