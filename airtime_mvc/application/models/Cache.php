<?php

class Cache
{
	
	private function createCacheKey($key, $userId = null) {
		
		$CC_CONFIG = Config::getConfig();
		$a = $CC_CONFIG["apiKey"][0];
		$cacheKey = "{$key}{$userId}{$a}";
		
		return $cacheKey;
	}
	
	public function store($key, $value, $userId = null) {
		
		$cacheKey = self::createCacheKey($key, $userId);
		return apc_store($cacheKey, $value);
	}
	
	public function fetch($key, $userId = null) {
		
		$cacheKey = self::createCacheKey($key, $userId);
		return apc_fetch($cacheKey);
	}
}