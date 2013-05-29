<?php
/**
 * Toolbox test
 * @package Toolbox
 * @subpackage Tests
 */

require_once 'class.builder.php';

/**
* Unit testing for Builder
*
* @package Toolbox
* @subpackage Tests
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @see  PHPUnit_Framework_TestCase
* @see  Builder
*/

class BuilderTest extends PHPUnit_Framework_TestCase {
	
	public function testBuild()
	{
		$this->assertClassHasAttribute('_config', 'Builder');
	}
}