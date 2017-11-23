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
        
        if (isset($getData["_path"])) {
            $path = $getData["_path"];
            unset($getData["_path"]);
        }
        
        if (isset($getData["_method"])) {
            $method = $getData["_method"];
            unset($getData["_method"]);
        }
        
        // TODO: merge raw post json into data
        
        return $this->oJsonService->handleJsonQuery($path, $method, $data, null);
    }
    
}