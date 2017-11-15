<?php

namespace PatroNet\Core\Autoload;


/**
 * Path resolution autoloader with custom pattern support
 */
class RegexPathAutoloader implements FileAutoloader
{
    use FileAutoloaderTrait;
    
    protected $searchPattern;
    protected $replacePattern;
    protected $replacement;
    protected $underscoreToNested;
    
    /**
     * @param string $searchPattern
     * @param string $replacePattern
     * @param string|callable $replacement
     * @param boolean $underscoreToNested
     */
    public function __construct($searchPattern, $replacePattern, $replacement, $underscoreToNested = true)
    {
        $this->searchPattern = $searchPattern;
        $this->replacePattern = $replacePattern;
        $this->replacement = $replacement;
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
        if (!preg_match($this->searchPattern, $classname, $match)) {
            return null;
        }
        $namespace = $match[0];
        if (is_callable($this->replacement)) {
            $path = preg_replace_callback($this->replacePattern, $this->replacement, $namespace);
        } else {
            $path = preg_replace($this->replacePattern, $this->replacement, $namespace);
        }
        $oPathAutoloader = new PathAutoloader($namespace, $path, $this->underscoreToNested);
        return $oPathAutoloader->getFile($classname);
    }
    
}
