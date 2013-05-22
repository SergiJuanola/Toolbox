<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */
require_once 'class.builder.php';
require_once 'interface.iinputoutput.php';
@include 'class.vault.php';

/**
* Class to connect to external sources. You should extend this class (or implement its interface) if you use a class that gets data from a source
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Builder
* @link Iinputoutput
*/
abstract class Inputoutput extends Builder implements Iinputoutput {
	/**
	* Default properties.
	*/
	public static $default = array(
		'__vault' => NULL
	);

	/**
	* Building method
	* @param array $config The config array
	* @link Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}
	
	/**
	* Gets information from the data source
	* @param string $key The key to retrieve
	* @param mixed $default The default value, in case no key exists. Defaults to NULL
	* @return mixed The value to get
	*/
	public abstract function retrieve($key, $default = NULL);

	/**
	* Saves class information to the data source
	* @param string $key The key to store to
	* @param mixed $value The value to store
	*/
	public abstract function store($key, $value);

	/**
	* Connects to the data source
	*/
	public abstract function connect();

	/**
	* Disconnects from the data source
	*/
	public abstract function disconnect();

	/**
	* Sets the Vault object
	* @param Vault $vault The Vault you want to use
	*/
	function setVault(Vault $vault)
	{
		$this->__vault = $vault;
	}

	/**
	* Gets the current Vault object, if present.
	* @return mixed The current Vault object, NULL otherwise
	*/
	public function getVault()
	{
		if($this->hasVault())
		{
			return $this->__vault;
		}
	}

	/**
	* Checks if a Vault is set
	* @return boolean TRUE if Vault is present, FALSE otherwise
	*/
	public function hasVault()
	{
		if(!empty($this->__vault) && !is_string($this->__vault) && get_class($this->__vault) == "Vault")
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Process the input using Vault, if present
	* @param $input mixed The input
	* @return String A processed input, if possible
	*/
	public function processInput($input)
	{
		if(!is_string($input) && !is_numeric($input) && !is_bool($input))
			return $input;
		if(!$this->hasVault())
			return $input;
		return $this->getVault()->encrypt($input);
	}

	/**
	* Process the output using Vault, if present
	* @param $output mixed The output
	* @return String A processed output, if possible
	*/
	public function processOutput($output)
	{
		if(!is_string($output) && !is_numeric($output) && !is_bool($output))
			return $output;
		if(!$this->hasVault())
			return $output;
		return $this->getVault()->decrypt($output);
	}
}
