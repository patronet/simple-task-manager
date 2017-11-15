<?php

namespace PatroNet\Core\Content;


/**
 * Interface for writable sream handlers
 */
interface Writable
{
    
    /**
     * Sends content into the stream
     *
     * @param string $content
     */
    public function write($content);
    
}

