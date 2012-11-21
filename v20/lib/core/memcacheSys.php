<?php
/*
 * Memcache 简易版
 * Author tongseo@gmail.com
 */
class memcacheSys
{
	static $mem;
	static function getIntance()
	{
		static $obj;
		if(empty($obj))
		{
			$obj = new self;
		}
		return $obj;

	}
	function __construct()
	{
			self::$mem = new Memcache;
			foreach(C('memcache') as $item)
			{
				self::$mem->addServer($item[0],$item[1]);
			}
	}
	function set($key,$value,$time=10)
	{
		return self::$mem->set($key,$value, MEMCACHE_COMPRESSED, $time);
	}

	function get($key)
	{
		return self::$mem->get($key);
	}
	function del($key)
	{
		return self::$mem->delete($key);
	}
}
