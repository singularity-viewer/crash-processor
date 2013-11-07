<?php
class Memc
{
    static $mem;
    static $active = false;
    static $expire = 7200;
	
    function init()
    {
		if (!class_exists("Memcached", false)) return;
		
        self::$mem = new Memcached("scr");
		$servers = self::$mem->getServerList();
		if (empty($servers))
		{
			//This code block will only execute if we are setting up a new EG(persistent_list) entry
			self::$mem->setOption(Memcached::OPT_RECV_TIMEOUT, 1000);
			self::$mem->setOption(Memcached::OPT_SEND_TIMEOUT, 3000);
			self::$mem->setOption(Memcached::OPT_TCP_NODELAY, true);
			self::$mem->setOption(Memcached::OPT_PREFIX_KEY, "cr_");
			self::$mem->addServer("localhost", 11211);
		}
		
		self::$active = true;
	}
	
	function getq($q)
	{
		if (!self::$active) return false;

		$key = md5($q);
		return self::$mem->get($key);
	}

	function setq($q, $data)
	{
		if (!self::$active) return false;

		$key = md5($q);
		return self::$mem->set($key, $data, self::$expire);
	}
	
	function flush()
	{
		if (!self::$active) return false;
		self::$mem->flush();
	}

}
?>
