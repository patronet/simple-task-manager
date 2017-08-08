<?php

namespace PatroNet\Core\Autoload;


/**
 * PSR0 like autoloader with custom pattern support
 */
class RegexPsr0Autoloader implements FileAutoloader
{
    use FileAutoloaderTrait;
    
    protected $searchPattern;
    protected $replacePattern;
    protected $replacement;
    
    /**
     * @param string $searchPattern
     * @param string $replacePattern
     * @param string|callable $replacement
     */
    public function __construct($searchPattern, $replacePattern, $replacement)
    {
        $this->searchPattern = $searchPattern;
        $this->replacePattern = $replacePattern;
        $this->replacement = $replacement;
    }
    
    /**
     * Gets the filename for the given class
     *
     * @param string $classname
     * @return string|null
     */
    public function getFile($classname)
    {
        if (!preg_match($this->searchPattern, $classname, $match)) {
            return null;
        }
        $namespace = $match[0];
        if (is_callable($this->replacement)) {
            $path = preg_replace_callback($this->replacePattern, $this->replacement, $namespace);
        } else {
            $path = preg_replace($this->replacePattern, $this->replacement, $namespace);
        }
        $oPsr0Autoloader = new Psr0Autoloader($namespace, $path);
        return $oPsr0Autoloader->getFile($classname);
    }
    
}
