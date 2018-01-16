<?php

namespace PatroNet\Core\Content;


/**
 * Binary string handler class
 */
class BinaryString implements String
{
    
    use SourceTrait;
    
    protected $str;
    
    /**
     * @param string $str
     */
    public function __construct($str)
    {
        $this->str = $str;
    }
    
    /**
     * Gets the text
     *
     * @return string
     */
    public function get()
    {
        return $this->str;
    }
    
    /**
     * Prints the text
     */
    public function flush()
    {
        echo $this->str;
    }
    
    /**
     * Sets the text
     *
     * @param string $str
     */
    public function put($str)
    {
        $this->str = $str;
    }
    
    /**
     * Gets the byte size of the text
     *
     * @return int
     */
    public function getSize()
    {
        return strlen($this->str);
    }
    
    /**
     * Gets the character length of the text
     *
     * @return int
     */
    public function getLength()
    {
        return strlen($this->str);
    }
    
    /**
     * Extracts a substring of the text
     *
     * @return self
     */
    public function substr($start, $length = null)
    {
        if (is_null($length)) {
            return new self(substr($this->str, $start));
        } else {
            return new self(substr($this->str, $start, $length));
        }
    }
    
    /**
     * Creates a reversed version of the text
     *
     * @return self
     */
    public function reverse()
    {
        return new self(strrev($this->str));
    }
    
    /**
     * Creates an upper case version of the text
     *
     * @return self
     */
    public function toUpperCase()
    {
        return new self(strtoupper($this->str));
    }
    
    /**
     * Creates a lower case version of the text
     *
     * @return self
     */
    public function toLowerCase()
    {
        return new self(strtolower($this->str));
    }
    
}
