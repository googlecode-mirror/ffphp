<?php
/*
 * 域控制器基类
 */
class DemoControl extends control
{
	function init()
	{
	//	echo 111;
	}
	function index()
	{
		//$url = array(
		//	array(),
		//	array(),
		//);
		//$this->urlcheck();
		$date = $this->getParam('qpage:int','id','page');
		var_dump($date,Q());
		$this->display('list2.html',0,2);
	}

	function arr()
	{

		$arr = range(0,100);
		$this->assign('arr',$arr);
		$this->display('arr.html',8);
	}

	function module()
	{

		$indexModule = M('Indexlist');

		self::$modules['xx']=22222;
			
		M('Indexlist');
		M('Indexlist');

		var_dump(M());
		$indexModule -> getListBy(2222);
	}
	function test()
	{
		$this->display('cachePro.html',4);
	}

	function eerror()
	{
		$this->success('成功');
	}

}
