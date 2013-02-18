<?php

require_once 'class.toolbox.php';

class ToolboxTest extends PHPUnit_Framework_TestCase {
	
	public function testDefault()
	{
		$app = Toolbox::build();
		$this->assertEquals(array(), $app->getDefault('Base'));
		$app = Toolbox::build(array('base'=>array('var1' => 'val1')));
		$this->assertEquals(array(), $app->getDefault('Base'));
		$app->need(array('base'=>array('var1' => 'val1')));
		$this->assertEquals(array('var1'=>'val1'), $app->getDefault('Base'));
	}

	/**
	 * @depends testDefault
	 */
	public function testSingleton()
	{
		$this->assertSame(Toolbox::build(), Toolbox::build());
	}
}