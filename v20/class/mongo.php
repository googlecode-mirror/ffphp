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


}
