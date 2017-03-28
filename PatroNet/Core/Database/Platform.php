<?php

namespace PatroNet\Core\Database;


/**
 * Interface for database platform drivers
 */
interface Platform {
    
    /**
     * Returns with the name of the default sql dialect
     *
     * @return string
     */
    public function getDefaultSql();
    
    /**
     * Returns with true if the driver supports or emulates transaction savepoints
     *
     * @return boolean
     */
    public function supportSavepoints();
    
    /**
     * Tries to create a transaction savepoint
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $name
     * @return boolean
     */
    public function createSavepoint($oConnection, $name);
    
    /**
     * Tries to release a transaction savepoint
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $name
     * @return boolean
     */
    public function releaseSavepoint($oConnection, $name);
    
    /**
     * Tries to rollback a transaction savepoint
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $name
     * @return boolean
     */
    public function rollbackSavepoint($oConnection, $name);
    
    /**
     * Sets the transaction isolation level
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $level
     * @return boolean
     */
    public function setTransactionIsolation($oConnection, $level);
    
    /**
     * Gets the current transaction isolation level
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @return string
     */
    public function getTransactionIsolation($oConnection);
    
}
