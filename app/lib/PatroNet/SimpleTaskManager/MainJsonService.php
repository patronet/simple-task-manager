<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\SimpleTaskManager\Rest\JsonService;
use PatroNet\Core\Request\ResponseBuilder;
use PatroNet\SimpleTaskManager\Model\Project;

class MainJsonService implements JsonService
{
    
    public function handleJsonQuery($path, $method, $data, $oCredential)
    {
        // TODO: [Abstract]RoutingJsonService
        
        // XXX
        return (new ResponseBuilder())->initJson(Project::getRepository()->getJsonDataList(["project_id" => ["in", [1, 4, 2]]], ["label" => "asc"], 2))->build();
    }

}