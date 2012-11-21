<?php
/*
 * 二级缓存控制器
 * Author tongseo@gmail.com
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
				trigger_error("[<b>{$module}Module->{$action}()</b>] This function has no return value or the return value is null");
			}
			return $string;
		}
		else
		{
			trigger_error("[<b>{$module}Module->{$action}()</b>] This Function Does Not Exist");
			return false;
		}
	}
}
