<?php

namespace PatroNet\Core\Database;


/**
 * Simplified database filter generator
 */
class CommonFilter implements Filter
{
    
    protected $where = [];
    
    protected $defaultCompareMode;
    
    /**
     * @param string $defaultCompareMode
     */
    public function __construct($defaultCompareMode = "=")
    {
        $this->defaultCompareMode = $defaultCompareMode;
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
    
    /**
     * Adds a subcondition with AND relation
     *
     * @return self
     */
    public function addAnd()
    {
        $keyAndValue = $this->createKeyAndValue(func_get_args());
        if ($keyAndValue !== false) {
            list($key, $value) = $keyAndValue;
            if (is_null($key)) {
                $this->where[] = $value;
            } else {
                while (array_key_exists($key, $this->where)) {
                    $key = "&".$key;
                }
                $this->where[$key] = $value;
            }
        }
        return $this;
    }
    
    /**
     * Adds a subcondition with OR relation
     *
     * @return self
     */
    public function addOr()
    {
        $keyAndValue = $this->createKeyAndValue(func_get_args());
        if ($keyAndValue !== false) {
            list($key, $value) = $keyAndValue;
            if (empty($this->where)) {
                if (is_null($key)) {
                    $this->where[] = $value;
                } else {
                    $this->where[$key] = $value;
                }
            } else {
                if (count($this->where)!=1 || !isset($this->where[0]) || !is_array($this->where[0][0])) {
                    $this->where = [[[$this->where]]];
                }
                $subwhere = [];
                if (is_null($key)) {
                    $subwhere = $value;
                } else {
                    $subwhere = $value;
                }
                $this->where[0][0][] = [$subwhere];
            }
        }
        return $this;
    }
    
    protected function createKeyAndValue($args)
    {
        $argc = count($args);
        $key = NULL;
        switch ($argc) {
            case 0:
                return false;
            case 1:
                // TODO
                break;
            case 2:
                $key = $args[0];
                $value = [$this->defaultCompareMode, $args[1]];
                break;
            case 3:
            default:
                $key = $args[0];
                $value = [$args[1], $args[2]];
                break;
        }
        return [$key, $value];
    }
    
}

