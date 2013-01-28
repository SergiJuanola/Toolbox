<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */
/**
* Builder class for Toolbox tools
*
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @version	0.5
*/
abstract class Builder {

	/**
	 * @var array $_config The array used to store configurations for each tool
	 */
	protected $_config = array();

	/**
	 * Constructor for Builder classes.
	 * @param array $config The configuration used by this tool
	 */
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

	/**
	 * Checks if there is a Toolbox accessible
	 * @return boolean TRUE if Toolbox exists, FALSE otherwise
	 */
	public static function hasToolbox()
	{
		return class_exists("Toolbox");
	}

	/**
	 * Sets a Builder's configuration variable
	 * @param string $key The variable name
	 * @param mixed $value The variable value. It can be of any type.
	 * @return Builder An instance of itself
	 */
	public function __set($key, $value)
	{
		$this->_config[$key] = $value;
		return $this;
	}

	/**
	 * Gets a Builder's configuration variable
	 * @param string $key The variable name
	 * @return mixed The configuration value for the selected variable
	 */
	public function __get($key)
	{
		if(array_key_exists($key, $this->_config))
			return $this->_config[$key];
		else
			return NULL;
	}

	/**
	 * Checks if a configuration variable is set
	 * @param string $name The variable name
	 * @return boolean TRUE if variable exists, FALSE otherwise
	 */
	public function __isset($name)
    {
        return isset($this->_config[$name]);
    }

	/**
	 * Unsets a variable from the configuration
	 * @param string $name The variable name
	 */
    public function __unset($name)
    {
        unset($this->_config[$name]);
    }

    /**
     * If Toolbox is enabled, sets the configuration array as default
     * @param string $class the class' name of this tool
     * @param array $default the configuration that replaces the class' one
     */
	public static function setDefault($class, $default) {
		if(self::hasToolbox())
			Toolbox::build()->setDefault($class, $default);
	}

    /**
     * If Toolbox is enabled, adds the configuration array as default
     * @param string $class the class' name of this tool
     * @param array $default the configuration that is added to the class' one
     */
	public static function addDefault($class, $default) {
		if(self::hasToolbox())
			Toolbox::build()->setDefault($class, array_merge($default, self::getDefault($class)));
	}

    /**
     * If Toolbox is enabled, sets the configuration array as default
     * @param string $class the class' name of this tool
     * @return mixed The class' default configuration, or an empty array if Toolbox is not set
     */
	public static function getDefault($class) {
		if(self::hasToolbox())
			return Toolbox::build()->getDefault($class);
		else
			return array();
	}

    /**
     * Resets the tool's instance to the default configuration
     * @param string $class the class' name of this tool
     */
	public function resetToDefault($class) { foreach (self::getDefault($class) as $key => $value) { $this->_config[$key] = $value; } }

	/**
	 * Building method
	 * @param array $config The configuration for this builder
	 * @return Builder An instance of itself
	 */
	public static function build($config = array()) { return new self($config); }
}
