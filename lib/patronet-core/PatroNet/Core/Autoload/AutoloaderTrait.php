<?php

namespace PatroNet\Core\Autoload;


/**
 * Common methods for autoloader classes
 */
trait AutoloaderTrait
{
    
    /**
     * Tries to load the given class
     *
     * @param string $classname
     * @return boolean
     */
    public function __invoke($classname)
    {
        return $this->load($classname);
    }
    
    /**
     * Check whether the given class exists
     *
     * {@inheritdoc}
     *
     * @param string $classname
     * @return boolean
     */
    public function exists($classname)
    {
        if (class_exists($classname, false)) {
            return true;
        } else {
            return $this->_exists($classname);
        }
    }
    
    abstract protected function _exists($classname);
    
}
