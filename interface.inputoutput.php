<?php 
/**
 * Interface for Toolbox
 * @package Toolbox
 */
/**
* Interface used for InputOutput classes
* 
* This interface is used for classes that need
* to connect to a data source, just as a way to
* standarize all the classes related to external
* sources.
*/
interface InputOutput {
	/**
	* Gets information from the data source
	*/
	function retrieve();

	/**
	* Saves class information to the data source
	*/
	function store();

	/**
	* Connects to the data source
	*/
	function connect();

	/**
	* Disconnects from the data source
	*/
	function disconnect();
}