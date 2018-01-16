<?php

namespace PatroNet\Core\Template\Driver;

use PatroNet\Core\Content\SourceTrait;
use PatroNet\Core\Template\FileTemplate;


/**
 * Fake template class
 */
class FakeTemplate implements FileTemplate
{
    
    use SourceTrait;
    
    protected $file;
    
    // TODO
    protected $debugEnabled = false;
    
    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }
    
    /**
     * Gets the associated file name
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Enables or disables debug mode
     *
     * @param boolean $enable
     * @return self
     */
    public function setDebug($enable = true)
    {
        $this->debugEnabled = $enable;
        return $this;
    }
    
    /**
     * Assigns a template variable
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function assign($variable, $value)
    {
        return $this;
    }
    
    /**
     * Assigns multiple template variables
     *
     * @param array $variables
     * @return self
     */
    public function assignAll($variables)
    {
        return $this;
    }
    
    /**
     * Gets the text content
     *
     * @return string
     */
    public function get()
    {
        return "";
    }
    
}
