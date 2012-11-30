<?php
//配置文件
return array(
	'defaultControl'=>'index',
	'defaultAction'=>'index',
	'tempPath'=>SYS_PATH.'tmp/',

	'memcache'=>array(
		array('127.0.0.1', 11211),	
	),
	'database'=>array(
		'db1'=>array('mysql:host=localhost;dbname=b','root','123456'),
		'db2'=>array('mysql:host=localhost;dbname=ffbbs','root','123456'),
		'db3'=>array('mysql:host=localhost;dbname=hucai','root','123456'),
	),
	'prefix'=>'',

	'mongodb'=>'',
);
