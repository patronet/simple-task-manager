<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\ResponseBuilder;

class RepositoryResponseHelper
{
    
    private $oRepository;
    
    private $defaultLimitCount;
    
    public function __construct(JsonDataRepository $oRepository, $defaultLimitCount = null)
    {
        $this->oRepository = $oRepository;
        $this->defaultLimitCount = $defaultLimitCount;
    }
    
    // XXX
    public function getDefaultListResponse($data, $oCredential)
    {
        $filter = $this->extractFilter($data);
        $order = $this->extractOrder($data);
        $limit = $this->extractLimit($data);
        // TODO
        return $this->getListResponse($filter, $order, $limit, null); // XXX
    }
    
    // XXX
    public function getListResponse($filter = null, $order = null, $limit = null, $entityViewParameters = null)
    {
        $totalCount = $this->oRepository->count($filter);
        $dataList = $this->oRepository->getJsonDataList($filter, $order, $limit, $entityViewParameters);
        return
            (new ResponseBuilder())
            ->initJson($dataList)
            ->addHeader("X-Total-Count: {$totalCount}")
            ->build()
        ;
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
    
}