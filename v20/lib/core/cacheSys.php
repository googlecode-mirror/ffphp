<?php
/*
 * 缓存控制器
 */
namespace Sys;
class cache
{
	public $cachePro = array();
	public $cacheContent;
	private $config;
	private $cacheKey;
	function __construct(route $route)
	{
		$this->config = $route->getConfig();

		unset($route);

		$this->getCacheKey();

		$this->getCache();
	}
	public function getConfig()
	{
		return array(
			'type' => $this->config['type'],
			'control' => $this->config['control'],
			'action' => $this->config['action'],
			'args' => $this->config['args'],
			'cacheKey'=>$this->cacheKey,
			'cacheTime'=>$this->config['cache'],
		);
	}
	private function getCache()
	{
		if($this->cacheContent = memcache::getInstance()->get($this->cacheKey))
		{
			if(preg_match_all('/{{{(.*?)}}}/',$this->cacheContent,$this->cachePro))
			{
				return;
			}

			//如果没有二级更新则直接输出
			echo $this->cacheContent,
			'<span style="border:1px #ccc dashed;font-size:14px;padding:5px;"><font color="red">Cache Data</font> cacheKey:['.$this->cacheKey.'] time:',
			mf() - $GLOBALS['start_time'],
			'</span>';
			exit;
		}
	}
	private function getCacheKey()
	{
		$this->cacheKey = SYS_NAME.'_'.$this->config['control'].'_'.$this->config['action'];
		empty($this->config['args']) or $this->cacheKey .= '_'.join('_',$this->config['args']);
	}

}
