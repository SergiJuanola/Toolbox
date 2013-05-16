<?php 
require_once 'class.builder.php';
require_once 'interface.iinputoutput.php';
@include 'class.vault.php';

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
	* @see Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}

	/**
	* Gets information from the data source
	*/
	abstract function retrieve();

	/**
	* Saves class information to the data source
	*/
	abstract function store();

	/**
	* Connects to the data source
	*/
	abstract function connect();

	/**
	* Disconnects from the data source
	*/
	abstract function disconnect();

	/**
	* Sets the Vault object
	* @param $vault Vault The Vault you want to use
	*/
	function setVault(Vault $vault)
	{
		$this->__vault = $vault;
	}

	/**
	* Gets the current Vault object, if present.
	* @return mixed The current Vault object, NULL otherwise
	*/
	function getVault()
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
	function hasVault()
	{
		if(!empty($this->__vault) && get_class($this->__vault) == "Vault")
		{
			return TRUE;
		}
		return FALSE;
	}
}