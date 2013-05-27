<?php
/**
 * Tool for Toolbox
 * @package Toolbox
 */
require_once 'class.builder.php';

/**
* Improved controller helper. Controllers should use this class as much as possible, as this solves
* setting some variables by default, such as Brush or Vault.
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Match
*/
class Controllertool extends Builder {
	/**
	* Default properties.
	* @param Brush $brush The Brush instance calling this controller
	* @param Cookie $cookie The Cookie instance calling this controller
	* @param Dictionary $dictionary The Dictionary instance calling this controller
	* @param Match $match The Match instance calling this controller
	* @param Pdo $pdo The Pdo instance coming from Pdotool
	* @param Session $session The Session instance calling this controller
	* @param Toolbox $toolbox The Toolbox instance, if present
	* @param Vault $vault The Vault instance calling this controller
	*/
	public static $default = array(
		'brush' => NULL,
		'cookie' => NULL,
		'dictionary' => NULL,
		'match' => NULL,
		'pdo' => NULL,
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
	* @return Brush The Brush if set, or a default instance otherwise
	*/
	public function getBrush()
	{
		if(empty($this->brush))
			$this->brush = Brush::build();
		return $this->brush;
	}

	/**
	* Sets the Brush to the tool
	* @param Brush $brush The new Brush
	*/
	public function setBrush(Brush $brush)
	{
		$this->brush = $brush;
		return $this;
	}

	/**
	* Gets the current Cookie class if correctly set.
	* @return Cookie The Cookie if set, or a default instance otherwise
	*/
	public function getCookie()
	{
		if(empty($this->cookie))
			$this->cookie = Cookie::build();
		return $this->cookie;
	}

	/**
	* Sets the Cookie to the tool
	* @param Cookie $cookie The new Cookie
	*/
	public function setCookie(Cookie $cookie)
	{
		$this->cookie = $cookie;
		return $this;
	}

	/**
	* Gets the current Dictionary class if correctly set.
	* @return Dictionary The Dictionary if set, or a default instance otherwise
	*/
	public function getDictionary()
	{
		if(empty($this->dictionary))
			$this->dictionary = Dictionary::build();
		return $this->dictionary;
	}

	/**
	* Sets the Dictionary to the tool
	* @param Dictionary $dictionary The new Dictionary
	*/
	public function setDictionary(Dictionary $dictionary)
	{
		$this->dictionary = $dictionary;
		return $this;
	}

	/**
	* Gets the current Match class if correctly set.
	* @return Match The Match if set, or a default instance otherwise
	*/
	public function getMatch()
	{
		if(empty($this->match))
			$this->match = Match::build();
		return $this->match;
	}

	/**
	* Sets the Match to the tool
	* @param Match $match The new Match
	*/
	public function setMatch(Match $match)
	{
		$this->match = $match;
		return $this;
	}

	/**
	* Gets the current Pdo class if correctly set.
	* @return Pdo The Pdo if set, or a default instance otherwise
	*/
	public function getPdo()
	{
		if(empty($this->pdo))
			$this->pdo = Pdotool::build();
		return $this->pdo;
	}

	/**
	* Sets the Pdo to the tool
	* @param Pdo $session The new Pdo
	*/
	public function setPdo(Pdo $pdo)
	{
		$this->pdo = $pdo;
		return $this;
	}

	/**
	* Gets the current Session class if correctly set.
	* @return Session The Session if set, or a default instance otherwise
	*/
	public function getSession()
	{
		if(empty($this->session))
			$this->session = Session::build();
		return $this->session;
	}

	/**
	* Sets the Session to the tool
	* @param Session $session The new Session
	*/
	public function setSession(Session $session)
	{
		$this->session = $session;
		return $this;
	}

	/**
	* Gets the Toolbox instance, if present.
	* @return Toolbox The Toolbox instance if present, NULL otherwise
	*/
	public function getToolbox()
	{
		if(empty($this->toolbox))
			$this->toolbox = Toolbox::build();
		return $this->toolbox;
	}

	/**
	* Sets the Toolbox to the tool
	* @param Toolbox $toolbox The new Toolbox
	*/
	public function setToolbox(Toolbox $toolbox)
	{
		$this->toolbox = $toolbox;
		return $this;
	}

	/**
	* Gets the current Vault class if correctly set.
	* @return Vault The Vault if set, or a default instance otherwise
	*/
	public function getVault()
	{
		if(empty($this->vault))
			$this->vault = Vault::build();
		return $this->vault;
	}

	/**
	* Sets the Vault to the tool
	* @param Vault $vault The new Vault
	*/
	public function setVault(Vault $vault)
	{
		$this->vault = $vault;
		return $this;
	}
}