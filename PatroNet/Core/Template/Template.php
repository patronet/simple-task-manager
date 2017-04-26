<?php

namespace PatroNet\Core\Template;

use PatroNet\Core\Content\Source;


/**
 * Interface for template classes
 */
interface Template extends Source
{
    
    /**
     * Enables or disables debug mode
     *
     * @param boolean $enable
     * @return self;
     */
    public function setDebug($enable = true);
    
    /**
     * Assigns a variable to the template
     *
     * @param string $variable
     * @param mixed $value
     * @return self;
     */
    public function assign($variable, $value);
    
    /**
     * Assigns multiple variables to the template
     *
     * @param array $variables
     * @return self;
     */
    public function assignAll($variables);
    
}

