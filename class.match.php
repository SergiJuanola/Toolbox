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

	public function matchAny($uri, $callback)
	{
		$any = $this->any;
		$any[$uri] = $callback;
		$this->any = $any;
		return $this;
	}

	public function fire()
	{
		$matchedUri = $this->basePath()->cleanUri()->matchUri();
		if(!empty($matchedUri))
			$this->matched = $matchedUri;
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

	private static function cleanParams($uri)
	{
		$find = array(
			"@{((\w+):int)}@",
			"@{((\w+):string)}@",
			"@{((\w+):\w+)}@",
			"@{((\w+))}@",
		);
		$replace = array(
			"(?<\${2}>\d+)",
			"(?<\${2}>\w+)",
			"(?<\${2}>[a-zA-Z0-9\+]+)",
			"(?<\${2}>[a-zA-Z0-9\+]+)",
		);
		return preg_replace($find, $replace, $uri);
	}

	private function matchUri()
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		foreach ($this->{$method} as $uri => $callback) {
			$oriUri = $uri;
			$uri = Match::cleanParams($uri);
			$result = preg_match("@^".$uri."$@", $this->uri, $params);
			if($result === 1)
				foreach($params as $key=>$var){ 
					if(is_numeric($key)){ 
						unset($params[$key]); 
					} 
				} 
				return array(
								'method' => $method,
								'rule' => $uri,
								'uri' => $oriUri,
								'callback' => $callback,
								'params'=> $params);
		}
		foreach ($this->any as $uri => $callback) {
			$oriUri = $uri;
			$uri = Match::cleanParams($uri);
			$result = preg_match("@^".$uri."$@", $this->uri, $params);
			if($result === 1)
				foreach($params as $key=>$var){ 
					if(is_numeric($key)){ 
						unset($params[$key]); 
					} 
				} 
				return array(
								'method' => $method,
								'rule' => $uri,
								'uri' => $oriUri,
								'callback' => $callback,
								'params'=> $params);
		}
		return null;
	}
}