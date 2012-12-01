<?php
//{{{DeBug
function mf()
{
    list ($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}
$start_time = mf();
function unloadSys()
{
	echo '<span style="border:1px #ccc dashed;font-size:14px;padding:5px;">cacheKey:['.Q('cacheKey').'] 
	time:';
	echo mf() - $GLOBALS['start_time'];
	echo '</span>';
}
register_shutdown_function('unloadSys');
//}}}


define('SYS_PATH',dirname(dirname(__FILE__)).'/');

//多项目时这里判断
//if():
	define ('SYS_NAME','app');
	define ('SYS_APP',SYS_PATH.SYS_NAME.'/');
//endif;

require SYS_PATH.'/init.php';
