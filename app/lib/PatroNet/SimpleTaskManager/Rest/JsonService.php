<?php

namespace PatroNet\SimpleTaskManager\Rest;

interface JsonService
{
    
    public function handleJsonQuery($path, $method, $data, $oCredential);
    
}