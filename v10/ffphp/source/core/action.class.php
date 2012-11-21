<?php
// +----------------------------------------------------------------------
// | 按制器基类，用来初始化控制器和对接视图
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 5
// +----------------------------------------------------------------------

class action extends view{
	function __construct(){
		parent::__construct();
		if(DEVE){
			debug::$action['m'] = $_GET['m'];
			debug::$action['a'] = $_GET['a'];
		}
	}

	//-----------------------------------|
	// 空操作提示
	// 默认404错误
	//-----------------------------------|
	function __call($fun,$arg){
		//如果方法是动作方法就执行空方法
		if($fun==$_GET['a'])
			$this->_empty($fun);
		//如果方法是动作方法用到的普通方法则报错
		else
			debug::error('调用不存在的成员方法:'.$fun.'();',205001);
	}
	function _empty($fun){
		
		if(file_exists(_APP_.'template/'.C('TEMPLATE_STYLE').'/'.$_GET['m'].'/'.$_GET['a'].'.'.C('TEMPLATE_SUFFIX')))
			$this->display();
		else
			debug::error('没有找到该操作！',20202);
	}

		// ----------------------------------|
	//success
	//成功提示方法 
	// （提示信息，转向地址，等待时间）
	//-----------------------------------|
	protected function success($msg='操作成功!',$url='',$time=3){
		$this->jump($msg,$url,$time,1);
	}

	//-----------------------------------|
	// error
	//失败提示方法
	// （提示信息，转向地址，等待时间）
	// ----------------------------------|
	protected function error($msg='操作失败!',$url='',$time=3){
		$this->jump($msg,$url,$time,2);
	}

	//-----------------------------------|
	//go
	//转向函数
	// （提示信息，转向地址，等待时间）
	// ----------------------------------|
	protected function go($msg='正在转向中...',$url='',$time=3){
		$this->jump($msg,$url,$time,0);		
	}

	private function jump($msg,$url,$time,$status){

		$this->template_dir=_FFPHP_.'extend';
		$url=trim($url,'/');
		empty($url) or strstr($url,'/') or $url=$_GET['m'].'/'.$url;
		$this->assign('time',$time);		
		$this->assign('msg',$msg);
		$this->assign('status',$status);
		$this->assign('location',$url);	
		$this->display('template/error');	
		exit;
	}
}
