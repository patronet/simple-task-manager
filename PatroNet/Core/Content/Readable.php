<?php

namespace PatroNet\Core\Content;


/**
 * Interface for readable sream handlers
 */
interface Readable
{
    
    /**
     * Reads the next part
     *
     * @return string|null
     */
    public function read();
    
}
