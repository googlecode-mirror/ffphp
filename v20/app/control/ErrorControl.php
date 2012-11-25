<?php
/*
 * 访问错误控制器
 */
class ErrorControl extends controlSys
{

	function Error404()
	{
		header('HTTP/1.1 404 Not Found');
		var_dump(Q());
		echo '<center><h2>Error 404</h2></center>';
	}
	
}
