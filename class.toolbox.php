<?php 
/**
 * Toolbox framework
 */

/**
* Create, include and predefine tools to be used for the user
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
*/
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
		$this->prepareDependencies();
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
			try {
				$classDefault = $reflector->getStaticPropertyValue('default');
			} catch (ReflectionException $e) {
				$classDefault = array();				
			}
			$this->_loaded[$name] = array('default'=>array_merge($classDefault, $default));
		}
	}

	private function prepareDependencies($caughtDependencies = 0)
	{
		$currentCaughtDependencies = 0;
		$_loaded = $this->_loaded;
		foreach ($_loaded as $class => $config) {
			if(($dependency = $this->containsDependency($config['default'])) !== FALSE)
			{
				$currentCaughtDependencies++;
				if($this->containsDependency($this->getDefault($dependency)) === FALSE)
				{
					$className = ucfirst($dependency);
					$r = new ReflectionClass($className);
					$object = $r->getMethod('build')->invoke(null, $this->getDefault($dependency));
					$newConfig = $this->fixDependency($config['default'], $dependency, $object );
					$_loaded[$class]['default'] = $newConfig;
				}
			}
		}
		$this->_loaded = $_loaded;

		if($currentCaughtDependencies > 0)
		{
			if($caughtDependencies === $currentCaughtDependencies)
				throw new Exception('Loop!');
			else
				$this->prepareDependencies($currentCaughtDependencies);
		}
	}

	private function fixDependency(&$config, $dependencyName, $dependency)
	{
		foreach ($config as $key => &$value) {
			if(is_array($value))
			{
				$config[$key] =  $this->fixDependency($value, $dependencyName, $dependency);
				return $config;
			}
			else
			{
				if(is_string($value) && strcmp($value, "Toolbox::".$dependencyName) === 0)
				{
					$value = $dependency;
					return $config;
				}
			}
		}
	}

	private function containsDependency($config)
	{
		foreach ($config as $key => $value) {
			if(is_array($value))
			{
				$contains = $this->containsDependency($value);
				if($contains !== FALSE)
					return $contains;
			}
			elseif(is_string($value))
			{
				if(strpos($value, "Toolbox::") === 0)
					return substr($value, 9);
			}
		}
		return FALSE;
	}

	public function getDefault($class)
	{
		$class = strtolower($class);
			
		if(!array_key_exists($class, $this->_loaded))
			$this->load($class, array());
		return $this->_loaded[$class]['default'];
	}

	public function getWholeDefault($class)
	{
		$defaultConfig = $this->getDefault($class);
		$parentClass = get_parent_class($class);
		while($parentClass !== FALSE)
		{
			$default = $this->getDefault($parentClass);
			foreach ($default as $key => $value) {
				if(!array_key_exists($key, $defaultConfig))
				{
					$defaultConfig[$key] = $value;
				}
			}
			$parentClass = get_parent_class($parentClass);
		}
		return $defaultConfig;
	}

	public function setDefault($class, $default)
	{
		$this->_loaded[$class]['default'] = $default;
	}
}