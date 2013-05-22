<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';
require_once 'vendor/WideImage/WideImage.php';

/**
* Wrapper for Wideimage
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Builder
*/
class Wideimagetool extends Builder {
	/**
	* Default properties.
	*/
	public static $default = array();

	/**
	* Building method. If $image is defined in the $config, it will automatically call Wideimagetool::load() and return the same kind of item it would return.
	* @param array $config The config array
	* @return mixed WideImage_Image WideImage_PaletteImage or WideImage_TrueColorImage instance if $image was set, or itself otherwise.
	* @link Builder::build()
	* @link Wideimagetool::load()
	*/
	public static function build($config = array()) {
		$tool = new self($config);
		if(!empty($config['image']))
			return WideImage::load($config['image']);
		else
			return $tool;
	}

	/**
	* Load an image into WideImage, and return a processable item
	* @param mixed $image File name, url, HTML file input field name, binary string, or a GD image resource
	* @return WideImage_Image WideImage_PaletteImage or WideImage_TrueColorImage instance
	* @link WideImage::load()
	*/
	public function load($image)
	{
		return WideImage::load($image);
	}
}