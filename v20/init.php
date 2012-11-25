<?php
//开启DEBUG
define ('DEBUG',true);
date_default_timezone_set('PRC');
header('content-type:text/html;charset=utf-8');

DEBUG or error_reporting(0);

define ('SYS_LIB',SYS_PATH.'lib/');
define ('SYS_INC',SYS_PATH.'inc/');
define ('SYS_CLASS',SYS_PATH.'class/');

//多项目时这里判断
	define ('SYS_NAME','app');
	define ('SYS_APP',SYS_PATH.SYS_NAME.'/');

//加载核心文件
require SYS_INC.'function.php';
require SYS_LIB.'core/routeSys.php';
require SYS_LIB.'core/cacheSys.php';
require SYS_LIB.'core/memcacheSys.php';
//加载配置文件
C(require SYS_INC.'config.php');
//路径解析
//$SysRoute = new routeSys(require SYS_APP.'route.php');
//$SysCache = new cacheSys($SysRoute->getConfig());
$SysCache = new cacheSys(new routeSys(require SYS_APP.'route.php'));

//记录解析后的数据
Q($SysCache->getConfig());

//设置类的自动加载
$SysIncludePath=array(
	'./',
	SYS_CLASS,
);
set_include_path(join(PATH_SEPARATOR,$SysIncludePath));
function __autoload($className)
{
	include strtolower($className).'.php';
}


//载入数据库类
include SYS_LIB.'core/dbSys.php';

//载入二级缓存类
include SYS_LIB.'core/cacheProSys.php';


if($SysCache->cachePro)
{
	$SysCachePro = new cacheProSys($SysCache->cachePro);
	//二更缓存在些更新输出
	DEBUG or exit(strtr($SysCache->cacheContent,$SysCachePro->getConfig()));

	echo strtr($SysCache->cacheContent,$SysCachePro->getConfig()),
	'<span style="border:1px #ccc dashed;font-size:14px;padding:5px;"><font color="red">CachePro Data</font> cacheKey:['.Q('cacheKey').'] time:',
	mf() - $GLOBALS['start_time'],
	'</span>';
	exit;
}

unset($SysCache);

//没有缓存和二级缓存 进入下面的控制器构建
$_control = ucfirst(strtolower(Q('control'))).'Control';
$_control_file = SYS_APP.'control/'.$_control.'.php';
$_action = Q('action');
include SYS_LIB.'smarty/Smarty.class.php';
include SYS_LIB.'core/viewSys.php';
include SYS_LIB.'core/controlSys.php';

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


//{{{DeBug
function unloadSys()
{
	echo '<span style="border:1px #ccc dashed;font-size:14px;padding:5px;">cacheKey:['.Q('cacheKey').'] 
	time:';
	echo mf() - $GLOBALS['start_time'];
	echo '</span>';
}
DEBUG and register_shutdown_function('unloadSys');
//}}}
