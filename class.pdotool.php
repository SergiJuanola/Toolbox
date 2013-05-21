<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

/**
* Create a PDO link to a database
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @see Builder
*/
class Pdotool extends Builder {
	/**
	* Default properties.
	*/
	public static $default = array(
		'host' => '',
		'user' => '',
		'pass' => '',
		'dbname' => '',
		'dsn' => null,
		'charset' => 'utf8',
		'driver' => 'mysql'
	);

	/**
	* Building method
	* @param array $config The config array
	* @see Builder::build()
	*/
	public static function build($config = array()) {
		$self = new self($config);
		if(empty($self->dsn))
		{
			switch ($self->driver) {
				default:
				case 'mysql':
					$self->dsn = 'mysql:host='.$self->host.';dbname='.$self->dbname.';charset='.$self->charset;
					break;
			}
		}
		$pdo = new PDO($self->dsn, $self->user, $self->pass);
		return $pdo;
	}


}