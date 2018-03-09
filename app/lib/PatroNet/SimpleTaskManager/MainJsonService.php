<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\SimpleTaskManager\Rest\RoutingJsonService;
use PatroNet\SimpleTaskManager\Rest\JsonService\_ExactPathRoute;
use PatroNet\SimpleTaskManager\Model\Project;
use PatroNet\SimpleTaskManager\Rest\JsonService\_MatchingPathRoute;
use PatroNet\SimpleTaskManager\Rest\RepositoryResponseHelper;
use PatroNet\SimpleTaskManager\Model\Sprint;
use PatroNet\Core\Request\ResponseBuilder;
use PatroNet\SimpleTaskManager\Rest\Credentials;

class MainJsonService extends RoutingJsonService {
    
    public function __construct()
    {
        $this->addRoute(new _ExactPathRoute("get", "", function ($method, $data, Credentials $oCredentials) {
            return
                (new ResponseBuilder())
                ->initJson([
                    "apiName" => "simple-task-manager",
                    "apiVersion" => "0.1",
                    "information" => "horvath@patronet.net, PatroNet C., Hungary",
                ])
                ->build()
            ;
        }));
        
        $this->addRoute(new _ExactPathRoute("get", "dashboard", function ($method, $data, Credentials $oCredentials) {
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
    
    public function handleJsonQuery($path, $method, $data, Credentials $oCredentials)
    {
        if (!$oCredentials->isAuthorized()) {
            return
                (new ResponseBuilder())
                ->initJson(["message" => "Authentikációs hiba"])
                ->setHttpStatus(401)
                ->build()
            ;
        }
        
        return parent::handleJsonQuery($path, $method, $data, $oCredentials);
    }
    
    private function addRepositoryService($path, RepositoryResponseHelper $oRepositoryResponseHelper)
    {
        $this->addRoute(new _ExactPathRoute("get", $path, function ($method, $data, Credentials $oCredentials) use ($oRepositoryResponseHelper) {
            $listUrl = "xxxxx"; // XXX
            return $oRepositoryResponseHelper->getDefaultListResponse($listUrl, $data, $oCredentials);
        }));
        
        $this->addRoute(new _MatchingPathRoute("get", '@^' . preg_quote($path, '@') . '/(?P<entityId>\\d+)$@', function ($match, $method, $data, $oCredentials) use ($oRepositoryResponseHelper) {
            $entityId = $match["entityId"];
            if (!$oRepositoryResponseHelper->getRepository()->exists($entityId)) {
                return $oRepositoryResponseHelper->getEntityNotFoundResponse($entityId);
            }
            return $oRepositoryResponseHelper->getEntityResponse($entityId);
        }));
        
            $this->addRoute(new _ExactPathRoute("post", $path, function ($method, $data, Credentials $oCredentials) use ($path, $oRepositoryResponseHelper) {
            return $oRepositoryResponseHelper->handleCreate($data);
        }));
        
        $this->addRoute(new _MatchingPathRoute("post", '@^' . preg_quote($path, '@') . '/(?P<entityId>\\d+)$@',
            function ($match, $method, $data, Credentials $oCredentials) use ($path, $oRepositoryResponseHelper) {
                $entityId = $match["entityId"];
                if (!$oRepositoryResponseHelper->getRepository()->exists($entityId)) {
                    return $oRepositoryResponseHelper->getEntityNotFoundResponse($entityId);
                }
                return $oRepositoryResponseHelper->handleUpdate($data, $entityId);
            }
        ));
        
        $this->addRoute(new _MatchingPathRoute("delete", '@^' . preg_quote($path, '@') . '/(?P<entityId>\\d+)$@',
            function ($match, $method, $data, Credentials $oCredentials) use ($path, $oRepositoryResponseHelper) {
                $entityId = $match["entityId"];
                if (!$oRepositoryResponseHelper->getRepository()->exists($entityId)) {
                    return $oRepositoryResponseHelper->getEntityNotFoundResponse($entityId);
                }
                return $oRepositoryResponseHelper->handleDelete($entityId);
            }
        ));
        
    }
    
}