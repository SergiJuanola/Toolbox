<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

class Match extends Builder
{
	/**
	* The callback is unknown
	*/
	const CALLER_UNKNOWN = 0;
	/**
	* The callback is a controller specially designed for Match
	* @see Controller
	*/
	const CALLER_CONTROLLER = 1;
	/**
	* The callback is a URI from Match.
	* In case the URI is not wellformed, it will anyway redirect the page there
	*/
	const CALLER_URI = 2;
	/**
	* The callback is an external URL. Match will redirect the page to this URL
	*/
	const CALLER_EXTERNAL = 3;
	/**
	* The callback is an external class defined by the user (or another library)
	*/
	const CALLER_CLASS = 4;

	/**
	* Default properties.
	*/
	public static $default = array(
		'get'=>array(),
		'post'=>array(),
		'put'=>array(),
		'delete'=>array(),
		'error'=>NULL,
		'locales' => array(),
		'localeUris' => array(),
	);

	/**
	* Building method
	* @param array $config The config array
	* @see Builder::build()
	*/
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
				if(!isset($this->localeUris[$uri]))
				{
					$localeUris = $this->localeUris;
					$localeUris[$uri] = $includesLocale;
					$this->localeUris = $localeUris;
				}
			}
			else
			{
				$get[$this->makeItLocale($uri)] = $callback;
				$get[$uri] = $callback;
			}
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
				if(!isset($this->localeUris[$uri]))
				{
					$localeUris = $this->localeUris;
					$localeUris[$uri] = $includesLocale;
					$this->localeUris = $localeUris;
				}
			}
			else
			{
				$post[$this->makeItLocale($uri)] = $callback;
				$post[$uri] = $callback;
			}
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
				if(!isset($this->localeUris[$uri]))
				{
					$localeUris = $this->localeUris;
					$localeUris[$uri] = $includesLocale;
					$this->localeUris = $localeUris;
				}
			}
			else
			{
				$put[$this->makeItLocale($uri)] = $callback;
				$put[$uri] = $callback;
			}
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
				if(!isset($this->localeUris[$uri]))
				{
					$localeUris = $this->localeUris;
					$localeUris[$uri] = $includesLocale;
					$this->localeUris = $localeUris;
				}
			}
			else
			{
				$delete[$this->makeItLocale($uri)] = $callback;
				$delete[$uri] = $callback;
			}
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


	/*
		Home::index > inner controller
		/home/ > reinject
		http://www.google.com > redirect
		#Home::index > external controller
	*/
	private function decideCaller()
	{
		$callback = $this->matched['callback'];
		if(filter_var($callback, FILTER_VALIDATE_URL))
		{
			return Match::CALLER_EXTERNAL;
		}
		if(preg_match("@#[a-zA-Z0-9_]+::[a-zA-Z0-9_]+@", $callback)===1)
		{
			return Match::CALLER_CLASS;
		}
		if(preg_match("@(/[a-zA-Z0-9\+-_{}:]+)+/?@", $callback)===1)
		{
			return Match::CALLER_URI;
		}
		if(preg_match("@[a-zA-Z0-9_]+::[a-zA-Z0-9_]+@", $callback)===1)
		{
			return Match::CALLER_CONTROLLER;
		}
		return Match::CALLER_UNKNOWN;
	}

	public function fire()
	{
		$matchedUri = $this->basePath()->cleanUri()->matchUri();

		if(!empty($matchedUri))
		{
			$this->matched = $matchedUri;
			switch ($this->decideCaller()) {
				case Match::CALLER_CONTROLLER:
					$this->callController();
					break;
				case Match::CALLER_URI:
					$this->locale = (empty($this->matched['params']['__locale']))? $this->getDefaultLocale() : $this->matched['params']['__locale'];
					$this->redirect($this->matched['callback'], $this->locale);
					break;
				case Match::CALLER_EXTERNAL:
					header("Location: ".$this->matched['callback'],TRUE,302);
					break;
				case Match::CALLER_CLASS:
					$callback = str_replace("#", "", $this->matched['callback']);
					$parts = explode("::", $callback);
					$item = new $parts[0];
					$item->{$parts[1]};
					break;
				case Match::CALLER_UNKNOWN:
				default:
					$this->fireCode(404);
					break;
			}
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
		if(empty($this->locales))
			return 0;

		$min = $max = strlen($this->locales[0]);
		foreach ($this->locales as $locale) {
			$thisLocaleLen = strlen($locale);
			if($thisLocaleLen>$max)
				$max = $thisLocaleLen;
			if($thisLocaleLen<$min)
				$min = $thisLocaleLen;
		}
		return "$min,$max";
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

	public function setLocale($locale)
	{
		$this->locale = in_array($locale, $this->locales)? $locale : FALSE;
	}

	public function getLocale()
	{
		if(!empty($this->locale) && in_array($this->locale, $this->locales))
			return $this->locale;
		return $this->getDefaultLocale();
	}

	private function cleanParams($uri, $strict = TRUE)
	{
		$find = array(
			"@{{((\w+):slug)}}@",
			"@{{((\w+):int)}}@",
			"@{{((\w+):locale)}}@",
			"@{{((\w+):string)}}@",
			"@{{((\w+):\w+)}}@",
			"@{{((\w+))}}@",
		);
		$replace = array(
			"(?P<\\2>[a-zA-Z0-9\+\-_]+)",
			"(?P<\\2>\d+)",
			"(?P<\\2>\w{".$this->getLocaleLength()."})",
			"(?P<\\2>\w+)",
			"(?P<\\2>\w+)",
			"(?P<\\2>[a-zA-Z0-9\+\-_]+)",
		);

		if($strict === FALSE) // If strict is false, accept anything from some types
		{
			$replace[0] = "(?P<\\2>.+)"; //slug
		}
		return preg_replace($find, $replace, $uri);
	}

	private function matchUri()
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		foreach ($this->{$method} as $uri => $callback) {
			$oriUri = $uri;
			$uri = $this->cleanParams($uri);
			if (strpos($uri, "/") == (strlen($uri)-1)) {
				$uri = substr($uri, 0, (strlen($uri)-1));
			}
			$result = preg_match("@^".$uri."/?$@", $this->uri, $params);
			if($result === 1)
			{
				foreach($params as $key=>$var){ 
					if(is_numeric($key)) { 
						unset($params[$key]); 
					}
					else {
						$_GET[$key] = $var;
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
			foreach ($uris as $locale => $localeUri) {
				$localeUri = $this->cleanParams($localeUri);
				$result = preg_match("@^".$localeUri."$@", $this->uri, $params);
				if($result == 1)
				{
					$paramFind = array();
					$paramReplace = array();
					foreach ($params as $key => $value) {
						$paramFind[] = "@{{".$key."(:\w+)?}}@";
						$paramReplace[] = $value;
					}
					return preg_replace($paramFind, $paramReplace, $alias);
				}
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
			return $this->basePath.$url;

		foreach ($this->localeUris as $localeUri => $localeUris) {
			$localeUri = $this->cleanParams($localeUri, FALSE);
			$result = preg_match("@^".$localeUri."$@", $url, $params);
			$callback = new MatchCallbacks($this, $params);
			if($result==1)
			{
				if(array_key_exists($locale, $localeUris))
				{
					$localizedUri = $localeUris[$locale];
					$paramFind = array();
					$paramReplace = array();
					foreach ($params as $key => $value) {
						$paramFind[] = "@{{(".$key.")(:(\w+))?}}@";
						$paramReplace[] = $value;
					}
					//return preg_replace($paramFind, $paramReplace, $localizedUri);
					$preparedUri = preg_replace_callback($paramFind, array($callback, "urlPregCallback"), $localizedUri);
					return $this->basePath.$preparedUri;
				}
			}
		}
		return $this->basePath."/".$locale.$url;
	}

	public function redirect($url, $locale = FALSE)
	{
		$newUrl = $this->url($url, $locale);
		header("Location: ".$newUrl,TRUE,302);
	}

	public function slug($str)
	{
		$strnew=strtolower($str);
		$strnew = str_replace( array('à','á','â','ã','ä', 'ç',
				'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö',
				'ù','ú','û','ü', 'ý','ÿ'),
				array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n',
						'o','o','o','o','o', 'u','u','u','u', 'y','y'), $strnew);
		$strnew = preg_replace('~[^\\pL\d]+~u', '-', $strnew);
		$strnew = trim($strnew, '-');
		$strnew = preg_replace('~[^-\w]+~', '', $strnew);
	
		return $strnew;
	}

	public function deslug($str)
	{
		return str_replace("-", " ", $str);
	}

	public function escape($string)
	{
		$replace = array(
			"\x00"  => '\x00',
			"\n"    => '\n',
			"\r"    => '\r',
			'\\'    => '\\\\',
			"'"     => "\'",
			'"'     => '\"',
			"\x1a"  => '\x1a'
		);
		return strtr($string, $replace);
	}

	public function jsonResponse($object)
	{
		header("Content-type: application/json; charset=utf-8");
		echo json_encode($object);
	}

	/**
	 * Based on the script by Jesse Skinner
	 * @see http://www.thefutureoftheweb.com/blog/use-accept-language-header
	 */
	public function getAcceptLanguages($httpAcceptLanguage = NULL)
	{
		$langs = array();

		if(empty($httpAcceptLanguage))
		{
			$httpAcceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}

		if (!empty($httpAcceptLanguage)) {
		    preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $httpAcceptLanguage, $lang_parse);

		    if (count($lang_parse[1])) {
		        $langs = array_combine($lang_parse[1], $lang_parse[4]);
		    	
		        foreach ($langs as $lang => $val) {
		            $langs[$lang] = ($val === '')? 1 : floatval($val);
		        }

		        arsort($langs, SORT_NUMERIC);
		    }
		}
		return $langs;
	}

	public function sortLocales($langs)
	{
		$locales = $this->locales;
		$sortedLocales = array();
		$pos = 1;
		foreach ($locales as $locale)
		{
			$sortedLocales[$locale] = 0.1 / ($pos++);
			foreach ($langs as $lang => $val) {
				if(strpos($lang, $locale) === 0)
				{
					$sortedLocales[$locale] = $val;
					break;
				}
			}
		}
		arsort($sortedLocales, SORT_NUMERIC);

		return $sortedLocales;
	}

	public function getMostRelevantLocale($httpAcceptLanguage = NULL)
	{
		$sortedLocales = $this->sortLocales($this->getAcceptLanguages($httpAcceptLanguage));
		$locales = array_keys($sortedLocales);
		return $locales[0];
	}

	public function isLocalePresentInUri()
	{
		if(empty($this->matched['params']['__locale']))
			return FALSE;

		$uriLocale = $this->matched['params']['__locale'];
		foreach ($this->locales as $locale) {
			if($locale==$uriLocale)
				return TRUE;
		}
		return FALSE;
	}
}

class MatchCallbacks
{
	private $params;
	private $match;

	public function __construct($match, $params)
	{
		$this->match = $match;
		$this->params = $params;
	}

	public function urlPregCallback($matches)
	{
		if($matches[3]=="slug")
			return $this->match->slug($this->params[$matches[1]]);
		else
			return $this->params[$matches[1]];
	}
}