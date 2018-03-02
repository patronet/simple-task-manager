<?php

namespace PatroNet\Core\Database;


/**
 * Interface for database result handling
 */
class ErrorContainer implements ErrorStatus
{
    
    private $sqlState;
    
    private $platformErrorCode;
    
    private $platformErrorDescription;
    
    public function __construct($sqlState, $platformErrorCode, $platformErrorDescription)
    {
        $this->sqlState = $sqlState;
        $this->platformErrorCode = $platformErrorCode;
        $this->platformErrorDescription = $platformErrorDescription;
    }
    
    public function getSqlState()
    {
        return $this->sqlState;
    }
    
    public function getPlatformErrorCode()
    {
        return $this->platformErrorCode;
    }
    
    public function getPlatformErrorDescription()
    {
        return $this->platformErrorDescription;
    }
    
    static public function fromErrorStatus(ErrorStatus $oErrorStatus)
    {
        return new self(
            $oErrorStatus->getSqlState(),
            $oErrorStatus->getPlatformErrorCode(),
            $oErrorStatus->getPlatformErrorDescription()
        );
    }
    
}