<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

/**
* Facade for REST API calls
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @see  Builder
*/
class Rester extends Builder {

	/**
	* Default properties.
	* @param string $algorithm The algorithm name. Defaults to sha256
	* @param int $validDateRange The time in seconds while a timestamp is valid. Defaults to 300 seconds (5 minutes)
	* @param string $timestampFieldName The name of the parameter holding the timestamp. Defaults to rester_hmac_timestamp
	* @param string $dataFieldName The name of the parameter holding the call data. Defaults to rester_hmac_data
	* @param string $keyFieldName The name of the parameter holding the user public key. Defaults to rester_hmac_key
	* @param string $digestFieldName The name of the parameter holding the HMAC string (digest). Defaults to rester_hmac_digest
	*/
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

	/**
	* Building method
	* @param array $config The config array
	* @return Rester An instance of itself
	* @see  Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}

	/**
	* Generates a HMAC string with the chosen data and a secret key
	* @param array $data The data array
	* @param string $secret The secret key
	* @param string timestamp The current timestamp, formatted Y-m-d\TH:i:sO. Optional, defaults to the current timestamp
	* @see  DateTime::ISO8601
	* @return array The resulting HMAC and the timestamp used for this call.
	*/
	public function generateHMAC($data, $secret, $timestamp = NULL)
	{
		if(empty($timestamp))
			$timestamp = $this->getCurrentDate();
		ksort($data, SORT_REGULAR);
		$string = "";
		foreach ($data as $key => $value) {
			$string .= $key.'='.$value.'&';
		}
		$string .= $this->timestampFieldName.'='.$timestamp;
		$hmac = hash_hmac($this->algorithm, $string, $secret);

		return array($hmac, $timestamp);
	}

	/**
	* Checks if an HMAC is correctly generated
	* @param array $data The data array
	* @param string $secret The secret key
	* @param string timestamp The current timestamp, formatted Y-m-d\TH:i:sO. You need to pass the timestamp the user sent
	* @see  DateTime::ISO8601
	* @param string $hmac The HMAC you are comparing to
	* @return string The resulting hmac
	*/
	public function checkHMAC($data, $secret, $timestamp, $hmac)
	{
		list($hmac1, $list) = $this->generateHmac($data, $secret, $timestamp);
		return $hmac == $hmac1;
	}

	/**
	* Returns the current date in an accepted format
	* @return string The current date in ISO8601 format
	* @see  DateTime::ISO8601
	*/
	public function getCurrentDate()
	{
		return gmdate(DateTime::ISO8601);
	}

	/**
	* Checks if the date is within the valid range for a call
	* @see  Rester::validDateRange
	* @param string $date The date you are comparing to
	* @return bool TRUE if the date is valid, FALSE otherwise
	*/
	public function isDateValid($date)
	{
		$now = strtotime($this->getCurrentDate());
		$then = strtotime($date);
		return (($now-$then) <= $this->validDateRange);
	}

	/**
	* Packs the information into a signed pack, so you can make a call to your API
	* @param array $data The data array
	* @param string $key The user public key
	* @param string $secret The user secret
	* @return array The processed data pack, ready to be sent.
	*/
	public function packSignature($data, $key, $secret)
	{
		$processedData = array();
		$processedData[$this->dataFieldName] = $data;
		$processedData[$this->keyFieldName] = $key;
		list($hmac, $timestamp) = $this->generateHMAC($data, $secret);
		$processedData[$this->digestFieldName] = $hmac;
		$processedData[$this->timestampFieldName] = $timestamp;
		return $processedData;
	}

	/**
	* Unpacks the information from a signed pack, so you can check everything worked.
	* @param array $processedData The processed data array
	* @return mixed FALSE if anything stopped working, the  array $data if everything worked.
	*/
	public function unpackSignature($processedData)
	{
		$timestamp = $processedData[$this->timestampFieldName];
		$hmac = $processedData[$this->digestFieldName];
		$data = $processedData[$this->dataFieldName];
		if(!$this->isDateValid($timestamp))
		{
			throw new AccessDeniedResterException('The call has expired. Re-call it again.');
			return FALSE;
		}

		$key = $processedData[$this->keyFieldName];
		$secret = $this->getUserSecret($key);
		if($secret === FALSE)
		{
			throw new AccessDeniedResterException('The user doesn\'t exist.');
			return FALSE;
		}

		if($this->isNoncePresent($key, $hmac, $timestamp) === TRUE)
		{
			throw new AccessDeniedResterException('The call has been triggered once.');
			return FALSE;
		}
		else
		{
			$this->writeNonce($key, $hmac, $timestamp);
		}

		if($this->checkHMAC($data, $secret, $timestamp, $hmac) === FALSE)
		{
			throw new AccessDeniedResterException('The secret is incorrect or the request is malformed.');
			return FALSE;
		}

		return $data;
	}

	/**
	* Gets the user secret from a persistent system. You need to override this function.
	* @param string $key The user key
	* @return mixed A string containing the user secret, FALSE if key is not present.
	*/
	protected function getUserSecret($key)
	{
		throw new NotImplementedResterException('getUserSecret is not implemented yet. If you are on the Server side, you need to extend Rester and implement this method.');
	}

	/**
	* Checks if the call has been used before in a persistent system. You need to override this function.
	* @param string $key The user key
	* @param string $hmac The HMAC
	* @param string $timestamp The call timestamp
	* @return bool TRUE if the Nonce is present, FALSE otherwise
	*/
	protected function isNoncePresent($key, $hmac, $timestamp)
	{
		throw new NotImplementedResterException('isNoncePresent is not implemented yet. If you are on the Server side, you need to extend Rester and implement this method.');
	}

	/**
	* Writes a nonce into a persistent system. You need to override this function.
	* @param string $key The user key
	* @param string $hmac The HMAC
	* @param string $timestamp The call timestamp
	*/
	protected function writeNonce($key, $hmac, $timestamp)
	{
		throw new NotImplementedResterException('writeNonce is not implemented yet. If you are on the Server side, you need to extend Rester and implement this method.');
	}

	/**
	* Set a your Vault for the rester to use it
	* @param Vault $vault The Vault
	*/
	public function setVault(Vault $vault)
	{
		$this->__vault = $vault;
		return $this;
	}

	/**
	* Add data to the Rester call
	* @param string $key The data key
	* @param mixed $value The value you want to pass
	* @param boolean $isEncrypted If the information needs to be encrypted. Defaults to FALSE
	*/
	public function addData($key, $value, $isEncrypted = FALSE)
	{
		$data = $this->data;
		if(FALSE === $isEncrypted)
			$data[$key] = $value;
		elseif(isset($this->__vault) && get_class($this->__vault) == "Vault")
			$data[$key] = $this->__vault->encrypt($value);
		$this->data = $data;
		return $this;
	}

	/**
	* Makes a call to the Rester's API target
	* @param string $uri The URI you are querying
	*/
	public function call($uri)
	{
		$curl = curl_init($uri);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);

		$data = $this->packSignature($this->data, $this->userKey, $this->userSecret);
		$data = http_build_query($data);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$curl_response = curl_exec($curl);
		$this->data = array();
		curl_close($curl);
		echo $curl_response;
	}
}


/**
* Generic Rester Exception
*
* @package Toolbox
* @subpackage Exceptions
* @see  Rester
*/
class ResterException extends Exception {}

/**
* Exception called when a Rester method is not implemented
*
* @package Toolbox
* @subpackage Exceptions
* @see  Rester
*/
class NotImplementedResterException extends ResterException {}

/**
* Exception called when the user has no access to an API
*
* @package Toolbox
* @subpackage Exceptions
* @see  Rester
*/
class AccessDeniedResterException extends ResterException {}
