<?php

namespace PatroNet\Core\Database;


/**
 * Static database filter
 */
class StaticFilter implements Filter
{
    
    protected $where;
    
    /**
     * @param mixed $where
     */
    public function __construct($where)
    {
        $this->where = $where;
    }
    
    /**
     * Gets filter data
     *
     * @return array
     */
    public function toArray()
    {
        return $this->where;
    }
    
}

