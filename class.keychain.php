<?php 

require_once 'class.builder.php';

class Keychain extends Builder {
	public static $default = array(
		'format' 	=> '^######',
		'salt'		=> '',
		'__sets'	=> array(
							'lower'	=> 'abcdefghijklmnopqrstuvwxyz',
							'upper'	=> 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
							'number'=> '0123456789'
						),
	);

	public static function build($config = array()) {
		return new self($config);
	}

	public function addSet($name, $set)
	{
		$sets = $this->__sets;
		$sets[$name] = $set;
		$this->__sets = $sets;
		return $this;
	}

	public function getFormat()
	{
		return $this->format;
	}
	public function setFormat($thisFormat)
	{
		$this->format = $thisFormat;
		return $this;
	}

	/**
	 *	Symbology:
	 *	#: Number
	 *	_: Lowercase letter
	 *	^: Uppercase letter
	 *	@: Uppercase or Lowercase letter
	 *	%: Number or Uppercase letter
	 *	$: Number, Uppercase or Lowercase letter
	 *	{setname}: Any of the elements in setname
	 **/

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

	public function batchGenerate($amount)
	{
		$keys = array();
		for ($i=0; $i < $amount; $i++) { 
			$keys[] = $this->generate();
		}
		return $keys;
	}

	public function isValid($key)
	{
		$regex = $this->generateRegex();
		return preg_match($regex, $key) == 1;
	}

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