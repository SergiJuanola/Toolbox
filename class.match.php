<?php 

require_once 'class.builder.php';

class Match extends Builder
{
	public static $default = array(
		'get'=>array(),
		'post'=>array(),
		'put'=>array(),
		'delete'=>array(),
		'any'=>array(),
	);

	public static function build($config = array()) {
		return new self($config);
	}

	public function get($rule, $callback)
	{
		$get = $this->get;
		$get[$rule] = $callback;
		$this->get = $get;
		return $this;
	}

	public function post($uri, $callback)
	{
		$post = $this->post;
		$post[$uri] = $callback;
		$this->post = $post;
		return $this;
	}

	public function put($uri, $callback)
	{
		$put = $this->put;
		$put[$uri] = $callback;
		$this->put = $put;
		return $this;
	}

	public function delete($uri, $callback)
	{
		$delete = $this->delete;
		$delete[$uri] = $callback;
		$this->delete = $delete;
		return $this;
	}

	public function match($uri, $callback)
	{
		$any = $this->any;
		$any[$uri] = $callback;
		$this->any = $any;
		return $this;
	}

	public function fire()
	{
		$matchedUri = $this->basePath()->cleanUri()->matchUri();
		var_dump($matchedUri);
		return $this;
	}

	private function cleanUri()
	{
		$uri = str_replace($this->basePath, "", $_SERVER['REQUEST_URI']);
		$this->uri = $uri;
		return $this;
	}

	private function basePath()
	{
		$params = explode("/", $_SERVER['SCRIPT_NAME']);
		array_pop($params);
		$this->basePath = implode("/", $params);
		return $this;
	}

	private function matchUri()
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		foreach ($this->{$method} as $uri => $callback) {
			$result = preg_match("@^".preg_quote($uri)."$@", $this->uri);
			if($result === 1)
				return array(
								'method' => $method,
								'uri' => $uri,
								'callback' => $callback);
		}
		foreach ($this->any as $uri => $callback) {
			$result = preg_match("@^".preg_quote($uri)."$@", $this->uri);
			if($result === 1)
				return array(
								'method' => $method,
								'uri' => $uri,
								'callback' => $callback);
		}
		return null;
	}
}