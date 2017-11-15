<?php

namespace PatroNet\Core\Database\ConnectionDriver\Pdo;

use PatroNet\Core\Database\ConnectionUriParser;
use PatroNet\Core\Database\ConnectionManager;
use PatroNet\Core\Database\Connection as ConnectionInterface;
use PatroNet\Core\Database\Exception as DatabaseException;
use PatroNet\Core\Database\Table;


/**
 * PDO connection driver
 */
class Connection implements ConnectionInterface
{
    private $uri = null;
    private $config = [];
    private $oPdo = null;
    private $oPdoConnectException = null;
    private $queryBuilderClass = "";
    private $oPlatform = null;
    private $open = false;
    private $inited = false;
    
    private $transactionNestingLevel = 0;
    
    /**
     * @param string|null $uri
     */
    public function __construct($uri = null)
    {
        if (!is_null($uri)) {
            $this->init($uri);
        }
    }
    
    /**
     * Gets the PDO instance
     *
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->oPdo;
    }
    
    /**
     * Initializes the connection, but do not open it
     *
     * @param string $uri
     */
    public function init($uri)
    {
        if (!is_null($this->uri)) {
            throw new DatabaseException("This connection is already inited");
        }
        $config = ConnectionUriParser::parse($uri);
        if ($config === false) {
            throw new DatabaseException("Invalid connection URI: " . $uri . " (" . $_SERVER["HTTP_HOST"] . ")");
        }
        $this->uri = $uri;
        $this->config = $config;
        
        if (!isset($this->config["platform"])) {
            $defaultPlatform = $this->getDefaultPlatform();
            if (is_null($defaultPlatform)) {
                throw new DatabaseException("No default platform for this driver");
            }
            $this->config["platform"] = $defaultPlatform;
        }
        
        $platformClass = ConnectionManager::getPlatformClass($this->config["platform"]);
        $this->oPlatform = new $platformClass();
        
        if (!isset($this->config["sql"])) {
            $defaultSql = $this->oPlatform->getDefaultSql();
            if (is_null($defaultSql)) {
                throw new DatabaseException("No default sql for this platform");
            }
            $this->config["sql"] = $defaultSql;
        }
        
        $this->queryBuilderClass = ConnectionManager::getQueryBuilderClass($this->config["sql"]);
    }
    
    /**
     * Gets the name of the default database platform
     *
     * @return string|null
     */
    public function getDefaultPlatform()
    {
        return null;
    }
    
    /**
     * Tries to open the connection
     *
     * @return boolean
     */
    public function open()
    {
        if ($this->open) {
            return true;
        }
        $c = $this->config;
        
        // FIXME
        $dsn = $c["platform"].":host=".$c["host"].";dbname=".$c["database"].";charset=".$c["options"]["charset"];
        
        try {
            $this->oPdo = new \PDO($dsn, $c["username"], $c["password"]);
        } catch (\PDOException $oException) {
            $this->oPdoConnectException = $oException;
            return false;
        }
        
        $this->oPdoConnectException = null;
        $this->open = true;
        return true;
    }
    
    /**
     * Tries to close the connection
     *
     * @return boolean
     */
    public function close()
    {
        // TODO: processkiller code (get from platform object)
        $this->oPdo = null;
        $this->open = false;
        return true; // FIXME
    }
    
    /**
     * Checks whether the connection is open
     *
     * @return boolean
     */
    public function isOpen()
    {
        return $this->open;
    }
    
    /**
     * Gets the standard SQL state code
     *
     * @return string|int|null
     */
    public function getSqlState()
    {
        if ($this->oPdo) {
            return $this->oPdo->errorCode();
        } elseif ($this->oPdoConnectException) {
            // FIXME
            $fullmessage = $this->oPdoConnectException->getMessage();
            preg_match('#^sqlstate\\s*\\[(\\w+)\\]#i', $fullmessage, $match);
            return $match[1]*1;
        } else {
            return null;
        }
    }
    
    /**
     * Gets the platform speicfic error code
     *
     * @return string|int|null
     */
    public function getPlatformErrorCode()
    {
        if ($this->oPdo) {
            return $this->oPdo->errorInfo()[1];
        } elseif ($this->oPdoConnectException) {
            // FIXME
            $fullmessage = $this->oPdoConnectException->getMessage();
            preg_match('#^sqlstate\\s*\\[\\w+\\]\\s+\\[(\\w+)\\]#i', $fullmessage, $match);
            return $match[1]*1;
        } else {
            return null;
        }
    }
    
    /**
     * Gets the platform speicfic error message
     *
     * @return string|null
     */
    public function getPlatformErrorDescription()
    {
        if ($this->oPdo) {
            return $this->oPdo->errorInfo()[2];
        } elseif ($this->oPdoConnectException) {
            // FIXME
            $fullmessage = $this->oPdoConnectException->getMessage();
            return preg_replace('#^sqlstate\\s*\\[\\w+\\]\\s+\\[\\w+\\]\\s+#i', '', $fullmessage);
        } else {
            return null;
        }
    }
    
    /**
     * Executes an SQL query
     *
     * @param string $sql
     * @return \PatroNet\Core\Database\Result
     */
    public function execute($sql)
    {
        if ($this->isInTransaction()) {
            $this->oPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        $oPdoStatement = $this->oPdo->prepare($sql);
        $oPdoStatement->execute();
        if ($this->isInTransaction()) {
            $this->oPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        }
        return new Result($this, $oPdoStatement);
    }
    
    /**
     * Prepares an SQL statement
     *
     * @param string $sql
     * @param boolean $useNative
     * @return \PatroNet\Core\Database\PreparedStatement
     */
    public function prepare($sql, $useNative = false)
    {
        return new PreparedStatement($this, $sql, $useNative);
    }
    
    /**
     * Gets the last generated id
     *
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->oPdo->lastInsertId();
    }
    
    /**
     * Escapes a string for using in a string in an SQL query
     *
     * @param string $str
     * @return string
     */
    public function escapeString($str)
    {
        // FIXME
        return substr($this->quoteString($str), 1, -1);
    }
    
    /**
     * Quotes a string for using as a string in an SQL query
     *
     * @param string $str
     * @return string
     */
    public function quoteString($str)
    {
        return $this->oPdo->quote("" . $str, \PDO::PARAM_STR);
    }
    
    /**
     * Quotes a value for using in an SQL query
     *
     * @param string $str
     * @return string
     */
    public function quote($value)
    {
        // FIXME
        if (is_null($value)) {
            return "NULL";
        } elseif (is_int($value)) {
            return $this->oPdo->quote($value, \PDO::PARAM_INT);
        } else {
            return $this->oPdo->quote("" . $value, \PDO::PARAM_STR);
        }
    }
    
    /**
     * Quotes an identifier
     *
     * @param string $str
     * @return string
     */
    public function quoteIdentifier($str)
    {
        $oQueryBuilder = $this->createQueryBuilder();
        $oQueryBuilder->setConnection(null);
        return $oQueryBuilder->quoteIdentifier($str);
    }
    
    /**
     * Quotes an atomic identifier name
     *
     * @param string $str
     * @return string
     */
    public function quoteIdentifierRaw($str)
    {
        $oQueryBuilder = $this->createQueryBuilder();
        $oQueryBuilder->setConnection(null);
        return $oQueryBuilder->quoteIdentifierRaw($str);
    }
    
    /**
     * Gets the loaded platform object
     *
     * @return \PatroNet\Core\Database\Platform
     */
    public function getPlatform()
    {
        return $this->oPlatform;
    }
    
    /**
     * Creates a query builder associated with this connection
     *
     * @return \PatroNet\Core\Database\QueryBuilder
     */
    public function createQueryBuilder()
    {
        $queryBuilderClass = $this->queryBuilderClass;
        return new $queryBuilderClass($this);
    }
    
    /**
     * Creates a table object associated with this connection
     *
     * @param string $table
     * @param mixed $uniqueKey
     * @param string $tableAlias
     * @return \PatroNet\Core\Database\Table
     */
    public function getTable($table, $uniqueKey = null, $tableAlias = "self")
    {
        return new Table($this, $table, $uniqueKey, $tableAlias);
    }
    
    /**
     * Tries to start a transaction
     *
     * @param string $handleCurrent
     * @return boolean
     */
    public function beginTransaction($handleCurrent = ConnectionInterface::TRANSACTION_BEGIN_DEFAULT)
    {
        if ($this->transactionNestingLevel == 0) {
            if ($this->oPdo->beginTransaction()) {
                $this->transactionNestingLevel++;
                return true;
            } else {
                return false;
            }
        } else {
            switch ($handleCurrent) {
                case ConnectionInterface::TRANSACTION_BEGIN_DEFAULT:
                    return false;
                case ConnectionInterface::TRANSACTION_BEGIN_NESTED:
                    if ($this->createSavepoint("NESTEDLEVELSTART" . ($this->transactionNestingLevel +1))) {
                        $this->transactionNestingLevel++;
                        return true;
                    } else {
                        return false;
                    }
                case ConnectionInterface::TRANSACTION_BEGIN_COMMIT:
                    return (
                        $this->commitTransaction(false) &&
                        $this->beginTransaction(ConnectionInterface::TRANSACTION_BEGIN_NESTED)
                    );
                case ConnectionInterface::TRANSACTION_BEGIN_ROLLBACK:
                    return (
                        $this->rollbackTransaction(false) &&
                        $this->beginTransaction(ConnectionInterface::TRANSACTION_BEGIN_NESTED)
                    );
            }
        }
    }
    
    /**
     * Tries to commit current transaction
     *
     * @param boolean $toplevel
     * @return boolean
     */
    public function commitTransaction($toplevel = false)
    {
        if ($this->transactionNestingLevel == 0) {
            // FIXME
            throw new DatabaseException("No transaction exists to commit");
        } elseif ($toplevel || $this->transactionNestingLevel == 1) {
            // FIXME: are there aborted transactions?
            $result = $this->oPdo->commit();
            $this->transactionNestingLevel = 0;
            return $result;
        } else {
            // FIXME
            $this->releaseSavepoint("NESTEDLEVELSTART" . ($this->transactionNestingLevel));
            $this->transactionNestingLevel--;
            return true;
        }
    }
    
    /**
     * Tries to rollback current transaction
     *
     * @param boolean $toplevel
     * @return boolean
     */
    public function rollbackTransaction($toplevel = false)
    {
        if ($this->transactionNestingLevel == 0) {
            // FIXME
            throw new DatabaseException("No transaction exists to rollback");
        } elseif ($toplevel || $this->transactionNestingLevel == 1) {
            // FIXME: failed rollback??
            $result = $this->oPdo->rollback();
            $this->transactionNestingLevel = 0;
            return $result;
        } else {
            // FIXME
            if ($this->rollbackSavepoint("NESTEDLEVELSTART" . ($this->transactionNestingLevel))) {
                $this->releaseSavepoint("NESTEDLEVELSTART" . ($this->transactionNestingLevel));
                $this->transactionNestingLevel--;
                return true;
            } else {
                return false;
            }
        }
    }
    
    /**
     * Calls a callback enclosed with a transaction
     *
     * @param callable $procedure
     * @param callable $finally
     * @param boolean $rethrowException
     * @param string $handleCurrent
     * @throws \Exception if an error occured
     * @return boolean
     */
    // FIXME/TODO: currently exceptions work with only execute (not with preparation or other operations)
    public function transactional(
        callable $procedure,
        callable $finallyCallback = null,
        $rethrowException = true,
        $handleCurrent = ConnectionInterface::TRANSACTION_BEGIN_DEFAULT
    )
    {
        $oException = null;
        $this->beginTransaction($handleCurrent);
        try {
            $procedure($this);
            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            $oException = $e;
        }
        if (!is_null($finallyCallback)) {
            $finallyCallback($this, is_null($oException));
        }
        if ($rethrowException && !is_null($oException)) {
            throw $oException;
        } else {
            return is_null($oException);
        }
    }
    
    /**
     * Checks whether a transaction is active
     *
     * @return boolean
     */
    public function isInTransaction()
    {
        return ($this->transactionNestingLevel > 0);
    }
    
    /**
     * Gets the transaction nesting deep level
     *
     * @return int
     */
    public function getTransactionNestingLevel()
    {
        return $this->transactionNestingLevel;
    }
    
    /**
     * Tries to create a transaction save point
     *
     * @param string $name
     * @return boolean
     */
    public function createSavepoint($name)
    {
        return $this->oPlatform->createSavepoint($this, $name);
    }
    
    /**
     * Tries to release a transaction save point
     *
     * @param string $name
     * @return boolean
     */
    public function releaseSavepoint($name)
    {
        return $this->oPlatform->releaseSavepoint($this, $name);
    }
    
    /**
     * Tries to rollback to a transaction save point
     *
     * @param string $name
     * @return boolean
     */
    public function rollbackSavepoint($name)
    {
        return $this->oPlatform->rollbackSavepoint($this, $name);
    }
    
    /**
     * Sets the transaction isolation level
     *
     * @param string $level
     * @return boolean
     */
    public function setTransactionIsolation($level)
    {
        return $this->oPlatform->setTransactionIsolation($this, $level);
    }
    
}
