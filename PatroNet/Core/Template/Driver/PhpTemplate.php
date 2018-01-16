<?php

namespace PatroNet\Core\Template\Driver;

use PatroNet\Core\Content\SourceTrait;
use PatroNet\Core\Template\FileTemplate;


/**
 * Template class to handle template files written in pure PHP
 */
class PhpTemplate implements FileTemplate
{
    
    use SourceTrait;
    
    protected $file;
    
    protected $variables = [];
    
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
        $this->variables[$variable] = $value;
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
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }
    
    /**
     * Gets the text content
     *
     * @return string
     */
    public function get()
    {
        ob_start();
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
    
    /**
     * Flushes the text content
     */
    public function flush()
    {
        extract($this->variables);
        try {
            @include($this->file);
        } catch (\Exception $oException) {
        }
    }
    
}
