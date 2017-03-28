<?php

namespace PatroNet\Core\Cache;

use \PatroNet\Core\Content\Source;
use \PatroNet\Core\Content\Storage;
use \PatroNet\Core\Content\SourceTrait;


/**
 * Cached content decorator with time based expiration
 */
class ExpirableCacheSource implements CacheSource
{
    
    use SourceTrait;
    
    protected $oSource;
    
    protected $oStorage;
    
    protected $lifetime;
    
    /**
     * @param \PatroNet\Core\Content\Source $oSource
     * @param \PatroNet\Core\Content\Storage $oSource
     * @param int $lifetime
     */
    public function __construct(Source $oSource, Storage $oStorage, $lifetime)
    {
        $this->oSource = $oSource;
        $this->oStorage = $oStorage;
        $this->lifetime = $lifetime;
    }
    
    /**
     * Gets the content
     *
     * Provides cached content if a valid cached version exists.
     * 
     * @return string
     */
    public function get()
    {
        $this->validate();
        return $this->oStorage->get();
    }
    
    /**
     * Flushes the content
     *
     * Provides cached content if a valid cached version exists.
     */
    public function flush()
    {
        $this->validate();
        $this->oStorage->flush();
    }
    
    /**
     * Gets the byte size of the content
     *
     * @return int
     */
    public function getSize()
    {
        $this->validate();
        return $this->oStorage->getSize();
    }
    
    /**
     * Gets the timestamp of the last cache reload
     *
     * @return int
     */
    public function getChangeTime()
    {
        return $this->oStorage->getChangeTime();
    }
    
    /**
     * Explicitly clears the cache
     */
    public function clear()
    {
        $this->oStorage->delete();
    }
    
    /**
     * Checks whether the cache is not expired
     *
     * @return booelan
     */
    public function isValid()
    {
        return (
            $this->oStorage->exists() &&
            $this->oStorage->getChangeTime() + $this->lifetime > time()
        );
    }
    
    /**
     * Regenerate the cache if the time has expired
     *
     * @return booelan
     */
    public function validate()
    {
        if (!$this->isValid()) {
            $this->oStorage->put($this->oSource->get());
        }
    }
    
    /**
     * Gets the content
     *
     * Provides cached content if a valid cached version exists.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }
    
}
