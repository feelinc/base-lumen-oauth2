<?php namespace App\OAuth2\TokenType;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\TokenType\Bearer as BearerStrategy;
use Symfony\Component\HttpFoundation\Request;

class Bearer extends BearerStrategy
{
  /**
   * {@inheritdoc}
   */
  public function generateResponse()
  {
    $return = [
      'access_token' => $this->getParam('access_token'),
      'token_type'   => 'Bearer',
      'expires'      => $this->getParam('expires'),
      'expires_in'   => $this->getParam('expires_in'),
    ];

    if (!is_null($this->getParam('refresh_token'))) {
      $return['refresh_token'] = $this->getParam('refresh_token');
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function determineAccessTokenInHeader(Request $request)
  {
    $header = $request->headers->get('Authorization');
    $accessToken = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $header));

    return ($accessToken === 'Bearer' || $accessToken === 'Basic') ? '' : $accessToken;
  }
}
