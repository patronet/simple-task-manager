<?php

namespace PatroNet\Core\Content;

// FIXME/TODO: prepend/append, method chaining (?)

/**
 * Composite from content sources
 */
class ContentSourceList implements Source
{
    
    use SourceTrait;
    
    protected $contentSources;
    
    protected $separator;
    
    /**
     * @param \PatroNet\Core\Content\Source[] $contentSources
     * @param string $separator
     */
    public function __construct($contentSources = [], $separator = "")
    {
        $this->contentSources = $contentSources;
        $this->separator = $separator;
    }
    
    /**
     * Adds a new content source to the list
     *
     * @param \PatroNet\Core\Content\Source $oContentSource
     */
    public function add(Source $oContentSource)
    {
        $this->contentSources[] = $oContentSource;
    }
    
    /**
     * Gets the composite content
     *
     * @return string
     */
    public function get()
    {
        return implode($this->separator, array_map(function(Source $oContentSource){
            return $oContentSource->get();
        }, $this->contentSources));
    }
    
    /**
     * Prints the composite content
     */
    public function flush()
    {
        $first = true;
        foreach ($this->contentSources as $oContentSource) {
            if (!$first) {
                echo $this->separator;
            } else {
                $first = false;
            }
            $oContentSource->flush();
        }
    }
    
}
