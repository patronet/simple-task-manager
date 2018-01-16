<?php

namespace PatroNet\Core\Session;


/**
 * Session manager
 */
class SessionManager
{
    protected $sessions = [];
    
    public function __construct()
    {
    }
    
    /**
     * Registers a session
     *
     * @param \PatroNet\Core\Session\Session $oSession
     * @param string $name
     */
    public function register(Session $oSession, $name = "default") 
    {
        $this->sessions[$name] = $oSession;
    }
    
    /**
     * Gets a registered session object
     *
     * @param string $name
     * @return \PatroNet\Core\Session\Session|null
     */
    public function get($name = "default")
    {
        if (isset($this->sessions[$name])) {
            return $this->sessions[$name];
        } else {
            return null;
        }
    }
    
}
