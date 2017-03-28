<?php

namespace PatroNet\Core\Content;


// TODO/FIXME: intl grapheme support
/**
 * Unicode string handler class
 */
class UnicodeString implements String
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
        return mb_strlen($this->str, 'utf-8');
    }
    
    /**
     * Extracts a substring of the text
     *
     * @return self
     */
    public function substr($start, $length = null)
    {
        return new self(mb_substr($this->str, $start, $length, 'utf-8'));
    }
    
    /**
     * Creates a reversed version of the text
     *
     * @return self
     */
    public function reverse()
    {
        $result = "";
        $len = $this->getLength();
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($this->str, $i, 1, 'utf-8');
            $result = $char.$result;
        }
        return $result;
    }
    
    /**
     * Creates an upper case version of the text
     *
     * @return self
     */
    public function toUpperCase()
    {
        return new self(mb_convert_case($this->str, \MB_CASE_UPPER, 'utf-8'));
    }
    
    /**
     * Creates a lower case version of the text
     *
     * @return self
     */
    public function toLowerCase()
    {
        return new self(mb_convert_case($this->str, \MB_CASE_LOWER, 'utf-8'));
    }
    
}
