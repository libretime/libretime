<?php

class Cache
{

	private function createCacheKey($key, $isUserValue, $userId = null) {

		$CC_CONFIG = Config::getConfig();
		$a = $CC_CONFIG["apiKey"][0];

		if ($isUserValue) {
			$cacheKey = "{$key}{$userId}{$a}";
		}
		else {
			$cacheKey = "{$key}{$a}";
		}

		return $cacheKey;
	}

	private static function getMemcached() {

	    $CC_CONFIG = Config::getConfig();

	    $memcached = new Memcached();
	    //$server is in the format "host:port"
	    foreach($CC_CONFIG['memcached']['servers'] as $server) {

	        list($host, $port) = explode(":", $server);
	        $memcached->addServer($host, $port);
	    }

	    return $memcached;
	}

	public function store($key, $value, $isUserValue, $userId = null) {

        $cache = self::getMemcached();
		$cacheKey = self::createCacheKey($key, $userId);

		return $cache->set($cacheKey, $value);
	}

	public function fetch($key, $isUserValue, $userId = null) {

	    $cache = self::getMemcached();
		$cacheKey = self::createCacheKey($key, $isUserValue, $userId);
		$value = $cache->get($cacheKey);

		$found = true;
		if ($cache->getResultCode() == Memcached::RES_NOTFOUND) {
            $found = false;
		}

		//need to return something to distinguish a cache miss from a stored "false" preference.
		return array(
		   "found" => $found,
		   "value" => $value,
		);
	}
}