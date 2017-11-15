<?php

namespace PatroNet\Core\Autoload;

/**
 * Common methods for file based autoloaders
 */
trait FileAutoloaderTrait
{
    use AutoloaderTrait;
    
    /**
     * Tries to load the given class
     *
     * @param string $classname
     * @return boolean
     */
    public function load($classname)
    {
        $file = $this->getFile($classname);
        if (!is_null($file) && file_exists($file)) {
            require_once($file);
            return class_exists($classname, false);
        } else {
            return false;
        }
    }
    
    public function _exists($classname)
    {
        $file = $this->getFile();
        return (!is_null($file) && file_exists($file));
    }
    
    /**
     * Gets the filename for the given class
     *
     * @param string $classname
     * @return string|null
     */
    abstract public function getFile($classname);
    
}
