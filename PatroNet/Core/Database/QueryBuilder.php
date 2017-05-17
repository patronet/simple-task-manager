<?php

namespace PatroNet\Core\Database;

use PatroNet\Core\Common\Resource;


/**
 * Interface for query builders
 *
 * Designed for SQL based database communication.
 */
interface QueryBuilder
{
    
    const QUERYTYPE_SELECT = "select";
    const QUERYTYPE_DELETE = "delete";
    const QUERYTYPE_INSERT = "insert";
    const QUERYTYPE_INSERT_SELECT = "insert_select";
    const QUERYTYPE_INSERT_IGNORE = "insert_ignore";
    const QUERYTYPE_INSERT_IGNORE_SELECT = "insert_ignore_select";
    const QUERYTYPE_REPLACE = "replace";
    const QUERYTYPE_REPLACE_SELECT = "replace_select";
    const QUERYTYPE_UPDATE = "update";
    
    // FIXME
    const JOINTYPE_INNER = "inner";
    const JOINTYPE_LEFT = "left";
    
    /**
     * Associates connection with the query builder
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @return self
     */
    public function setConnection(Connection $oConnection = null);
    
    /**
     * Sets the type of the query
     *
     * Supported query types are specified with as constants with QUERYTYPE_ prefix.
     *
     * @param string $type
     * @return self
     */
    public function setType($type);
    
    /**
     * Gets the type of the query
     *
     * @return string
     * @return self
     */
    public function getType();
    
    /**
     * Initializes a SELECT query
     *
     * If an INSERT query is open, creates INSERT SELECT query.
     *
     * @param array|string|null $fields
     * @return self
     */
    public function select($fields = null);
    
    /**
     * Initializes a DELETE query
     *
     * @return self
     */
    public function delete();
    
    /**
     * Initializes an INSERT query
     *
     * @param array|string|null $fields
     * @return self
     */
    public function insert($fields = null);
    
    /**
     * Initializes an INSERT IGNORE query
     *
     * @param array|string|null $fields
     * @return self
     */
    public function insertIgnore($fields = null);
    
    /**
     * Initializes a REPLACE query
     *
     * @param array|string|null $fields
     * @return self
     */
    public function replace($fields = null);
    
    /**
     * Initializes an UPDATE query
     *
     * @param string $baseTable
     * @param string|null $alias
     * @return self
     */
    public function update($baseTable, $alias = null);
    
    /**
     * Sets the base table of the query
     *
     * $baseTable can be an other QueryBuilder.
     * It will be interpreted as a subquery table.
     * This is useful especially with nested aggregations.
     *
     * @param mixed $baseTable
     * @param string|null $alias
     * @return self
     */
    public function from($baseTable, $alias = null);
    
    /**
     * Adds a JOIN to the query
     *
     * @param string $table
     * @param string $alias
     * @param array|string $joinCondition
     * @param string $joinType
     * @return self
     */
    public function join($table, $alias, $joinCondition, $joinType = self::JOINTYPE_INNER);
    
    /**
     * Adds an INNER JOIN to the query
     *
     * @param string $table
     * @param string $alias
     * @param array|string $joinCondition
     * @return self
     */
    public function innerJoin($table, $alias, $joinCondition);
    
    /**
     * Adds a LEFT JOIN to the query
     *
     * @param string $table
     * @param string $alias
     * @param array|string $joinCondition
     * @return self
     */
    public function leftJoin($table, $alias, $joinCondition);
    
    /**
     * Sets the base target table of the query
     *
     * @param string $baseTable
     * @param array|null $fields
     * @return self
     */
    public function into($targetTable, $fields = null);
    
    /**
     * Sets the save values
     *
     * @param array $datas
     * @return self
     */
    public function values($datas); // FIXME: multiple?
    
    /**
     * Sets the save values
     *
     * @param array $datas
     * @return self
     */
    public function set($datas);
    
    /**
     * Sets the WHERE condition
     *
     * @param array|string|null $whereCondition
     * @return self
     */
    public function where($whereCondition);
    
    /**
     * Sets the WHERE condition
     *
     * @param array|string|null $whereCondition
     * @return self
     */
    public function filter($whereCondition);
    
    /**
     * Adds one or more grouping
     *
     * @param string[] $groupBy
     * @return self
     */
    public function groupBy($groupBy);
    
    /**
     * Sets the HAVING condition
     *
     * @param array|string|null $havingCondition
     * @return self
     */
    public function having($havingCondition);
    
    /**
     * Adds one or more order
     *
     * @param string[string] $orderBy
     * @return self
     */
    public function orderBy($orderBy);
    
    /**
     * Sets the limit
     *
     * @param int|array|string|null $limit
     * @return self
     */
    public function limit($limit);
    
    /**
     * Generates the SQL query
     *
     * @return string
     */
    public function generateQuery();
    
    /**
     * Resets the build
     *
     * @return self
     */
    public function reset();
    
    /**
     * Executes the query with the given connection or the stored connection
     *
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @return \PatroNet\Core\Database\Result
     */
    public function execute(Connection $oConnection = null);
    
    /**
     * Escapes a string for using in a string in an SQL query
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
     * @param mixed $value
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
     * Cuts a query by quotes
     *
     * @param string $query
     * @return array
     */
    public function cutByQuotes($query);
    
}
