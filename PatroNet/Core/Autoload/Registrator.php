<?php

namespace PatroNet\Core\Autoload;

// FIXME...
// TODO: remember registered autoloaders
//   TODO: provide exists() and aggregate methods
//   TODO: $Autoloader->getRootNamespace() -> sort by root namespace (desc)

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
