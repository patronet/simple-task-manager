<?php

namespace PatroNet\Core\Request;


/**
 * Interface for controllers
 */
interface Controller
{
    
    /**
     * Handles request and return with a response
     * 
     * @param Request $oRequest
     * @return Response
     */
    public function handle(Request $oRequest);
    
}
