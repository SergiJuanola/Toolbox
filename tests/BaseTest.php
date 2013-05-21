<?php
/**
 * Toolbox test
 * @package Toolbox
 * @subpackage Tests
 */

require_once 'class.base.php';

/**
* Unit testing for Base
*
* @package Toolbox
* @subpackage Tests
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link PHPUnit_Framework_TestCase
* @link Base
*/
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