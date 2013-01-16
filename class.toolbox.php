<?php 

class Toolbox {
	protected $_config = array();
	private $_loaded = array();
	protected $_required;
	protected static $myself = null;

	protected function __construct($config)
	{
		$this->_config = $config;
		foreach ($this->_config as $name => $default) {
			$this->load($name, $default);
		}
		return $this;
	}

	public function __set($key, $value)
	{
		$this->_config[$key] = $value;
		return $this;
	}

	public function __get($key)
	{
		if(array_key_exists($key, $this->_config))
			return $this->_config[$key];
		else
			return NULL;
	}

	public static function build($config = array()) { 
		if(!isset(self::$myself))
			self::$myself = new self($config);
		return self::$myself;
	}

    final private function __clone() {}

	public function need($files)
	{
		foreach ($files as $name =>$default) {
			$this->load($name, $default);
		}
	}

	private function load($name, $default)
	{
		if (!array_key_exists($name, $this->_loaded)) {
			require_once('class.'.$name.'.php');
			$class = ucfirst($name);
			$reflector = new ReflectionClass($class);
			$classDefault = $reflector->getStaticPropertyValue('default');
			$this->_loaded[$class] = array('default'=>array_merge($classDefault, $default));
		}
	}

	public function getDefault($class)
	{
		return $this->_loaded[$class]['default'];
	}

	public function setDefault($class, $default)
	{
		$this->_loaded[$class]['default'] = $default;
	}
}