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
* @link Inputoutput
*/
class Session extends Inputoutput {
	public static $default = array(
		'prefix' => '',
	);

	public static function build($config = array()) {
		return new self($config);
	}

	public function __construct($config)
	{
		parent::__construct($config);
		$this->connect();
	}

	public function store()
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
		if(!empty($_SESSION[$this->prefix.$key]))
			return $this->processOutput($_SESSION[$this->prefix.$key]);
		else
			return $default;
	}

	public function connect()
	{
		@session_start();
		return $this;
	}

	public function disconnect()
	{
		session_write_close();
		return $this;
	}

	public function destroy()
	{
		$this->connect();
		session_unset();
		session_destroy();
		$_SESSION = array();
		return $this;
	}

	public function __destruct()
	{
		$this->disconnect();
		return $this;
	}
}