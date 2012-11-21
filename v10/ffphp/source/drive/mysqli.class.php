<?php
// +----------------------------------------------------------------------
// | mysqli数据库驱动
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 13
// +----------------------------------------------------------------------
require _FFPHP_.'source/core/ffdb.class.php';
class db extends ffdb{
	private $mysqli;

	function __construct($tableName){
		C('DEVE') and debug::$info['dataBase']=true;
		$this->connect();
		$this->tableName=$tableName;
		$this->getTableInfo();
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

	//----------------添加数据---
	function add($data=array()){
		//过滤不存在的字段
		$data=$this->dataFilter($data);
		//合并自动获取的数据
		$data=array_merge($this->data,$data);
		empty($data) and debug::error('没有数据可以添加',113002);
		$mysqli=$this->mysqli->stmt_init();
		$fields=array_keys($data);
		$sql='INSERT INTO `'.$this->tableName.'` ';
		$sql.='(`'.join($fields,'`, `').'`) VALUES (?';
		$sql.=str_repeat(',?',count($fields)-1).')';
		$str='';
		foreach($fields as $f)
			$str.=$this->tableInfo['list'][$f];
		//将数据类型压入数据中
		$data=array_values($data);
		array_unshift($data,$str);
		//执行语句
		$mysqli->prepare($sql);
		//处理数据，变成引用变量
		for($i=0;$i<count($data);$i++)
			$data[$i]=&$data[$i];
		call_user_func_array(array($mysqli,'bind_param'), $data);
		if($mysqli->execute()){
			$this->last_id = $mysqli->insert_id;
			$mysqli->close();
			return true;
		}else{
			$this->error=array($mysqli->errno,$mysqli->error);
			$mysqli->close();
			return false;
		}
	}


	//-----------------更新数据----
	function set($data=array()){
		//过滤不存在的字段
		$data=$this->dataFilter($data);
		//合并自动获取的数据
		$data=array_merge($this->data,$data);
		empty($data) and debug::error('没有数据可以修改',113003);
		$sql='UPDATE  `'.$this->tableName.'` SET ';
		foreach($data as $key=>$value){
			$sql.='`'.$key.'` = ?,';	
		}
		$sql=chop($sql,',').$this->where.$this->order.$this->limit;
		$mysqli=$this->mysqli->prepare($sql);

		$str='';
		foreach($data as $key=>$value)
			$str.=$this->tableInfo['list'][$key];
		//将数据类型压入数据中
		$data=array_values($data);
		array_unshift($data,$str);
		//处理数据，变成引用变量
		$data=array_values($data);
		for($i=0;$i<count($data);$i++)
			$data[$i]=&$data[$i];
		call_user_func_array(array($mysqli,'bind_param'),$data);
		$mysqli or debug::error('数据更新失败!',113006);
		if($mysqli->execute()){
			$this->affected_rows=$mysqli->affected_rows;
			$mysqli->close();
			return true;
		}else{
			$this->error=array($mysqli->errno,$mysqli->error);
			$mysqli->close();
			return false;
		}
	}

	//------------查询操作------
	private function read($field){
		$args=implode('`,`',str_replace(',','`,`',$field));
		$sql='SELECT `'.$args.'` FROM  `'.$this->tableName.'`';
		$sql.=$this->where.$this->order.$this->limit;
		DEVE and debug::$sql[]=$sql;
		$mysqli=$this->mysqli->prepare($sql) or debug::error('没有查询到数据！',113004);
		$mysqli->execute();
		$mysqli->store_result();
		return $mysqli;
	}
	function find(){
		$this->limit=' LIMIT 1';		
		$field=func_get_args()?func_get_args():array_keys($this->tableInfo['list']);
		$data=array();
		$read=$this->read($field);
		//处理数据，变成引用变量
		for($i=0;$i<count($field);$i++)
			$data[$i]=&$data[$i];
		call_user_func_array(array($read,'bind_result'),$data);
		$read->fetch();
		return array_combine($field,$data);
	}
	function select(){
		$field=func_get_args()?func_get_args():array_keys($this->tableInfo['list']);
		$data=array();
		$returnData=array();
		$read=$this->read($field);
		//处理数据，变成引用变量
		for($i=0;$i<count($field);$i++)
			$data[$i]=&$data[$i];
		call_user_func_array(array($read,'bind_result'),$data);
		while($read->fetch()){
			$dataItem=array();
			for($i=0;$i<count($data);$i++)
				$dataItem[$field[$i]]=$data[$i];
			array_push($returnData,$dataItem);			
		}
		$this->affected_rows=$read->affected_rows;
		return $returnData;
		
	}

	//-------------删除操作------
	function delete(){
		$sql='DELETE FROM `'.$this->tableName.'`';
		$sql.=$this->where.$this->order.$this->limit;
		$mysqli=$this->mysqli->prepare($sql);
		$mysqli or debug::error('删除失败!',113005);
		if($mysqli->execute()){
			$this->affected_rows=$mysqli->affected_rows;
			$mysqli->close();
			return true;
		}else{
			$this->error=array($mysqli->errno,$mysqli->error);
			$mysqli->close();
			return false;
		}
	}




	//获取并解析表信息
	protected function descTable(){
		$table=array();
		$tableInfo=$this->mysqli->query('desc '.$this->tableName);
		$tableInfo or debug::error('数据表不存在!',113006);
		while($arr=$tableInfo->fetch_assoc()){
			if(is_int(stripos($arr['Type'],'int'))){
				$FieldType='i';
			}elseif(is_int(stripos($arr['Type'],'float')) || is_int(stripos($arr['Type'],'double'))){
				$FieldType='d';
			
			}elseif(is_int(stripos($arr['Type'],'blob'))){
				$FieldType='b';
			}else{
				$FieldType='s';
			}
			$table['list'][$arr['Field']]=$FieldType;
			if($arr['Key']=='PRI')
				$table['pri'] = $arr['Field'];
		}
		empty($table['pri']) and $table['pri']=$table['list'][0];
		$this->tableInfo=$table;
		return $table;
	}


}
