<?php

namespace PatroNet\Core\Database\ConnectionDriver\Pdo;

use PatroNet\Core\Database\PreparedStatement as PreparedStatementInterface ;

// TODO: use native

/**
 * PDO prepared statement handler
 */
class PreparedStatement
{
    
    protected $typeMap = [
        PreparedStatementInterface::PARAM_STR => \PDO::PARAM_STR,
        PreparedStatementInterface::PARAM_INT => \PDO::PARAM_INT,
    ];
    
    protected $oConnection;
    
    protected $oPdoStatement;
    
    protected $originalSql;
    
    protected $sql;
    
    protected $native;
    
    protected $paramMap = null;
    
    /**
     * @param \PatroNet\Core\Database\Connection $oConnection
     * @param string $sql
     * @param boolean $native
     */
    public function __construct(Connection $oConnection, $sql, $native)
    {
        $originalSql = $sql;
        $oPdo = $oConnection->getPdo();
        $oQueryBuilder = $oConnection->createQueryBuilder();
        // FIXME:
        if ($native) {
            $oPdo->setAttribute(\Pdo::ATTR_EMULATE_PREPARES, false);
        } else {
            $oPdo->setAttribute(\Pdo::ATTR_EMULATE_PREPARES, true);
            
            // TODO: ezt általánosítani (a régi Wrame-megoldás alapján)
            $firstpos = strpos($sql, ":");
            $lastpos = strrpos($sql, ":");
            $sameFound = false;
            if (($firstpos !== false) && ($lastpos !== false) && ($firstpos != $lastpos)) {
                $parts = $oQueryBuilder->cutByQuotes($sql);
                $paramMap = [];
                $paramIndex = 0;
                foreach ($parts as $i=>$part) {
                    if ($i % 2 == 0) {
                        $parts[$i] = preg_replace_callback("/:\\w+/", function ($match) use (&$paramIndex, &$sameFound, &$paramMap)
                            {
                                $paramIndex++;
                                $param = $match[0];
                                $paramReplacement = ":param" . $paramIndex;
                                if (in_array($param, $paramMap)) {
                                    $sameFound = true;
                                }
                                $paramMap[$paramReplacement] = $param;
                                return $paramReplacement;
                            },
                        $part);
                    }
                }
                if ($sameFound) {
                    $this->paramMap = $paramMap;
                    $sql = implode("", $parts);
                }
            }
        }
        $oPdoStatement = $oPdo->prepare($sql);
        $this->oConnection = $oConnection;
        $this->oPdoStatement = $oPdoStatement;
        $this->originalSql = $originalSql;
        $this->sql = $sql;
        $this->native = $native;
    }
    
    /**
     * Executes the query with the given values
     *
     * @param array $binds
     * @return \PatroNet\Core\Database\Result;
     */
    public function execute($binds = [])
    {
        foreach ($binds as $name=>$value) {
            if (is_array($value)) {
                $type = $value[1];
                $value = $value[0];
            } else {
                $type = PreparedStatementInterface::PARAM_AUTO ;
            }
            $this->bind($name, $value, $type);
        }
        $this->oPdoStatement->execute();
        return new Result($this->oConnection, $this->oPdoStatement);
    }
    
    /**
     * Binds a value
     *
     * @param string $name
     * @param string $value
     * @param string $type
     * @return self
     */
    public function bind($name, $value, $type = PreparedStatementInterface::PARAM_AUTO)
    {
        // TODO handle multiple occurences
        $finalType = $type;
        if ($finalType == PreparedStatementInterface::PARAM_AUTO) {
            // FIXME
            $finalType = PreparedStatementInterface::PARAM_STR;
        }
        $pdoType = $this->typeMap[$finalType];
        if ($this->native || is_null($this->paramMap)) {
            $this->oPdoStatement->bindValue($name, $value, $pdoType);
        } else {
            $params = array_keys($this->paramMap, $name);
            if (empty($params)) {
                $this->oPdoStatement->bindValue($name, $value, $pdoType);
            } else {
                foreach ($params as $param) {
                    $this->oPdoStatement->bindValue($param, $value, $pdoType);
                }
            }
        }
        return $this;
    }
    
}
