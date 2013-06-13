<?php
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

/**
* Hold translations for a website
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @see  Builder
*/
class Dictionary extends Builder {

	/**
	* Default properties.
	* @param Match $match The Match class used for getting the correct language
	* @param string $dictionaries The path to the dictionaries
	* @param boolean $canTranslate TRUE if everything is set, and the selected dictionary exists, FALSE otherwise. Defaults to FALSE
	* @param array $__dictionary Array containing the translation to the selected $locale
	* @param string $locale The locale selected, according to the Match
	*/
	public static $default = array(
		'match'=>NULL,
		'dictionaries'=>NULL,
		'canTranslate'=>FALSE,
		'__dictionary' => NULL,
		'locale'=>NULL,
	);

	/**
	* Building method
	* @param array $config The config array
	* @see  Builder::build()
	*/
	public static function build($config = array()) {
		$dict = new self($config);
		$dict->prepare();
		return $dict;
	}

	private function prepare()
	{
		if(empty($this->dictionaries) || empty($this->match) || !$this->match->hasLocale())
		{
			$this->canTranslate = FALSE;
		}
		else
		{
			$locale = empty($this->match->locale)? $this->match->getDefaultLocale() : $this->match->locale;
			$this->locale = $locale;
			$file = $this->dictionaries.$locale.'.php';
			if(!file_exists($file))
			{
				$this->canTranslate = FALSE;
			}
			else
			{
				$this->__dictionary=include($file);
				$this->canTranslate = TRUE;
			}
		}
	}

	/**
	* Saves an instance of Match
	* @param Match $match The desired $match
	* @return Brush An instance of itself
	* @see  Match, Brush::getMatch($match)
	*/
	public function setMatch($match)
	{
		$this->match = $match;
		$this->prepare();
		return $this;
	}

	/**
	* Retrieves the saved Match instance
	* @return Match the stored $match
	* @see  Match, Brush::setMatch($match)
	*/
	public function getMatch()
	{
		return $this->match;
	}

	/**
	* Get the current locale set by Match
	* @return string The current locale
	*/
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	* Translate the text to the selected locale
	* @param string $sentence The sentence to be translated
	* @return string The translated sentence, or itself if not present
	*/
	public function t($sentence)
	{
		if($this->canTranslate === TRUE && array_key_exists($sentence, $this->__dictionary))
			return $this->__dictionary[$sentence];
		return $sentence;
	}
}