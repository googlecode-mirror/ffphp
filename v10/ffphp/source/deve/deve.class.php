<?php
// +----------------------------------------------------------------------
// | 编译函数库【开发模式】
// +----------------------------------------------------------------------
// | [Framework For PHP] 超轻量级PHP框架 (简称:FFPHP)
// +----------------------------------------------------------------------
// | Licensed  (http://www.apache.org/licenses/LICENSE-2.0)
// | Copyright (c) 2012 http://www.ffphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 张通 (tongseo@gmail.com)
// | File ID: 10
// +----------------------------------------------------------------------

class deve{
	//开发加载函数
	static function load($max){
		if(is_string($max)){
			if(include $max){
				array_push(debug::$files,$max);				
			}else{
				debug::error('文件加载错误!'.$max,11001);
			}		
		}elseif(is_array($max)){
			foreach ($max as $v){
				if(include $v){
					array_push(debug::$files,$v);
				
				}else{
					debug::error('文件加载错误!'.$v,11002);
				}
			}
		}else{
				debug::error('无效参数,系统开发加载函数出错。',11003);			

		}
	}
	//格式化域控制器
	static function buildAction($m){
		$newFile=_APP_.'core/action/'.strtolower($m).'Action.class.php';
		$content=file_get_contents(_APP_.'action/'.$m.'.class.php');
		if(!file_exists($newFile) || filemtime(_APP_.'action/'.$m.'.class.php') > filemtime($newFile)){

			//已有父类的只更改类名
			if(preg_match('/extends\s+(.+?)\s*{/i',$content, $arr)) {
			$content=preg_replace('/class\s+(.+?)\s+extends\s+(.+?)\s*{/i','class \1Action extends \2 {',$content);
			}else{ 
			//没有父类的更改类名并继承系统ACTION类
			$content=preg_replace('/class\s+(.+?)\s*{/i','class \1Action extends action {',$content);
			}

			//继承父类的初始化函数
			preg_match('/parent::__construct(.*);/i',$content) or $content=preg_replace('/__construct((.*))\s*{/i','__construct\1{
				parent::__construct();',$content);

			file_put_contents($newFile,$content);
				debug::$info['buildAction']=true;
		}
	}
	//构建模型
	static function buildModel($model){
			$modelFile=_APP_.'core/model/'.$model.'.php';
			$modelBase=_APP_.'model/'.$model.'.model.php';
			if(file_exists($modelBase)){
				if(file_exists($modelFile) || filemtime($modelBase) > filemtime($modelFile)){
					include(_APP_.'model/'.$model.'.model.php');
					$fields=array_combine(str_replace('@',C('DB_FIX'),array_keys($fields)),array_values($fields));
					$links=array_combine(str_replace('@',C('DB_FIX'),array_keys($links)),str_replace('@',C('DB_FIX'),array_values($links)));
					//在数据中解析出表名
					$tables=array();
					foreach(array_keys($fields) as $v){
						array_push($tables,array_shift(explode('.',$v)));
					}
					$tables=array_unique($tables);

					//获取关联关系
					$arr=array();
					foreach($links as $k=>$v)
						array_push($arr,$k.'='.$v);
					$links=$arr;

					$table['tables']=$tables;
					$table['fields']=$fields;
					$table['links']=$links;
					file_put_contents($modelFile,"<?php\nreturn ".var_export($table,true)."\n?>");
				}
			}else{
				file_exists($modelFile) and unlink($modelFile);
				debug::error('模型'.$model.'文件不存在.',11004);
			}
	}

	static function construct(){
		//检测创建缓存目录
		$runTimeFIles=array(
			_ROOT_.'runtime/log',
			_ROOT_.'runtime/table',
			_ROOT_.'runtime/template/'.C('TEMPLATE_STYLE'),
		);
		self::mdir($runTimeFIles);

		//检测创建核心文件目录
		$coreFiles=array(
			_APP_.'action',
			_APP_.'model',
			_APP_.'core/action',
			_APP_.'core/model',
			_APP_.'template/'.C('TEMPLATE_STYLE'),	
			_ROOT_.'behavior',
		);
		self::mdir($coreFiles);

		$indexActionStr=<<<str
<?php
class index{
	//系统默认创建的缺省控制器，你可以更改或删除它。
	function index(){
		echo '<h1>It Works</h1>';
	}

}
?>
str;
		self::mfile(_APP_.'action/index.class.php',$indexActionStr);

		$configIncStr=<<<str
<?php
return array(
	'DEVE'=>true,		//开发模式
	'DEBUG'=>true,		//输出DEVE控制台
	'TEMPLATE_STYLE'=>'default',	//模版名
	'CONNECT_TYPE'=>2,	//数据库连接类型: 1)mysqli  2)pdo
	'DB_USER'=>'root',	//数据库用户名
	'DB_PWD'=>'123456',	//数据库密码
	'DB_FIX'=>'ff_',	//数据表前缀		
	'DB_HOST'=>'localhost',	//数据库主机
	'DB_NAME'=>'ffphp10',	//数据库名
	'DB_DSN'=>'mysql:host=localhost;dbname=ffphp10',	//pdo DSN 当MYSQL_TYPR=2的时候生效,而且将取代上二项
	
	);
str;
		self::mfile(_ROOT_.'config.php',$configIncStr);
		
		
	}

		
	//创建文件函数
	static function mfile($file,$con){
		if(!file_exists($file)){
			file_put_contents($file,$con);
		}
	}
	//创建目录函数
	static function mdir($dir){
		if(is_array($dir)){
			foreach($dir as $d){
				if(!file_exists($d)){
					mkdir($d,0755,true);
				}
			}
		}else{
				if(!file_exists($dir)){
					mkdir($dir,0755,true);
				}
		}
	}


}
