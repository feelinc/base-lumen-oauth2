<?php namespace App\OAuth2\Entity;

/*
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use League\OAuth2\Server\Entity\ClientEntity as BaseClientEntity;

/**
 * Client entity class
 */
class ClientEntity extends BaseClientEntity
{
  /**
   * Client request limit
   *
   * @var integer
   */
  protected $requestLimit = null;

  /**
   * Client current total request
   *
   * @var integer
   */
  protected $currentTotalRequest = null;

  /**
   * Client request limit until
   *
   * @var timestamp
   */
  protected $requestLimitUntil = null;

  /**
   * Client last request at
   *
   * @var timestamp
   */
  protected $lastRequestAt = null;

  /**
   * Returnt the client request limit
   *
   * @return integer
   */
  public function getRequestLimit()
  {
    return $this->requestLimit;
  }

  /**
   * Returnt the current total request
   *
   * @return integer
   */
  public function getCurrentTotalRequest()
  {
    return $this->currentTotalRequest;
  }

  /**
   * Returnt the request limit until
   *
   * @return integer
   */
  public function getRequestLimitUntil()
  {
    return $this->requestLimitUntil;
  }

  /**
   * Returnt the request limit until
   *
   * @return integer
   */
  public function getLastRequestAt()
  {
    return $this->lastRequestAt;
  }
}
