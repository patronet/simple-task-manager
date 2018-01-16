<?php

namespace PatroNet\Core\Common;


/**
 * Interface for accessing resources
 */
interface Resource
{
    
    /**
     * Tries to open the resource
     *
     * @return boolean
     */
    public function open();
    
    // TODO/FIXME: close($forceLeave = false)
    /**
     * Tries to close the resource
     *
     * @return boolean
     */
    public function close();
    
    // TODO/FIXME: desc.: Returns true if the resource is open and not leaved
    /**
     * Checks whether the resource is open
     *
     * @return boolean
     */
    public function isOpen();
    
}
