<?php
namespace MrMe\Web;

use MrMe\Web\Controller as Controller;
use MrMe\Util\StringFunc as StringFunc;

class Router
{
	private $_CONFIG;
	private $mapping;
	private $controller_;
	private $function_;
	private $params_;

	public function __construct($_CONFIG = NULL)
	{
		if ($_CONFIG == null) return null;

		$this->_CONFIG = $_CONFIG;
	}

	public function __destruct()
	{
		unset($this->_CONFIG);
		unset($this->mapping);
	}

	public function setMapping($mapping = null)
	{
		$this->mapping = $mapping;
	}
	// x/k/{F}

	public function route($key, Controller $controller)
	{
		$offset = null;
		if (!empty($this->_CONFIG['URL']['M_OFFSET']))
			$offset = $this->_CONFIG['URL']['M_OFFSET'];

		$url = $this->parseUrl($offset);
		$urlStr = implode($url, '/');
		//var_dump($urlStr);
		$urlStrSub = substr($urlStr, 0, strpos($urlStr, '?'));
		if ($urlStrSub)
			$urlStr = $urlStrSub;

		$url = explode('/', $urlStr);
		//preg_match('^.*.\/{', $urlStr, $matches, PREG_OFFSET_CAPTURE, 0);
		//print_r($matches);

		$keys = explode('/', $key);

		$index = array_search('{F}', $keys);
		
		$func = empty($url[$index]) ? "index" : $url[$index];
		
		$prefix = array_slice($keys, 0, $index);
		$prefix = implode($prefix, '/');
		
		$params = array();
		$urlParams = array_slice($url, $index + 1);
		$urlParams = $this->getParamFromUrl($urlParams);
	
		foreach ($urlParams as $u)
		 	$params = array_merge($params, $u);
		// //var_dump($params);
		
		//echo "XXX";

		if (StringFunc::startWith($urlStr, $prefix) && 
			empty($this->controller) && empty($this->func_))
		{
			//echo $urlStr . " vs " .$prefix . "<br>\r\n"; 
			//var_dump($controller);
			if (method_exists($controller, $func))
			{
				$this->controller_ = $controller;
				$this->controller_->setConfig($this->_CONFIG);
				$this->controller_->setParams($params);
				$this->func_       = $func;
			}
		}
	}

	public function start()
	{
		if (!empty($this->controller_) && !empty($this->func_) && 
			method_exists($this->controller_, $this->func_))
		{
			call_user_func_array(array($this->controller_, $this->func_), array());
		}
		else
		{
			echo "Request destination doesn't exist <!";
		}
	}

	private function getParamFromUrl($url)
	{
		$head = true;
		$params = array();
		foreach ($url as $i => $u)
		{
			if ($head)
			{
				$key = $url[$i];
				$val = "";
				if (!empty($url[$i+1]))
					$val = $url[$i+1];

				array_push($params, array($key => $val));
				$head = false;
				continue;
			}
			$head = true;
		}
		return $params;
	}

	private function parseUrl($offset = 0)
	{
		// $offset = empty($this->_CONFIG['URL']['S_OFFSET']) ? $offset : $this->CONFIG['URL']['S_OFFSET'];
		$url = 0;

		$url = explode('/', filter_var(rtrim($_SERVER['REQUEST_URI'], '/'), FILTER_SANITIZE_URL));
		
		foreach ($url as $i => $u) 
		{
			if (empty($u)) 
			{
				unset($url[$i]); 
			}
		}
		
		$url = array_slice($url, $offset); 
		if (!empty($this->CONFIG['COMMON']['DEBUG']))
			var_dump($url);
		return $url;
	}
}
?>