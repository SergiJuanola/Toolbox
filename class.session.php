<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';
require_once 'interface.inputoutput.php';

class Session extends Builder implements InputOutput {
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
			$_SESSION[$this->prefix.$key] = $value;
		}
	}

	public function retrieve()
	{
		$args = func_get_args();
		if(func_num_args() == 1)
		{
			$args[] = NULL;
		}
		if(count($args) == 2)
		{
			$key = $args[0];
			$default = $args[1];
			if(!empty($_SESSION[$this->prefix.$key]))
				return $_SESSION[$this->prefix.$key];
			else
				return $default;
		}
	}

	public function connect()
	{
		if(session_id() == '') {
			session_start();
		}
	}

	public function disconnect()
	{
		session_write_close();
	}

	public function destroy()
	{
		$_SESSION = array();
		session_destroy();
	}

	public function __destruct()
	{
		$this->disconnect();
	}
}