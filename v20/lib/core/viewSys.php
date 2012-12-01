<?php
/*
 * 视图基类
 */
namespace Sys;
class view extends \Smarty
{
	function __construct()
	{
		parent::__construct();
	 	$this->template_dir=SYS_APP.'view/';//设置模版目录
		$this->compile_dir=C('tempPath');//设置编译文件目录
		$this->left_delimiter='<{';
		$this->right_delimiter='}>';
		$this->assign('app',V_APP);
		$this->assign('public',V_PUBLIC);
	}
	function display($template,$cache=0,$args=null)
	{
		$displayHtml = parent::fetch($template);

		if(is_null($args)|| (int)$args > count(Q('args'))){
			if(intval($cache) > 0)
			{
				\SysFactory::memcache()->set(Q('cacheKey'),$displayHtml,$cache);
			}
			else if(Q('cacheTime')>0)
			{
				\SysFactory::memcache()->set(Q('cacheKey'),$displayHtml,Q('cacheTime'));
			}
		}


		if(preg_match_all('/{{{(.*?)}}}/',$displayHtml,$cacheProMatch))
		{
			$SysCachePro = new cachePro($cacheProMatch);
			$displayHtml = strtr($displayHtml,$SysCachePro->getConfig());
			unset($SysCachePro);
		}

		echo $displayHtml;
	}

	function displayHtml($template,$filename=null)
	{
		$displayHtml = parent::fetch($template);
		$dir = dirname($_SERVER['SCRIPT_FILENAME']);
		is_null($filename) and $filename = Q('cacheKey').'.html';
		$file =  $dir.'/'.$filename;	
		strpos($file,'index.php')===false or die('Index.php is System file');
		strpos($file,'.htaccess')===false or die('.htaccess is System file');
		file_put_contents($file,$displayHtml);
		echo $displayHtml;
	}
}
