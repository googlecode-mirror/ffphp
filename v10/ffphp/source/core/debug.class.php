<?php
// +----------------------------------------------------------------------
// | 运行模式下的Debug输出
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 21
// +----------------------------------------------------------------------

class debug{

	static function error($msg){
		self::show($msg);
	}




	static function show($msg,$title='操作失败',$cacheId='null',$cacheTime=-1){
		$error=new view();
		$error->template_dir=_FFPHP_.'extend';
		$error->assign('title',$title);
		$error->assign('msg',$msg);
		$error->display('template/error');
		exit;
	}

}
