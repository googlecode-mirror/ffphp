<?php
/*
 * 二级缓存控制器
 */
namespace Sys;
class cachePro
{
	private $config=array();
	private $moduleReturn=array();
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
		unset($this->moduleReturn);
	}
	private function getModuleString($value)
	{
		$field = explode('.',$value);
		list($module,$action) = $field;
		//获取Module的返回值
		$reutrn = $this->getModuleReturn(trim($module),trim($action));
		//判断是否整个输出
		if(isset($field[2]))
		{
			if(is_string($reutrn))
			{
				errorMsg("[错误标签:<b>{{{{$value}}}}</b>] [调用方法:<b>{$module}Module->{$action}()</b>] 这个模型中的函数返回不是一个数组,无法对其进行字段输出!",E_USER_NOTICE);
			}
			return $reutrn[trim($field[2])];
		}
		else
		{
			if(is_array($reutrn))
			{
				errorMsg("[错误标签:<b>{{{{$value}}}}</b>] [调用方法:<b>{$module}Module->{$action}()</b>] 这个模型中的函数返回一个数组,请尝试字段输出!",E_USER_NOTICE);
			}
			return $reutrn;
		}

	}
	private function getModuleReturn($module,$action)
	{
		//标识符
		$key = $module.'#'.$action;
		if(!isset($this->moduleReturn[$key]))
		{
			if(method_exists(M($module),$action))
			{
				$string = M($module)->$action();
				if($string === null)
				{
					errorMsg("[错误标签:<b>{{{{$module}.{$action}}}}</b>] [调用方法:<b>{$module}Module->{$action}()</b>] 这个模型中的函数没有返回值或返回值为空!",E_USER_NOTICE);
				}
				$this->moduleReturn[$key] = $string;
			}
			else
			{
				errorMsg("[错误标签:<b>{{{{{$module}.{$action}}}}}</b>] [调用方法:<b>{$module}Module->{$action}()</b>] 这个模型中的函数不存在!",E_USER_NOTICE);
				$this->moduleReturn[$key] = false;
			}
		}
		return $this->moduleReturn[$key];
	}
}
