<?php
//系统内置模版变量
return array(
	//超全局变量模版赋值
	'get'=>$_GET,
	'post'=>$_GET,
	//项目根路径
	'root'=>_APP_?ROOT_PATH.'/'._APP_:ROOT_PATH,
	//项目入口文件
	'app'=>$_SERVER['SCRIPT_NAME'].'/',
	//当前模块地址
	'url'=>$_SERVER['SCRIPT_NAME'].'/'.$_GET['m'].'/',
);
