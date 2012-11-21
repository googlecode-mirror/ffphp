<?php
//检测并定义项目路径
if(defined('APP')){
	if(ltrim(APP,'./'))
		define('_APP_',rtrim(ltrim(APP,'./'),'/').'/');
	else
		define('_APP_','');
}else{
	define('_APP_','');
}
//缺省定义FFPHP
defined('FFPHP') or define('FFPHP','./ffphp/'); 
//格式化定义框架文件包
if(strncmp(FFPHP,'./',2))
	define('_FFPHP_',trim(FFPHP,'/').'/');
else
	define('_FFPHP_',rtrim(substr(FFPHP,2),'/').'/');
//定义网站根路径
define('_ROOT_',dirname(_FFPHP_).'/');
//定义缓存文件唯一标识符
define('_TMP_',trim(str_replace('/','_',rtrim($_SERVER['SCRIPT_NAME'],'.php')),'_'));

//加载核心函数库
require _FFPHP_.'source/common/ffphp.fun.php';

//定义网站根路径
defineRootDir(_FFPHP_,dirname($_SERVER['SCRIPT_NAME']));

//加载系统缺省配置文件
C(require _FFPHP_.'source/common/default.inc.php');
//加载用户配置文件
$config_file=_ROOT_.'config.php';
file_exists($config_file) and C(include ($config_file));
//定义输出格式
header("Content-Type:text/html;charset=utf-8"); 
//选择性载入内核
if(C('DEVE'))
	require _FFPHP_.'source/deve/deve.php';
else
	require _FFPHP_.'source/core/apprun.php';


