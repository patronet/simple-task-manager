<?php

namespace PatroNet\Core\Content;


/**
 * Default buffer implementation
 */
class MemoryBuffer implements Buffer
{
    
    protected $readSize;
    
    protected $buffer;
    
    /**
     * @param int $readSize
     */
    public function __construct($readSize = 1024)
    {
        $this->readSize = $readSize;
    }
    
    /**
     * Sets maximum byte size of read content parts
     *
     * @param int $readSize
     */
    public function setReadSize($readSize)
    {
        $this->readSize = $readSize;
    }
    
    /**
     * Appends content to the end of buffer's content
     *
     * @param string $content
     */
    public function write($content)
    {
        $this->buffer .= $content;
    }
    
    /**
     * Reads a part from the buffer
     *
     * @param int|null $size
     * @return string
     */
    public function read($size = null)
    {
        if (is_null($size)) {
            $size = $this->readSize;
        }
        $size = min(strlen($this->buffer), $size);
        if ($size == 0) {
            return "";
        }
        $result = substr($this->buffer, 0, $size);
        $this->buffer = substr($this->buffer, $size);
        return $result;
    }
    
    /**
     * Reads entire remaining content
     *
     * @return string
     */
    public function readAll()
    {
        $result = $this->buffer;
        $this->buffer = "";
        return $result;
    }
    
    /**
     * Checks whether the buffer is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (strlen($this->buffer) == 0);
    }
    
}

