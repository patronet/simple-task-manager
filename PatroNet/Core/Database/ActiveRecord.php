<?php

namespace PatroNet\Core\Database;


/**
 * Active record pattern implementation
 */
class ActiveRecord implements \ArrayAccess, \IteratorAggregate
{
    
    const STATUS_BOUND = "bound";
    const STATUS_UNBOUND = "unbound";
    
    const DATALEVEL_LOADED = "loaded";
    const DATALEVEL_CHANGES = "changes";
    const DATALEVEL_MERGED = "merged";
    //const DATALEVEL_CALCULATED = "calculated"; // FIXME / TODO eg: ["+", "5"]
    
    protected $oTable;
    
    protected $id;
    
    protected $loadedRow;
    
    protected $changesRow = [];
    
    protected $status;
    
    /**
     * @param \PatroNet\Core\Database\Table $oTable
     * @param int|null $id
     * @param mixed $loadedRow
     */
    public function __construct(Table $oTable, $id = null, $loadedRow = null)
    {
        $this->oTable = $oTable;
        $this->id = $id;
        $this->loadedRow = $loadedRow;
        $this->status = is_null($loadedRow) ? self::STATUS_BOUND : self::STATUS_UNBOUND;
    }
    
    /**
     * Gets a field from the current row
     *
     * Same as getField() with DATALEVEL_MERGED.
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->getField($key);
    }
    
    /**
     * Sets the value of the given field
     *
     * Same as setField().
     *
     * @param string $key
     * @param string $value
     */
    public function offsetSet($key, $value)
    {
        return $this->setField($key, $value);
    }
    
    /**
     * Removes unsaved value of the given field if exists
     *
     * Same as rollbackField().
     *
     * @param string $fieldName
     */
    public function offsetUnset($key)
    {
        // FIXME / TODO
    }
    
    /**
     * Checks whether the field exists or is changed
     *
     * Same as checkField() with DATALEVEL_MERGED
     *
     * @param string $key
     * @return boolean
     */
    public function offsetExists($key)
    {
        // FIXME / TODO
    }
    
    /**
     * Gets iterator for the row
     *
     * @param string $dataLevel
     * @return \Iterator
     */
    public function getIterator($dataLevel = self::DATALEVEL_MERGED)
    {
        return new \ArrayIterator($this->getRow());
    }
    
    /**
     * Gets a field from the current row
     *
     * Same as getField() with DATALEVEL_MERGED.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getField($key);
    }
    
    /**
     * Sets the value of the given field
     *
     * Same as setField().
     *
     * @param string $key
     * @param string $value
     */
    public function __set($key, $value)
    {
        return $this->setField($key, $value);
    }
    
    /**
     * Checks whether the field exists or is changed
     *
     * Same as checkField() with DATALEVEL_MERGED
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        // FIXME / TODO
    }
    
    /**
     * Removes unsaved value of the given field if exists
     *
     * Same as rollbackField().
     *
     * @param string $fieldName
     */
    public function __unset($key)
    {
        // FIXME / TODO
        // FIXME: set NULL?
    }
    
    /**
     * Loads the row from the database
     */
    public function load()
    {
        // FIXME / TODO
    }
    
    /**
     * Gets the current status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Checks whether unsaved changes exist
     *
     * @return boolean
     */
    public function hasChanges()
    {
        return (count($this->changesRow) > 0);
    }
    
    /**
     * Gets the current row
     *
     * @param string $dataLevel
     * @return array
     */
    public function getRow($dataLevel = self::DATALEVEL_MERGED)
    {
        if ($this->status == self::STATUS_UNBOUND && $dataLevel != self::DATALEVEL_CHANGES) {
            $this->load();
        }
        switch ($dataLevel) {
            case self::DATALEVEL_LOADED:
                return $this->loadedRow;
            case self::DATALEVEL_CHANGES:
                return $this->changesRow;
            case self::DATALEVEL_MERGED:
                return $this->changesRow + $this->loadedRow;
        }
        return [];
    }
    
    /**
     * Gets a field from the current row
     *
     * @param string $fieldName
     * @param string $dataLevel
     * @return mixed
     */
    public function getField($fieldName, $dataLevel = self::DATALEVEL_MERGED)
    {
        $sourceRow = $this->getRow($dataLevel);
        if (array_key_exists($fieldName, $sourceRow)) {
            return $sourceRow[$fieldName];
        } else {
            return null;
        }
    }
    
    /**
     * Checks whether the field exists or is changed
     *
     * @param string $fieldName
     * @param string $dataLevel
     * @return boolean
     */
    public function checkField($fieldName, $dataLevel = self::DATALEVEL_MERGED)
    {
        $sourceRow = $this->getRow($dataLevel);
        return array_key_exists($fieldName, $sourceRow);
    }
    
    /**
     * Sets the value of the given field
     *
     * @param string $fieldName
     * @param string $value
     */
    public function setField($fieldName, $value)
    {
        $this->load();
        if (array_key_exists($fieldName, $this->loadedRow)) {
            $this->changesRow[$fieldName] = $value;
        } else {
            // FIXME/TODO: exception
        }
    }
    
    /**
     * Removes unsaved value of the given field if exists
     *
     * @param string $fieldName
     */
    public function rollbacktField($fieldName)
    {
        unset($this->changesRow[$fieldName]);
    }
    
    /**
     * Removes unsaved changes
     *
     * @return boolean
     */
    public function rollback() // FIXME: reset?
    {
        if (is_null($this->loadedRow)) {
            $this->load();
        }
        $this->changesRow = [];
        return true;
    }
    
    /**
     * Saves changes
     *
     * @return boolean
     */
    public function commit() // FIXME: save?
    {
        if (is_null($this->loadedRow)) {
            $this->load();
        }
        if (empty($this->changesRow)) {
            return true;
        }
        $this->oTable->save($this->changesRow, $this->id);
    }
    
}

