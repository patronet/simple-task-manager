<?php

namespace PatroNet\Core\Cache;


/**
 * Memcached based key-value cache table implementation
 */
class MemcachedKeyValueCacheTable implements KeyValueCacheTable
{
    
    const MAXIMUM_LIFETIME = 2592000;
    
    private $oMemcached;
    
    private $defaultLifeTime;
    
    /**
     * @param string $host
     * @param int $port
     * @param int $defaultLifeTime
     * @throws \Exception When Memcached class does not exist or connection failed
     */
    public function __construct($host, $port, $defaultLifeTime = 300) {
        $memcachedClass = "Memcached";
        if (!class_exists($memcachedClass)) {
            throw new \Exception("Memcached class does not exist");
        }
        $this->oMemcached = new $memcachedClass();
        if (!$this->oMemcached->addServer($host, $port)) {
            throw new \Exception("Can not connect to memcached at {$host}:{$port}");
        }
        $this->defaultLifeTime = max(0, min(self::MAXIMUM_LIFETIME, $defaultLifeTime));
    }
    
    public function set($key, $valueOrCallback, $lifeTime = null) {
        $value = is_callable($valueOrCallback) ? $valueOrCallback() : $valueOrCallback;
        $lifeTime = is_null($lifeTime) ? $this->defaultLifeTime : max(0, min(self::MAXIMUM_LIFETIME, $lifeTime));
        $this->oMemcached->set($key, $value, $lifeTime);
    }
    
    public function get($key, $fallbackValueOrCallback = null, $storeFallback = true, $lifeTime = null) {
        $memcachedClass = "Memcached";
        $value = $this->oMemcached->get($key);
        if ($this->oMemcached->getResultCode() == $memcachedClass::RES_NOTFOUND) {
            if (!is_null($fallbackValueOrCallback)) {
                $fallbackValue = is_callable($fallbackValueOrCallback) ? $fallbackValueOrCallback() : $fallbackValueOrCallback;
                if ($storeFallback) {
                    $this->set($key, $fallbackValue, $lifeTime);
                }
            }
            return $fallbackValue;
        } else {
            return $value;
        }
    }
    
    public function remove($key) {
        $this->oMemcached->delete($key);
    }
    
    public function exists($key) {
        $memcachedClass = "Memcached";
        $this->oMemcached->get($key);
        return ($this->oMemcached->getResultCode() != $memcachedClass::RES_NOTFOUND);
    }
    
}
