<?php
/*
 * 二级缓存控制器
 */
class cacheProSys
{
	private $config=array();
	public function __construct($match)
	{
		$this->parseConfig($match);
	}
	public function getConfig()
	{
		return $this->config;
	}	
	private function parseConfig($match)
	{
		foreach($match[1] as $key => $value)
		{
			$this->config[$match[0][$key]] = $this->getModuleString($value);	
		}

	}
	private function getModuleString($value)
	{
		list($module,$action) = explode('.',$value);

		if(method_exists(M($module),$action))
		{
			$string = M($module)->$action();
			if($string === null)
			{
				errorMsg("[<b>{$module}Module->{$action}()</b>] 这个模型中的函数没有返回值或返回值为空!",E_USER_NOTICE);
			}
			return $string;
		}
		else
		{
			errorMsg("[<b>{$module}Module->{$action}()</b>] 这个模型中的函数不存在!",E_USER_NOTICE);
			return false;
		}
	}
}
