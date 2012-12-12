<?php 

	class Controller {
		 private $__match;
		 private $__toolbox;

		 public function __construct($match, $toolbox)
		 {
		 	$this->__match = $match;
		 	$this->__toolbox = $toolbox;
		 }

		 protected function getMatch()
		 {
		 	return !empty($this->__match)? $this->__match : null;
		 }

		 protected function getToolbox()
		 {
		 	return !empty($this->__toolbox)? $this->__toolbox : null;
		 }
	}
 ?>