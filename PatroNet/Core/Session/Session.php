<?php

namespace PatroNet\Core\Session;


/**
 * Interface for session access
 */
interface Session
{
    
    /**
     * Gets the current user of the session if any
     *
     * @return Session|null
     */
    public function getUser();
    
    /**
     * Gets a session variable
     *
     * @param string $varname
     * @return mixed
     */
    public function get($varname);
    
    /**
     * Sets a session variable
     *
     * @param string $varname
     * @param mixed $value
     */
    public function set($varname, $value);
    
    /**
     * Deletes a session variable
     *
     * @param string $varname
     */
    public function del($varname);
    
    // TODO
    
}
