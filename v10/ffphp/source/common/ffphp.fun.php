<?php
// +----------------------------------------------------------------------
// | FFPHP公用函数库
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 14
// +----------------------------------------------------------------------

function C($name=null,$value=null){
	static $config=array();
	if(is_array($name)){
		$name=array_change_key_case($name,CASE_UPPER);
		$config=array_merge($config,$name);
	}elseif(is_string($name)){
		$name=strtoupper($name);		
		if(is_null($value))
			return $config[$name];
		else
			$config[$name]=$value;
	}elseif(is_null($name)){
		return $config;
	}else{
		return false;
	}
}
function D($tableName){
	return new db(C('DB_FIX').$tableName);
}
//快速实例高级模型函数
function M($modelName){
	if(C('CONNECT_TYPE')==1)
		ff_include(_FFPHP_.'source/drive/mysqlimodel.class.php');
	else
		ff_include(_FFPHP_.'source/drive/pdomodel.class.php');

	return new dbmodel($modelName);

}
//效率高的自定义加载函数 include_once();
function ff_include($file){
	static $includeFile=array();
	if(!in_array($file,$includeFile)){
		include $file;	
		array_push($includeFile,$file);
	}
}

function extend($Name){
	if(strstr($Name,'class/')){
		include(_FFPHP_.'extend/'.$Name.'.class.php');
	}elseif(strstr($Name,'function/')){
		include(_FFPHP_.'extend/'.$Name.'.fun.php');
	}else{
		include(_FFPHP_.'extend/'.$Name);
	}
}




//定义网站根路径，定义为绝对路径
function defineRootDir($dir1,$dir2){
$dir3=substr($dir1,0,3);
if($dir3=='../'){
	$dir1=substr($dir1,3);
	$dir2=dirname($dir2);
	defineRootDir($dir1,$dir2);
}else{
	define('ROOT_PATH',$dir2.'/');
}

}


//获取客户端IP地址

function get_ip(){
	return $_SERVER['REMOTE_ADDR'];
}


//获取上级来源URL

function last_url(){
	return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
}
//判断是否是POST
function is_post(){
	return ($_SERVER['REQUEST_METHOD']=='POST')?true:false;
}

//判断是否是AJAX
function is_ajax(){
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and !strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'],'XMLHttpRequest'))?true:false;
}

//开发模式下的错误抛出
function errorHandler($errno, $errstr, $errfile, $errline){ 
	debug::throwError("<b>Custom error:</b> [$errno] $errstr Error on line<b> $errline </b>in $errfile<br /><hr />");
 }
