<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\Response;

interface JsonService
{
    
    /**
     * @param string $path
     * @param string $method
     * @param mixed $data
     * @param mixed $oCredential
     * @return Response
     */
    public function handleJsonQuery($path, $method, $data, $oCredential);
    
}