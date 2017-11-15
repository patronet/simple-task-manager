<?php

namespace PatroNet\Core\Request;


/**
 * Interface for request routers
 */
interface Router
{
    
    /**
     * Tries to handle the given request
     *
     * @param \PatroNet\Core\Request\Request
     * @return\PatroNet\Core\Request\Response
     */
    public function handleRequest(Request $oRequest);
    
}
