<?php

namespace PatroNet\Core\Template;

use PatroNet\Core\Content\SourceTrait;


/**
 * Simple sample template implementation
 */
class SampleTemplate implements Template
{

    use SourceTrait;
    
    protected $source;
    
    protected $variables = [];
    
    protected $debugEnabled = false;
    
    /**
     * @param string $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }
    
    /**
     * Returns with the generated content
     *
     * @return string
     */
    public function get()
    {
        $source = $this->source;
        if (!is_string($source)) {
            $source = "" . $source;
        }
        return preg_replace_callback('#\\{(\\w+)\\}#', function ($match) {
            if (isset($this->variables[$match[1]])) {
                return $this->variables[$match[1]];
            }
            return "?";
        }, $source);
    }
    
    /**
     * Prints the generated content
     */
    public function flush()
    {
        echo $this->get();
    }
    
    /**
     * Enables or disables debug mode
     *
     * @param boolean $enable
     * @return self;
     */
    public function setDebug($enable = true)
    {
        $this->debugEnabled = $enable;
        return $this;
    }
    
    /**
     * Assigns a variable to the template
     *
     * @param string $variable
     * @param mixed $value
     * @return self;
     */
    public function assign($variable, $value)
    {
        $this->variables[$variable] = $value;
        return $this;
    }
    
    /**
     * Assigns multiple variables to the template
     *
     * @param array $variables
     * @return self;
     */
    public function assignAll($variables)
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }
    
}

