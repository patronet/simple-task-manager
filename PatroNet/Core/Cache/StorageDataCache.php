<?php

namespace PatroNet\Core\Cache;

use PatroNet\Core\Content\Source;
use PatroNet\Core\Content\Storage;
use PatroNet\Core\Content\SourceTrait;
use PatroNet\Core\Common\DataSource;


/**
 * Base class for cached data sources
 */
abstract class StorageDataCache implements DataSource
{
    protected $oStorage;
    protected $lifetime;
    protected $data = null;
    
    /**
     * @param \PatroNet\Core\Content\Storage $oStorage
     * @param int $lifetime
     */
    public function __construct(Storage $oStorage, $lifetime)
    {
        $this->oStorage = $oStorage;
        $this->lifetime = $lifetime;
    }
    
    /**
     * Gets the data
     *
     * @return mixed
     */
    public function getData()
    {
        if(is_null($this->data))
        {
            if($this->isValid())
            {
                $this->data = unserialize($this->oStorage->get());
            } else {
                $this->data = $this->_getData();
                $this->oStorage->put(serialize($this->data));
            }
        }
                
        return $this->data;
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
     * Explicitly clears the cache
     */
    public function clear()
    {
        $this->oStorage->delete();
    }
    
    abstract protected function _getData();
}
