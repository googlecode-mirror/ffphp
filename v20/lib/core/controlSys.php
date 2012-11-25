<?php
/*
 * 域控制器基类
 */

class controlSys extends viewSys
{
	static $modules;
	public static function getInstance($control)
	{
		if(empty(static::$modules))
		{
			$_Instance = new static;
			static::$modules = &M();
		}
		return $_Instance;
	}
	final public function jump($url,$msg,$time=3,$status=0)
	{
		$this->assign('url',$url);
		$this->assign('msg',$msg);
		$this->assign('status',$status);
		$this->assign('time',$time);
		$this->display('jump.html',0);
	}
	final public function success($msg,$url='',$time=3)
	{
		$this->jump($url,$msg,$time,1);
	}
	final public function error($msg,$url='',$time=3)
	{
		$this->jump($url,$msg,$time,2);
	}
}
