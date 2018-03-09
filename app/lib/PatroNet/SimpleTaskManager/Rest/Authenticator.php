<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\Request;

interface Authenticator
{
    
    /**
     * @return Credentials
     */
    public function getCredentials(Request $oRequest);
    
}