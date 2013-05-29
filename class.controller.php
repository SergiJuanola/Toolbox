<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

/**
* Controller class, used by Match to map rules. Controllers should extend this class.
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @see  Match
*/
class Controller {
	/**
	* Controllertool The centralised tool holding defaults
	*/
	protected $tool;

	/**
	* Constructor called for every Controller
	* @param Match $match The loaded Match, with all the rules set
	*/
	public function __construct($match, $toolbox)
	{
		$this->tool = Controllertool::build()->setMatch($match)->setToolbox($toolbox);
	}

	/**
	* Gets the current Brush class if correctly set.
	* @return Brush The Brush if set, or a default instance otherwise
	*/
	protected function getBrush()
	{
		return $this->tool->getBrush();
	}

	/**
	* Sets the Brush to the tool
	* @param Brush $brush The new Brush
	*/
	protected function setBrush(Brush $brush)
	{
		$this->tool->setBrush($brush);
		return $this;
	}

	/**
	* Gets the current Cookie class if correctly set.
	* @return Cookie The Cookie if set, or a default instance otherwise
	*/
	protected function getCookie()
	{
		return $this->tool->getCookie();
	}

	/**
	* Sets the Cookie
	* @return Cookie $cookie The new Cookie
	*/
	protected function setCookie(Cookie $cookie)
	{
		$this->tool->setCookie($cookie);
		return $this;
	}

	/**
	* Gets the current Dictionary class if correctly set.
	* @return Dictionary The Dictionary if set, or a default instance otherwise
	*/
	protected function getDictionary()
	{
		return $this->tool->getDictionary();
	}

	/**
	* Sets the Dictionary to the tool
	* @param Dictionary $dictionary The new Dictionary
	*/
	protected function setDictionary(Dictionary $dictionary)
	{
		$this->tool->setDictionary($dictionary);
		return $this;
	}

	/**
	* Gets the current Match class if correctly set.
	* @return Match The Match if set, or a default instance otherwise
	*/
	protected function getMatch()
	{
		return $this->tool->getMatch();
	}

	/**
	* Sets the Match to the tool
	* @param Match $match The new Match
	*/
	protected function setMatch(Match $match)
	{
		$this->tool->setMatch($match);
		return $this;
	}

	/**
	* Gets the current Pdo class if correctly set.
	* @return Pdo The Pdo if set, or a default instance otherwise
	*/
	protected function getPdo()
	{
		return $this->tool->getPdo();
	}

	/**
	* Sets the Pdo to the tool
	* @param Pdo $pdo The new Pdo
	*/
	protected function setPdo(Pdo $pdo)
	{
		$this->tool->setPdo($pdo);
		return $this;
	}

	/**
	* Gets the current Session class if correctly set.
	* @return Session The Session if set, or a default instance otherwise
	*/
	protected function getSession()
	{
		return $this->tool->getSession();
	}

	/**
	* Sets the Session to the tool
	* @param Session $session The new Session
	*/
	protected function setSession(Session $session)
	{
		$this->tool->setSession($session);
		return $this;
	}

	/**
	* Gets the Toolbox instance, if present.
	* @return Toolbox The Toolbox instance if present, NULL otherwise
	*/
	protected function getToolbox()
	{
		return $this->tool->getToolbox();
	}

	/**
	* Sets the Toolbox to the tool
	* @param Toolbox $toolbox The new Toolbox
	*/
	protected function setToolbox(Toolbox $toolbox)
	{
		$this->tool->setToolbox($toolbox);
		return $this;
	}

	/**
	* Gets the current Vault class if correctly set.
	* @return Vault The Vault if set, or a default instance otherwise
	*/
	protected function getVault()
	{
		return $this->tool->getVault();
	}

	/**
	* Sets the Vault to the tool
	* @param Vault $vault The new Vault
	*/
	protected function setVault(Vault $vault)
	{
		$this->tool->setVault($vault);
		return $this;
	}

	/**
	* Function called before the selected action is called.
	* 
	* This function is called every time an action is called. This
	* means it can be used in case you always instance the same classes
	* with a concrete configuration, or to check if a user is logged in,
	* for example.
	* @see  Controller::afterFire()
	*/
	public function beforeFire() {}

	/**
	* Function called after the selected action is called.
	* 
	* This function is called every time an action is called. This
	* means it can be used for storing information in non volatile
	* systems (databases, sessions, files) that happen to be used
	* in every action.
	* @see  Controller::beforeFire()
	*/
	public function afterFire() {}


}
 ?>