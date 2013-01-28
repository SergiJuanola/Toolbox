<?php
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

class Dictionary extends Builder {

	public static $default = array(
		'match'=>NULL,
		'dictionaries'=>NULL,
		'canTranslate'=>FALSE,
		'__dictionary' => NULL,
		'locale'=>NULL,
	);

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

	public function getLocale()
	{
		return $this->locale;
	}

	public function t($sentence)
	{
		if($this->canTranslate === TRUE && array_key_exists($sentence, $this->__dictionary))
			return $this->__dictionary[$sentence];
		return $sentence;
	}
}