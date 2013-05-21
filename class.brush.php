<?php
/**
 * Tool for Toolbox
 * @package Toolbox
 */
require_once 'class.builder.php';

/**
* A template rendering class for Toolbox
*
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @see Builder
*/
class Brush extends Builder {

	/**
	* Default properties.
	* @param Match $match The desired $match
	* @param string $views The folder where the views are stored. End if with '/'
	* @param string $layout The layout being used. If set to NULL, no layout is used
	*/
	public static $default = array(
		'match'=>NULL,
		'layout'=>NULL,
	);

	/**
	* Building method
	* @param array $config The config array
	* @return Brush An instance of itself
	* @see Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}

	/**
	* Saves an instance of Match
	* @param Match $match The desired $match
	* @return Brush An instance of itself
	* @see Match, Brush::getMatch($match)
	*/
	public function setMatch($match)
	{
		$this->match = $match;
		return $this;
	}

	/**
	* Retrieves the saved Match instance
	* @return Match the stored $match
	* @see Match, Brush::setMatch($match)
	*/
	public function getMatch()
	{
		return $this->match;
	}
	
	/**
	* Localises an url using Match
	* 
	* If Brush contains a Match instance, it uses the Match::url() function
	* to localise the url. In case it isn't set, it just returns the same url
	* @param string $url The url we want to translate
	* @param mixed $locale The locale we want to use. If not set (or FALSE), the current $locale from template is used. Defaults to FALSE
	* @return string the localised url
	* @see Match::url(), Brush::setMatch()
	*/
	public function url($url, $locale = FALSE)
	{
		if(empty($this->match))
			return $url;
		else
			return $this->match->url($url, $locale);
	}
	
	/**
	* Localises the current url using Match
	* 
	* If Brush has a $match, it gets the current url and localises to the selected locale.
	* @param string $locale The locale we want to use
	* @return string the localised url
	* @see Match::url(), Brush::setMatch()
	*/
	public function getCurrentUrlLocalized($locale)
	{
		if(empty($this->match))
			return FALSE;
		return $this->url($this->match->matched['alias'], $locale);
	}
	
	/**
	* Renders a page, given the selected layout
	* @param string $view The view you want to render
	* @param array $params The list of parameters being passed to the view and layout
	* @param boolean $partial TRUE if the view doesn't need layout, FALSE otherwise. Defaults to FALSE
	* @param boolean $return TRUE if the view needs to be returned, FALSE if it needs to be echoed. Defaults to FALSE
	* @return mixed The rendered view if $return is TRUE, or returns TRUE otherwise
	*/
	public function render($view, $params = array(), $partial = FALSE, $return = FALSE)
	{
		foreach ( $params as $key => $value )
		{
			$$key = $value;
		}

		ob_start();
		include $this->views.$view;
		$content = ob_get_clean();

		if($partial === FALSE && !empty($this->layout))
		{
			ob_start();
			include $this->views.$this->layout;
			$content = ob_get_clean();
		}

		if($return === TRUE)
			return $content;

		echo $content;
		return TRUE;
	}
	
	/**
	* Alias of render()
	* @param string $view The view you want to render
	* @param array $params The list of parameters being passed to the view and layout
	* @param boolean $partial TRUE if the view doesn't need layout, FALSE otherwise. Defaults to FALSE
	* @param boolean $return TRUE if the view needs to be returned, FALSE if it needs to be echoed. Defaults to FALSE
	* @return mixed The rendered view if $return is TRUE, or returns TRUE otherwise
	* @see Brush::render()
	*/
	public function paint($view, $params = array(), $partial = FALSE, $return = FALSE)
	{
		return $this->render($view, $params, $partial, $return);
	}
}