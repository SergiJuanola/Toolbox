<?php 
/**
 * Toolbox framework
 */

/**
* Create, include and predefine tools to be used for the user. Singleton class
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
*/
class Toolbox {

	/**
	* array The selected configuration for Toolbox and all of its modules
	*/
	protected $_config = array();


	/**
	* array The preloaded configuration for each tool
	*/
	private $_loaded = array();


	/**
	* Toolbox A reference to itself
	*/
	protected static $myself = NULL;


	/**
	* Protected constructor. It should never be publicly accessed
	* @param array $config The config array
	*/
	protected function __construct($config)
	{
		$this->_config = $config;
		foreach ($this->_config as $name => $default) {
			$this->load($name, $default);
		}
		$this->prepareDependencies();
		return $this;
	}


	/**
	* Set a value to the config structure
	* @param string $key The key to the configuration
	* @param mixed $value The value to store
	*/
	public function __set($key, $value)
	{
		$this->_config[$key] = $value;
		return $this;
	}


	/**
	* Set a value to the config structure
	* @param string $key The key to the configuration
	* @return mixed $value The value to retrieve
	*/
	public function __get($key)
	{
		if(array_key_exists($key, $this->_config))
			return $this->_config[$key];
		else
			return NULL;
	}


	/**
	* Building method. It is the only way to create Toolbox
	* @param array $config The config array
	* @link Builder::build()
	*/
	public static function build($config = array()) { 
		if(!isset(self::$myself))
			self::$myself = new self($config);
		return self::$myself;
	}


	/**
	* Clone the object. It is not accessible
	*/
    final private function __clone() {}


    /**
    * Load the configuration for the selected tools
    * @param array $files The list to be included
    */
	public function need($files)
	{
		foreach ($files as $name =>$default) {
			$this->load($name, $default);
		}
	}


	/**
	* Load the configuration for a defined tool
	* @param string $name The tool name, in lowercase
	* @param array $default The default configuration for a tool
	*/
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


	/**
	* Prepare the dependencies of any reference to a tool in the configuration
	* @param int $caughtDependencies The amount of previously caught dependencies
	*/
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


	/**
	* Fix a dependency for a tool
	* @param array $config The configuration for the selected dependency
	* @param string $dependencyName The name for this dependency
	* @param object $dependency The object generated for the passed config
	* @return array The prepared configuration with the dependencies fixed
	*/
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


	/**
	* Check if a configuration contains a dependency
	* @param array $config The configuration to be checked
	* @return boolean TRUE if a configuration contains a dependency, FALSE otherwise
	*/
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


	/**
	* Get the default configuration of a class, set in Toolbox
	* @param string $class The class to check
	* @return array The default configuration of a class, or an empty array otherwise
	*/
	public function getDefault($class)
	{
		$class = strtolower($class);
			
		if(!array_key_exists($class, $this->_loaded))
			$this->load($class, array());
		return $this->_loaded[$class]['default'];
	}


	/**
	* Get the default configuration of a class, set in Toolbox, plus the class default configuration
	* @param string $class The class to check
	* @return array The whole default configuration of a class, or an empty array otherwise
	*/
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


	/**
	* Set the default Toolbox configuration of a tool
	* @param string $class The class to be reset
	* @param array The default configuration to be set
	*/
	public function setDefault($class, $default)
	{
		$this->_loaded[$class]['default'] = $default;
	}
}