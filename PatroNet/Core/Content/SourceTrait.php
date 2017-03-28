<?php

namespace PatroNet\Core\Content;


/**
 * Default methods for content sources
 */
trait SourceTrait
{
    
    /**
     * Gets the content
     *
     * @return string
     */
    abstract public function get();
    
    /**
     * Prints the content
     */
    public function flush()
    {
        echo $this->get();
    }
    
    /**
     * Gets the content
     *
     * Same as get().
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }
    
}
