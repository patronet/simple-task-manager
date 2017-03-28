<?php

namespace PatroNet\Core\Content;


/**
 * Interface for general text handler classes
 */
interface String extends Container
{
    
    /**
     * Gets the byte size of the text
     *
     * @return int
     */
    public function getSize();
    
    /**
     * Gets the character length of the text
     *
     * @return int
     */
    public function getLength();
    
    /**
     * Extracts a substring of the text
     *
     * @return self
     */
    public function substr($start, $length = null);
    
    /**
     * Creates a reversed version of the text
     *
     * @return self
     */
    public function reverse();
    
    /**
     * Creates an upper case version of the text
     *
     * @return self
     */
    public function toUpperCase();
    
    /**
     * Creates a lower case version of the text
     *
     * @return self
     */
    public function toLowerCase();
    
    // TODO
    
}
