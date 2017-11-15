<?php

namespace PatroNet\Core\Content;


/**
 * Interfaces for content sources that has existing stored content
 */
interface StoredSource extends Source
{
    
    /**
     * Gets content's byte size
     *
     * @return int
     */
    public function getSize();
    
    /**
     * Gets the time of the last modification
     *
     * @return int
     */
    public function getChangeTime();
    
}
