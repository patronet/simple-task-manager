<?php

namespace PatroNet\Core\Cache;


/**
 * Fake key-value cache table
 */
class FakeKeyValueCacheTable implements KeyValueCacheTable
{
    
    public function set($key, $valueOrCallback, $lifeTime = null) {
    }
    
    public function get($key, $fallbackValueOrCallback = null, $storeFallback = true, $lifeTime = null) {
        return is_callable($fallbackValueOrCallback) ? $fallbackValueOrCallback() : $fallbackValueOrCallback;
    }
    
    public function remove($key) {
    }
    
    public function exists($key) {
        return false;
    }
    
}
