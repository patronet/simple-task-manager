<?php

namespace PatroNet\Core\Content;


/**
 * Source implementation with user defined code generator callback
 */
class OutputClosureSource implements Source
{
    
    use SourceTrait;
    
    protected $oClosure;
    
    /**
     * @param \Closure $oClosure
     */
    public function __construct(\Closure $oClosure)
    {
        $this->oClosure = $oClosure;
    }
    
    /**
     * Gets the content
     *
     * @return string
     */
    public function get()
    {
        ob_start();
        $this->flush();
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
    
    /**
     * Prints the content
     */
    public function flush()
    {
        call_user_func($this->oClosure);
    }
    
}
