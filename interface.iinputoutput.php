<?php 
/**
 * Interface for Toolbox
 */
/**
* Interface used for InputOutput classes
* 
* This interface is used for classes that need
* to connect to a data source, just as a way to
* standarize all the classes related to external
* sources.
* 
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
*/
interface Iinputoutput {
	
	/**
	* Gets information from the data source
	* @param string $key The key to retrieve
	* @param mixed $default The default value, in case no key exists. Defaults to NULL
	* @return mixed The value to get
	*/
	function retrieve($key, $default = NULL);

	/**
	* Saves class information to the data source
	* @param string $key The key to store to
	* @param mixed $value The value to store
	*/
	function store($key, $value);

	/**
	* Connects to the data source
	*/
	function connect();

	/**
	* Disconnects from the data source
	*/
	function disconnect();

	/**
	* Sets the Vault object
	* @param Vault $vault The Vault you want to use
	*/
	function setVault(Vault $vault);

	/**
	* Gets the current Vault object, if present.
	* @return mixed The current Vault object, NULL otherwise
	*/
	function getVault();

	/**
	* Checks if a Vault is set
	* @return boolean TRUE if Vault is present, FALSE otherwise
	*/
	function hasVault();
}