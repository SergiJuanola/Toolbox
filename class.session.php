<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.inputoutput.php';

/**
* Connect to your current session, store and retrieve data
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @see  Inputoutput
*/
class Session extends Inputoutput {
	/**
	* Default properties.
	* @param string $prefix The session prefix you'd like to use. Defaults to an empty string
	*/
	public static $default = array(
		'prefix' => '',
	);

	/**
	* Building method
	* @param array $config The config array
	* @see  Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}

	/**
	* The constructor. It connects to the session
	* @param array $config The config array
	*/
	public function __construct($config)
	{
		parent::__construct($config);
		$this->connect();
	}

	/**
	* Saves class information to the data source
	* @param string $key The session's key
	* @param mixed $value The session's value
	*/
	public function store($key, $value)
	{
		if(func_num_args() == 2)
		{
			$args = func_get_args();
			$key = $args[0];
			$value = $args[1];
			$_SESSION[$this->prefix.$key] = $this->processInput($value);
		}
		return $this;
	}

	/**
	* Gets information from the data source
	* @param string $key The session's key
	* @param mixed $default The session's value in case no value is defined. Defaults to NULL
	* @return mixed The session value
	*/
	public function retrieve($key, $default = NULL)
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
		if(!empty($_SESSION[$this->prefix.$key]))
			return $this->processOutput($_SESSION[$this->prefix.$key]);
		else
			return $default;
	}

	/**
	* Starts the session, in case it was not started
	*/
	public function connect()
	{
		@session_start();
		return $this;
	}

	/**
	* Writes and closes the current session
	*/
	public function disconnect()
	{
		session_write_close();
		return $this;
	}

	/**
	* Totally destroys the current session, removing its information
	*/
	public function destroy()
	{
		$this->connect();
		session_unset();
		session_destroy();
		$_SESSION = array();
		return $this;
	}

	/**
	* Disconnects the current session
	* @see  Session::disconnect()
	*/
	public function __destruct()
	{
		$this->disconnect();
		return $this;
	}
}