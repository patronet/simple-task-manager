<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\Controller;
use PatroNet\Core\Request\Request;
use PatroNet\Core\Request\Response;

class DefaultJsonServiceControllerAdapter implements Controller
{
    
    private $oJsonService;
    
    public function __construct(JsonService $oJsonService)
    {
        $this->oJsonService = $oJsonService;
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
        $data = $oRequest->getGet();
        
        // TODO: data? post?
        
        return $this->oJsonService->handleJsonQuery($path, $method, $data, null);
    }
    
}