<?php

namespace PatroNet\Core\Database;

use PatroNet\Core\Common\Resource;


/**
 * Interface for database connection drivers
 */
interface Connection extends Resource, ErrorStatus
{
    
    const TRANSACTION_READ_UNCOMMITTED = "read_uncommitted";
    const TRANSACTION_READ_COMMITTED = "read_committed";
    const TRANSACTION_REPEATABLE_READ = "repeatable_read";
    const TRANSACTION_SERIALIZABLE = "serializable";
    
    const TRANSACTION_BEGIN_DEFAULT = "begin_default";
    const TRANSACTION_BEGIN_NESTED = "begin_nested";
    const TRANSACTION_BEGIN_COMMIT = "begin_commit";
    const TRANSACTION_BEGIN_ROLLBACK = "begin_rollback";
    
    /**
     * Initializes the connection, but do not open it
     *
     * @param string $uri
     */
    public function init($uri);
    
    /**
     * Gets the name of the default database platform
     *
     * @return string|null
     */
    public function getDefaultPlatform();
    
    /**
     * Executes an SQL query
     *
     * @param string $sql
     * @return \PatroNet\Core\Database\Result
     */
    public function execute($sql);
    
    /**
     * Prepares an SQL statement
     *
     * @param string $sql
     * @param boolean $useNative
     * @return \PatroNet\Core\Database\PreparedStatement
     */
    public function prepare($sql, $useNative = false);
    
    /**
     * Gets the last generated id
     *
     * @return int
     */
    public function getLastInsertId();
    
    /**
     * Escapes a string for using as a string in an SQL query
     *
     * @param string $str
     * @return string
     */
    public function escapeString($str);
    
    /**
     * Quotes a string for using as a string in an SQL query
     *
     * @param string $str
     * @return string
     */
    public function quoteString($str);
    
    /**
     * Quotes a value for using in an SQL query
     *
     * @param string $str
     * @return string
     */
    public function quote($value);
    
    /**
     * Quotes an identifier
     *
     * @param string $str
     * @return string
     */
    public function quoteIdentifier($str);
    
    /**
     * Quotes an atomic identifier name
     *
     * @param string $str
     * @return string
     */
    public function quoteIdentifierRaw($str);
    
    /**
     * Gets the loaded platform object
     *
     * @return \PatroNet\Core\Database\Platform
     */
    public function getPlatform();
    
    /**
     * Creates a query builder associated with this connection
     *
     * @return \PatroNet\Core\Database\QueryBuilder
     */
    public function createQueryBuilder();
    
    /**
     * Creates a table object associated with this connection
     *
     * @param string $table
     * @param mixed $uniqueKey
     * @param string $tableAlias
     * @return \PatroNet\Core\Database\Table
     */
    public function getTable($table, $uniqueKey = null, $tableAlias = "self");
    
    /**
     * Tries to start a transaction
     *
     * @param string $handleCurrent
     * @return boolean
     */
    public function beginTransaction($handleCurrent = self::TRANSACTION_BEGIN_DEFAULT);
    
    /**
     * Tries to commit current transaction
     *
     * @param boolean $toplevel
     * @return boolean
     */
    public function commitTransaction($toplevel = false);
    
    /**
     * Tries to rollback current transaction
     *
     * @param boolean $toplevel
     * @return boolean
     */
    public function rollbackTransaction($toplevel = false);
    
    /**
     * Calls a callback enclosed with a transaction
     *
     * @param callback $procedure
     * @param callback $finally
     * @param boolean $rethrowException
     * @param string $handleCurrent
     * @throws Exception if an error occured
     * @return boolean
     */
    public function transactional(
        callable $procedure,
        callable $finally = null,
        $rethrowException = true,
        $handleCurrent = ConnectionInterface::TRANSACTION_BEGIN_DEFAULT
    );
    
    /**
     * Checks whether a transaction is active
     *
     * @return boolean
     */
    public function isInTransaction();
    
    /**
     * Gets the transaction nesting deep level
     *
     * @return int
     */
    public function getTransactionNestingLevel();
    
    /**
     * Tries to create a transaction save point
     *
     * @param string $name
     * @return boolean
     */
    public function createSavepoint($name);
    
    /**
     * Tries to release a transaction save point
     *
     * @param string $name
     * @return boolean
     */
    public function releaseSavepoint($name);
    
    /**
     * Tries to rollback to a transaction save point
     *
     * @param string $name
     * @return boolean
     */
    public function rollbackSavepoint($name);
    
    /**
     * Sets the transaction isolation level
     *
     * @param string $level
     * @return boolean
     */
    public function setTransactionIsolation($level);
    
}
