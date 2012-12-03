<?php
//开启DEBUG
define ('DEBUG',true);
date_default_timezone_set('PRC');
header('content-type:text/html;charset=utf-8');

DEBUG or error_reporting(0);

define ('SYS_LIB',SYS_PATH.'lib/');
define ('SYS_INC',SYS_PATH.'inc/');
define ('SYS_CLASS',SYS_PATH.'class/');

//加载核心文件
require SYS_INC.'function.php';
require SYS_LIB.'core/routeSys.php';
require SYS_LIB.'core/cacheSys.php';
require SYS_LIB.'core/memcacheSys.php';
//加载配置文件
C(require SYS_INC.'config.php');

//路由缓存联合控制器
$SysCache = new \Sys\cache(new \Sys\route(require SYS_APP.'route.php'));

//记录解析后的数据
Q($SysCache->getConfig());

//设置类的自动加载[可优化到Apache配置文件中]
$SysIncludePath=array(
	'./',
	SYS_CLASS,
);
set_include_path(join(PATH_SEPARATOR,$SysIncludePath));
function __autoload($className)
{
	include strtolower($className).'.php';
}

//载入Dao层
include SYS_LIB.'core/dbSys.php';

//载入二级缓存类
include SYS_LIB.'core/cacheProSys.php';

//如果是二级缓存则解析输出
if($SysCache->cachePro)
{
	$SysCachePro = new \Sys\cachePro($SysCache->cachePro);
	//二更缓存在些更新输出
	DEBUG or exit(strtr($SysCache->cacheContent,$SysCachePro->getConfig()));

	///{{{DEBUG
	echo strtr($SysCache->cacheContent,$SysCachePro->getConfig()),
	'<span style="border:1px #ccc dashed;font-size:14px;padding:5px;"><font color="red">CachePro Data</font> cacheKey:['.Q('cacheKey').'] time:',
	mf() - $GLOBALS['start_time'],
	'</span>';
	exit;
	///}}}
}

unset($SysCache);

//没有缓存和二级缓存 进入下面的控制器构建
$_control = ucfirst(strtolower(Q('control'))).'Control';
$_control_file = SYS_APP.'control/'.$_control.'.php';
$_action = Q('action');
include SYS_LIB.'smarty/Smarty.class.php';
include SYS_LIB.'core/viewSys.php';
include SYS_LIB.'core/controlSys.php';
class_alias('\Sys\control','control');

//定义模版中用到的变量
define('V_DOMAIN','http://'.$_SERVER['SERVER_NAME']);
define('V_APP',V_DOMAIN.'/');
//资源域名在这里修改
define('V_PUBLIC',V_DOMAIN.'/public/');

//架构控制器
if(file_exists($_control_file))
{
	include SYS_APP.'control/'.$_control.'.php';
	$_obj = $_control::getInstance($_control);
	if(is_callable(array($_obj,$_action)))
	{
		$_obj -> $_action();
	}
	else
	{
		unset($_obj);
	}
}
if(!isset($_obj))
{
		include SYS_APP.'control/ErrorControl.php';	
		$_obj = ErrorControl::getInstance('ErrorControl');
		$_obj -> Error404();
}
