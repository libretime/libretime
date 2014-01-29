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
