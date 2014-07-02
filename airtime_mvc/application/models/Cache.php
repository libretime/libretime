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

<<<<<<< HEAD
        return $cacheKey;
    }
    
    public function store($key, $value, $isUserValue, $userId = null) {
            
        $cacheKey = self::createCacheKey($key, $userId);
        //XXX: Disabling APC on SaaS because it turns out we have multiple webservers
        //     running, which means we have to use a distributed data cache like memcached.
        //return apc_store($cacheKey, $value);
        return false;
    }
    
    public function fetch($key, $isUserValue, $userId = null) {
            
        $cacheKey = self::createCacheKey($key, $isUserValue, $userId);
        //XXX: Disabling APC on SaaS because it turns out we have multiple webservers
        //     running, which means we have to use a distributed data cache like memcached.
        //return apc_fetch($cacheKey);
        return false;
    }
}
=======
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
	
	public static function clear()
	{
	    apc_clear_cache('user');
	    apc_clear_cache();
	}
}
>>>>>>> cc-5709-airtime-analyzer-buy-now
