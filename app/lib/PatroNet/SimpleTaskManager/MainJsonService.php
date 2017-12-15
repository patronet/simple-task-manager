<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\SimpleTaskManager\Rest\RoutingJsonService;
use PatroNet\SimpleTaskManager\Rest\JsonService\_ExactPathRoute;
use PatroNet\Core\Request\ResponseBuilder;
use PatroNet\SimpleTaskManager\Model\Project;
use PatroNet\SimpleTaskManager\Rest\JsonService\_MatchingPathRoute;
use PatroNet\SimpleTaskManager\Rest\RepositoryResponseHelper;
use PatroNet\SimpleTaskManager\Model\Sprint;

class MainJsonService extends RoutingJsonService {
    
    public function __construct() {
        
        $this->addRoute(new _ExactPathRoute("get", "projects", function ($method, $data, $oCredential) {
            return (new RepositoryResponseHelper(Project::getRepository(), 20))->getDefaultListResponse($data, $oCredential);
        }));
        
        $this->addRoute(new _ExactPathRoute("get", "sprints", function ($method, $data, $oCredential) {
            return (new RepositoryResponseHelper(Sprint::getRepository(), 20))->getDefaultListResponse($data, $oCredential);
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