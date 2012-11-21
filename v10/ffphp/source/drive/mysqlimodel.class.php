<?php
// +----------------------------------------------------------------------
// | mysqli数据模型驱动
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 15
// +----------------------------------------------------------------------

require _FFPHP_.'source/core/ffdbmodel.class.php';
class dbmodel extends ffdbmodel{
	private $mysqli;
	private $fields;
	function __construct($modelName){
		$this->connect();
		$this->modelName=$modelName;		
		$this->getModelInfo();
	}
	function __destruct(){
		$this->mysqli->close();
	}
	private function connect(){
		$this->mysqli=new mysqli(C('DB_HOST'),C('DB_USER'),C('DB_PWD'),C('DB_NAME'));
		if(mysqli_connect_errno()){
			debug::error('数据库连接失败:'.mysqli_connect_error(),213001);
			exit;
		}
	}

//方法--------查询（单条返回）
	function find($arg=null){
	$args=$arg?implode(',',func_get_args()):false;
	$read=$this->read($args);
	$data=array();	
	//处理数据，变成引用变量
	for($i=0;$i<count($this->fields);$i++)
		$data[$i]=&$data[$i];
	call_user_func_array(array($read,'bind_result'),$data);
	$read->fetch();
	return array_combine($this->fields,$data);
	}
//方法--------查询（多条返回）
	function select($arg=null){
	$args=$arg?implode(',',func_get_args()):false;
	$read=$this->read($args);
	$returnData=array();	
	//处理数据，变成引用变量
	for($i=0;$i<count($this->fields);$i++)
		$data[$i]=&$data[$i];
	call_user_func_array(array($read,'bind_result'),$data);


	while($read->fetch()){
		$dataItem=array();
		for($i=0;$i<count($data);$i++)
			$dataItem[$this->fields[$i]]=$data[$i];
		array_push($returnData,$dataItem);			
	}
	$this->affected_rows=$read->affected_rows;
	return $returnData;
	}

//为select和find 提供的查询基类
	private function read($arg){
	$sql='SELECT ';	
	$fields=array();
	if($arg==null){
	$this->fields=array_values($this->modelInfo['fields']);		
		foreach($this->modelInfo['fields'] as $k=> $v){
			array_push($fields,$k.' as '.$v);
		}
	}else{
	$args=explode(',',$arg);
	$this->fields=$args;
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
	$mysqli=$this->mysqli->prepare($sql) or debug::error('没有查询到数据！',113004);
	$mysqli->execute();
	$mysqli->store_result();
	return $mysqli;

	}

}
