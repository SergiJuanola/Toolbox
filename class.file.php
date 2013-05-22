<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.inputoutput.php';

/**
* Access to server files, edit and read them
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Builder
*/
class File extends Inputoutput {
	public static $default = array(
		'folder'=>NULL,
		'filename'=>NULL,
		'__resource'=>NULL,
	);

	public static function build($config = array()) {
		return new self($config);
	}

	public function connect()
	{
		$args = func_get_args();
		$mode = $args[0];
		$this->__resource = fopen($this->folder.$this->filename, $mode);
	}

	public function disconnect()
	{
		if($this->__resource)
			fclose($this->__resource);
	}

	public function store($contents, $nothing = NULL)
	{
		if(func_num_args() == 1)
		{
			$this->connect("w+");
			$args = func_get_args();
			$contents = $args[0];
			fwrite($this->__resource, $contents);
		}

		return $this;
	}

	public function retrieve($filename = NULL, $nothing = NULL)
	{
		$this->connect("r");
		clearstatcache();
		return fread($this->__resource, filesize($this->folder.$this->filename));
	}

	public function __destruct()
	{
		$this->disconnect();
	}
}