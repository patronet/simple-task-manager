<?php

namespace PatroNet\Core\Database;


/**
 * Abstract base class for query builders
 */
abstract class AbstractQueryBuilder implements QueryBuilder
{
    
    protected $oConnection = null;
    
    protected $type = QueryBuilder::QUERYTYPE_SELECT;
    
    protected $parts = [];
    
    /**
     * Associates connection with the query builder
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @return self
     */
    public function setConnection(Connection $oConnection = null)
    {
        $this->oConnection = $oConnection;
        return $this;
    }
    
    /**
     * Sets the type of the query
     *
     * Supported query types are specified with as constants with QUERYTYPE_ prefix.
     *
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    
    /**
     * Gets the type of the query
     *
     * @return string
     * @return self
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Initializes a SELECT query
     *
     * If an INSERT query is open, creates INSERT SELECT query.
     *
     * @param array|null $fields
     * @return self
     */
    public function select($fields = null)
    {
        $secondaryTypeMap = [
            QueryBuilder::QUERYTYPE_INSERT => QueryBuilder::QUERYTYPE_INSERT_SELECT,
            QueryBuilder::QUERYTYPE_INSERT_IGNORE => QueryBuilder::QUERYTYPE_INSERT_IGNORE_SELECT,
            QueryBuilder::QUERYTYPE_REPLACE => QueryBuilder::QUERYTYPE_REPLACE_SELECT,
        ];
        if (array_key_exists($this->type, $secondaryTypeMap)) {
            $this->setType($secondaryTypeMap[$this->type]);
        } else {
            $this->setType(QueryBuilder::QUERYTYPE_SELECT);
        }
        $this->parts["selectFields"] = $fields;
        return $this;
    }
    
    /**
     * Initializes a DELETE query
     *
     * @return self
     */
    public function delete($tables = null)
    {
        $this->setType(QueryBuilder::QUERYTYPE_DELETE);
        $this->parts["deleteTables"] = is_string($tables) ? [$tables] : $tables;
        return $this;
    }
    
    /**
     * Initializes an INSERT query
     *
     * @param array|string|null $fields
     * @return self
     */
    public function insert($fields = null)
    {
        $this->setType(QueryBuilder::QUERYTYPE_INSERT);
        $this->parts["saveFields"] = $fields;
        return $this;
    }
    
    /**
     * Initializes an INSERT IGNORE query
     *
     * @param array|string|null $fields
     * @return self
     */
    public function insertIgnore($fields = null)
    {
        $this->setType(QueryBuilder::QUERYTYPE_INSERT_IGNORE);
        $this->parts["saveFields"] = $fields;
        return $this;
    }
    
    /**
     * Initializes a REPLACE query
     *
     * @param array|string|null $fields
     * @return self
     */
    public function replace($fields = null)
    {
        $this->setType(QueryBuilder::QUERYTYPE_REPLACE);
        $this->parts["saveFields"] = $fields;
        return $this;
    }
    
    /**
     * Initializes an UPDATE query
     *
     * @param string $baseTable
     * @param string|null $alias
     * @return self
     */
    public function update($baseTable, $alias = null)
    {
        $this->setType(QueryBuilder::QUERYTYPE_UPDATE);
        $this->parts["baseTable"] = $baseTable;
        $this->parts["baseTableAlias"] = $alias;
        return $this;
    }
    
    /**
     * Sets the base table of the query
     *
     * {@inheritdoc}
     *
     * @param mixed $baseTable
     * @param string|null $alias
     * @return self
     */
    public function from($baseTable, $alias = null)
    {
        $this->parts["baseTable"] = $baseTable;
        $this->parts["baseTableAlias"] = $alias;
        return $this;
    }
    
    /**
     * Adds a JOIN to the query
     *
     * @param string $table
     * @param string $alias
     * @param array|string $joinCondition
     * @param string $joinType
     * @return self
     */
    public function join($table, $alias, $joinCondition, $joinType = QueryBuilder::JOINTYPE_INNER)
    {
        $this->parts["joins"][] = [$joinType, $table, $alias, $joinCondition];
        return $this;
    }
    
    /**
     * Adds an INNER JOIN to the query
     *
     * @param string $table
     * @param string $alias
     * @param array|string $joinCondition
     * @return self
     */
    public function innerJoin($table, $alias, $joinCondition)
    {
        $this->join($table, $alias, $joinCondition, QueryBuilder::JOINTYPE_INNER);
        return $this;
    }
    
    /**
     * Adds a LEFT JOIN to the query
     *
     * @param string $table
     * @param string $alias
     * @param array|string $joinCondition
     * @return self
     */
    public function leftJoin ($table, $alias, $joinCondition)
    {
        $this->join($table, $alias, $joinCondition, QueryBuilder::JOINTYPE_LEFT);
        return $this;
    }
    
    /**
     * Sets the base target table of the query
     *
     * @param string $baseTable
     * @param array|null $fields
     * @return self
     */
    public function into($targetTable, $fields = null)
    {
        $this->parts["targetTable"] = $targetTable;
        $this->parts["saveFields"] = $fields;
        return $this;
    }
    
    /**
     * Sets the save values
     *
     * @param array $datas
     * @return self
     */
    public function values($datas)
    {
        $this->parts["saveDatas"] = $datas;
        return $this;
    }
    
    /**
     * Sets the save values
     *
     * @param array $datas
     * @return self
     */
    public function set($datas)
    {
        $this->parts["saveDatas"] = $datas;
        return $this;
    }
    
    /**
     * Sets the WHERE condition
     *
     * @param mixed $whereCondition
     * @return self
     */
    public function where($whereCondition)
    {
        $this->parts["where"] = $whereCondition;
        return $this;
    }
    
    /**
     * Sets the WHERE condition
     *
     * @param mixed $whereCondition
     * @return self
     */
    public function filter($whereCondition)
    {
        $this->parts["where"] = $whereCondition;
        return $this;
    }
    
    /**
     * Adds one or more grouping
     *
     * @param string[] $groupBy
     * @return self
     */
    public function groupBy($groupBy)
    {
        $this->parts["groupBy"] = $groupBy;
        return $this;
    }
    
    /**
     * Sets the HAVING condition
     *
     * @param array|string|null $havingCondition
     * @return self
     */
    public function having($havingCondition)
    {
        $this->parts["having"] = $havingCondition;
        return $this;
    }
    
    /**
     * Adds one or more order
     *
     * @param string[string] $orderBy
     * @return self
     */
    public function orderBy($orderBy)
    {
        $this->parts["orderBy"] = $orderBy;
        return $this;
    }
    
    /**
     * Sets the limit
     *
     * @param int|array|string|null $limit
     * @return self
     */
    public function limit($limit)
    {
        $this->parts["limit"] = $limit;
        return $this;
    }
    
    /**
     * Resets the build
     *
     * @return self
     */
    public function reset()
    {
        $this->type = QueryBuilder::QUERYTYPE_SELECT;
        $this->parts = [];
        return $this;
    }
    
    /**
     * Executes the query with the given connection or the stored connection
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @return \PatroNet\Core\Database\Result
     */
    public function execute(Connection $oConnection = null)
    {
        if (is_null($oConnection)) {
            $oConnection = $this->oConnection;
        }
        return $oConnection->execute($this->generateQuery());
    }
    
}
