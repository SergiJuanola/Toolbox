<?php

require_once 'class.builder.php';

class Dictionary extends Builder {

	public static $default = array(
		'locale' => 'en',
		'orm' => null,
		'transTableName' => 'text',
		'dictionarySuffix' => '_ml'
	);

	public static function build($config = array()) {
		return new self($config);
	}

	public function translate(&$object) {
		$suffixLen = strlen($this->dictionarySuffix);
		foreach ($object as $key => $value) {
			if(strpos($key, $this->dictionarySuffix)===strlen($key)-$suffixLen)
			{
				$row = $this->orm->from($this->transTableName)->selectById($value);
				$text = $row[$this->locale];
				$object[substr($key, 0,-$suffixLen)] = $text;
			}
		}
	}
}