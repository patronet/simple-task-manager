<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\Controller;
use PatroNet\Core\Request\Request;
use PatroNet\Core\Request\Response;

class DefaultJsonServiceControllerAdapter implements Controller
{
    
    private $oJsonService;
    
    private $oAuthenticator;
    
    public function __construct(JsonService $oJsonService, Authenticator $oAuthenticator = null)
    {
        $this->oJsonService = $oJsonService;
        $this->oAuthenticator = $oAuthenticator;
    }
    
    /**
     * @see \PatroNet\Core\Request\Controller::handle()
     * @param Request
     * @return Response
     */
    public function handle(Request $oRequest)
    {
        $path = $oRequest->getPath();
        $method = $oRequest->getMethod();
        $data = array_merge($oRequest->getGet(), $oRequest->getPost()); // XXX
        return $this->oJsonService->handleJsonQuery($path, $method, $data, $this->oAuthenticator->getCredentials($oRequest));
    }
    
}