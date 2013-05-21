<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

/**
* Alphanum code generator
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Builder
*/
class Keychain extends Builder {

	/**
	* Default properties.
	* @param string $format The current format for the code generation
	* @param string $salt The salt used to generate codes
	* @param array $__sets Array that stores all the sets used to generate codes.
	*/
	public static $default = array(
		'format' 	=> '^######',
		'salt'		=> '',
		'__sets'	=> array(
							'lower'	=> 'abcdefghijklmnopqrstuvwxyz',
							'upper'	=> 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
							'number'=> '0123456789'
						),
	);

	/**
	* Building method
	* @param array $config The config array
	* @return Keychain An instance of itself
	* @link Builder::build()
	*/
	public static function build($config = array()) {
		return new self($config);
	}

	/**
	 * Adds a set for code generation
	 * @param string $name The set's name
	 * @param string $set The set's available characters. They should be concatenated in a string.
	 * @return Keychain An instance of itself
	 */
	public function addSet($name, $set)
	{
		$sets = $this->__sets;
		$sets[$name] = $set;
		$this->__sets = $sets;
		return $this;
	}

	/**
	 * Gets the current format for this instance
	 * @return string $format The current format
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * Sets another format for this instance
	 * @param string $thisFormat The new format
	 * @return Keychain An instance of itself
	 */
	public function setFormat($thisFormat)
	{
		$this->format = $thisFormat;
		return $this;
	}

	/**
	 * Generates a new alphanumeric code, according to the current format
	 *
	 *	Symbology:
	 *	#: Number
	 *	_: Lowercase letter
	 *	^: Uppercase letter
	 *	@: Uppercase or Lowercase letter
	 *	%: Number or Uppercase letter
	 *	$: Number, Uppercase or Lowercase letter
	 *	{setname}: Any of the elements in setname
	 * @return A new code
	 * @link Keychain::batchGenerate($amount)
	 */
	public function generate()
	{
		$key = "";
		$format = $this->format;
		for ($i=0; $i < strlen($format); $i++) { 
			switch ($format[$i]) {
				case '#':
					$set = $this->__sets['number'];
					break;
				case '_':
					$set = $this->__sets['lower'];
					break;
				case '^':
					$set = $this->__sets['upper'];
					break;
				case '@':
					$set = $this->__sets['upper'].$this->__sets['lower'];
					break;
				case '%':
					$set = $this->__sets['upper'].$this->__sets['number'];
					break;
				case '$':
					$set = $this->__sets['upper'].$this->__sets['lower'].$this->__sets['number'];
					break;
				case '{':
					$next = strpos($format, "}", $i);
					if($next === FALSE)
						return FALSE;
					$set = $this->__sets[substr($format, $i+1, $next-$i-1)];
					$i = $next;
					break;
				default:
					$set = $format[$i];
					break;
			}
			$key .= $set[mt_rand(0, strlen($set)-1)];
		}
		return $key;
	}

	/**
	 * Generate an amount of alphanumeric codes
	 * @param integer $amount 
	 * @return array The array of generated codes
	 * @link Keychain::generate()
	 */
	public function batchGenerate($amount)
	{
		$keys = array();
		for ($i=0; $i < $amount; $i++) { 
			$keys[] = $this->generate();
		}
		return $keys;
	}

	/**
	 * Checks if a code can be generated with the current format
	 * @param string $key The key to be checked
	 * @return boolean TRUE if $key can be generated, FALSE otherwise
	 * @link Keychain::generateRegex()
	 */
	public function isValid($key)
	{
		$regex = $this->generateRegex();
		return preg_match($regex, $key) == 1;
	}

	/**
	 * Generates a regex for the current format, so it can be checked with isValid
	 * @return string The generated regex
	 * @link Keychain::isValid()
	 */
	private function generateRegex()
	{
		$regex = "/^";
		$format = $this->format;
		for ($i=0; $i < strlen($format); $i++) { 
			switch ($format[$i]) {
				case '#':
					$set = $this->__sets['number'];
					break;
				case '_':
					$set = $this->__sets['lower'];
					break;
				case '^':
					$set = $this->__sets['upper'];
					break;
				case '@':
					$set = $this->__sets['upper'].$this->__sets['lower'];
					break;
				case '%':
					$set = $this->__sets['upper'].$this->__sets['number'];
					break;
				case '$':
					$set = $this->__sets['upper'].$this->__sets['lower'].$this->__sets['number'];
					break;
				case '{':
					$next = strpos($format, "}", $i);
					if($next === FALSE)
						return FALSE;
					$set = $this->__sets[substr($format, $i+1, $next-$i-1)];
					$i = $next;
					break;
				default:
					$set = $format[$i];
					break;
			}
			$regex .= "[".preg_quote($set)."]";
		}
		$regex .= '$/';
		return $regex;
	}
}