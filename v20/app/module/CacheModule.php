<?php
/*
 * 域控制器基类
 */
class cacheModule
{
	function echotime()
	{
		$db = D('caimeng','db3');
		$res = $db->find();
		var_dump(D());
		//$v = D('ddd');
		//$v -> where('id = 1');
		//$v -> order('qq');
		//var_dump($v -> select());
		//var_dump(D(),$v);
		return date('Y-m-d H:i:s',time()).'<hr>'.$res['qq'];
	}
	function index()
	{
		var_dump($_GET);
		$data['ss']=2222;
		$data['qqq']=2222;
		return 	$data;
	}
	function index2()
	{
		$data['dd']=01000;
		return 	$data;
	}
	function index0()
	{
		return 'string return';
	}
}
