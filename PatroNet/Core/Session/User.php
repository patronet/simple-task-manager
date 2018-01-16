<?php

namespace PatroNet\Core\Session;


// TODO: Entity
/**
 * Interface for user objects
 */
interface User
{
    
    /**
     * Gets the ID of the user
     *
     * @return int
     */
    public function getId();
    
    /**
     * Gets the username of the user
     *
     * @return string
     */
    public function getUsername();
    
    // TODO
    
}
