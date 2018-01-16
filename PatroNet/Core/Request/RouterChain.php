<?php

namespace PatroNet\Core\Request;


/**
 * Chained router collection
 */
class RouterChain implements Router
{
    
    protected $routers = [];
    
    /**
     * @param \PatroNet\Core\Request\Router[]
     */
    public function __construct(array $routers = [])
    {
        $this->routers = $routers;
    }
    
    /**
     * Tries to handle the given request
     *
     * @param \PatroNet\Core\Request\Request
     * @return\PatroNet\Core\Request\Response
     */
    public function handleRequest(Request $oRequest)
    {
        foreach ($this->routers as $oRouter) {
            $oResponse = $oRouter->handleRequest($oRequest);
            $applicableStatus = $oResponse->getApplicableStatus();
            switch ($applicableStatus) {
                case Response::APPLICABLE_HANDLED:
                    return $oResponse;
                case Response::APPLICABLE_HANDLED:
            }
            if ($applicableStatus != Response::APPLICABLE_CONTINUE) {
                return $oResponse;
            }
        }
        return (new ResponseBuilder())->setHttpStatus(404)->setApplicableStatus(Response::APPLICABLE_CONTINUE)->build();
    }
    
    /**
     * Prepends a router to begin of the collection
     *
     * @param \PatroNet\Core\Request\Router $oRouter
     */
    public function prepend(Router $oRouter)
    {
        array_unshift($this->routers, $oRouter);
    }
    
    /**
     * Appends a router to end of the collection
     *
     * @param \PatroNet\Core\Request\Router $oRouter
     */
    public function append(Router $oRouter)
    {
        $this->routers[] = $oRouter;
    }
    
    /**
     * Prepends multiple routers to begin of the collection
     *
     * @param \PatroNet\Core\Request\Router[] $routers
     * @param boolean $forwardOrder
     */
    public function prependAll(array $routers, $forwardOrder = false)
    {
        if ($forwardOrder) {
            array_reverse($routers);
        }
        foreach ($routers as $oRouter) {
            $this->prepend($oRouter);
        }
    }
    
    /**
     * Appends multiple routers to end of the collection
     *
     * @param \PatroNet\Core\Request\Router[] $routers
     */
    public function appendAll(array $routers)
    {
        foreach ($routers as $oRouter) {
            $this->append($oRouter);
        }
    }
    
}
