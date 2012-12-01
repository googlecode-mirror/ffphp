<?php
/*
 * 函数库
 */
//配置函数
function C($key=null,$value=null)
{
	static $_config;
	is_array($_config) or 
		$_config = array();
	if(is_string($key))
	{
		if(is_null($value))
			return $_config[$key];
		else
			$_config[$key]=$value;
	}
	else if(is_array($key))
	{
		$_config = array_merge($_config,$key);
		return true;
	}
	else if(is_null($key))
	{
		return $_config;		
	}
	else
	{
		return false;
	}
}

//数据库操作
function D($tableName=null,$dbName=null)
{
	//空参数返回SQL语句
	if(is_null($tableName))
	{
		return \Sys\pdo::$sql;
	}
	//一个参数返回虚拟表
	elseif(is_null($dbName))
	{
		return \Sys\pdoView::getInstance($tableName);
	}
	//两个参数返回数表模型
	else
	{
		return \Sys\pdo::getInstance(C('prefix').$tableName,$dbName);
	}
}

//请求参数
function Q($key=null)
{
	static $_config;
	is_array($_config) or 
		$_config = array();
	if(is_string($key))
		return $_config[$key];
	else if(is_array($key))
		$_config = array_merge($_config,$key);
	else if(is_null($key))
		return $_config;		
	else
		return false;
}


//模型加载
function &M($name=null)
{
	static $module;
	empty($module) and $module = array();

	if(empty($name))
	{
		return $module;
	}
	if(!(isset($module[$name]) && is_object($module[$name])))
	{
		$class = ucfirst(strtolower($name)).'Module';
		include SYS_APP.'module/'.$class.'.php';
		$module[$name] = new $class;
	}

	return $module[$name];
}

// 异常抛出方法
//
// $msg string 错误信息
// $level int 错误级别 [E_USER_NOTICE E_USER_WARNING E_USER_ERROR]
// $type int 错误类型 [1=>sql语句]
// $add string 错误附加信息
function errorMsg($msg,$level=E_USER_NOTICE,$type=0,$add=null)
{
	//如果没有开启DEBUG
	if(!DEBUG)
	{
		return;
	}
	//开启DEBUG 目前采用系统异常抛出
	else
	{
		switch ($type)
		{
			case 0:
				break;
			case 1:
				echo 'SQL:',end(D());
				break;
			default:
				echo $add;
		}
		trigger_error($msg,$level);
	}

}
