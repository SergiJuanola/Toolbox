<?php

require_once 'class.base.php';

class BaseTest extends PHPUnit_Framework_TestCase {
	
	public function testBuild()
	{
		$this->assertClassHasAttribute('_config', 'Base');
		$this->assertClassHasStaticAttribute('default', 'Base');

		$item = Base::build();
		$this->assertEquals('Base', get_class($item));
		$this->assertInstanceOf('Builder', $item);
	}

}