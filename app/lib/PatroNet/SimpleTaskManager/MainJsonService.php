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
        $this->addRoute(new _ExactPathRoute("get", "dashboard", function ($method, $data, $oCredential) {
            $currentDate = date("Y-m-d");
            $activeProjectCount = Project::getRepository()->count([[[
                ["status" => "progress"],
                [[
                    [
                        ["has_startdate" => 0],
                        ["date_startdate" => ["<=", $currentDate]]
                    ],
                    [
                        ["has_duedate" => 0],
                        ["date_duedate" => [">=", $currentDate]]
                    ],
                ]],
            ]]]);
            return
                (new ResponseBuilder())
                ->initJson(["activeProjectCount" => $activeProjectCount])
                ->build()
            ;
        }));
        
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
            $entityId = $match["entityId"];
            if (!$oRepositoryResponseHelper->getRepository()->exists($entityId)) {
                return $oRepositoryResponseHelper->getEntityNotFoundResponse($entityId);
            }
            return $oRepositoryResponseHelper->getEntityResponse($entityId);
        }));
        
        $this->addRoute(new _ExactPathRoute("post", $path, function ($method, $data, $oCredential) use ($path, $oRepositoryResponseHelper) {
            return $oRepositoryResponseHelper->handleCreate($data);
        }));
        
        $this->addRoute(new _MatchingPathRoute("post", '@^' . preg_quote($path, '@') . '/(?P<entityId>\\d+)$@',
            function ($match, $method, $data, $oCredential) use ($path, $oRepositoryResponseHelper) {
                $entityId = $match["entityId"];
                if (!$oRepositoryResponseHelper->getRepository()->exists($entityId)) {
                    return $oRepositoryResponseHelper->getEntityNotFoundResponse($entityId);
                }
                return $oRepositoryResponseHelper->handleUpdate($data, $entityId);
            }
        ));
        
        $this->addRoute(new _MatchingPathRoute("delete", '@^' . preg_quote($path, '@') . '/(?P<entityId>\\d+)$@',
            function ($match, $method, $data, $oCredential) use ($path, $oRepositoryResponseHelper) {
                $entityId = $match["entityId"];
                if (!$oRepositoryResponseHelper->getRepository()->exists($entityId)) {
                    return $oRepositoryResponseHelper->getEntityNotFoundResponse($entityId);
                }
                return $oRepositoryResponseHelper->handleDelete($entityId);
            }
        ));
        
    }
    
}