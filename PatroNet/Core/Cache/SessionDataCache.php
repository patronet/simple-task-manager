<?php

namespace PatroNet\Core\Cache;

use PatroNet\Core\Content\Source;
use PatroNet\Core\Content\Storage;
use PatroNet\Core\Content\SourceTrait;


/**
 * Base class for cached data sources
 */
class SessionDataCache implements DataCache
{
    protected $oSession;
    protected $name;
    protected $lifetime;
    protected $data = null;
    
    /**
     * @param \PatroNet\Core\Session\Session $oSession
     * @param string $name
     * @param int $lifetime
     */
    public function __construct($oSession, $name, $lifetime)
    {
        $this->oSession = $oSession;
        $this->name = $name;
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
                $this->data = $this->oSession->get($this->name);
            } else {
                $this->data = $this->_getData();
                $this->setData($this->data);
            }
        }
                
        return $this->data;
    }
    
    /**
     * Sets the data
     *
     * @param array $data_array
     */
    public function setData($data_array)
    {
        $this->data = $data_array;
        
        $this->oSession->set($this->name, ["timestamp" => time(), "data" => $this->data]);
    }
    
    /**
     * Checks whether the cache is not expired
     *
     * @return booelan
     */
    public function isValid()
    {
        $session_item = $this->oSession->get($this->name);
        
        return (
            $session_item &&
            $session_item["timestamp"] + $this->lifetime > time()
        );
    }
    
    /**
     * Explicitly clears the cache
     */
    public function clear()
    {
        $this->oSession->del($this->name);
    }
}
