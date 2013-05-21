<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';
require_once 'vendor/php-markdown-extra/markdown.php';

/**
* Parse a Markdown text to html
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Builder
*/
class Marker extends Builder {
	public static $default = array();

	public static function build($config = array()) {
		$config['__parser'] = new MarkdownExtra_Parser();
		return new self($config);
	}

	public function transform($text, $return = FALSE)
	{
		$marked = $this->__parser->transform($text);
		if($return === TRUE)
			return $marked;
		else
			echo $marked;
	}

	public function show($text)
	{
		$this->transform($text, FALSE);
	}
}