<?php

namespace PatroNet\Core\Content;


/**
 * Buffer decorator with unicode support
 */
class UnicodeBufferDecorator implements Buffer
{
    
    const USE_TEXTLENGTH = "textlength";
    
    const USE_BYTESIZE = "bytesize";
    
    protected $readSize;
    
    protected $readLength;
    
    protected $mode;
    
    protected $preparedContent;
    
    protected $oBuffer;
    
    /**
     * @param \PatroNet\Core\Content\Buffer $oBuffer
     * @param int $readLength
     */
    public function __construct(Buffer $oBuffer, $readLength = 1024)
    {
        $this->oBuffer = $oBuffer;
        $this->preparedContent = "";
        $this->setReadLength($readLength);
    }
    
    /**
     * Sets maximum byte size of read content parts
     *
     * @param int $readSize
     */
    public function setReadSize($readSize)
    {
        $this->mode = self::USE_BYTESIZE;
        $this->readSize = $readSize;
    }
    
    /**
     * Sets maximum character length of read content parts
     *
     * @param int $readSize
     */
    public function setReadLength($readLength)
    {
        $this->readLength = $readLength;
    }
    
    /**
     * Appends content to the end of buffer's content
     *
     * @param string $content
     */
    public function write($content)
    {
        $this->oBuffer->write($content);
    }
    
    /**
     * Reads a part from the buffer
     *
     * If the encoding of the buffer's content is UTF-8,
     * then it is guaranteed that the read part is also a valid UTF-8 string.
     *
     * @param int|null $size
     * @return string
     */
    public function read($size = null)
    {
        // TODO
    }
    
    /**
     * Reads entire remaining content
     *
     * @return string
     */
    public function readAll()
    {
        return $this->preparedContent . $this->oBuffer->readAll();
    }
    
    /**
     * Checks whether the buffer is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (strlen($this->preparedContent) == 0 && $this->oBuffer->isEmpty());
    }
    
}

