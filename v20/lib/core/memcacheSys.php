<?php
/*
 * Memcache 简易版
 */
class memcacheSys
{
	private static $obj;
	private static $mem;
	public static function getInstance()
	{
		if(empty(self::$obj))
		{
			self::$obj = new self;
		}
		return self::$obj;

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
	function close()
	{
		return self::$mem->close();
	}

}
