<?php

namespace PatroNet\Core\Request;


/**
 * Interface for downloadable things
 */
interface Downloadable
{
    
    /**
     * Runs the download
     */
    public function download();
    
}
