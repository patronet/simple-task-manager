<?php

namespace PatroNet\Core\Template\Driver;

use \PatroNet\Core\Content\SourceTrait;
use \PatroNet\Core\Template\FileTemplate;


/**
 * Template class for the Blade templating system
 */
class BladeTemplate implements FileTemplate
{
    
    use SourceTrait;
    
    protected $file;
    
    // TODO
    protected $debugEnabled = false;
    
    /**
     * @param string $file
     */
    public function __construct($file) // TODO: cache dir etc.
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
        // TODO
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
        // TODO
        return $this;
    }
    
    /**
     * Gets the text content
     *
     * @return string
     */
    public function get()
    {
        // TODO
        return "";
    }
    
    /**
     * Flushes the text content
     */
    public function flush()
    {
        // TODO
    }
    
}
