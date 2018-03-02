<?php

namespace PatroNet\Core\Database\ConnectionDriver\Pdo;

use PatroNet\Core\Database\ResultSet as ResultSetInterface;


/**
 * PDO result set handler
 */
class ResultSet implements ResultSetInterface
{
    
    protected $oPdoStatement;
    
    protected $index = -1;
    
    protected $row = NULL;
    
    protected $open = true;
    
    protected $fetchMode = ResultSetInterface::FETCH_ASSOC;
    protected $fetchParam1 = null;
    protected $fetchParam2 = null;
    
    /**
     * @param \PDOStatement $oPdoStatement
     */
    public function __construct(\PDOStatement $oPdoStatement)
    {
        $this->oPdoStatement = $oPdoStatement;
    }
    
    public function next()
    {
        $this->fetchNext();
    }
    
    public function current()
    {
        return $this->row;
    }
    
    public function key()
    {
        return $this->index;
    }
    
    public function valid()
    {
        if ($this->row === false) {
            if ($this->open) {
                $this->close();
            }
            return false;
        } else {
            return true;
        }
    }
    
    public function rewind()
    {
        $this->reset();
        $this->next();
    }
    
    /**
     * Counts the rows in the set
     *
     * @return int
     */
    public function count()
    {
        return $this->oPdoStatement->rowCount();
    }
    
    /**
     * Tries to open the resource
     *
     * @return boolean
     */
    public function open()
    {
        $this->rewind();
    }
    
    /**
     * Tries to close the resource
     *
     * @return boolean
     */
    public function isOpen()
    {
        return $this->open;
    }
    
    /**
     * Checks whether the resource is open
     *
     * @return boolean
     */
    public function close()
    {
        $this->oPdoStatement->closeCursor();
        $this->open = false;
    }
    
    /**
     * Resets the cursor
     *
     * @return self
     */
    public function reset()
    {
        if ($this->index>(-1)) {
            // FIXME
            $this->oPdoStatement->closeCursor();
            $this->oPdoStatement->execute();
        }
        $this->oPdoStatement->setFetchMode(\PDO::FETCH_ASSOC);
        $this->open = true;
        $this->index = -1;
        return $this;
    }
    
    /**
     * Sets the fetch mode
     *
     * @return self
     */
    public function setFetchMode($mode, $param1 = null, $param2 = null)
    {
        $this->fetchMode = $mode;
        $this->fetchParam1 = $param1;
        $this->fetchParam2 = $param2;
        return $this;
    }
    
    /**
     * Fetches the next row
     *
     * @param string|null $mode
     * @param mixed $param1
     * @param mixed $param2
     * @return boolean
     */
    public function fetchNext($mode = null, $param1 = null, $param2 = null)
    {
        if (is_null($mode)) {
            $fetchMode = $this->fetchMode;
            $fetchParam1 = $this->fetchParam1;
            $fetchParam2 = $this->fetchParam2;
        } else {
            $fetchMode = $mode;
            $fetchParam1 = $param1;
            $fetchParam2 = $param2;
        }
        $callback = array($this->oPdoStatement, "fetch");
        $parameters = array(\PDO::FETCH_ASSOC);
        switch ($fetchMode) {
            case ResultSetInterface::FETCH_ASSOC:
                $parameters = array(\PDO::FETCH_ASSOC);
                break;
            case ResultSetInterface::FETCH_NUM:
                $parameters = array(\PDO::FETCH_NUM);
                break;
            case ResultSetInterface::FETCH_ARRAY:
                $parameters = array(\PDO::FETCH_BOTH);
                break;
            case ResultSetInterface::FETCH_OBJECT:
                $parameters = array(\PDO::FETCH_OBJ);
                break;
            case ResultSetInterface::FETCH_TYPE:
                $callback = array($this->oPdoStatement, "fetchObject");
                $parameters = array($fetchParam1, $fetchParam2);
                break;
            case ResultSetInterface::FETCH_CALLBACK:
                if (is_null($fetchParam2)) {
                    $parameters = array(\PDO::FETCH_ASSOC);
                } else {
                    switch ($fetchParam2) {
                        case ResultSetInterface::FETCH_ASSOC:
                            $parameters = array(\PDO::FETCH_ASSOC);
                            break;
                        case ResultSetInterface::FETCH_NUM:
                            $parameters = array(\PDO::FETCH_NUM);
                            break;
                        case ResultSetInterface::FETCH_ARRAY:
                            $parameters = array(\PDO::FETCH_BOTH);
                            break;
                        case ResultSetInterface::FETCH_OBJECT:
                            $parameters = array(\PDO::FETCH_OBJ);
                            break;
                    }
                }
                break;
            case ResultSetInterface::FETCH_FIELD:
                if (is_null($fetchParam1)) {
                    $fetchParam1 = 0;
                }
                if (is_string($fetchParam1)) {
                    $parameters = array(\PDO::FETCH_ASSOC);
                } else {
                    if (is_numeric($fetchParam1)) {
                        intval($fetchParam1);
                    } else {
                        $fetchParam1 = 0;
                    }
                    $parameters = array(\PDO::FETCH_NUM);
                }
                break;
        }
        $result = call_user_func_array($callback, $parameters);
        if ($result === false) {
            $this->row = false;
            return false;
        } else {
            switch ($fetchMode) {
                case ResultSetInterface::FETCH_CALLBACK:
                    $result = call_user_func($fetchParam1, $result);
                    break;
                case ResultSetInterface::FETCH_FIELD:
                    $result = $result[$fetchParam1];
                    break;
            }
            $this->row = $result;
            $this->index++;
            return true;
        }
    }
    
    /**
     * Fetches the next row and returns with it
     *
     * @param string|null $mode
     * @param mixed $param1
     * @param mixed $param2
     * @return mixed
     */
    public function fetch($mode = null, $param1 = null, $param2 = null)
    {
        $this->fetchNext($mode, $param1, $param2);
        return $this->row;
    }
    
    /**
     * Fetches all the rows
     *
     * @param string|null $mode
     * @param mixed $param1
     * @param mixed $param2
     * @param boolean $reset
     * @return mixed[]
     */
    public function fetchAll($mode = null, $param1 = null, $param2 = null, $reset = true)
    {
        if (is_null($mode)) {
            $fetchMode = $this->fetchMode;
            $fetchParam1 = $this->fetchParam1;
            $fetchParam2 = $this->fetchParam2;
        } else {
            $fetchMode = $mode;
            $fetchParam1 = $param1;
            $fetchParam2 = $param2;
        }
        if ($reset) {
            $this->reset();
        }
        $result = array();
        while ($this->fetchNext($fetchMode, $fetchParam1, $fetchParam2)) {
            $result[] = $this->row;
        }
        return $result;
    }
    
}
