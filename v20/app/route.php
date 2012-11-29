<?php
//路由访问处理
return array(
	'404\.htm'=>array(
		'control'=>'error',
		'action'=>'e404',
		'cache'=>0
	),
	'demo\.php'=>array(
		'control'=>'demo',
		'action'=>'test',
		'cache'=>6,
	),
	'asdasd/afafasdc'=>array(
	),
	'dd/(.*)/(.*)'=>array(
		'control'=>'demo',
		'action'=>'test',
	),
	'demo\.php'=>array(
		'control'=>'demo',
		'action'=>'test',
		'cache'=>5,
	),
	'bbs/d-(\d*)\.htm'=>array(
		'control'=>'index',
		'action'=>'htm',
		'cache'=>10,
	//	'keys'=>array($_GET['d']),		
	),

);

