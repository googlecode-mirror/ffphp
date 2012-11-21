<?php
// +----------------------------------------------------------------------
// | Smarty模版驱动
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 13
// +----------------------------------------------------------------------

require _FFPHP_.'extend/smarty/Smarty.class.php';
class view extends Smarty{
	function __construct(){
		$this->template_dir=_APP_.'template/'.C('TEMPLATE_STYLE');//设置模版目录
		$this->compile_dir=_ROOT_.'runtime/template/'.C('TEMPLATE_STYLE').'/';//设置编译文件目录
		$this->left_delimiter='<{';
		$this->right_delimiter='}>';
		$this->cache_dir=_ROOT_.'runtime/cache/'.C('TEMPLATE_STYLE');		
	}
	function display($fileName=null,$cacheId=null,$complieId=null){
		//添加模版中的默认变量
		foreach (include _FFPHP_.'source/common/template.inc.php' as $key => $value){
			$this->assign($key,$value);
		}
		if(is_null($fileName))
			$fileName=$_GET['m'].'/'.$_GET['a'].'.'.C('TEMPLATE_SUFFIX');
		elseif(strstr($fileName,'/'))
			$fileName=$fileName.'.'.C('TEMPLATE_SUFFIX');
		else
			$fileName=$_GET['m'].'/'.$fileName.'.'.C('TEMPLATE_SUFFIX');
		parent::display($fileName,$cacheId,$complieId);
	
	} 

}
