<?php

namespace PatroNet\Core\Entity;

use \PatroNet\Core\Database\ActiveRecord;


/**
 * Database active record based entity
 */
class ActiveRecordEntity implements Entity
{
    
    protected $oActiveRecord;
    
    public function __construct(ActiveRecord $oActiveRecord)
    {
        $this->oActiveRecord = $oActiveRecord;
    }
    
    /**
     * Gets the ID of the entity
     *
     * @return int
     */
    public function getId()
    {
        return $this->oActiveRecord->getId();
    }
    
    /**
     * Gets the label of the entity
     *
     * @return string
     */
    public function getLabel()
    {
        $id = $this->getId();
        $classname = (new \ReflectionClass($this))->getShortName();
        return $id ? $classname . "[" . $this->getId() . "]" : "New " . $classname;
    }
    
    /**
    * Gets the wrapped active record
    * 
    * @return \PatroNet\Core\Database\ActiveRecord
    */
    public function getActiveRecord()
    {
        return $this->oActiveRecord;
    }
    
    /**
     * Saves changes
     *
     * @return boolean
     */
    public function save()
    {
        return $this->oActiveRecord->commit();
    }
    
    public function __toString()
    {
        return $this->getLabel();
    }
}

