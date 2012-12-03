<?php
/*
 * MongoDB类 简易版
 */
class mongo
{
	static $obj;
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
			self::$mem = new \Memcache;
			foreach(C('memcache') as $item)
			{
				self::$mem->addServer($item[0],$item[1]);
			}
	}

}
