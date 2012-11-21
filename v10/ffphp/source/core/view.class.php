<?php
// +----------------------------------------------------------------------
// | 模版接口类
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 24
// +----------------------------------------------------------------------
class view{
	private $_fileContent;
	private $_model;
	private $_action;
	private $_unique;
	private $cacheFile;
	private $templateFile;
	private $var;
	function __construct(){
		//设置模版目录
		$this->template_dir=_APP_.'template/'.C('TEMPLATE_STYLE');
		$this->_unique=ltrim(_TMP_,'_');
		//加载系统内置变量		
		$this->var=include _FFPHP_.'source/common/template.inc.php';		
	}
	function display($arg=null){
		if(is_null($arg)){
			$this->_model=$_GET['m'];
			$this->_action=$_GET['a'];
		}else{
			$srcArr=explode('/',trim($arg,'/'));
			if(count($srcArr)>1){
				$this->_model=$srcArr[0];
				$this->_action=$srcArr[1];
			}else{
				$this->_model=$_GET['m'];
				$this->_action=$srcArr[0];				
			}
		}

		$this->templateFile=$this->template_dir.'/'.$this->_model.'/'.$this->_action.'.'.C('TEMPLATE_SUFFIX');
		$this->cacheFile=_ROOT_.'runtime/template/'.C('TEMPLATE_STYLE').'/'.$this->_unique.'_'.$this->_model.'%'.$this->_action.'.php';	
		$this->show();
	}

	function assign($key,$value){
		$this->var[$key]=$value;
	}
	function show(){
		if(file_exists($this->templateFile)){
			//开发模式下记录模板
			C('DEVE') and debug::$info['templaceFile']=$this->templateFile;			
			if(file_exists($this->cacheFile) && filemtime($this->cacheFile) > filemtime($this->templateFile)){
				include($this->cacheFile);
			}else{ 
				$this->cache();	
				include($this->cacheFile);				
			}
		
		}else{
			debug::error('模版不存在:'.$this->templateFile,202001);
		}
 
	}


	function cache(){
			$content=compile::input(file_get_contents($this->templateFile),array_keys($this->var));
			file_put_contents($this->cacheFile,$content);
	}

}
