<?php

namespace PatroNet\Core\Autoload;


/**
 * Interface for file based autoloaders
 */
interface FileAutoloader extends Autoloader {
    
    /**
     * Gets the filename for the given class
     *
     * @param string $classname
     * @return string|null
     */
    public function getFile($classname);
    
}
