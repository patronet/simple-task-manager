<?php

namespace PatroNet\Core\Content;


/**
 * Interface for containers with existing stored content
 */
interface Storage extends Container, StoredSource
{
    
    /**
     * Checks whether the content resource exists
     *
     * @return boolean
     */
    public function exists();
    
    /**
     * Deletes the content resource
     *
     * @return boolean
     */
    public function delete();
    
}
