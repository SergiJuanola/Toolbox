<?php 

require_once 'class.builder.php';

class Match extends Builder
{
	public static $default = array(
		'get'=>array(),
		'post'=>array(),
		'put'=>array(),
		'delete'=>array(),
		'error'=>NULL,
		'locales' => array(),
		'localeUris' => array(),
	);

	public static function build($config = array()) {
		return new self($config);
	}

	private function makeItLocale($uri)
	{
		return "/{{__locale:locale}}".$uri;
	}

	private function includesLocale(&$uri, $locale)
	{
		$uri = "/".$locale.$uri;
		$find = array(
			"@^/(\w{".$this->getLocaleLength()."})/@",
		);
		$replace = array(
			"/(?P<__locale>\\1)/",
		);
		return preg_replace($find, $replace, $uri);
	}

	public function get($uri, $callback, $includesLocale = FALSE)
	{
		$get = $this->get;
		if($this->hasLocale())
		{
			if($includesLocale)
			{
				foreach ($includesLocale as $locale => &$localeUri) {
					$get[$this->includesLocale($localeUri, $locale)] = $callback;
				}
				$localeUris = $this->localeUris;
				$localeUris[$uri] = $includesLocale;
				$this->localeUris = $localeUris;
			}
			else
				$get[$this->makeItLocale($uri)] = $callback;
		}
		else
			$get[$uri] = $callback;
		$this->get = $get;
		return $this;
	}

	public function post($uri, $callback, $includesLocale = FALSE)
	{
		$post = $this->post;
		if($this->hasLocale())
		{
			if($includesLocale)
			{
				foreach ($includesLocale as $locale => $localeUri) {
					$post[$this->includesLocale($localeUri, $locale)] = $callback;
				}
				$localeUris = $this->localeUris;
				$localeUris[$uri] = $includesLocale;
				$this->localeUris = $localeUris;
			}
			else
				$post[$this->makeItLocale($uri)] = $callback;
		}
		else
			$post[$uri] = $callback;
		$this->post = $post;
		return $this;
	}

	public function put($uri, $callback, $includesLocale = FALSE)
	{
		$put = $this->put;
		if($this->hasLocale())
		{
			if($includesLocale)
			{
				foreach ($includesLocale as $locale => $localeUri) {
					$put[$this->includesLocale($localeUri, $locale)] = $callback;
				}
				$localeUris = $this->localeUris;
				$localeUris[$uri] = $includesLocale;
				$this->localeUris = $localeUris;
			}
			else
				$put[$this->makeItLocale($uri)] = $callback;
		}
		else
			$put[$uri] = $callback;
		$this->put = $put;
		return $this;
	}

	public function delete($uri, $callback, $includesLocale = FALSE)
	{
		$delete = $this->delete;
		if($this->hasLocale())
		{
			if($includesLocale)
			{
				foreach ($includesLocale as $locale => $localeUri) {
					$delete[$this->includesLocale($localeUri, $locale)] = $callback;
				}
				$localeUris = $this->localeUris;
				$localeUris[$uri] = $includesLocale;
				$this->localeUris = $localeUris;
			}
			else
				$delete[$this->makeItLocale($uri)] = $callback;
		}
		else
			$delete[$uri] = $callback;
		$this->delete = $delete;
		return $this;
	}

	public function matchAny($uri, $callback, $includesLocale = FALSE)
	{
		$this
		->get($uri, $callback, $includesLocale)
		->post($uri, $callback, $includesLocale)
		->put($uri, $callback, $includesLocale)
		->delete($uri, $callback, $includesLocale);
		return $this;
	}

	public function setErrorCallback($callback)
	{
		$this->error = $callback;
		return $this;
	}

	public function fire()
	{
		$matchedUri = $this->basePath()->cleanUri()->matchUri();
		if(!empty($matchedUri))
		{
			$this->matched = $matchedUri;
			$this->callController();
		}
		else
		{
			$this->matched = array(
									'method' => 'unknown',
									'uri' => $this->uri,
									'alias' => $this->getUriAlias(),
									'rule' => NULL,
									'regex' => NULL,
									'callback' => NULL,
									'params'=> array());
			$this->fireCode(404);
		}
		return $this;
	}

	public function hasLocale()
	{
		return !empty($this->locales);
	}

	public function setDefaultLocale($locale)
	{
		$locales = $this->locales;
		if(($key = array_search($locale, $locales)) !== false) {
			unset($locales[$key]);
		}
		array_unshift($locales, $locale);
		$this->locales = $locales;
	}

	public function getDefaultLocale()
	{
		return empty($this->locales)? NULL : $this->locales[0];
	}

	public function getLocaleLength()
	{
		return empty($this->locales)? 0 : strlen($this->locales[0]);
	}

	private function callController()
	{
		$callback = $this->matched['callback'];
		$parts = explode("::", $callback);
		if(count($parts)==2)
		{
			require_once($this->matchbox.'controller.'.strtolower($parts[0]).'.php');
			$parts[0] = $parts[0].'Controller';
			$reflector = new ReflectionMethod($parts[0], $parts[1]);
			$params = array();
			foreach ($reflector->getParameters() as $param) {
			    $params[] = $this->matched['params'][$param->name];
			}
			$controller = new $parts[0]($this, $this->hasToolbox()? Toolbox::build() : null);
			if($this->hasLocale())
			{
				if(isset($this->matched['params']['__locale']))
				{
					$this->locale = $this->matched['params']['__locale'];
					if(!in_array($this->locale, $this->locales))
						$this->locale = $this->defaultLocale;
				}
				else
				{
					$this->locale = $this->getDefaultLocale();
				}
			}
			$controller->beforeFire();
			call_user_func_array(array($controller, $parts[1]), $params);
			$controller->afterFire();
		}
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

	public function getLocale()
	{
		if(!empty($this->locale) && in_array($this->locale, $this->locales))
			return $this->locale;
		return $this->getDefaultLocale();
	}

	private function cleanParams($uri)
	{
		$find = array(
			"@{{((\w+):int)}}@",
			"@{{((\w+):locale)}}@",
			"@{{((\w+):slug)}}@",
			"@{{((\w+):string)}}@",
			"@{{((\w+):\w+)}}@",
			"@{{((\w+))}}@",
		);
		$replace = array(
			"(?P<\\2>\d+)",
			"(?P<\\2>\w{".$this->getLocaleLength()."})",
			"(?P<\\2>[a-zA-Z0-9\+-_]+)",
			"(?P<\\2>\w+)",
			"(?P<\\2>\w+)",
			"(?P<\\2>[a-zA-Z0-9\+-_]+)",
		);
		return preg_replace($find, $replace, $uri);
	}

	private function matchUri()
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		foreach ($this->{$method} as $uri => $callback) {
			$oriUri = $uri;
			$uri = $this->cleanParams($uri);
			$result = preg_match("@^".$uri."$@", $this->uri, $params);
			if($result === 1)
			{
				foreach($params as $key=>$var){ 
					if(is_numeric($key)){ 
						unset($params[$key]); 
					} 
				} 
				return array(
								'method' => $method,
								'uri' => $this->uri,
								'alias' => $this->getUriAlias(),
								'rule' => $oriUri,
								'regex' => $uri,
								'callback' => $callback,
								'params'=> $params);
			}
		}
		return null;
	}

	private function getStatusCodeMessage($status)
	{
		$codes = Array(
		    100 => 'Continue',
		    101 => 'Switching Protocols',
		    200 => 'OK',
		    201 => 'Created',
		    202 => 'Accepted',
		    203 => 'Non-Authoritative Information',
		    204 => 'No Content',
		    205 => 'Reset Content',
		    206 => 'Partial Content',
		    300 => 'Multiple Choices',
		    301 => 'Moved Permanently',
		    302 => 'Found',
		    303 => 'See Other',
		    304 => 'Not Modified',
		    305 => 'Use Proxy',
		    306 => '(Unused)',
		    307 => 'Temporary Redirect',
		    400 => 'Bad Request',
		    401 => 'Unauthorized',
		    402 => 'Payment Required',
		    403 => 'Forbidden',
		    404 => 'Not Found',
		    405 => 'Method Not Allowed',
		    406 => 'Not Acceptable',
		    407 => 'Proxy Authentication Required',
		    408 => 'Request Timeout',
		    409 => 'Conflict',
		    410 => 'Gone',
		    411 => 'Length Required',
		    412 => 'Precondition Failed',
		    413 => 'Request Entity Too Large',
		    414 => 'Request-URI Too Long',
		    415 => 'Unsupported Media Type',
		    416 => 'Requested Range Not Satisfiable',
		    417 => 'Expectation Failed',
		    500 => 'Internal Server Error',
		    501 => 'Not Implemented',
		    502 => 'Bad Gateway',
		    503 => 'Service Unavailable',
		    504 => 'Gateway Timeout',
		    505 => 'HTTP Version Not Supported'
		);
		return (isset($codes[$status])) ? $codes[$status] : '';
	}

	public function fireCode($status = 200, $notCatch = FALSE)
	{
		$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->getStatusCodeMessage($status);
		header($status_header);
		if($notCatch === FALSE && !empty($this->error))
		{
			$matched = $this->matched;
			$this->matched = array(
									'method' => $matched['method'],
									'uri' => $this->uri,
									'alias' => $this->getUriAlias(),
									'rule' => '/error',
									'regex' => '/error',
									'callback' => $this->error,
									'params'=> array('code'=>$status, 'origin'=>$matched));
			$this->callController();
			die();
		}
	}

	private function getUriAlias()
	{
		if(!$this->hasLocale())
			return $this->uri;

		$localeUris = $this->localeUris;
		foreach ($localeUris as $alias => $uris) {
			if (in_array($this->uri, $uris)) {
				return $alias;
			}
		}
		return substr($this->uri, 3);
	}

	public function url($url, $locale = FALSE)
	{
		if(empty($url))
			$url = "/";
		if($locale === FALSE)
		{
			$locale = $this->getLocale();
		}
		if(!in_array($locale, $this->locales))
			return $url;

		foreach ($this->localeUris as $localeUri => $localeUris) {
			$localeUri = $this->cleanParams($localeUri);
			$result = preg_match("@^".$localeUri."$@", $url, $params);
			if($result==1)
			{
				if(array_key_exists($locale, $localeUris))
				{
					$localizedUri = $localeUris[$locale];
					$paramFind = array();
					$paramReplace = array();
					foreach ($params as $key => $value) {
						$paramFind[] = "@{{".$key."(:\w+)?}}@";
						$paramReplace[] = $value;
					}
					return preg_replace($paramFind, $paramReplace, $localizedUri);
				}
			}
		}
		return "/".$locale.$url;
	}
}