<?php
// +----------------------------------------------------------------------
// | 开发模式内核【开发模式】
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 22
// +----------------------------------------------------------------------

//开发模式[DEVE]
define('DEVE',true);
require _FFPHP_.'source/deve/deve.class.php';
require _FFPHP_.'source/deve/debug.class.php';
//记录系统开始时间
debug::$startTime=microtime();
//设置默认错误抛出
set_error_handler('errorHandler',E_ALL);

//检测并创建目录
deve::construct();


//检测是否开启Smarty模板引擎
if(C('TEMPLATE_SMARTY'))
	deve::load(_FFPHP_.'source/drive/smarty.class.php');
else
	deve::load(_FFPHP_.'source/core/view.class.php');

//加载核心文件
deve::load(array(
	_FFPHP_.'source/core/ffphp.class.php',
	_FFPHP_.'source/core/compile.class.php',
	_FFPHP_.'source/core/action.class.php',	
));

//检测数据连接类型
if(C('CONNECT_TYPE')==1)
	deve::load(_FFPHP_.'source/drive/mysqli.class.php');
else
	deve::load(_FFPHP_.'source/drive/pdo.class.php');
//URL路由
ffphp::pathUrl();

//类的自动加载
function __autoload($className){
	deve::load(_APP_.'behavior/'.$className.'.class.php');
}
//控制器的格式化创建
$actionfile=_APP_.'action/'.strtolower($_GET['m']).'.class.php';
if(file_exists($actionfile)){
	deve::buildAction($_GET['m']);
	$newAction=strtolower($_GET['m']).'Action';
	deve::load(_APP_.'core/action/'.$newAction.'.class.php');
	$action=new $newAction();
	$action->$_GET['a']();	
}else{
	debug::error('控制器不存在!',202102);
}

if(C('DEBUG'))
       	debug::$showed or debug::show();
