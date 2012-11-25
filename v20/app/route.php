<?php
//路由访问处理
return array(
	'404\.htm'=>array(
		'control'=>'demo',
		'action'=>'test',
		'cache'=>3600
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

