<?php
return array(
'db' => 'db3',
'fields'=> array( 
	//调用各个表中的字段来构建自己的虚拟表 
	'@caihui.id'=>'id',
	'@caihui.sex'=>'sex',
	'@caihui.qq'=>'qq',
	'@caimeng.date'=>'date',
	'@caimeng.ip'=>'ip', 
),
	//定义表之间的关联关系 
'links'=>array( 
	'@caihui.id'=>'@caimeng.id', 
));
