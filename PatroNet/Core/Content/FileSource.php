<?php

namespace PatroNet\Core\Content;


/**
 * File based content source
 */
class FileSource implements Source
{
    
    use SourceTrait;
    
    protected $filename;
    
    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    
    /**
     * Gets the path of the file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->filename;
    }
    
    /**
     * Gets the content of the file
     *
     * @return string
     */
    public function get()
    {
        clearstatcache();
        if (file_exists($this->filename)) {
            return file_get_contents($this->filename);
        } else {
            return "";
        }
    }
    
    /**
     * Prints the content of the file
     */
    public function flush()
    {
        clearstatcache();
        if (file_exists($this->filename)) {
            readfile($this->filename);
        }
    }
    
}
