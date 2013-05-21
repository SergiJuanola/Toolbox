<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

/**
* Encrypt/decrypt texts automatically and url safe
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Builder
*/
class Vault extends Builder {
	public static $default = array(
		'algorithm' => MCRYPT_RIJNDAEL_256,
		'urlSafe' => false,
		'mode' => MCRYPT_MODE_CBC,
	);

	public static function build($config = array()) {
		return new self($config);
	}

	public function setAlgorithm($newAlgorithm)
	{
		$this->algorithm = $newAlgorithm;
		return $this;
	}

	public function getAlgorithm()
	{
		return $this->algorithm;
	}

	public function setIV($newIV)
	{
		$this->iv = $newIV;
		return $this;
	}

	public function getIV()
	{
		return $this->iv;
	}

	public function setMode($newMode)
	{
		$this->mode = $newMode;
		return $this;
	}

	public function getMode()
	{
		return $this->mode;
	}

	public function setKey($newKey)
	{
		$this->key = $newKey;
		return $this;
	}

	public function getKey()
	{
		return $this->key;
	}

	public function setUrlSafe($isUrlSafe)
	{
		$this->urlSafe = $isUrlSafe;
		return $this;
	}

	public function isUrlSafe()
	{
		return isset($this->urlSafe) && $this->urlSafe;
	}

	private function needsIV()
	{
		$needsIV = mcrypt_get_iv_size($this->algorithm, $this->mode);
		return ($needsIV !== FALSE && $needsIV !== 0);
	}

	private function getIVLength()
	{
		return mcrypt_get_iv_size($this->algorithm, $this->mode);
	}

	private function generateCongruentIV()
	{
		$ivLength = $this->getIVLength();
		if(!empty($this->iv))
		{
			if(strlen($this->iv) != $ivLength)
			{
				$this->iv = str_pad($this->iv, $ivLength, "#");
			}
		}
		else
		{
			$this->iv = mcrypt_create_iv($ivLength);
		}
		return $this;
	}

	public function encrypt($text)
	{

		if($this->needsIV())
		{
			$this->generateCongruentIV();
			$encrypted = mcrypt_encrypt($this->algorithm, $this->key, $text, $this->mode, $this->iv);
		}
		else
		{
			$encrypted = mcrypt_encrypt($this->algorithm, $this->key, $text, $this->mode);
		}
		if($this->isUrlSafe())
		{
			$encrypted = Vault::base64urlEncode($encrypted);
		}
		return $encrypted;
	}

	public function decrypt($encrypted)
	{
		if($this->isUrlSafe())
		{
			$encrypted = Vault::base64urlDecode($encrypted);
		}
		if($this->needsIV())
		{
			$this->generateCongruentIV();
			$text = mcrypt_decrypt($this->algorithm, $this->key, $encrypted, $this->mode, $this->iv);
		}
		else
		{
			$text = mcrypt_decrypt($this->algorithm, $this->key, $encrypted, $this->mode);
		}
		return $text;
	}

	private static function base64urlEncode($data) { 
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
	} 

	private static function base64urlDecode($data) { 
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
	} 
}