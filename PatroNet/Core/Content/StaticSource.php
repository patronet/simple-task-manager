<?php

namespace PatroNet\Core\Content;


/**
 * Basic source implementation
 */
class StaticSource implements Source
{
    
    use SourceTrait;
    
    protected $content;
    
    /**
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }
    
    /**
     * Gets the content
     *
     * @return string
     */
    public function get()
    {
        return $this->content;
    }
    
    /**
     * Prints the content
     */
    public function flush()
    {
        echo $this->content;
    }
    
}
