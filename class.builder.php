<?php 

abstract class Builder {
	protected $_config = array();

	public function __construct($config)
	{
		foreach (self::getDefault(get_class($this)) as $key => $value) {
			if(!array_key_exists($key, $config))
			{
				$config[$key] = $value;
			}
		}
		$this->_config = $config;
	}

	public static function hasToolbox()
	{
		return class_exists("Toolbox");
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

	public function __isset($name)
    {
        return isset($this->_config[$name]);
    }

    public function __unset($name)
    {
        unset($this->_config[$name]);
    }

	public static function setDefault($class, $default) {
		if(self::hasToolbox())
			Toolbox::build()->setDefault($class, $default);
	}

	public static function addDefault($class, $default) {
		if(self::hasToolbox())
			Toolbox::build()->setDefault($class, array_merge($default, self::getDefault($class)));
	}

	public static function getDefault($class) {
		if(self::hasToolbox())
			return Toolbox::build()->getDefault($class);
	}

	public function resetToDefault($class) { foreach (self::getDefault($class) as $key => $value) { $this->_config[$key] = $value; } }

	public static function build($config = array()) { return new self($config); }
}
