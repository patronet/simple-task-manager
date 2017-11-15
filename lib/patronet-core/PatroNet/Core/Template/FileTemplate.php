<?php

namespace PatroNet\Core\Template;


/**
 * Interface for file based templates
 */
interface FileTemplate extends Template
{
    
    /**
     * Gets the template file's path
     */
    public function getFile();
    
}

