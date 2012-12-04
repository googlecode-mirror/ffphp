<?php
/*
 * 路由解析类
 */
namespace Sys;
class route
{
	private $routeConfig;
	private $pregArr;
	private $matchArr;
	public function __construct($config)
	{
		$this->routeConfig = $config;
		$this->pregArr = array_keys($config);
	}
	
	//获取配置
	public function getConfig()
	{
		if($this->routeUrl())
		{
			return $this->routeConfig();
		}
		else
		{
			return $this->defaultUrl();
		}

	}
	//解析路由地址
	private function routeUrl()
	{
	    if(empty($_SERVER['REDIRECT_URL']))
	    {
	        $_SERVER['REDIRECT_URL'] = null;
	        return false;
	    }
		foreach($this->pregArr as $item)
		{

			if(preg_match('~^'.trim($item,'/').'$~i',trim($_SERVER['REDIRECT_URL'],'/'),$match))
			{
				array_push($match,$item);
				$this->matchArr = $match;
				return true;
			}

		}
	
		return false;
	}
	//路径地址配置
	private function routeConfig()
	{
		$key = array_pop($this->matchArr);
		array_shift($this->matchArr);
		$_config = $this->routeConfig[$key];
		return array(
				'type' => 1,
				'control' => $_config['control'],
				'action' => $_config['action'],
				'args' => $this->matchArr,
				'cache' => isset($_config['cache']) ? intval($_config['cache']) : 0,
				'keys' => isset($_config['keys']) && is_array($_config['keys']) ? $_config['keys'] : null

		);
	}
	//解析默认地址
	private function defaultUrl()
	{
		$urlInfo = array();
		$urlStr = strtok($_SERVER['REDIRECT_URL'],'/');
		if($urlStr!==false)
		{
			array_push($urlInfo,$urlStr);
			while(($urlStr = strtok('/'))!==false)
			{
				$urlStr === '' or
				array_push($urlInfo,$urlStr);
			}
		}
		switch (count($urlInfo))
		{
			case 0 :
				return array(
					'type' => 0,
					'control' => C('defaultControl'),
					'action' => C('defaultAction'),
					'args'=>array(),
					'cache' => 0,
					'keys' => null
				);		
			case 1 :
				return array(
					'type' => 0,
					'control' =>reset($urlInfo),
					'action' => C('defaultAction'),	
					'args'=>array(),
					'cache' => 0,
					'keys' => null
				);
			default :
				return array(
					'type' => 0,
					'control' => array_shift($urlInfo),
					'action' => array_shift($urlInfo),
					'args' => $urlInfo,
					'cache' => 0,
					'keys' => null
				);
		}
	}

}
