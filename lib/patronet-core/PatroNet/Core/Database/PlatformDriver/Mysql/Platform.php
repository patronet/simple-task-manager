<?php

namespace PatroNet\Core\Database\PlatformDriver\Mysql;

use PatroNet\Core\Database\Connection;
use PatroNet\Core\Database\ResultSet;
use PatroNet\Core\Database\Exception as DatabaseException;


/**
 * MySQL platform driver
 */
class Platform implements \PatroNet\Core\Database\Platform {
    
    protected $transactionLevelNames = [
        Connection::TRANSACTION_READ_UNCOMMITTED => "READ UNCOMMITTED",
        Connection::TRANSACTION_READ_COMMITTED => "READ COMMITTED",
        Connection::TRANSACTION_REPEATABLE_READ => "REPEATABLE READ",
        Connection::TRANSACTION_SERIALIZABLE => "SERIALIZABLE",
    ];
    
    /**
     * Returns with the name of the default sql dialect
     *
     * @return string
     */
    public function getDefaultSql()
    {
        return "mysql";
    }
    
    /**
     * Returns with true if the driver supports or emulates transaction savepoints
     *
     * With this class it is always true.
     *
     * @return boolean
     */
    public function supportSavepoints()
    {
        return true;
    }
    
    /**
     * Tries to create a transaction savepoint
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $name
     * @return boolean
     */
    public function createSavepoint($oConnection, $name)
    {
        if (!$this->supportSavepoints()) {
            throw new DatabaseException("Save points not supported by this platform");
        }
        return $oConnection->execute("SAVEPOINT ".$oConnection->quoteIdentifier($name));
    }
    
    /**
     * Tries to release a transaction savepoint
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $name
     * @return boolean
     */
    public function releaseSavepoint($oConnection, $name)
    {
        if (!$this->supportSavepoints()) {
            throw new DatabaseException("Save points not supported by this platform");
        }
        return $oConnection->execute("RELEASE SAVEPOINT ".$oConnection->quoteIdentifier($name));
    }
    
    /**
     * Tries to rollback a transaction savepoint
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $name
     * @return boolean
     */
    public function rollbackSavepoint($oConnection, $name)
    {
        if (!$this->supportSavepoints()) {
            throw new DatabaseException("Save points not supported by this platform");
        }
        return $oConnection->execute("ROLLBACK TO SAVEPOINT ".$oConnection->quoteIdentifier($name));
    }
    
    /**
     * Sets the transaction isolation level
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $level
     * @return boolean
     */
    public function setTransactionIsolation($oConnection, $level)
    {
        $levelname = $this->transactionLevelNames[$level];
        return $oConnection->execute("SET SESSION TRANSACTION ISOLATION LEVEL ".$levelname);
    }
    
    // FIXME
    /**
     * Gets the current transaction isolation level
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @return string
     */
    public function getTransactionIsolation($oConnection)
    {
        $oResult = $oConnection->execute("SHOW VARIABLES LIKE 'tx_isolation';");
        if ($oResult->isSuccess()) {
            $oResultSet = $oResult->getResultSet();
            $nativeValue = $oResult->getResultSet()->fetch(ResultSet::FETCH_FIELD, "Value");
            $oResultSet->close();
        }
        if ($nativeValue) {
            return $nativeValue;
        } else {
            return null;
        }
    }
    
}
