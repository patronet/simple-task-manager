<?php

namespace PatroNet\Core\Content;


/**
 * File based storage implementation
 */
class FileStorage implements Storage
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
    
    /**
     * Gets the filesize in bytes
     *
     * @return int
     */
    public function getSize()
    {
        clearstatcache();
        if (file_exists($this->filename)) {
            return filesize($this->filename);
        } else {
            return 0;
        }
    }
    
    /**
     * Gets the last modification time of the file
     *
     * @return int
     */
    public function getChangeTime()
    {
        clearstatcache();
        if (file_exists($this->filename)) {
            return filemtime($this->filename);
        } else {
            return 0;
        }
    }
    
    /**
     * Sets the file's content
     */
    public function put($content)
    {
        file_put_contents($this->filename, $content);
    }
    
    /**
     * Checks whether the file exists
     *
     * @return boolean
     */
    public function exists()
    {
        clearstatcache();
        return file_exists($this->filename);
    }
    
    /**
     * Deletes the file
     *
     * @return boolean
     */
    public function delete()
    {
        clearstatcache();
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
    
}
