<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

/**
* Controller class, used by Match to map rules. Classes should extend this class.
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Match
*/
class Controller {
	/**
	* Match instance that called this Controller
	*/
	private $__match;
	/**
	* If present, Toolbox instance that manages the system
	*/
	private $__toolbox;

	/**
	* Constructor called for every Controller
	* @param Match $match The Match instance calling this controller
	* @param Toolbox $toolbox The Toolbox instance, if present
	*/
	public function __construct($match, $toolbox)
	{
		$this->__match = $match;
		$this->__toolbox = $toolbox;
	}

	/**
	* Gets the current Match class if correctly set.
	* @return Match The Match if set, or NULL otherwise
	*/
	protected function getMatch()
	{
		return !empty($this->__match)? $this->__match : null;
	}

	/**
	* Gets the Toolbox instance, if present.
	* @return Toolbox The Toolbox instance if present, NULL otherwise
	*/
	protected function getToolbox()
	{
		return !empty($this->__toolbox)? $this->__toolbox : null;
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
 ?>