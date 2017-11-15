<?php

namespace PatroNet\Core\Autoload;


/**
 * Path resolution standard autoloader
 */
class PathAutoloader implements FileAutoloader
{
    use FileAutoloaderTrait;
    
    protected $path;
    protected $namespace;
    protected $underscoreToNested;
    
    /**
     * @param string $namespace
     * @param string $path
     * @param boolean $underscoreToNested
     */
    public function __construct($namespace, $path, $underscoreToNested = true)
    {
        $this->path = preg_replace('#/$#', '', $path);
        $this->namespace = preg_replace('#(^\\\\|\\\\$)#', '', $namespace);
        $this->underscoreToNested = $underscoreToNested;
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
        
        $relativeName = substr($classname, $nslen + 1);
        $subPath = str_replace("\\", "/", $relativeName);
        
        if ($this->underscoreToNested) {
            $subPath = preg_replace('#/_([^_].*)?$#', '', $subPath);
        }
        
        $file = $this->path . "/" . $subPath . ".php";
        return $file;
    }
    
}
