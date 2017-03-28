<?php

namespace PatroNet\Core\Database\QueryBuilderDriver\Mysql;

use PatroNet\Core\Database\Connection;
use PatroNet\Core\Database\AbstractQueryBuilder;
use PatroNet\Core\Database\QueryBuilder as QueryBuilderInterface;
use PatroNet\Core\Database\Filter;


/**
 * MySQL query builder
 */
class QueryBuilder extends AbstractQueryBuilder
{
    
    /**
     * @param \PatroNet\Core\Database\Connection $oConnection
     */
    public function __construct(Connection $oConnection = null)
    {
        $this->setConnection($oConnection);
    }
    
    /**
     * Generates the SQL query
     *
     * @return string
     */
    public function generateQuery()
    {
        // TODO/FIXME: error handling
        
        $result = "";
        switch ($this->type) {
            case QueryBuilderInterface::QUERYTYPE_INSERT:
            case QueryBuilderInterface::QUERYTYPE_INSERT_SELECT:
            case QueryBuilderInterface::QUERYTYPE_INSERT_IGNORE:
            case QueryBuilderInterface::QUERYTYPE_INSERT_IGNORE_SELECT:
            case QueryBuilderInterface::QUERYTYPE_REPLACE:
            case QueryBuilderInterface::QUERYTYPE_REPLACE_SELECT:
                if (empty($this->parts["saveDatas"])) {
                    // TODO: error
                }
                
                $startMap = [
                    QueryBuilderInterface::QUERYTYPE_INSERT => "INSERT",
                    QueryBuilderInterface::QUERYTYPE_INSERT_SELECT => "INSERT",
                    QueryBuilderInterface::QUERYTYPE_INSERT_IGNORE => "INSERT IGNORE",
                    QueryBuilderInterface::QUERYTYPE_INSERT_IGNORE_SELECT => "INSERT IGNORE",
                    QueryBuilderInterface::QUERYTYPE_REPLACE => "REPLACE",
                    QueryBuilderInterface::QUERYTYPE_REPLACE_SELECT => "REPLACE",
                ];
                $result .= $startMap[$this->type] . " INTO " . $this->quoteIdentifier($this->parts["targetTable"]);
                
                $dataKeys = array_keys($this->parts["saveDatas"]);
                $dataKeysCount = count($dataKeys);
                $fieldList = [];
                $valueList = [];
                if ($dataKeys === range(0, $dataKeysCount - 1)) { // XXX: === , TODO: utils
                    if (empty($this->parts["saveFields"])) {
                        $valueList = array_values($this->parts["saveDatas"]);
                    } else {
                        $fieldCount = min($dataKeysCount, count($this->parts["saveFields"]));
                        $fieldList = array_slice(array_values($this->parts["saveFields"]), 0, $fieldCount);
                        $valueList = array_slice(array_values($this->parts["saveDatas"]), 0, $fieldCount);
                    }
                } elseif (empty($this->parts["saveFields"])) { // FIXME (is_null? ...)
                    $fieldList = array_keys($this->parts["saveDatas"]);
                    $valueList = array_values($this->parts["saveDatas"]);
                } else {
                    foreach ($this->parts["saveDatas"] as $dataKey=>$dataValue) {
                        if (in_array($dataKey, $this->parts["saveFields"])) {
                            $fieldList[] = $dataKey;
                            $valueList[] = $dataValue;
                        }
                    }
                    if (empty($valueList)) {
                        // TODO: error
                    }
                }
                if (!empty($fieldList)) {
                    $result .= " (" . implode(", ", array_map([$this, "quoteIdentifier"], $fieldList)) . ")";
                }
                
                if (in_array($this->type, [
                    QueryBuilderInterface::QUERYTYPE_INSERT_SELECT,
                    QueryBuilderInterface::QUERYTYPE_INSERT_IGNORE_SELECT,
                    QueryBuilderInterface::QUERYTYPE_REPLACE_SELECT,
                ])) {
                    $oSelectQueryBuilder = new QueryBuilder($this->oConnection);
                    $oSelectQueryBuilder->setType(QueryBuilderInterface::QUERYTYPE_SELECT);
                    $oSelectQueryBuilder->parts = $this->parts;
                    $result .= " " . $oSelectQueryBuilder->generateQuery();
                } else {
                    $result .= " VALUES (" . implode(", ", array_map([$this, "quote"], $valueList)) . ")"; // FIXME
                }
                break;
            case QueryBuilderInterface::QUERYTYPE_DELETE:
                $result .= "DELETE";
                if (isset($this->parts["deleteTables"])) {
                    $result .= " " . implode(", ", array_map([$this, "quoteIdentifier"], $this->parts["deleteTables"]));
                }
                $result .= " FROM " . $this->quoteIdentifier($this->parts["baseTable"]);
                if (isset($this->parts["joins"])) {
                    $result .= " " . $this->generateJoinsPart($this->parts["joins"]);
                }
                if (isset($this->parts["where"])) {
                    $result .= " WHERE " . $this->generateWherePart($this->parts["where"]);
                }
                if (isset($this->parts["orderBy"])) {
                    $result .= " ORDER BY " . $this->generateOrderByPart($this->parts["orderBy"]);
                }
                if (isset($this->parts["limit"])) {
                    $result .= " LIMIT " . $this->generateLimitPart($this->parts["limit"]);
                }
                break;
            case QueryBuilderInterface::QUERYTYPE_UPDATE:
                if (empty($this->parts["saveDatas"])) {
                    // TODO: error
                }
                
                $result .= "UPDATE " . $this->quoteIdentifier($this->parts["baseTable"]);
                $result .= " SET " . $this->generateUpdateSetPart($this->parts["saveDatas"]);
                if (isset($this->parts["where"])) {
                    $result .= " WHERE " . $this->generateWherePart($this->parts["where"]);
                }
                if (isset($this->parts["orderBy"])) {
                    $result .= " ORDER BY " . $this->generateOrderByPart($this->parts["orderBy"]);
                }
                if (isset($this->parts["limit"])) {
                    $result .= " LIMIT " . $this->generateLimitPart($this->parts["limit"]);
                }
                break;
            case QueryBuilderInterface::QUERYTYPE_SELECT:
            default:
                $result .= "SELECT";
                $result .= " " . (isset($this->parts["selectFields"])?$this->generateSelectFieldsPart($this->parts["selectFields"]):"*");
                $baseTableAlias = isset($this->parts["baseTableAlias"]) ? $this->parts["baseTableAlias"] : null;
                $result .= " FROM " . $this->generateBaseTablePart($this->parts["baseTable"], $baseTableAlias);
                if (isset($this->parts["joins"])) {
                    $result .= " " . $this->generateJoinsPart($this->parts["joins"]);
                }
                if (isset($this->parts["where"])) {
                    $result .= " WHERE " . $this->generateWherePart($this->parts["where"]);
                }
                if (isset($this->parts["groupBy"])) {
                    $result .= " GROUP BY " . $this->generateGroupByPart($this->parts["groupBy"]);
                }
                if (isset($this->parts["having"])) {
                    $result .= " HAVING " . $this->generateWherePart($this->parts["having"]);
                }
                if (isset($this->parts["orderBy"])) {
                    $result .= " ORDER BY " . $this->generateOrderByPart($this->parts["orderBy"]);
                }
                if (isset($this->parts["limit"])) {
                    $result .= " LIMIT " . $this->generateLimitPart($this->parts["limit"]);
                }
        }
        return $result;
    }
    
    protected function generateSelectFieldsPart($selectFields)
    {
        if (is_string($selectFields)) {
            // FIXME: native?
            return $selectFields;
        } elseif (is_array($selectFields)) {
            if (empty($selectFields)) {
                return "NULL AS `NULL`";
            }
            $selectItems = [];
            foreach ($selectFields as $key=>$value) {
                if (is_string($value)) {
                    $selectItem = $this->quoteIdentifier($value);
                } elseif (is_array($value)) {
                    switch ($value[0]) {
                        case "aggregate":
                            $selectItem = strtoupper($value[1]) . "(*)"; // FIXME ("escape"?)
                            break;
                        case "expression":
                            // TODO
                            break;
                    }
                } else {
                    $selectItem = "NULL";
                }
                if (!is_int($key)) {
                    $selectItem .= " AS ".$this->quoteIdentifierRaw($key);
                }
                $selectItems[] = $selectItem;
            }
            return implode(", ", $selectItems);
        } else {
            return "*";
        }
    }
    
    protected function generateBaseTablePart($baseTable, $baseTableAlias)
    {
        if (is_string($baseTable)) {
            if (is_null($baseTableAlias)) {
                return $this->quoteIdentifier($baseTable);
            } else {
                return $this->quoteIdentifier($baseTable) . "AS" . $this->quoteIdentifier($baseTableAlias);
            }
        } elseif ($baseTable instanceof QueryBuilderInterface) {
            if (is_null($baseTableAlias)) {
                $baseTableAlias = "base";
            }
            return "(" . $baseTable->generateQuery() . ") AS " . $this->quoteIdentifierRaw($baseTableAlias);
        }
    }
    
    protected function generateJoinsPart($joins)
    {
        $joinItems = [];
        foreach ($joins as $joinRow) {
            $joinItem = "";
            list($type, $table, $alias, $joinCondition) = $joinRow;
            if ($type == "inner") {
                $joinItem .= "INNER JOIN";
            } else {
                $joinItem .= "LEFT JOIN";
            }
            $joinItem .= " ".$this->quoteIdentifier($table);
            if (!is_null($alias)) {
                $joinItem .= " AS ".$this->quoteIdentifierRaw($alias);
            }
            $joinItem .= " ON ".$this->generateWherePart($joinCondition, "=f");
            $joinItems[] = $joinItem;
        }
        return implode(" ", $joinItems);
    }
    
    protected function generateUpdateSetPart($saveDatas)
    {
        $updateExpressions = [];
        foreach ($saveDatas as $field=>$value) {
            if (!is_array($value)) {
                $value = array("=", $value);
            }
            $valueData = is_array($value[1]) ? array_map([$this, "resolveSpecialValue"], $value[1]) : $this->resolveSpecialValue($value[1]);
            $quotedField = $this->quoteIdentifier($field);
            $operator = $value[0];
            switch ($operator) {
                case "=":
                    $updateExpressions[] = $quotedField . "=" . $this->quote($valueData);
                    break;
                case "+":
                case "-":
                case "*":
                case "/":
                case "%":
                    $updateExpressions[] = $quotedField . "=" . $quotedField . $operator . $this->quote($valueData);
                    break;
                case "set":
                    $updateExpressions[] = $quotedField . "=" . "'" . implode(", ", array_map([$this, "escapeString"], $valueData)) . "'";
                    break;
                // TODO
            }
        }
        return implode(", ", $updateExpressions);
    }
    
    protected function generateWherePart($where, $defaultCompareMode = "=")
    {
        if (is_string($where)) {
            return $where; // FIXME SQL 92 or something? native?
        } if (is_object($where) && $where instanceof Filter) {
            $where = $where->toArray();
        }
        if (is_array($where)) {
            $wheres = [];
            foreach ($where as $field=>$value) {
                
                if (is_string($field)) {
                    $field = preg_replace("/^&+(\\(\\w*\\))?/", "", $field);
                    $field = preg_replace("/^\\\\&/", "&", $field);
                    $field = $this->quoteIdentifier($field);
                }
                
                if (is_null($value)) {
                    $value = ["null"];
                } elseif (is_bool($value)) {
                    $value = ["=", $value ? "1" : "0"];
                } elseif (!is_array($value)) {
                    $value = [$defaultCompareMode, $value];
                }
                
                if (empty($value)) {
                    $wheres[] = "NOT(" . $this->quoteIdentifier($field) . ")";
                } elseif (is_array($value[0])) { // OR relation
                    $subwheres = [];
                    foreach ($value[0] as $subwhere) {
                        $subwheres[] = $this->generateWherePart($subwhere);
                    }
                    $subwhere = "(" . implode(") OR (", $subwheres) . ")";
                    $wheres[] = $subwhere;
                } else {
                    $compareMode = $value[0];
                    $compareValue = isset($value[1]) ? $value[1] : null;
                    if (is_array($compareValue)) {
                        $compareValue = array_map([$this, "resolveSpecialValue"], $compareValue);
                    } else {
                        $compareValue = $this->resolveSpecialValue($compareValue);
                    }
                    switch ($compareMode) {
                        case '=':
                        case '>':
                        case '<':
                        case '<=':
                        case '>=':
                        case '!=':
                        case '<>':
                            $wheres[] = $field . $compareMode . $this->quote($compareValue);
                            break;
                        case '=f':
                        case '>f':
                        case '<f':
                        case '<=f':
                        case '>=f':
                        case '!=f':
                        case '<>f':
                            $wheres[] = $field . substr($compareMode, 0, -1) . $this->quoteIdentifier($compareValue);
                            break;
                        case 'null':
                            $wheres[] = "$field IS NULL";
                            break;
                        case 'notnull':
                            $wheres[] = "$field IS NOT NULL";
                            break;
                        case 'bw':
                        case 'between':
                            $wheres[] = "$field BETWEEN " . $this->quote($compareValue[0]) . " AND " . $this->quote($compareValue[1]);
                            break;
                        case 'in':
                            $wheres[] = "$field IN (" . implode(",", array_map([$this, "quote"], $compareValue)) . ")";
                            break;
                        case 'notin':
                            $wheres[] = "$field NOT IN (" . implode(",", array_map([$this,"quote"], $compareValue)) . ")";
                            break;
                        case '^':
                             $wheres[] = "$field LIKE '" . $this->escapeString($compareValue) . "%'";
                            break;
                        case '$':
                             $wheres[] = "$field LIKE '%" . $this->escapeString($compareValue) . "'";
                            break;
                        case '^$':
                        case 'like':
                             $wheres[] = "$field LIKE '" . $this->escapeString($compareValue) . "'";
                            break;
                        case '%':
                        case 'like%':
                             $wheres[] = "$field LIKE '%" . $this->escapeString($compareValue) . "%'";
                            break;
                        case 'date':
                            if (is_int($compareValue)) {
                                $compareValue = date("Y-m-d H:i:s");
                            }
                            @list($date, $time) = explode(" ", $compareValue);
                            $date = preg_replace("/\\-?\$/", "", preg_replace("/\\D/", "-", "$date"));
                            $time = preg_replace("/\\:?\$/", "", preg_replace("/\\D/", ":", "$time"));
                            if (preg_match("/^\\d+\$/", $date)) {
                                $wheres[] = "YEAR($field)='" . $this->escapeString($date) . "'";
                            } elseif (preg_match("/^\\d+\\-\\d+\$/", $date)) {
                                list($year, $month) = explode("-", $date);
                                $wheres[] = "YEAR($field)='" . $this->escapeString($year) . "' AND MONTH($field)='" . $this->escapeString($month) . "'";
                            } elseif (preg_match("/^\\d+\\-\\d+\\-\\d+\$/", $date)) {
                                $checkdate = "DATE($field)='" . $this->escapeString($date) . "'";
                                if (preg_match("/^\\d+\$/", $time)) {
                                    $wheres[] = "$checkdate AND HOUR($field)='" . $this->escapeString($time) . "'";
                                } elseif (preg_match("/^\\d+\\:\\d+\$/", $time)) {
                                    list($hour, $minute) = explode(":", $time);
                                    $wheres[] = "$checkdate AND HOUR($field)='" . $this->escapeString($hour) . "' AND MINUTE($field)='" . $this->escapeString($minute) . "'";
                                } elseif (preg_match("/^\\d+\\:\\d+:\\d+\$/", $time)) {
                                    $wheres[] = "$field='" . $this->escapeString("$date $time") . "'";
                                } else {
                                    $wheres[] = $checkdate;
                                }
                            }
                            break;
                        case 'expr':
                        case 'expression': // TODO
                            break;
                        case 'not':
                            $not = $this->generateWherePart($compareValue);
                            $wheres[] = "NOT($not)";
                            break;
                    }
                }
            }
            if ($wheres) {
                return "(" . implode(") AND (", $wheres) . ")";
            } else {
                return "1";
            }
        } elseif (is_string($where)) {
            return $where;
        } else {
            return "0";
        }
    }
    
    protected function generateGroupByPart($groupBy)
    {
        if (is_array($groupBy)) {
            return implode(", ", array_map([$this, "quoteIdentifier"], $groupBy)); // FIXME: group by expression
        } else {
            return "".$groupBy; // FIXME
        }
    }
    
    protected function generateOrderByPart($orderBy)
    {
        if (is_numeric($orderBy)) {
            return "" . $orderBy; // FIXME
        } elseif (is_string($orderBy)) {
            return $orderBy; // FIXME $this->quoteIdentifier($orderBy)." ASC" or something...? // XXX: group by expression
        } elseif (is_array($orderBy)) {
            $orderByItems = array();
            foreach ($orderBy as $field => $sort) {
                if (is_string($sort)) {
                    $sort = strtoupper($sort);
                } else {
                    $sort = $sort ? "ASC" : "DESC";
                }
                $orderByItems[] = $this->quoteIdentifier($field) . " " . $sort;
            }
            return implode(", ", $orderByItems);
        } else {
            return "1";
        }
    }
    
    protected function generateLimitPart($limit)
    {
        $maxLimit = 18446744073709551615;
        if (is_array($limit)) {
            if (array_key_exists("from", $limit)) {
                $limit_from = $limit["from"];
                if (array_key_exists("limit", $limit)) {
                    $limit_limit = $limit["limit"];
                } elseif (array_key_exists("to", $limit)) {
                    $limit_limit = $limit["to"] - $limit_from;
                } else {
                    $limit_limit = $maxLimit;
                }
                $limit = $limit_from . ", " . $limit_limit;
				return "" . $limit;
            } elseif (array_key_exists("page", $limit)) { // page is zero based!
                $limit_page = $limit["page"];
                if (array_key_exists("limit", $limit)) {
                    $limit_limit = $limit["limit"];
                } else {
                    $limit_limit = 1;
                }
                $limit_from = $limit_limit * $limit_page;
                $limit = $limit_from . ", " . $limit_limit;
				return "" . $limit;
            } else {
                return "" . $maxLimit;
            } 
        } else {
            return "" . $limit;
        }
    }
    
    protected function resolveSpecialValue($value)
    {
        // FIXME: DateTimeInterface introduced in PHP 5.5
        if (is_object($value) && ($value instanceof \DateTime || $value instanceof \DateTimeInterface)) {
            $value = $value->format("Y-m-d H:i:s");
        }
        return $value;
    }
    
    /**
     * Escapes a string for using in a string in an SQL query
     *
     * @param string $str
     * @return string
     */
    public function escapeString($str)
    {
        if ($this->oConnection) {
            return $this->oConnection->escapeString($str);
        } else {
            return addslashes($str); // FIXME
        }
    }
    
    /**
     * Quotes a string for using as a string in an SQL query
     *
     * @param string $str
     * @return string
     */
    public function quoteString($str)
    {
        if ($this->oConnection) {
            return $this->oConnection->quoteString($str);
        } else {
            return "'" . $this->escapeString($str) . "'";
        }
    }
    
    /**
     * Quotes a value for using in an SQL query
     *
     * @param mixed $value
     * @return string
     */
    public function quote($value)
    {
        if ($this->oConnection) {
            return $this->oConnection->quote($value);
        } else {
            return "'" . $this->escapeString($value) . "'";
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
        // FIXME
        if ($this->oConnection) {
            return $this->oConnection->quoteIdentifier($str);
        } else {
            return "`" . preg_replace_callback('#\\\\\\\\|\\\\\\.|\\.|`#', function ($match) {
                $substr = $match[0];
                switch ($substr) {
                    case "\\\\":
                        return "\\";
                    case "\\.":
                        return ".";
                    case ".":
                        return "`.`";
                    case "`";
                        return "``";
                    default:
                        return $substr;
                }
            }, $str) . "`";
        }
    }
    
    /**
     * Quotes an atomic identifier name
     *
     * @param string $str
     * @return string
     */
    public function quoteIdentifierRaw($str)
    {
        // FIXME
        if ($this->oConnection) {
            return $this->oConnection->quoteIdentifierRaw($str);
        } else {
            return "`" . str_replace('`', '``', $str) . "`";
        }
    }
    
    /**
     * Cuts a query by quotes
     *
     * @param string $query
     * @return array
     */
    public function cutByQuotes($sql)
    {
        return preg_split('/((?:`(?:[^`]|``)*`|\'(?:[^\'\\\\]|\\\\\'|\\\\\\\\)*\'))/', $sql, -1, PREG_SPLIT_DELIM_CAPTURE);
    }
    
}
