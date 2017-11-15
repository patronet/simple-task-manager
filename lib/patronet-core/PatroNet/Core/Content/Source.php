<?php

namespace PatroNet\Core\Content;


/**
 * Interface for content sources
 */
interface Source
{
    
    /**
     * Gets the content
     *
     * @return string
     */
    public function get();
    
    /**
     * Prints the content
     */
    public function flush();
    
    /**
     * Gets the content
     *
     * Same as get().
     *
     * @return string
     */
    public function __toString();
    
}
