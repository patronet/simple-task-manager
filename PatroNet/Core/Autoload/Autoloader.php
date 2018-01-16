<?php

namespace PatroNet\Core\Autoload;


/**
 * Interface for autoloader classes
 */
interface Autoloader
{
    
    /**
     * Tries to load the given class
     *
     * @param string $classname
     * @return boolean
     */
    public function __invoke($classname);
    
    /**
     * Tries to load the given class
     *
     * @param string $classname
     * @return boolean
     */
    public function load($classname);
    
    /**
     * Check whether the given class exists
     *
     * It must return true if the class already exists, or if its resource exists.
     * It should not load the class and it should not execute expensive operations.
     *
     * @param string $classname
     * @return boolean
     */
    public function exists($classname);
    
}
