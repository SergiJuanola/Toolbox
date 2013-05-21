<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.inputoutput.php';

/**
* Create, delete or retrieve cookies
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Inputoutput
*/
class Cookie extends Inputoutput {
	/**
	* Default properties.
	*/
	public static $default = array(
		'prefix' => '',
		'expirationTime' => 2592000, // 1 month in seconds
	);

	/**
	* Building method
	* @param array $config The config array
	* @link Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}

	/**
	* Gets information from the data source
	* @return mixed The cookie value
	*/
	public function retrieve()
	{
		$args = func_get_args();
		if(func_num_args() == 0)
		{
			return NULL;
		}
		if(count($args) == 1)
		{
			$default = NULL;
		}
		else
		{
			$default = $args[1];
		}
		$key = $args[0];
		if(!empty($_COOKIE[$this->prefix.$key]))
			return $this->processOutput($_COOKIE[$this->prefix.$key]);
		else
			return $default;
	}

	/**
	* Saves class information to the data source
	* @param string $key The cookie's key
	* @param mixed $value The cookie's value
	* @param int $expirationTime The specific expiration time from now.
	*/
	public function store()
	{
		if(func_num_args() >= 2)
		{
			$args = func_get_args();
			$key = $args[0];
			$value = $args[1];
			if(func_num_args() == 3)
				$expirationTime = $args[3];
			else
				$expirationTime = $this->expirationTime;
			setcookie($this->prefix.$key, $this->processInput($value), time()+$expirationTime);
		}
		return $this;
	}

	/**
	* Remove a cookie
	* @param string $key The cookie's key
	*/
	public function remove($key)
	{
		setcookie($this->prefix.$key, "", time()-3600);
		return $this;
	}

	/**
	* Connects to the data source
	*/
	public function connect()
	{
		// No need to connect
	}

	/**
	* Disconnects from the data source
	*/
	public function disconnect()
	{
		// No need to disconnect
	}
}