<?php

namespace PatroNet\Core\Cache;


/**
 * Interface for key-value cache tables
 */
interface KeyValueCacheTable
{
    
    /**
     * Sets a value at a key
     * 
     * @param string $key
     * @param mixed $valueOrCallback Value, or a callback returning with value
     * @param int|null $lifeTime Expiration in seconds, 0 = no explicit lifeend, null = use default value
     */
    public function set($key, $valueOrCallback, $lifeTime = null);
    
    /**
     * Gets a value by its key
     * 
     * @param string $key
     * @param mixed $fallbackValueOrCallback When the specified key does not exist this fallback or callback will be used, see set()
     * @param boolean $storeFallback true = store fallback if used, false = do not store anything
     * @param int|null $lifeTime Expiration in seconds, 0 = no explicit lifeend, null = use default value
     * @return mixed
     */
    public function get($key, $fallbackValueOrCallback = null, $storeFallback = true, $lifeTime = null);
    
    /**
     * Removes the specified key
     * 
     * @param string $key
     */
    public function remove($key);
    
    /**
     * Check existence of the specified key
     * 
     * @param string $key
     * @return boolean
     */
    public function exists($key);
    
}
