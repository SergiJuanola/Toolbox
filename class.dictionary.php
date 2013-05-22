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
* @link Builder
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
	* @link Builder::build()
	*/
	public static function build($config = array()) {
		$dict = new self($config);
		if(empty($dict->dictionaries) || empty($dict->match) || !$dict->match->hasLocale())
		{
			$dict->canTranslate = FALSE;
		}
		else
		{
			$locale = empty($dict->match->locale)? $dict->match->getDefaultLocale() : $dict->match->locale;
			$dict->locale = $locale;
			$file = $dict->dictionaries.$locale.'.php';
			if(!file_exists($file))
			{
				$dict->canTranslate = FALSE;
			}
			else
			{
				$dict->__dictionary=include($file);
				$dict->canTranslate = TRUE;
			}
		}
		return $dict;
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