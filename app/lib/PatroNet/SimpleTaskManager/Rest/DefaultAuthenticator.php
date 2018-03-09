<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\Request;
use PatroNet\SimpleTaskManager\Model\User;

class DefaultAuthenticator implements Authenticator
{
    
    public function getCredentials(Request $oRequest)
    {
        $loginData = [];
        $loginData["username"] = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : "";
        $loginData["password"] = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : "";
        
        $userData = null;
        if (!empty($loginData["username"]) && !empty($loginData["password"])) {
            $oUser = User::getRepository()->getByUsername($loginData["username"]);
            if (!empty($oUser)) {
                if ($oUser->checkPassword($loginData["password"])) {
                    $userData = $oUser->toJsonData(null);
                }
            }
        }
        
        return new Credentials($loginData, $userData);
    }
    
}