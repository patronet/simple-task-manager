<?php

namespace PatroNet\Core\Autoload;


/**
 * Callback based autoloader
 */
class CallbackAutoloader implements Autoloader
{
    
    use AutoloaderTrait;
    
    protected $callback;
    
    
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
    
    public function load($classname)
    {
        $callback = $this->callback;
        return $callback($classname);
    }
    
    protected function _exists($classname) {
        return $this->load($classname);
    }
    
}
