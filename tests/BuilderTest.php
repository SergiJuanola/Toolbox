<?php

require_once 'class.builder.php';

class BuilderTest extends PHPUnit_Framework_TestCase {
	
	public function testBuild()
	{
		$this->assertClassHasAttribute('_config', 'Builder');
	}
}