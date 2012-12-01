<?php
/*
 * 访问错误控制器
 */
class ErrorControl extends control
{
	function E404()
	{
		header('HTTP/1.1 404 Not Found');
		echo '<center><h2>Error 404</h2></center>';
		//输出自己的404模版
		//$this->display('404.html');
	}
}
