<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

/**
* Base example class for Tools
*/
class Base extends Builder {
	/**
	* Default properties.
	*/
	public static $default = array();

	/**
	* Building method
	* @param array $config The config array
	* @see Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}
}