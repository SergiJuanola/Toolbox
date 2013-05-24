<?php
/**
 * Tool for Toolbox
 * @package Toolbox
 */
require_once 'class.builder.php';

/**
* Improved controller class, used by Match to map rules. Controllers should extend this class.
* You can extend either this controller or the Controller class. They both do the same, except this
* is also treated as a tool, so you are able to set default configurations to it. This way, you 
* don't need to define and pass variables to brush.
* 
* Use this just in case you want to use Brush, Dictionary, Match, Session, Cookie and Vault
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Match
*/
class Controllertool extends Builder {
	/**
	* Default properties.
	* @param Match $match The Match instance calling this controller
	* @param Toolbox $toolbox The Toolbox instance, if present
	*/
	public static $default = array(
		'brush' => NULL,
		'cookie' => NULL,
		'dictionary' => NULL,
		'match' => NULL,
		'session' => NULL,
		'toolbox' => NULL,
		'vault' => NULL,
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
	* Gets the current Brush class if correctly set.
	* @return Brush The Brush if set, or NULL otherwise
	*/
	protected function getBrush()
	{
		return !empty($this->brush)? $this->brush : Brush::build();
	}

	/**
	* Gets the current Cookie class if correctly set.
	* @return Cookie The Cookie if set, or NULL otherwise
	*/
	protected function getCookie()
	{
		return !empty($this->cookie)? $this->cookie : Cookie::build();
	}

	/**
	* Gets the current Dictionary class if correctly set.
	* @return Match The Dictionary if set, or NULL otherwise
	*/
	protected function getDictionary()
	{
		return !empty($this->match)? $this->match : Dictionary::build();
	}

	/**
	* Gets the current Match class if correctly set.
	* @return Match The Match if set, or NULL otherwise
	*/
	protected function getMatch()
	{
		return !empty($this->match)? $this->match : Match::build();
	}

	/**
	* Gets the current Session class if correctly set.
	* @return Match The Session if set, or NULL otherwise
	*/
	protected function getSession()
	{
		return !empty($this->session)? $this->session : Session::build();
	}

	/**
	* Gets the Toolbox instance, if present.
	* @return Toolbox The Toolbox instance if present, NULL otherwise
	*/
	protected function getToolbox()
	{
		return !empty($this->toolbox)? $this->toolbox : Toolbox::build();
	}

	/**
	* Function called before the selected action is called.
	* 
	* This function is called every time an action is called. This
	* means it can be used in case you always instance the same classes
	* with a concrete configuration, or to check if a user is logged in,
	* for example.
	* @link Controller::afterFire()
	*/
	public function beforeFire() {}

	/**
	* Function called after the selected action is called.
	* 
	* This function is called every time an action is called. This
	* means it can be used for storing information in non volatile
	* systems (databases, sessions, files) that happen to be used
	* in every action.
	* @link Controller::beforeFire()
	*/
	public function afterFire() {}
}