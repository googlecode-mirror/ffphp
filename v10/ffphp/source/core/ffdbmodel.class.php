<?php
// +----------------------------------------------------------------------
// | 数据库模型抽象接口类
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 22
// +----------------------------------------------------------------------

//FFPHP数据库接口类
abstract class ffdbmodel{
	protected $where;
	protected $limit;
	protected $order;

	protected $modelInfo;
	protected $modelName;

//----------------扩展操作---------------------//
	function where($v=''){
			$this->where=$this->getFields($v);
			return $this;
	}
	function order($v=''){
			$this->order=$this->getFields($v);
			return $this;
	}
	function limit($v1,$v2=null){
		if(is_null($v2))
			$this->limit=$v1;
		else
			$this->limit=$v1.','.$v2;	
		return $this;
	}

	protected function getModelInfo(){
		$modelFile=_APP_.'core/model/'.$this->modelName.'.php';
		C('DEVE') and deve::buildModel($this->modelName);
		$this->modelInfo=include($modelFile);
	}
	protected function getFields($v){
	//将条件中的虚拟表字段替换为真实字段
	return str_replace(array_values($this->modelInfo['fields']),array_keys($this->modelInfo['fields']),$v);
	}
}
