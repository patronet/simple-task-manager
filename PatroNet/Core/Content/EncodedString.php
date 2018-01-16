<?php

namespace PatroNet\Core\Content;


// TODO/FIXME: intl grapheme support
/**
 * Text handler class with multiple encoding support
 */
class EncodedString implements String
{
    
    use SourceTrait;
    
    protected $str;
    
    /**
     * @param string $str
     * @param string $encoding
     */
    public function __construct($str, $encoding)
    {
        $this->str = $str;
        $this->encoding = $encoding;
    }
    
    /**
     * Gets the current encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
    
    /**
     * Creates a new instance with the same text with the given encoding
     *
     * @return string
     */
    public function convertEncoding($newEncoding)
    {
        return new self(mb_convert_encoding($this->str, $newEncoding, $this->encoding), $newEncoding);
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
        return mb_strlen($this->str, $this->encoding);
    }
    
    /**
     * Extracts a substring of the text
     *
     * @return self
     */
    public function substr($start, $length = null)
    {
        return new self(mb_substr($this->str, $start, $length, $this->encoding), $this->encoding);
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
            $char = mb_substr($this->str, $i, 1, $this->encoding);
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
        return new self(mb_convert_case($this->str, \MB_CASE_UPPER, $this->encoding), $this->encoding);
    }
    
    /**
     * Creates a lower case version of the text
     *
     * @return self
     */
    public function toLowerCase()
    {
        return new self(mb_convert_case($this->str, \MB_CASE_LOWER, $this->encoding), $this->encoding);
    }
    
}
