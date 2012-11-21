<?php
/*
 * 函数库
 * Author tongseo@gmail.com 
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
		return pdoSys::$sql;
	}
	//一个参数返回虚拟表
	elseif(is_null($dbName))
	{
		return pdoViewSys::getIntance($tableName);
	}
	//两个参数返回数表模型
	else
	{
		return pdoSys::getIntance(C('prefix').$tableName,$dbName);
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
