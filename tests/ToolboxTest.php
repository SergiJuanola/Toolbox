<?php
/**
 * Toolbox test
 * @package Toolbox
 * @subpackage Tests
 */

require_once 'class.toolbox.php';

/**
* Unit testing for Toolbox
*
* @package Toolbox
* @subpackage Tests
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link PHPUnit_Framework_TestCase
* @link Toolbox
*/
class ToolboxTest extends PHPUnit_Framework_TestCase {
	
	public function testDefault()
	{
		$app = Toolbox::build();
		$this->assertEquals(array(), $app->getDefault('Base'));
		$app = Toolbox::build(array('base'=>array('var1' => 'val1')));
		$this->assertEquals(array(), $app->getDefault('Base'));
		$app->need(array('base'=>array('var1' => 'val1')));
		$this->assertEquals(array('var1'=>'val1'), $app->getDefault('Base'));
		$app->need(array('base'=>array('var2' => 'val2')));
		$this->assertEquals(array('var2' => 'val2'), $app->getDefault('Base'));
	}

	/**
	 * @depends testDefault
	 */
	public function testSingleton()
	{
		$this->assertSame(Toolbox::build(), Toolbox::build());
	}
}