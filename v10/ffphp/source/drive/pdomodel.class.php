<?php
// +----------------------------------------------------------------------
// | PDO数据模型驱动
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 16
// +----------------------------------------------------------------------
require _FFPHP_.'source/core/ffdbmodel.class.php';
class dbmodel extends ffdbmodel{
	protected $fieds;
	protected $table;
	protected $links;

	function __construct($modelName){
		$this->modelName=$modelName;
		try{
		$this->pdo=new PDO(C('DB_DSN'),C('DB_USER'),C('DB_PWD'));
		} catch(PDOException $e){
			echo $e->getMessage();
		}
		$this->getModelInfo();
	}

//方法--------查询（单条返回）
	function find(){
	$args=func_get_args()?implode(',',func_get_args()):false;
	$select=$this->read($args);
	$select or debug::error('没有查询到数据',116001);
	return $select->fetch(PDO::FETCH_ASSOC);
	}
//方法--------查询（多条返回）
	function select(){
	$args=func_get_args()?implode(',',func_get_args()):false;
	$select=$this->read($args);
	$select or debug::error('没有查询到数据',116002);
	return $select->fetchAll(PDO::FETCH_ASSOC);
	}

//为select和find 提供的查询基类
	private function read($arg){
	$sql='SELECT ';
	$fields=array();		
	if($arg===false){
		foreach($this->modelInfo['fields'] as $k=> $v){
			array_push($fields,$k.' as '.$v);
		}
	}else{
	$args=explode(',',$arg);
		foreach($args as $v){
			array_push($fields,array_search($v,$this->modelInfo['fields']).' as '.$v);
		}
	}
	$sql.=join(',',$fields);
	$sql.=' FROM '.join(',',$this->modelInfo['tables']);
	$sql.=' WHERE '.join(' and ',$this->modelInfo['links']);

	if($this->where)
		$sql.=' and '.$this->where;	
	if($this->order)
		$sql.=' ORDER BY '.$this->order;
	if($this->limit)
		$sql.=' LIMIT '.$this->limit;
	DEVE and debug::$sql[]=$sql;	
	$pdo = $this->pdo->prepare($sql);
	$pdo->execute();
	return $pdo;
	}

}
