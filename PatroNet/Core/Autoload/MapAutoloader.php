<?php

namespace PatroNet\Core\Autoload;


/**
 * Autoloader with class to file association map
 */
class MapAutoloader implements FileAutoloader
{
    use FileAutoloaderTrait;
    
    protected $classmap;
    
    /**
     * @param array $classmap
     */
    public function __construct(array $classmap = [])
    {
        $this->classmap = $classmap;
    }
    
    /**
     * Gets the filename for the given class
     *
     * @param string $classname
     * @return string|null
     */
    public function getFile($class)
    {
        if (isset($this->classmap[$class])) {
            $file = $this->classmap[$class];
			if (!is_null($this->rootDirectory)) {
			    $file = str_replace('~', $this->rootDirectory, $file);
			}
			if (is_file($file)) {
				return $file;
			}
        }
        return null;
    }
    
    /**
     * Associates a class with a file
     *
     * @param string $class
     * @param string $file
     */
    public function register($class, $file)
    {
        $this->classmap[$class] = $file;
    }
    
    /**
     * Add multiple class to file association
     *
     * @param array $classmap
     */
    public function registerAll(array $classmap)
    {
        $this->classmap = array_merge($this->classmap, $classmap);
    }
    
}
