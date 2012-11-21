<?php
// +----------------------------------------------------------------------
// | PDO数据库驱动
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 14
// +----------------------------------------------------------------------
require _FFPHP_.'source/core/ffdb.class.php';
class db extends ffdb{
	private $pdo;
	function __construct($tableName){
		try{
		$this->pdo=new PDO(C('DB_DSN'),C('DB_USER'),C('DB_PWD'));
		} catch(PDOException $e){
			debug::error($e->getMessage(),113001);
		}
		$this->tableName=$tableName;
		//获取表结构信息
		$this->getTableInfo();
	}

//--------------------------------------//
//		CURD操作		//
//--------------------------------------//
//基本操作:add save select find delelte //
//扩展操作:where limit order		//
//--------------------------------------//
	function add($data=array()){
		//如果存在自动创建的数据则合并数据
		$data=array_merge($this->data,$data);
		//过滤非字段数据
		$data=$this->dataFilter($data);	
		$keys=array_keys($data);
		$key=array();
		//生成另一个键值数组
		foreach($keys as $k)
			array_push($key,':'.$k);
		//生成绑定数组
		$datas=array_combine($key,array_values($data));
		//生成准备查询语句
		$sql1=implode('`,`',$keys);
		$sql2=implode(',',$key);
		$sql='INSERT INTO '.$this->tableName.' (`'.$sql1.'`) VALUES ('.$sql2.')';
		//执行准备查询语句
		$pdoing=$this->pdo->prepare($sql);
		//绑定并执行语句
		if($pdoing->execute($datas)){
			$this->last_id=$this->pdo->lastInsertId();
			return true;
		}else {
			$Arr=$pdoing->errorInfo();
			$this->error=$Arr[2];
			return false;
		}
	}
//--------------修改
	function set($data=array()){
		//合并数据
		$data=array_merge($this->data,$data);
		//过滤非字段数据		
		$data=$this->dataFilter($data);	
		
		$keys=array_keys($data);		
		$key=array();
		//生成另一个键值数组
		foreach($keys as $k)
			array_push($key,':'.$k);
		$datas=array_combine($key,array_values($data));

		$sql='UPDATE  '.$this->tableName.' SET ';
		foreach($data as $k=>$d){
			$sql.=' '.$k.' = :'.$k.' ,';
		}

		$sql=rtrim($sql,',');
		$sql.=$this->where.$this->order.$this->limit;		
		$pdoing=$this->pdo->prepare($sql);

		//绑定并执行语句		
		if($pdoing->execute($datas)){
			$this->affected_rows = $pdoing->rowCount();
			return true;
		}else {
			$Arr=$pdoing->errorInfo();
			$this->error=$Arr[2];
			return false;
		}
	}

//方法-----------------------------读取
//为select和find 提供的查询基类
	private function read($args){
	$sql='SELECT '.$args.' FROM '.$this->tableName;
	$sql.=$this->where.$this->order.$this->limit;
	DEVE and debug::$sql[]=$sql;
	$pdo = $this->pdo->prepare($sql);
	$pdo->execute();
	return $pdo;
	}
//方法--------查询（单条返回）
	function find(){
	$this->limit=' LIMIT 1';
	$args=func_get_args()?'`'.implode('`,`',str_replace(',','`,`',func_get_args())).'`':'*';
	$select=$this->read($args);
	$select or  debug::error('没有查询到数据！',114004);
	return $select->fetch(PDO::FETCH_ASSOC);
	}
//方法--------查询（多条返回）
	function select(){
	$args=func_get_args()?'`'.implode('`,`',str_replace(',','`,`',func_get_args())).'`':'*';
	$select=$this->read($args);
	$select or debug::error('没有查询到数据！',114004);
	$this->affected_rows = $select->rowCount();
	return $select->fetchAll(PDO::FETCH_ASSOC);
	}

//-----------------删除
	function delete(){
	$sql='DELETE FROM '.$this->tableName;
	$sql.=$this->where.$this->order.$this->limit;
	$this->affected_rows=$this->pdo->exec($sql);
	if($this->affected_rows){
		return true;
	}else{
		$this->error=$this->pdo->errorInfo();
		return false;
	}
	}

//----------------扩展操作---------------------//
	function count(){
		$sql='select count(*) from '.$this->tableName;
		if($this->where)
			$sql.=' WHERE '.$this->where;
		$data=$this->pdo->query($sql);
		$num=$data->fetch();
		return $num[0];
	}

//-----------查询数据表信息
	protected function descTable(){
			$table=array();
			$table['list']=array();
			$pdoing=$this->pdo->prepare('desc '.$this->tableName);
			$pdoing or debug::error('数据表不存在!',114005);
			$pdoing->execute();
			$info=$pdoing->fetchAll(PDO::FETCH_ASSOC);
			foreach($info as $key=>$value){
				if(is_int(stripos($info[$key]['Type'],'int'))){
					$FieldType='i';
				}elseif(is_int(stripos($info[$key]['Type'],'float')) || is_int(stripos($info[$key]['Type'],'double'))){
					$FieldType='d';
				}elseif(is_int(stripos($info[$key]['Type'],'blob'))){
					$FieldType='b';
				}else{
					$FieldType='s';
				}
				//将所有的字段元素加入到数组中
				$table['list'][$info[$key]['Field']]=$FieldType;
				//将主键记录下来
				if($info[$key]['Key']=='PRI')
					$table['pri']=$info[$key]['Field'];
			}

			empty($table['pri']) and $table['pri']=$info[0]['Field'];
			//缓存表单结构
			return $table;
		}

}

?>
