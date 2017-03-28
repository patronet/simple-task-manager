<?php

namespace PatroNet\Core\Event;


/**
 * Represents an event
 */
class Event
{
    
    protected $name;
    
    protected $description;
    
    protected $eventData;
    
    protected $userData;
    
    protected $cancelled = false;
    
    /**
     * @param string $name
     * @param string $description
     * @param array $eventData
     * @param array $userData
     */
    public function __construct($name, $description = "", $eventData = array(), $userData = array())
    {
        $this->name = $name;
        $this->description = $description;
        $this->eventData = $eventData;
        $this->userData = $userData;
    }
    
    /**
     * Gets the type of the event
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Gets the description of the event
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Gets data associated with the event
     *
     * @return array
     */
    public function getEventData()
    {
        return $this->eventData;
    }
    
    /**
     * Gets data associated with the event by the user
     *
     * @return array
     */
    public function getUserData()
    {
        return $this->userData;
    }
    
    /**
     * Cancels the event
     */
    public function cancel()
    {
        $this->cancelled = true;
    }
    
    /**
     * Checks whether the event is cancelled
     *
     * @return boolean
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }
    
}

