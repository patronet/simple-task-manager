<?php

namespace PatroNet\Core\Content;


/**
 * Writable implementation which targets the standard output
 */
class EchoWritable
{
    
    /**
     * Prints the given content
     *
     * @param string $content
     */
    public function write($content)
    {
        echo $content;
    }
    
}

