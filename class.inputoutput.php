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
* @see  Builder
* @see  Iinputoutput
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
	* @see  Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}

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
