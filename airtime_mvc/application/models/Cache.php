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
	
	public function store($key, $value, $isUserValue, $userId = null) {
		
		$cacheKey = self::createCacheKey($key, $userId);
		return apc_store($cacheKey, $value);
	}
	
	public function fetch($key, $isUserValue, $userId = null) {
		
		$cacheKey = self::createCacheKey($key, $isUserValue, $userId);
		return apc_fetch($cacheKey);
	}
}