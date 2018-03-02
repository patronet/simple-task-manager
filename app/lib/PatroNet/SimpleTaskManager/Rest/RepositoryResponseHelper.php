<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\ResponseBuilder;
use PatroNet\Core\Database\ActiveRecord;

// XXX rename and handle insert, update, delete
class RepositoryResponseHelper
{
    
    private $oRepository;
    
    private $defaultLimitCount;
    
    
    // XXX: parameters?
    public function __construct(JsonDataRepository $oRepository, $defaultLimitCount = null)
    {
        $this->oRepository = $oRepository;
        $this->defaultLimitCount = $defaultLimitCount;
    }
    
    public function getRepository()
    {
        return $this->oRepository;
    }
    
    // XXX
    public function getDefaultListResponse($listUrl, $data, $oCredential)
    {
        $filter = $this->extractFilter($data);
        $order = $this->extractOrder($data);
        $limit = $this->extractLimit($data);
        // TODO
        return $this->getListResponse($listUrl, $filter, $order, $limit, null); // XXX
    }
    
    // XXX
    public function getListResponse($listUrl, $filter = null, $order = null, $limit = null, $entityViewParameters = null)
    {
        $totalCount = $this->oRepository->count($filter);
        $dataList = $this->oRepository->getJsonDataList($filter, $order, $limit, $entityViewParameters);
        $links = [];
        $links[] = "<{$listUrl}>;rel=first;title=First page";
        
        if (!empty($limit["limit"])) {
            $pageCount = ceil($totalCount / $limit["limit"]);
            $lastPageNo = $pageCount - 1;
            $links[] = "<" . $this->appendToUrl($listUrl, "page={$lastPageNo}") . ">;rel=last;title=Last page";
        }
        
        return
            (new ResponseBuilder())
            ->initJson($dataList)
            ->addHeader("X-Total-Count: {$totalCount}")
            ->addHeader("X-Page-Count: {$pageCount}")
            ->addHeader("Link: " . implode(",", $links))
            ->build()
        ;
    }
    
    // XXX
    public function getEntityResponse($entityId, $entityViewParameters = null)
    {
        $oEntity = $this->oRepository->get($entityId);
        
        if (empty($oEntity)) {
            // FIXME: throw 404 exception?
            return $this->getEntityNotFoundResponse();
        }
        
        if ($oEntity instanceof JsonDataEntity) {
            $entityData = $oEntity->toJsonData($entityViewParameters);
        } else {
            $entityData = $oEntity->getActiveRecord()->getRow();
        }
        
        return
            (new ResponseBuilder())
            ->initJson($entityData)
            ->build()
        ;
    }
    
    public function getEntityNotFoundResponse()
    {
        return (new ResponseBuilder())
            ->initJson(["message" => "Entity not found"])
            ->setHttpStatus(404)
            ->build()
        ;
    }
    
    // XXX: works with database based repositories only
    public function handleUpdate($data, $entityId)
    {
        $oEntity = $this->oRepository->get($entityId);
        
        if (empty($oEntity)) {
            // FIXME: throw 404 exception?
            return $this->getEntityNotFoundResponse();
        }
        
        // XXX
        /** @var ActiveRecord $oActiveRecord */
        $oActiveRecord = $oEntity->getActiveRecord();
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $oActiveRecord[$key] = $value;
            }
        }
        if ($oActiveRecord->commit()) {
            return
                (new ResponseBuilder())
                ->initJson(["success" => true])
                ->build()
            ;
        } else {
            $oConnection = $oActiveRecord->getTable()->getConnection();
            return
                (new ResponseBuilder())
                ->initJson([
                    "success" => false,
                    "message" => $oConnection->getPlatformErrorDescription(),
                ])
                ->build()
            ;
        }
    }
    
    public function handleCreate($data)
    {
        // TODO
    }
    
    public function handleDelete($entityId)
    {
        // TODO
    }
    
    private function extractFilter($data)
    {
        if (isset($data["filter"])) {
            return $data["filter"];
        } else {
            return null;
        }
    }
    
    private function extractOrder($data)
    {
        if (isset($data["sort"]) || isset($data["order"])) {
            $sortText = isset($data["order"]) ? $data["order"] : $data["sort"];
            if ($sortText === "" || !is_string($sortText)) {
                return null;
            }
            
            $result = [];
            
            $tokens = explode(",", $sortText);
            foreach ($tokens as $token) {
                if ($token === "") {
                    continue;
                }
                
                if ($token[0] == "-") {
                    $result[substr($token, 1)] = "desc";
                } else {
                    $result[$token] = "asc";
                }
            }
            
            return $result;
        } else {
            return null;
        }
    }
    
    private function extractLimit($data)
    {
        $result = [
            "limit" => $this->defaultLimitCount,
        ];
        
        if (isset($data["limit"])) {
            $result["limit"] = intval($data["limit"]);
        }
        
        if (isset($data["from"])) {
            $result["from"] = intval($data["from"]);
        } elseif (isset($data["page"])) {
            $result["page"] = intval($data["page"]);
        }
        
        if (is_null($result["limit"])) {
            return null;
        }
        
        return $result;
    }
    
    private function appendToUrl($url, $additionalQuery)
    {
        $sign = strpos($url, '?') === false ? '?' : '&';
        return $url . $sign . $additionalQuery;
    }
    
}