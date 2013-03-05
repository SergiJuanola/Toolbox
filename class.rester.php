<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

/**
* Facade for REST API calls
*
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @version	0.5
* @see Builder
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
	);

	/**
	* Building method
	* @param array $config The config array
	* @return Rester An instance of itself
	* @see Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}

	/**
	* Generates a HMAC string with the chosen data and a secret key
	* @param array $data The data array
	* @param string $secret The secret key
	* @param string timestamp The current timestamp, formatted Y-m-d\TH:i:sO. Optional, defaults to the current timestamp
	* @see DateTime::ISO8601
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

		return array($hmac, $timestmap);
	}

	/**
	* Checks if an HMAC is correctly generated
	* @param array $data The data array
	* @param string $secret The secret key
	* @param string timestamp The current timestamp, formatted Y-m-d\TH:i:sO. You need to pass the timestamp the user sent
	* @see DateTime::ISO8601
	* @param string $hmac The HMAC you are comparing to
	* @return string The resulting hmac
	*/
	public function checkHMAC($data, $secret, $timestamp, $hmac)
	{
		$hmac1 = $this->generateHmac($data, $secret, $timestamp);
		return $hmac == $hmac1;
	}

	/**
	* Returns the current date in an accepted format
	* @return string The current date in ISO8601 format
	* @see DateTime::ISO8601
	*/
	public function getCurrentDate()
	{
		return gmdate(DateTime::ISO8601);
	}

	/**
	* Checks if the date is within the valid range for a call
	* @see Rester::validDateRange
	* @param string $date The date you are comparing to
	* @return bool TRUE if the date is valid, FALSE otherwise
	*/
	public function isDateValid($date)
	{
		$now = strtotime(getCurrentDate());
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

		if($this->checkHMAC($data, $secret, $timestamp) === FALSE)
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
}

class ResterException extends Exception { }

class NotImplementedResterException extends ResterException {}
class AccessDeniedResterException extends ResterException {}
