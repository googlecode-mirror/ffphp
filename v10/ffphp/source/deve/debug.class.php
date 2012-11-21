<?php
// +----------------------------------------------------------------------
// | Debug输出【开发模式】
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 9
// +----------------------------------------------------------------------

class debug{
	//单例模式，禁止实例
	private function __construct(){
	
	}
	static $files=array('fphp/source/common/default.inc.php','fphp/source/common/ffphp.fun.php');
	static $action=array();
	static $info=array();
	static $sql=array();
	static $startTime;
	static $errorMsg='';
	static $showed=false;

	static $systemMsg='<center><h2>FFPHP System Error</h2></center>';
	static $systemCount=0;
	
	static function error($msg,$level){

		self::$errorMsg.='<div style="float:left;margin:0 40px;width:100%;color:red"><b>';
		if($level>200000)
			self::$errorMsg.='错误';
		else
			self::$errorMsg.='警告';
		self::$errorMsg.='</b>:'.$msg.'</div>';
		self::show(true);
		self::$showed=true;
	}

	static function throwError($msg){
		self::$systemMsg.=$msg;
		self::$systemCount++;
	}

	static function show($exit=false){
		//将系统错误临时记录
		file_put_contents(_ROOT_.'runtime/log/error.htm',self::$systemMsg);
		self::$systemCount and $systemError='<li style="cursor: pointer;font-size:14px;color:red" onclick=\'window.open("'.ROOT_PATH.'runtime/log/error.htm", "height=520, width=850, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, status=no");\'>有<b>'.self::$systemCount.'</b>个系统错误点击查看!</li>';
		//运行时间
		$runTime=round(microtime()-self::$startTime,5);
		//控制器信息
		$actionInfo='域控制器:'.$_GET['m'].',方法:'.$_GET['a'].',文件:'._APP_.'/action/'.$_GET['m'].'.class.php';
		//环境信息
		if(C('CONNECT_TYPE')==1)
			$runInfo='数据库使用mysqli驱动!';
		elseif(C('CONNECT_TYPE')==2)
			$runInfo='数据库使用PDO驱动!';
		if(C('TEMPLATE_SMARTY'))
			$runInfo.='视图使用Smarty模板引擎!';
		else
			$runInfo.='视图使用内置模板引擎!';
		//sql
		if(self::$sql)
			$sqlInfo='本页共进行'.count(self::$sql).'次SQL查询!';
		else
			$sqlInfo='没有使用SQL查询!';
		//lastSQL
		self::$sql and $lastSQL='<div style="font-size:12px;width:360px;float:left;margin:10px 0 0 40px"><span>[SQL]</span><ul style="margin-top:5px">最后一次SQL查询语句:<br />'.array_pop(self::$sql).'</ul></div>';
		//加载文件
		$fileStr='<li>'.implode('</li><li>',self::$files).'</li>';
		$errorMsg=self::$errorMsg;
		if(self::$info['templaceFile'])
			$templaceFile='使用模板:'.self::$info['templaceFile'];
		else
			$templaceFile='还没有使用任何模板!';

		echo <<<str
<div style="width:100%;border:#000 dotted 1px; float:left;background:#E7E4E4;color:#666">
<div style="width:100%;float:left">
<span style="margin:10px 0 0 30px;float:left;font-weight:bold;font-size:18px">DEVE控制台<sub>v1.0</sub></span>
<span style="margin:14px 20px;float:left;font-size:14px">[FFPHP开发控制台,让错误更加友好!]</span>

<span style="margin:10px 18px;float:right;font-size:14px;cursor: pointer;" onclick="this.parentNode.parentNode.style.display='none'">关闭</span>
</div>
{$errorMsg}
<div style="font-size:12px;width:400px;float:left;margin:10px 0 0 40px">
<span>[运行信息]</span>
<ul style="margin-top:5px">
{$systemError}
<li>系统用时:{$runTime}秒!开发模式,时间较长</li>
<li>{$actionInfo}</li>
<li>{$runInfo}</li>
<li>{$templaceFile}</li>
<li>{$sqlInfo}</li>
</ul>
</div>
{$lastSQL}
<div style="font-size:12px;width:360px;float:right;margin:10px 0 0 40px">
<span>[包含文件]</span>
<ul style="margin-top:5px">
$fileStr
</ul>
</div>
</div>
str;
		$exit and exit;

	}

}
