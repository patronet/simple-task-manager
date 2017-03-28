<?php

namespace PatroNet\Core\Content;

// TODO: byte/unicode...

/**
 * Interface for buffer handlers
 */
interface Buffer extends Readable, Writable
{
    
    /**
     * Sets maximum byte size of read content parts
     *
     * @param int $readSize
     */
    public function setReadSize($readSize);
    
    /**
     * Reads entire remaining content
     *
     * @return string
     */
    public function readAll();
    
    /**
     * Checks whether the buffer is empty
     *
     * @return boolean
     */
    public function isEmpty();
    
}

