<?php
/**
 * Tool for Toolbox
 * @package Toolbox
 */
require_once 'class.builder.php';

class Dice extends Builder {

	public static $default = array(
		'step' => 1
	);

	public static function build($config = array()) {
		return new self($config);
	}

	public function setMin($min) {
		$this->min = $min;
		return $this;
	}
	public function setMax($max) {
		$this->max = $max;
		return $this;
	}

	public function setRange($min, $max) {
		$this->min = min($min, $max);
		$this->max = max($min, $max);
		return $this;
	}

	public function setStep($step) {
		$this->step = $step;
		return $this;
	}

	public function roll() {
		$value = 0;
		if($this->step != 1)
		{
			$max = ($this->max-$this->min)/$this->step;
			$value = $this->min+$this->step*mt_rand(0, $max);
		}
		else
		{
			$value = mt_rand($this->min, $this->max);
		}
		return $value;
	}
}
