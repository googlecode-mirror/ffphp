<?php
// +----------------------------------------------------------------------
// | 数据库的抽象接口类
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 8
// +----------------------------------------------------------------------

//FFPHP数据库接口类
abstract class ffdb{
	protected $where;
	protected $limit;
	protected $order;

	public $last_id;
	public $affected_rows;
	public $error;
	public $data=array();
	protected $tableName;
	protected $tableInfo;

	abstract function add($data=array());
	abstract function set($data=array());
	abstract function find();
	abstract function select();
	abstract function delete();

	function setTableName($v){
		$this->tableName=$v;
	}
	function where($v=''){
		$this->where=' WHERE '.$v;
		return $this;
	}
	function order($v=''){
			$this->order=' ORDER BY '.$v;
			return $this;
	}
	function limit($v1,$v2=null){
		if(is_null($v2))
			$this->limit=' LIMIT '.$v1;
		else
			$this->limit=' LIMIT '.$v1.','.$v2;			
			return $this;
	}


	//读取数据表缓存文件，没有则缓存
	protected function getTableInfo(){
		$cacheFile=_ROOT_.'runtime/table/'.$this->tableName.'.php';
		$this->descTable();
		if(file_exists($cacheFile)){
			//存在缓存，直接读取
			$this->tableInfo=include($cacheFile);
		}else{
			//缓存表单结构
			file_put_contents($cacheFile,"<?php\nreturn ".var_export($this->descTable(),true).';');
		}
	}

//-----------自动创建数据 create
	function create(){
		$this->data=$_POST;
		$this->data=array_merge($this->data,$this->dataCheck($this->dataFilter($this->data)));
		return $this;
	}
//-----------添加数据方法
	function data($arr){
		$this->data=array_merge($this->data,$this->dataFilter($arr));
		return $this;
	}	

//-----------数据过渡功能
	protected function dataFilter($data){
		$checkedData=array();
		foreach($data as $key=>$value)
			if(array_key_exists($key,(array)$this->tableInfo['list']))
				$checkedData[$key]=$value;
		return $checkedData;
	}
//----------create方法的自动转译
	protected function dataCheck($data){
		if(!get_magic_quotes_gpc()){
		      	foreach($data as $key=>$value)
				$data[$key]=addslashes($value);
		}
		return $data;
	}


}
