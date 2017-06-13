<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\Core\Request\Controller;
use PatroNet\Core\Request\ResponseBuilder;
use PatroNet\Core\Request\Request;
use PatroNet\Core\Request\Response;
use PatroNet\Core\Content\ContentSourceList;
use PatroNet\Core\Common\StringUtil;
use PatroNet\Core\Template\Driver\PhpTemplate;

class ServiceController implements Controller
{
    
    const DEFAULT_PAGE = "index";
    
    public function handle(Request $oRequest)
    {
        $pageName = $this->getPageName($oRequest);
        $methodName = StringUtil::transformTokenCase("handle-" . $pageName, StringUtil::CASE_CAMEL);
        if (method_exists($this, $methodName)) {
            return $this->wrap($this->$methodName($oRequest), $pageName);
        }
        return (new ResponseBuilder())->initText("Not found!")->setHttpStatus(404)->build();
    }
    
    private function wrap(Response $oResponse, $pageName)
    {
        if (!$oResponse->isComplete()) {
            $oInnerContentSource = $oResponse->getContentSource();
            $oWrappedContentSource = new ContentSourceList();
            $oWrappedContentSource->add($this->getFrameTemplate(true, $pageName));
            $oWrappedContentSource->add($oInnerContentSource);
            $oWrappedContentSource->add($this->getFrameTemplate(false, $pageName));
            return (new ResponseBuilder($oResponse))->setContent($oWrappedContentSource)->setComplete(true)->build();
        } else {
            return $oResponse;
        }
    }
    
    private function getPageName(Request $oRequest)
    {
        $getData = $oRequest->getGet();
        if (isset($getData["page"])) {
            return $getData["page"];
        } else {
            return self::DEFAULT_PAGE;
        }
    }
    
    private function getFrameTemplate($isHeader, $pageName)
    {
        $oTemplate = new PhpTemplate("../app/view/frame.tpl.php");
        $oTemplate->assign("isHeader", $isHeader);
        $oTemplate->assign("pageName", $pageName);
        return $oTemplate;
    }
    
    
    //////////////////////////////////////////////////////
    

    private function handleIndex(Request $oRequest)
    {
        return (new ResponseBuilder())->initJson(["xxx" => "yyy"])->build();
    }
    
}