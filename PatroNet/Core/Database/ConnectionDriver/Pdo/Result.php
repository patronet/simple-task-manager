<?php

namespace PatroNet\Core\Database\ConnectionDriver\Pdo;


/**
 * PDO query result handler
 */
class Result implements \PatroNet\Core\Database\Result
{
    
    protected $oPdoStatement;
    
    private $lastInsertId;
    
    /**
     * @param \PDOStatement $oPdoStatement
     */
    public function __construct(Connection $oConnection, \PDOStatement $oPdoStatement)
    {
        $this->oPdoStatement = $oPdoStatement;
        $this->lastInsertId = $oConnection->getLastInsertId();
    }
    
    /**
     * Checks success
     *
     * @return boolean
     */
    public function isSuccess()
    {
        // FIXME
        return ($this->oPdoStatement->errorCode() == "00000");
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
        return $this->oPdoStatement->errorCode();
    }
    
    /**
     * Gets the platform speicfic error code
     *
     * @return string|int|null
     */
    public function getPlatformErrorCode()
    {
        return $this->oPdoStatement->errorInfo()[1];
    }
    
    /**
     * Gets the platform speicfic error message
     *
     * @return string|null
     */
    public function getPlatformErrorDescription()
    {
        return $this->oPdoStatement->errorInfo()[2];
    }
    
    /**
     * Gets the result set
     *
     * @return \PatroNet\Core\Database\ResultSet
     */
    public function getResultSet()
    {
        return new ResultSet($this->oPdoStatement);
    }
    
}
