<?php
// +----------------------------------------------------------------------
// | FFPHP缺省配置库
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 1
// +----------------------------------------------------------------------

return array(
	'DEVE'=>true,	//默认是开启开发模式的
	'DEBUG'=>true,	//是否显示开发控制台
	
	'DEFAULTE_MODEL'=>'index',	//默认控制器名
	'DEFAULTE_ACTION'=>'index',	//默认动作名

	'TEMPLATE_SMARTY'=>false,	//是否启用Smarty
	'TEMPLATE_STYLE'=>'default',	//模版名
	'TEMPLATE_SUFFIX'=>'html',	//模版后缀

	'CONNECT_TYPE'=>1,	//数据库连接类型: 1)mysqli  2)pdo
	'DB_USER'=>'root',	//数据库用户名
	'DB_PWD'=>'123456',	//数据库密码
	'DB_FIX'=>'ff_',	//数据表前缀		
	'DB_HOST'=>'localhost',	//数据库主机
	'DB_NAME'=>'ffphp20',	//数据库名
	'DB_DSN'=>'mysql:host=localhost;dbname=ffphp20',	//pdo DSN 当MYSQL_TYPR=2的时候生效,而且将取代上二项
	);
