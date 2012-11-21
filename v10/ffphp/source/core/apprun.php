<?php
// +----------------------------------------------------------------------
// | 网站动行模式下的内核文件
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 20
// +----------------------------------------------------------------------

//开发模式[DEVE]
define('DEVE',false);
include _FFPHP_.'source/core/debug.class.php';

//开启错误日志，将错误报告写入到日志中
ini_set('display_errors', 'Off');
ini_set('log_errors', 'On'); 
ini_set('error_log', _ROOT_.'runtime/log/apprun.log');


//检测是否开启Smarty模板引擎
if(C('TEMPLATE_SMARTY'))
	require _FFPHP_.'source/drive/smarty.class.php';
else
	require _FFPHP_.'source/core/view.class.php';

//加载核心文件
require _FFPHP_.'source/core/ffphp.class.php';
require _FFPHP_.'source/core/compile.class.php';
require _FFPHP_.'source/core/action.class.php';

//检测数据连接类型
if(C('CONNECT_TYPE')==1)
	require _FFPHP_.'source/drive/mysqli.class.php';
else
	require _FFPHP_.'source/drive/pdo.class.php';

//URL路由
ffphp::pathUrl();

//类的自动加载
function __autoload($className){
	require _APP_.'behavior/'.$className.'.class.php';
}

//控制器的格式化创建
$thisAction=strtolower($_GET['m']).'Action';
$actionfile=_APP_.'core/action/'.$thisAction.'.class.php';

if(file_exists($actionfile)){
	require $actionfile;
	$action=new $thisAction();
	$action->$_GET['a']();
}else{
	debug::error('控制器不存在!',202102);
}
