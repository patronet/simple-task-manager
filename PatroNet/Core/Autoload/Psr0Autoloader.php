<?php

namespace PatroNet\Core\Autoload;


/**
 * PSR0 standard autoloader
 */
class Psr0Autoloader implements FileAutoloader
{
    use FileAutoloaderTrait;
    
    protected $path;
    
    protected $namespace;
    
    /**
     * @param string $namespace
     * @param string $path
     */
    public function __construct($namespace, $path)
    {
        $this->path = preg_replace('#/$#', '', $path);
        $this->namespace = preg_replace('#(^\\\\|\\\\$)#', '', $namespace);
    }
    
    /**
     * Gets the filename for the given class
     *
     * @param string $classname
     * @return string|null
     */
    public function getFile($classname)
    {
        $nslen = strlen($this->namespace);
        $begin = substr($classname, 0, $nslen);
        if ($begin !== $this->namespace) {
            return null;
        }
        
        $relativename = substr($classname, $nslen + 1);
        $file = $this->path . "/" . str_replace("\\", "/", $relativename) . ".php";
        return $file;
    }
    
}
