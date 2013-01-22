<?php

require_once 'class.builder.php';

class Brush extends Builder {
	public static $default = array(
		'match'=>NULL,
		'layout'=>NULL,
	);

	public static function build($config = array()) {
		return new self($config);
	}

	public function setMatch($match)
	{
		$this->match = $match;
		return $this;
	}

	public function getMatch($match)
	{
		return $this->match;
	}

	public function url($url, $locale = FALSE)
	{
		if(empty($this->match))
			return $url;
		else
			return $this->match->url($url, $locale);
	}

	public function render($view, $params = array(), $partial = FALSE, $return = FALSE)
	{
		foreach ( $params as $key => $value )
		{
			$$key = $value;
		}

		ob_start();
		include $this->views.$view;
		$content = ob_get_clean();

		if($partial === FALSE && !empty($this->layout))
		{
			ob_start();
			include $this->views.$this->layout;
			$content = ob_get_clean();
		}

		if($return === TRUE)
			return $content;

		echo $content;
		return TRUE;
	}

	public function paint($view, $params = array(), $partial = FALSE, $return = FALSE)
	{
		return $this->render($view, $params, $partial, $return);
	}
}