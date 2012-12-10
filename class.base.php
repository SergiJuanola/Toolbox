<?php 

require_once 'class.builder.php';

class Base extends Builder {
	public static $default = array();

	public static function build($config = array()) {
		return new self($config);
	}
}