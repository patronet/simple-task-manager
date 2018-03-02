<?php

namespace PatroNet\Core\Database\ConnectionDriver\Pdo;


use PatroNet\Core\Database\ErrorContainer;
use PatroNet\Core\Database\EmptyResultSet;

/**
 * PDO query result handler
 */
class Result implements \PatroNet\Core\Database\Result
{
    
    protected $oConnection;
    
    protected $oPdoStatement;
    
    protected $oErrorContainer;
    
    private $lastInsertId;
    
    /**
     * @param \PDOStatement $oPdoStatement
     */
    public function __construct(Connection $oConnection, \PDOStatement $oPdoStatement = null, ErrorContainer $oErrorContainer = null)
    {
        $this->oConnection = $oConnection;
        $this->oPdoStatement = $oPdoStatement;
        $this->oErrorContainer = $oErrorContainer;
        
        // XXX: query for last insert id resets error messages
        $precalculatedSuccess = $this->isSuccess();
        $this->lastInsertId = $precalculatedSuccess ? $oConnection->getLastInsertId() : null;
    }
    
    /**
     * Gets the connection associated to this result
     *
     * @return Connection|null
     */
    public function getConnection()
    {
        return $this->oConnection;
    }
    
    /**
     * Checks success
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return ($this->getSqlState() == "00000");
    }
    
    /**
     * Gets last insert-id if any
     *
     * @return string|int|null
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }
    
    /**
     * Gets the standard SQL state code
     *
     * @return string|int|null
     */
    public function getSqlState()
    {
        if (!is_null($this->oErrorContainer)) {
            return $this->oErrorContainer->getSqlState();
        }
        return $this->oPdoStatement->errorCode();
    }
    
    /**
     * Gets the platform speicfic error code
     *
     * @return string|int|null
     */
    public function getPlatformErrorCode()
    {
        if (!is_null($this->oErrorContainer)) {
            return $this->oErrorContainer->getPlatformErrorCode();
        }
        return $this->oPdoStatement->errorInfo()[1];
    }
    
    /**
     * Gets the platform speicfic error message
     *
     * @return string|null
     */
    public function getPlatformErrorDescription()
    {
        if (!is_null($this->oErrorContainer)) {
            return $this->oErrorContainer->getPlatformErrorDescription();
        }
        return $this->oPdoStatement->errorInfo()[2];
    }
    
    /**
     * Gets the result set
     *
     * @return \PatroNet\Core\Database\ResultSet
     */
    public function getResultSet()
    {
        if (is_null($this->oPdoStatement)) {
            return new EmptyResultSet();
        }
        return new ResultSet($this->oPdoStatement);
    }
    
}
