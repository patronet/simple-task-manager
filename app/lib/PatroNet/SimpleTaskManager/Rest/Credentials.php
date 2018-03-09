<?php

namespace PatroNet\SimpleTaskManager\Rest;

class Credentials
{
    
    private $loginData;
    
    private $userData;
    
    public function __construct($loginData, $userData) {
        $this->loginData = $loginData;
        $this->userData = $userData;
    }
    
    /**
     * @return boolean
     * */
    public function isAuthorized()
    {
        return !is_null($this->userData);
    }
    
    /**
     * @return array
     */
    public function getLoginData()
    {
        return $this->authData;
    }
    
    /**
     * @return array
     */
    public function getUserData()
    {
        return $this->userData;
    }
    
}