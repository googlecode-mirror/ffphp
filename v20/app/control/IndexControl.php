<?php
/*
 * 域控制器基类
 */
class IndexControl extends controlSys
{

	function index()
	{
		echo '<h2>CachePro</h2>';
		$this->display('list1.html');
	}
	private function c()
	{
		var_dump(dirname($_SERVER['SCRIPT_FILENAME']));
	}
	function d()
	{
		Q('cacheTime',10);
		$this->display('list2.html');
	}
	function htm()
	{
		var_dump(Q());
		$this->display('list1.html');
	}
	function html()
	{
		$this->displayHtml('list1.html','demo.html');
	}

}
