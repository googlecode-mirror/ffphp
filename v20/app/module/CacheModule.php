<?php
/*
 * 域控制器基类
 */
class cacheModule
{
	function echotime()
	{
		$db = D('caimeng','db3');
		$res = $db->find('sss');
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
		
	}
}
