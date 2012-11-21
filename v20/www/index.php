<?php
function mf()
{
    list ($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}
$start_time = mf();

define('SYS_PATH',dirname(dirname(__FILE__)).'/');
require SYS_PATH.'/init.php';
