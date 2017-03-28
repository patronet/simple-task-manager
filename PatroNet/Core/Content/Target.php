<?php

namespace PatroNet\Core\Content;

/**
 * Interface for content targets
 */
interface Target
{
    
    /**
     * Puts the content to the target
     *
     * @param string $content
     */
    public function put($content);
    
}

