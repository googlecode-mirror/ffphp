<?php
function mf()
{
    list ($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}
$start_time = mf();


define('SYS_PATH',dirname(dirname(__FILE__)).'/');
require SYS_PATH.'/init.php';

//多项目时这里判断
//if():
	define ('SYS_NAME','app');
	define ('SYS_APP',SYS_PATH.SYS_NAME.'/');
//endif;
