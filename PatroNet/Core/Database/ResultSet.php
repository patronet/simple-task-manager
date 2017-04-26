<?php

namespace PatroNet\Core\Database;

use PatroNet\Core\Common\Resource;


/**
 * Interface for database result sets
 */
interface ResultSet extends \Iterator, \Countable, Resource
{
    
    const FETCH_ASSOC = "assoc";
    const FETCH_NUM = "num";
    const FETCH_ARRAY = "array";
    const FETCH_OBJECT = "object";
    const FETCH_TYPE = "type";
    const FETCH_CALLBACK = "callback";
    const FETCH_FIELD = "field";
    
    /**
     * Resets the cursor
     *
     * @return self
     */
    public function reset();
    
    /**
     * Sets the fetch mode
     *
     * @return self
     */
    public function setFetchMode($mode, $param1 = null, $param2 = null);
    
    /**
     * Fetches the next row
     *
     * @param string|null $mode
     * @param mixed $param1
     * @param mixed $param2
     * @return boolean
     */
    public function fetchNext($mode = null, $param1 = null, $param2 = null);
    
    /**
     * Fetches the next row and returns with it
     *
     * @param string|null $mode
     * @param mixed $param1
     * @param mixed $param2
     * @return mixed
     */
    public function fetch($mode = null, $param1 = null, $param2 = null);
    
    /**
     * Fetches all the rows
     *
     * @param string|null $mode
     * @param mixed $param1
     * @param mixed $param2
     * @param boolean $reset
     * @return mixed[]
     */
    public function fetchAll($mode = null, $param1 = null, $param2 = null, $reset = true);
    
}
