<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\SimpleTaskManager\Rest\RoutingJsonService;
use PatroNet\SimpleTaskManager\Rest\JsonService\_ExactPathRoute;
use PatroNet\SimpleTaskManager\Model\Project;
use PatroNet\SimpleTaskManager\Rest\JsonService\_MatchingPathRoute;
use PatroNet\SimpleTaskManager\Rest\RepositoryResponseHelper;
use PatroNet\SimpleTaskManager\Model\Sprint;
use PatroNet\Core\Request\ResponseBuilder;

class MainJsonService extends RoutingJsonService {
    
    public function __construct()
    {
        // FIXME: pagination settings
        // FIXME: declarations...
        $this->addRepositoryService("projects", new RepositoryResponseHelper(Project::getRepository(), 2));
        $this->addRepositoryService("sprints", new RepositoryResponseHelper(Sprint::getRepository(), 2));
    }
    
    private function addRepositoryService($path, RepositoryResponseHelper $oRepositoryResponseHelper)
    {
        $this->addRoute(new _ExactPathRoute("get", $path, function ($method, $data, $oCredential) use ($oRepositoryResponseHelper) {
            $listUrl = "xxxxx"; // XXX
            return $oRepositoryResponseHelper->getDefaultListResponse($listUrl, $data, $oCredential);
        }));
        
        $this->addRoute(new _MatchingPathRoute("get", '@^' . preg_quote($path, '@') . '/(?P<entityId>\\d+)$@', function ($match, $method, $data, $oCredential) use ($oRepositoryResponseHelper) {
            if (!$oRepositoryResponseHelper->getRepository()->exists($match["entityId"])) {
                return $oRepositoryResponseHelper->getEntityNotFoundResponse();
            }
            return $oRepositoryResponseHelper->getEntityResponse($match["entityId"]);
        }));
        
        $this->addRoute(new _ExactPathRoute("post", $path, function ($method, $data, $oCredential) use ($path, $oRepositoryResponseHelper) {
            return
                (new ResponseBuilder())
                ->initJson(["message" => "Insert into {$path}"])
                ->build()
            ;
        }));
        
        $this->addRoute(new _MatchingPathRoute("post", '@^' . preg_quote($path, '@') . '/(?P<entityId>\\d+)$@',
            function ($match, $method, $data, $oCredential) use ($path, $oRepositoryResponseHelper) {
                if (!$oRepositoryResponseHelper->getRepository()->exists($match["entityId"])) {
                    return $oRepositoryResponseHelper->getEntityNotFoundResponse();
                }
                return
                    (new ResponseBuilder())
                    ->initJson(["message" => "Update {$path}:{$match["entityId"]}"])
                    ->build()
                ;
            }
        ));
        
        $this->addRoute(new _MatchingPathRoute("delete", '@^' . preg_quote($path, '@') . '/(?P<entityId>\\d+)$@',
            function ($match, $method, $data, $oCredential) use ($path, $oRepositoryResponseHelper) {
                if (!$oRepositoryResponseHelper->getRepository()->exists($match["entityId"])) {
                    return $oRepositoryResponseHelper->getEntityNotFoundResponse();
                }
                return
                    (new ResponseBuilder())
                    ->initJson(["message" => "Delete {$path}:{$match["entityId"]}"])
                    ->build()
                ;
            }
        ));
        
    }
    
}