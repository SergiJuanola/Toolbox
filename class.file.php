<?php 

require_once 'class.builder.php';
require_once 'interface.inputoutput.php';

class File extends Builder implements InputOutput {
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

	public function store()
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

	public function retrieve()
	{
		$this->connect("r");
		clearstatcache(false, $this->folder.$this->filename);
		return fread($this->__resource, filesize($this->folder.$this->filename));
	}

	public function __destruct()
	{
		$this->disconnect();
	}
}