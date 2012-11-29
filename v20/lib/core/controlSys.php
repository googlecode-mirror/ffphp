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
			if(is_callable(array($_Instance,'init')))
			{
				$_Instance->init();
			}
			static::$modules = &M();
		}
		return $_Instance;
	}
	//跳转方法
	final public function jump($url,$msg,$time=3,$status=0)
	{
		$this->assign('url',$url);
		$this->assign('msg',$msg);
		$this->assign('status',$status);
		$this->assign('time',$time);
		$this->display('jump.html',0);
	}
	//成功跳转方法
	final public function success($msg,$url='',$time=3)
	{
		$this->jump($url,$msg,$time,1);
	}
	//失败跳转方法
	final public function error($msg,$url='',$time=3)
	{
		$this->jump($url,$msg,$time,2);
	}
	//404跳转方法
	final public function error404()
	{
		header('HTTP/1.1 404 Not Found');
		header('Location:/404.htm');
	}
	//取参方法
	final public function getParam()
	{
		$return_value = array();
		$key = func_get_args();
		$val = Q('args');
		foreach($key as $item)
		{
			$v = array_shift($val);
			if(is_null($v))
			{
				$return_value[$item] = NULL;
				break;
			}
			if($num = strpos($item,':'))
			{
				$param = substr($item,0,$num);
				$type  = substr($item,$num+1);
				//变量不合法则转向
				$this->checkType($v,$type) or $this->error404();
				$item = $param;
			}
			//还有多余参数则返回404
			$val and $this->error404();
			$return_value[$item] = $v;
		}
		return $return_value;
	}
	
	//检测字符真实类型
	final public function checkType($str,$type)
	{
			switch ($type)
			{
				//检测int
				case 'int':
					return $str==(string)(int)$str ? true : false;
				//字符串直接返回true
				case 'str':
				case 'string':
					return true;
				//其他情况返回false
				default :
					return false;
			}
	}
}
