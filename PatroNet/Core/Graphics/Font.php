<?php

namespace PatroNet\Core\Graphics;


// FIXME
/**
 * Class represents a font stored in file
 */
class Font
{
    
    protected $file;
    
    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }
    
    /**
     * Gets the font file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
    
}
