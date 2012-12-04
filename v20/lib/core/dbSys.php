<?php
/*
 * 数据库操作类
 */
namespace Sys;
class pdo extends dbBase
{

	 static $obj;			//单例对象

	/**
	 +----------------------------------------------------------
	 * 获取数据库类的实例
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $tableName 数据表名 不带前缀
	 * @param string $dbName  数据库名（在config.php中配置的Key
	 +----------------------------------------------------------
	 * @return object
	 +----------------------------------------------------------
	 */
	static function getInstance($tableName,$dbName)
	{
		empty(self::$obj) and
		       	self::$obj = new self;

		self::$obj -> tableName = $tableName;

		self::$obj -> pdo = db::getConnect($dbName);

		self::$obj -> tableInfo = db::getTableInfo($dbName,$tableName);

		self::$obj -> setNull();

		return self::$obj;
	}

	//
	// 添加数据 C
	//
	public function add($data=array())
	{
		//如果存在自动创建的数据则合并数据
		$data=array_merge($this->data,$data);
		//过滤非字段数据
		$data=$this->dataFilter($data);	
		$keys=array_keys($data);
		$key=array();
		//生成另一个键值数组
		foreach($keys as $k)
		{
			array_push($key,':'.$k);
		}
		$data or errorMsg('没有可以添加的数据!');
		//生成绑定数组
		$datas=array_combine($key,array_values($data));
		//生成准备查询语句
		$sql1=implode('`,`',$keys);
		$sql2=implode(',',$key);
		$sql='INSERT INTO `'.$this->tableName.'` (`'.$sql1.'`) VALUES ('.$sql2.')';
		DEBUG and self::$sql[]=strtr($sql,$datas);
		//执行准备查询语句
		$pdoing=$this->pdo->prepare($sql);
		//绑定并执行语句
		if($pdoing->execute($datas)){
			return $this->pdo->lastInsertId();
		}else {
			$Arr=$pdoing->errorInfo();
			$this->error=$Arr[2];
			return false;
		}
	}

	//
	// 更新数据 U
	//
	public function set($data=array())
	{
		//合并数据
		$data=array_merge($this->data,$data);
		//过滤非字段数据		
		$data=$this->dataFilter($data);	
		
		$keys=array_keys($data);		
		$key=array();
		//生成另一个键值数组
		foreach($keys as $k)
		{
			array_push($key,':'.$k);
		}
		$datas=array_combine($key,array_values($data));

		$sql='UPDATE  `'.$this->tableName.'` SET ';
		foreach($data as $k=>$d)
		{
			$sql.=' '.$k.' = :'.$k.' ,';
		}

		$sql=rtrim($sql,',');
		$sql.=$this->where.$this->group.$this->order.$this->limit;
		DEBUG and self::$sql[]=strtr($sql,$datas);		
		$pdoing=$this->pdo->prepare($sql);

		//绑定并执行语句		
		if($pdoing->execute($datas))
		{
			$this->rows = $pdoing->rowCount();
			return true;
		}
		else 
		{
			$Arr=$pdoing->errorInfo();
			$this->error=$Arr[2];
			return false;
		}
	}
	
	//
	// 查询 R
	//
	
	//查询单条
	public function find($args=null)
	{
		$this->limit=' LIMIT 1';
		if(is_null($args))
		{
			$args = '`'.join('`,`',$this->tableInfo['list']).'`';
		}
		$select=&$this->read($args);
		$select or errorMsg('<b>'.reset(self::$sql).'</b> 该SQl语句没有查询到数据!');
		return $select->fetch(\PDO::FETCH_ASSOC);
	}
	//查询多条
	public function select($args=null)
	{
		if(is_null($field))
		{
			$args = '`'.join('`,`',$this->tableInfo['list']).'`';
		}
		$select=&$this->read($args);
		$select or errorMsg('<b>'.reset(self::$sql).'</b> 该SQl语句没有查询到数据!');
		$this->rows = $select->rowCount();
		return $select->fetchAll(\PDO::FETCH_ASSOC);
	}
	//查询基类
	private function &read($args)
	{
		$sql='SELECT '.$args.' FROM `'.$this->tableName.'`';
		$sql.=$this->where.$this->group.$this->order.$this->limit;
		DEBUG and self::$sql[]=$sql;
		$pdo = $this->pdo->prepare($sql);
		$pdo or errorMsg('<b>['.end(self::$sql).']</b> SQL执行失败!',E_USER_ERROR);
		$pdo->execute();
		return $pdo;
	}
	// 统计查询
	public function count()
	{
		$sql='select count(*) from '.$this->tableName;
		$sql.=$this->where.$this->group.$this->order.$this->limit;		
		DEBUG and self::$sql[]=$sql;
		$data=$this->pdo->query($sql);
		$data or errorMsg('<b>['.end(self::$sql).']</b> SQL执行失败!',E_USER_ERROR);		
		$num=$data->fetch();
		return $num[0];
	}

	//
	// 删除 D 
	// 成功返回影响行数 失败返回$this->error
	//
	public function delete()
	{
		$sql='DELETE FROM `'.$this->tableName.'` ';
		$sql.=$this->where.$this->group.$this->order.$this->limit;
		DEBUG and self::$sql[]=$sql;
		$this->rows=$this->pdo->exec($sql);
		if($this->rows){
			return $this->rows;
		}else{
			$this->error=$this->pdo->errorInfo();
			return false;
		}
	}
	
	//
	// 高级函数 执行一个SQL语句 慎用
	//
	public function query($sql)
	{
		return 	$this->pdo->query($sql);
	}
	
	//
	//自动创建数据 create
	//
	public function create()
	{
		$this->data=array_merge($this->data,$this->dataCheck($this->dataFilter($_POST)));
		return $this;
	}

	//
	//添加数据方法
	//
	public function data($arr)
	{
		$this->data=array_merge($this->data,$this->dataFilter($arr));
		return $this;
	}	

	//
	//数据过渡功能
	//
	public function dataFilter($data)
	{
		$checkedData=array();
		foreach($data as $key=>$value)
		{
			if(in_array($key,$this->tableInfo['list']))
			{
				$checkedData[$key]=$value;
			}
		}
		return $checkedData;
	}
	//
	//数据自动转译
	//
	public function dataCheck($data)
	{
		if(!get_magic_quotes_gpc())
		{
			foreach($data as $key=>$value)
			{
				$data[$key]=addslashes($value);
			}
		}
		return $data;
	}
}

/*
 * 数据库视图类
 */
class pdoView extends dbBase
{
	 static $obj;			//单例对象
		
	/**
	 +----------------------------------------------------------
	 * 获取数据库类的实例
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $tableName 数据表名 不带前缀
	 * @param string $dbName  数据库名（在config.php中配置的Key
	 +----------------------------------------------------------
	 * @return object
	 +----------------------------------------------------------
	 */
	static function getInstance($viewName)
	{
		empty(self::$obj) and
	     self::$obj = new self;

	  self::$obj->tableName = $viewName;

	  self::$obj->tableInfo = self::$obj->getTableInfo();

	  self::$obj ->pdo = db::getConnect(self::$obj->tableInfo['db']);

	  self::$obj->where=self::$obj->tableInfo['links'];

	  return self::$obj;
	}

	//
	// 重载Where方法
	//
	public function where($str='')
	{
		if($str)
		{
			$this->where .=' and '.$this->replaceFields($str);
		}
		return $this;
	}
	
	//
	// 查询 R
	//
	
	//查询单条
	public function find($args=null)
	{
		$this->limit=' LIMIT 1';
		if(is_null($args))
		{
			$args = join(',',array_keys($this->tableInfo['fields']));
		}
		$select=&$this->read($args);
		$select or errorMsg('<b>'.reset(self::$sql).'</b> 该SQl语句没有查询到数据!');
		return $select->fetch(\PDO::FETCH_ASSOC);
	}
	//查询多条
	public function select($args=null)
	{
		if(is_null($args))
		{
			$args = join(',',array_keys($this->tableInfo['fields']));
		}
		$select=&$this->read($args);
		$select or errorMsg('<b>'.reset(self::$sql).'</b> 该SQl语句没有查询到数据!');
		$this->rows = $select->rowCount();
		return $select->fetchAll(\PDO::FETCH_ASSOC);
	}
	//查询基类
	private function &read($args)
	{
		$sql='SELECT '.$this->replaceFields($args).' FROM `'.join('`,`',$this->tableInfo['tables']).'`';
		$sql.=$this->where;
		$sql.=$this->replaceFields($this->group.$this->order.$this->limit);
		DEBUG and self::$sql[]=$sql;
		$pdo = $this->pdo->prepare($sql);
		$pdo or errorMsg('<b>['.end(self::$sql).']</b> SQL执行失败!',E_USER_ERROR);
		$pdo->execute();
		return $pdo;
	}
	// 统计查询
	public function count()
	{
		$sql='select count(*) from `'.join('`,`',$this->tableInfo['tables']).'`';
		$sql.=$this->where;
		$sql.=$this->replaceFields($this->group.$this->order.$this->limit);
		DEBUG and self::$sql[]=$sql;
		$data=$this->pdo->query($sql);
		$data or errorMsg('<b>['.end(self::$sql).']</b> SQL执行失败!',E_USER_ERROR);
		$num=$data->fetch();
		return $num[0];
	}
	
		private function replaceFields($str)
		{
			return strtr($str,$this->tableInfo['fields']);
		}
		//获取表信息
		private function getTableInfo()
		{
			//生成缓存Key [tableSys#虚拟表名]
			$memKey = 'tableSys#'.$this->tableName;
			//DEBUG模式下不记录缓存
			$info = DEBUG ? null : \SysFactory::memcache() -> get($memKey);
			if(is_null($info))
			{
				$config = $this->loadConfig(SYS_PATH.'data/'.strtolower($this->tableName).'.php');
				\SysFactory::memcache() -> set($memKey,$config,3600);
			}
			return $config;
		}
		//载入配置文件并解析
		private function loadConfig($url)
		{
			$return_value = array();
			$config = include $url;
			//检查配置文件是否合格
			if(!isset($config['db'],$config['fields'],$config['links']))
			{
				errorMsg('<b>'.$url.'</b> 该配置文件缺少参数,请核对!',E_USER_ERROR);
			}
			$return_value['db'] = $config['db'];
			$fieldKey = str_replace('@',C('prefix'),array_keys($config['fields']));
			$return_value['tables'] = array();
			foreach($fieldKey as $item)
			{
				$tableName = reset(explode('.',$item));
				in_array($tableName,$return_value['tables']) or array_push($return_value['tables'],$tableName);
			}
			$return_value['fields'] = array_combine(array_values($config['fields']),$fieldKey);
			$return_value['links'] = strtr(join(' and ',$config['links']),array('@'=>C('prefix')));
			return $return_value;
		}
}

/*
 * 数据库基类
 */
abstract class dbBase
{
	static $sql;			//SQL语句记录
	public $tableName;//数据表名
	public $tableInfo;//数据表信息
	public $rows;			//影响行数 Update Delete
	public $data;			//所有数据 Create
	protected $pdo;			//数据库链接
	protected $where;
	protected $group;	
	protected $order;
	protected $limit;

	//单例
	protected function __construct(){}
	protected function __clone(){}
	
	
	//
	// 初始化条件
	//
	public function setNull()
	{
		$this->where = '';
		$this->group = '';
		$this->order = '';
		$this->limit = '';
		$this->data = array();
	}

	//
	// 设置查询条件
	//
	public function where($str='')
	{
		empty ($str) or
			$this->where =' where '.$str;
		return $this;
	}
	public function group($str='')
	{
		empty ($str) or
			$this->group =' group by '.$str;
		return $this;
	
	}
	public function order($str='')
	{
		empty ($str) or
			$this->order =' order by '. $str;
		return $this;
	}
	public function limit($start=null,$stop=null)
	{
		if(is_null($start))
		{
			return $this;
		}
		else if(is_null($stop))
		{
			$this->limit =' limit '.$start;
		}
		else
		{
			$this->limit =' limit '.$start.','.$stop;
		}
		return $this;
	}

}
/*
 * 数据库信息管理类
 */
class db
{
	public static $connect=array(); //数据库链接
	public static $table = array(); //数据表信息
	//禁止实例化和克隆
	private function __construct(){}
	private function __clone(){}

	static function getConnect($dbName)
	{
		//如果有链接则直接返回
		if(isset($connect[$dbName]))
		{
			return $connect[$dbName];
		}
		else
		{
			//读取配置文件
			$config = C('database');
			if(isset($config[$dbName]))
			{
				list($dsn,$usr,$pwd) = $config[$dbName];
				//记录链接并返回
				return $connect[$dbName] = new \PDO($dsn,$usr,$pwd);
			}
			else
			{
				errorMsg('<b>'.$dbName.'</b> 没有找到这个数据库的配置信息,请查看C(\'database\')',E_USER_ERROR);
				return false;
			}
		}
	}
	static function getTableInfo($dbName,$tableName)
	{
		//生成缓存Key [tableSys#库名#表名]
		$memKey = 'tableSys#'.$dbName.'#'.$tableName;
		//DEBUG模式下不记录缓存
		$info = DEBUG ? null : \SysFactory::memcache() -> get($memKey);
		if(is_null($info))
		{
			$res = self::getConnect($dbName)->query('DESC '.$tableName);
			$res or errorMsg('<b>'.$tableName.'</b> 这个表在数据库中不存在!',E_USER_ERROR);
			$info=array();
			$info['list']=array();
			foreach($res->fetchAll(\PDO::FETCH_ASSOC) as $item)
			{
				//记录字段列表
				array_push($info['list'],$item['Field']);
				//记录主键
				if($item['Key']=='PRI')
				{
					$info['pri']=$item['Field'];
				}
			}
			//如里没有主键则第一具字段填充
			empty($info['pri']) and $table['pri'] = reset($table['list']);
			//加入缓存
			\SysFactory::memcache() -> set($memKey,$info,3600);
		}
		return $info;
	}
}
