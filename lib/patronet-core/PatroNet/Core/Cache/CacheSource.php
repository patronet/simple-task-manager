<?php

namespace PatroNet\Core\Cache;

use PatroNet\Core\Content\StoredSource;


/**
 * Interface for cached content providers
 */
interface CacheSource extends StoredSource
{
    
    /**
     * Clears the cache
     */
    public function clear();
    
}
