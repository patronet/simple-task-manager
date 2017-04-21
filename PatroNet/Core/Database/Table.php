<?php

namespace PatroNet\Core\Database;

use \PatroNet\Core\Common\StringUtil;


/**
 * Class for access data in a database table
 */
class Table implements \IteratorAggregate, \Countable
{
    
    const FETCH_ACTIVE = "active";
    
    const SAVETYPE_INSERT = "insert";
    const SAVETYPE_INSERT_IGNORE = "insert_ignore";
    const SAVETYPE_REPLACE = "replace";
    const SAVETYPE_UPDATE = "update";
    const SAVETYPE_UPDATE_ALL = "update_all";
    
    protected $oConnection;
    
    protected $tableName;
    
    protected $uniqueKey;
    
    protected $tableAlias;
    
    protected $fetchMode = ResultSet::FETCH_ASSOC;
    
    protected $relations = [];
    
    protected $lastSaveId = null;
    
    /**
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $tableName
     * @param mixed $uniqueKey
     * @param string $tableAlias
     */
    public function __construct(Connection $oConnection, $tableName, $uniqueKey = null, $tableAlias = "self")
    {
        $this->oConnection = $oConnection;
        $this->tableName = $tableName;
        $this->uniqueKey = $uniqueKey;
        $this->tableAlias = $tableAlias;
    }
    
    /**
     * Gets a result set for all the records
     *
     * @return \PatroNet\Core\Database\ResultSet
     */
    public function getIterator()
    {
        return $this->oConnection->createQueryBuilder()->select()->from($this->tableName)->execute()->getResultSet();
    }
    
    /**
     * Sets the default fetch mode
     *
     * @param string $fetchMode
     */
    public function setFetchMode($fetchMode)
    {
        $this->fetchMode = $fetchMode;
    }
    
    /**
     * Gets the name of the table
     *
     * @return string
     */
    public function getName()
    {
        return $this->tableName;
    }
    
    /**
     * Gets the default self alias of the table
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->tableAlias;
    }
    
    /**
     * Gets the default identifier key
     *
     * @return mixed
     */
    public function getUniqueKey()
    {
        return $this->uniqueKey;
    }
    
    /**
     * Gets the associated connection object
     *
     * @return \PatroNet\Core\Database\Connection
     */
    public function getConnection()
    {
        return $this->oConnection;
    }
    
    /**
     * Counts the records the table optionally by a filter
     *
     * @param mixed $filters
     * @return int
     */
    public function count($filter = null, $tables = null)
    {
        $oQueryBuilder = $this->oConnection->createQueryBuilder();
        $oQueryBuilder
            ->select([["aggregate", "count"]])
            ->from($this->tableName, $this->tableAlias)
            ->where($filter)
        ;
        if (!is_null($filter) && is_null($tables)) {
            $tables = $this->detectRelationTables($filter, null, null);
        }
        $result = $oQueryBuilder->execute()->getResultSet()->fetch(ResultSet::FETCH_FIELD);
        return intval($result);
    }
    
    /**
     * Associates a relation with the table
     *
     * @param string $alias
     * @param mixed $joinCondition
     * @param string|null $linkTo
     * @param string[] $requires
     * @param string $joinType
     */
    public function addRelation($alias, $joinCondition, $linkTo = null, $requires = [], $joinType = QueryBuilder::JOINTYPE_LEFT)
    {
        $tableName = is_null($linkTo) ? $alias : $linkTo;
        $this->relations[$alias] = [
            "tableName" => $tableName,
            "joinCondition" => $joinCondition,
            "requires" => $requires,
            "joinType" => $joinType,
        ];
    }
    
    /**
     * Gets the associated realtions
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }
    
    /**
     * Gets multiple records by a filter
     *
     * @param mixed $filter
     * @param string[string]|string|null $order
     * @param array|string|null $limit
     * @param array|string|null $fields
     * @param array|null $tables
     * @param string $fetchMode
     * @param mixed $fetchParam1
     * @param mixed $fetchParam2
     * @return \PatroNet\Core\Database\ResultSet
     */
    public function getAll(
        $filter = null,
        $order = null,
        $limit = null,
        $fields = null,
        $tables = null,
        $fetchMode = ResultSet::FETCH_ASSOC,
        $fetchParam1 = null,
        $fetchParam2 = null
    )
    {
        $selfFields = $this->oConnection->quoteIdentifier($this->tableAlias) . ".*";
        
        if ($fetchMode == self::FETCH_ACTIVE || ($fetchMode == ResultSet::FETCH_CALLBACK && $fetchParam2 == self::FETCH_ACTIVE)) {
            if (is_null($tables)) {
                $tables = $this->detectRelationTables($filter, $order, $fields);
            }
            $fields = empty($tables) ? null : $selfFields;
            
            $oQueryBuilder = $this->oConnection->createQueryBuilder();
            $oQueryBuilder
                ->select($fields)
                ->from($this->tableName, $this->tableAlias)
                ->where($filter)
                ->orderBy($order)
                ->limit($limit)
            ;
            
            $this->joinTables($oQueryBuilder, $tables);
            
            if ($fetchMode == self::FETCH_ACTIVE) {
                return $oQueryBuilder->execute()->getResultSet()->setFetchMode(ResultSet::FETCH_CALLBACK, function ($record) {
                    return new ActiveRecord($this, $this->getRecordKey($record), $record);
                });
            } else {
                $callback = $fetchParam1;
                return $oQueryBuilder->execute()->getResultSet()->setFetchMode(ResultSet::FETCH_CALLBACK, function ($record) use ($callback) {
                    $oActiveRecord = new ActiveRecord($this, $this->getRecordKey($record), $record);
                    return call_user_func($callback, $oActiveRecord);
                });
            }
        } else {
            $oQueryBuilder = $this->oConnection->createQueryBuilder();
			
            $oQueryBuilder
                ->select($fields)
                ->from($this->tableName, $this->tableAlias)
                ->where($filter)
                ->orderBy($order)
                ->limit($limit)
            ;
            if (is_null($tables)) {
                $tables = $this->detectRelationTables($filter, $order, $fields);
            }
            $this->joinTables($oQueryBuilder, $tables);
			
            return $oQueryBuilder->execute()->getResultSet()->setFetchMode($fetchMode, $fetchParam1, $fetchParam2);
        }
    }
    
    /**
     * Gets a single record by a filter
     *
     * @param mixed $filter
     * @param string[string]|string|null $order
     * @param array|string|null $fields
     * @param array|null $tables
     * @param string $fetchMode
     * @param mixed $fetchParam1
     * @param mixed $fetchParam2
     * @return mixed
     */
    public function getFirst(
        $filter = null,
        $order = null,
        $fields = null,
        $tables = null,
        $fetchMode = ResultSet::FETCH_ASSOC,
        $fetchParam1 = null,
        $fetchParam2 = null
    )
    {
        return $this->getAll($filter, $order, 1, $fields, $tables, $fetchMode, $fetchParam1, $fetchParam2)->fetch();
    }
    
    /**
     * Gets a single record by its id
     *
     * @param string|int $id
     * @param array|string|null $fields
     * @param array|null $tables
     * @param string $fetchMode
     * @param mixed $fetchParam1
     * @param mixed $fetchParam2
     * @return mixed
     */
    public function get(
        $id,
        $fields = null,
        $tables = null,
        $fetchMode = ResultSet::FETCH_ASSOC,
        $fetchParam1 = null,
        $fetchParam2 = null
    )
    {
        $filter = $this->id2where($id);
        return $this->getFirst($filter, null, $fields, $tables, $fetchMode, $fetchParam1, $fetchParam2);
    }
    
    /**
     * Gets a record from the table as an active record object
     *
     * @param string|int $id
     * @return \PatroNet\Core\Database\ActiveRecord
     */
    public function getActive($id)
    {
        return $this->get($id, null, null, self::FETCH_ACTIVE);
    }
    
    /**
     * Gets a single field data from the table
     *
     * @param string|int $id
     * @param string $field
     * @param array|null tables
     * @return array|null
     */
    public function getField($id, $field, $tables = null)
    {
        $row = $this->get($id, [$field], $tables, ResultSet::FETCH_ASSOC);
        if ($row) {
            return $row[$field];
        } else {
            return null;
        }
    }
    
    /**
     * Gets a field column from the table
     *
     * @param string $field
     * @param mixed $filter
     * @param string[string]|string|null $order
     * @param mixed $limit
     * @param array|null $tables
     * @return \PatroNet\Core\Database\ResultSet
     */
    public function getColumn($field, $filter = null, $order = null, $limit = null, $tables = null)
    {
        return $this->getAll($filter, $order, $limit, is_int($field) ? null : [$field], $tables, ResultSet::FETCH_FIELD, $field);
    }
    
    /**
     * Checks whether a record exists in the table
     *
     * @param string|int $id
     * @return boolean
     */
    public function exists($id)
    {
        if (is_null($this->uniqueKey) || $this->uniqueKey === "") {
            return false;
        }
        $fields = array_keys($this->id2where($id));
        $row = $this->get($id, $fields);
        return (!is_null($row) && $row !== false);
    }
    
    /**
     * Checks that any mathing record exists in the table
     *
     * @param mixed $filter
     * @param array|null $filter
     * @return boolean
     */
    public function existsAny(
        $filter = null,
        $tables = null
    )
    {
        $fields = null;
        if (!is_null($this->uniqueKey) && $this->uniqueKey !== "") {
            if (is_array($this->uniqueKey)) {
                $fields = array_keys($this->uniqueKey);
            } else {
                $fields = [$this->uniqueKey];
            }
        }
        $row = $this->getFirst($filter, null, $fields, $tables);
        return (!is_null($row) && $row !== false);
    }
    
    /**
     * Saves a record into the table
     *
     * @param array $data
     * @param string|int $id
     * @param string $saveType
     * @return \PatroNet\Core\Database\Result
     */
    public function save($data, $id = null, $saveType = null)
    {
        if (is_null($saveType)) {
            $saveType = is_null($id) ? self::SAVETYPE_INSERT : self::SAVETYPE_UPDATE;
        }
        switch ($saveType) {
            case self::SAVETYPE_UPDATE:
            case self::SAVETYPE_UPDATE_ALL:
                $filter = $this->id2where($id);
                $oQueryBuilder = $this->oConnection->createQueryBuilder();
                $oQueryBuilder
                    ->update($this->tableName, $this->tableAlias)
                    ->set($data)
                    ->where($filter)
                ;
                if ($saveType == self::SAVETYPE_UPDATE) {
                    $oQueryBuilder->limit(1);
                }
                $oResult = $oQueryBuilder->execute();
                if ($oResult->isSuccess()) {
                    $this->lastSaveId = $id;
                } else {
                    $this->lastSaveId = null;
                }
                return $oResult;
            case self::SAVETYPE_INSERT:
            case self::SAVETYPE_INSERT_IGNORE:
            case self::SAVETYPE_REPLACE:
                $oQueryBuilder = $this->oConnection->createQueryBuilder();
                if (!is_null($id)) {
                    $filter = $this->id2where($id);
                    $data = array_merge($filter, $data);
                }
                switch ($saveType) {
                    case self::SAVETYPE_INSERT:
                        $oQueryBuilder->insert();
                        break;
                    case self::SAVETYPE_INSERT_IGNORE:
                        $oQueryBuilder->insertIgnore();
                        break;
                    case self::SAVETYPE_REPLACE:
                        $oQueryBuilder->replace();
                        break;
                }
                
                $oQueryBuilder
                    ->into($this->tableName)
                    ->values($data)
                ;
                $oResult = $oQueryBuilder->execute();
                if ($oResult->isSuccess()) {
                    $this->lastSaveId = $oResult->getLastInsertId();
                } else {
                    $this->lastSaveId = null;
                }
                return $oResult;
        }
        // FIXME:
        throw new \Exception("Unsupported save type");
    }
    
    /**
     * Gets the id of the last saved record
     *
     * @return string|int
     */
    public function getLastSaveId()
    {
        return $this->lastSaveId;
    }
    
    /**
     * Deletes a record from the table
     *
     * @param int|string $id
     * @return \PatroNet\Core\Database\Result
     */
    public function delete($id)
    {
        $filter = $this->id2where($id);
        return $this->deleteAll($filter, 1);
    }
    
    // TODO: tables...
    /**
     * Deletes multiple records from the table
     *
     * @param mixed $filter
     * @param mixed $limit
     * @return \PatroNet\Core\Database\Result
     */
    public function deleteAll($filter, $limit = null)
    {
        $oQueryBuilder = $this->oConnection->createQueryBuilder();
        $oQueryBuilder
            ->delete()
            ->from($this->tableName, $this->tableAlias)
            ->where($filter)
        ;
        if (!is_null($limit)) {
            $oQueryBuilder->limit($limit);
        }
        return $oQueryBuilder->execute();
    }
    
    protected function id2where($id)
    {
        if (is_null($id)) {
            return [];
        }
        if (is_array($this->uniqueKey)) {
            if (is_string($id)) {
                $id = StringUtil::splitEscaped(",", "\\", $id);
            }
            $keyLength = count($this->uniqueKey);
            if (!is_array($id)) {
                if ($keyLength == 1) {
                    $where = [];
                } elseif (is_object($id) && $id instanceof Filter) {
                    $where = $id;
                } else {
                    $where = array_combine($this->uniqueKey, array_fill(0, $keyLength, $id)); // FIXME
                }
            } elseif (array_key_exists($keyLength-1, $id) && $keyLength == count($id)) {
                $where = array_combine($this->uniqueKey, $id);
            } else {
                $where = $id;
            }
        } else {
            if (is_array($id)) {
                $where = $id;
            } else {
                $where = [$this->uniqueKey => $id];
            }
        }
        return $where;
    }
    
    public function getRecordKey($record)
    {
        if (empty($this->uniqueKey)) {
            return null;
        } else if (is_array($this->uniqueKey)) {
            $keyMap = [];
            foreach ($this->uniqueKey as $keyField) {
                $keyMap[$keyField] = $record[$keyField];
            }
            return $keyMap;
        } else {
            return $record[$this->uniqueKey];
        }
    }
    
    // XXX
    static public function detectRelationTables($queryFilter, $queryOrder, $queryFields)
    {
        $items = [];
        
        if (!is_null($queryFilter)) {
            $items = array_merge($items, array_keys($queryFilter));
            // TODO: recursive
        }
        
        if (!is_null($queryOrder)) {
            $items = array_merge($items, array_keys($queryOrder));
        }
        
        if (!is_null($queryFields)) {
            $items = array_merge($items, array_values($queryFields));
        }
        
        $detectedAliases = [];
        
        $pattern = "/^(\\w)\\./";
        foreach ($items as $item) {
            if (is_string($item) && preg_match($pattern, $item, $match)) {
                $detectedAliases[] = $match[1];
            }
        }
        
        return array_values(array_unique($detectedAliases, \SORT_STRING));
    }
    
    protected function joinTables(QueryBuilder $oQueryBuilder, $tables)
    {
        $allTables = [];
        if (!is_null($tables)) {
            foreach ($tables as $table) {
                $allTables[] = $table;
                if (array_key_exists($table, $this->relations)) {
                    $allTables = array_merge($allTables, $this->relations[$table]["requires"]);
                }
            }
        }
        if (!empty($allTables)) {
            foreach ($allTables as $table) {
                if (array_key_exists($table, $this->relations)) {
                    $relation = $this->relations[$table];
                    $oQueryBuilder->join($relation["tableName"], $table, $relation["joinCondition"], $relation["joinType"]);
                } else {
                    // FIXME/TODO (exception?)
                }
            }
        }
    }
    
}

