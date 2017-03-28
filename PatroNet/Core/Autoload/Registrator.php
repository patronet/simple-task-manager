<?php

namespace PatroNet\Core\Autoload;

// FIXME...
// TODO: remember registered autoloaders, and provide exists() and other aggregate methods

/**
 * Autoloader registrator
 */
class Registrator
{
    
    /**
     * Registers a class autoloader
     */
    static public function register(Autoloader $oAutoloader)
    {
        spl_autoload_register($oAutoloader);
    }
    
}
