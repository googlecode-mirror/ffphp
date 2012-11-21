<?php
/*
 * 类库列表,类库都要将入口注册到这里
 */
class SysFactory
{
	static function memcache()
	{
		return memcacheSys::getIntance();
	}

}
