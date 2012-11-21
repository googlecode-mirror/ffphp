<?php
// +----------------------------------------------------------------------
// | FFPHP骨干方法库
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 23
// +----------------------------------------------------------------------

class ffphp{
	//url路由
	static function pathUrl(){
		if(isset($_SERVER['PATH_INFO'])){
			//分割系统变量
			$pathInfo = explode('/',trim($_SERVER['PATH_INFO'],'/'));
			//解析并设置控制器
			$_GET['m'] = empty($pathInfo[0])?C('DEFAULTE_MODEL'):$pathInfo[0];
			array_shift($pathInfo);
			//解析并设置动作
			$_GET['a'] = empty($pathInfo[0])?C('DEFAULTE_ACTION'):$pathInfo[0];
			array_shift($pathInfo);
			//把其他参数当值传入
			for($i=0;$i<count($pathInfo);$i+=2){
				isset($pathInfo[$i+1]) or $pathInfo[$i+1]=null;
				$_GET[$pathInfo[$i]]=$pathInfo[$i+1];
			}
		}else{
			//留空时候设置为缺省的值
			$_GET['m'] = (empty($_GET['m'])?C('DEFAULTE_MODEL'):$_GET['m']);
			$_GET['a'] = (empty($_GET['a'])?C('DEFAULTE_ACTION'):$_GET['a']);
		}
	}

}
