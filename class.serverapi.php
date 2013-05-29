<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.rester.php';

/**
* Basic, empty class example that implements Rester
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @see  Rester
*/
class Serverapi extends Rester {

	public static $default = array(
		'algorithm' => 'sha256',
		'validDateRange' => 300, // 5 minutes
		'timestampFieldName' => 'rester_hmac_timestamp',
		'dataFieldName' => 'rester_hmac_data',
		'keyFieldName' => 'rester_hmac_key',
		'digestFieldName' => 'rester_hmac_digest',
		'uri' => '',
		'data' => array(),
		'__vault' => NULL,
		'userKey' => '',
		'userSecret' => '',
	);

	public static function build($config = array()) {
		return new self($config);
	}

	protected function getUserSecret($key)
	{
		return $this->userSecret;
	}

	protected function isNoncePresent($key, $hmac, $timestamp)
	{
		return false;
	}

	protected function writeNonce($key, $hmac, $timestamp)
	{
		return;
	}
}