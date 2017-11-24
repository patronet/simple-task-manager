<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\SimpleTaskManager\Rest\RoutingJsonService;
use PatroNet\SimpleTaskManager\Rest\JsonService\_ExactPathRoute;
use PatroNet\Core\Request\ResponseBuilder;
use PatroNet\SimpleTaskManager\Model\Project;
use PatroNet\SimpleTaskManager\Rest\JsonService\_MatchingPathRoute;

class MainJsonService extends RoutingJsonService {
    
    public function __construct() {
        
        $this->addRoute(new _ExactPathRoute("get", "some/route", function ($method, $data, $oCredential) {
            return
                (new ResponseBuilder())
                ->initJson(Project::getRepository()->getJsonDataList(
                    ["project_id" => ["in", [1, 4, 2]]],
                    ["label" => "asc"],
                    2
                    ))
                ->build()
            ;
        }));
            
        $this->addRoute(new _MatchingPathRoute("all", '@^foo/(?P<fooId>\\d+)$@', function ($match, $method, $data, $oCredential) {
            return
                (new ResponseBuilder())
                ->initJson(["fooId" => $match["fooId"]])
                ->build()
            ;
        }));
        
    } 
    
}