<?php

namespace PatroNet\Core\Entity;

use PatroNet\Core\Database\ActiveRecord;
use PatroNet\Core\Database\ResultSet;
use PatroNet\Core\Database\Table;


/**
 * Database table based repository
 */
class TableRepository implements Repository
{
    
    private $oTable;
    
    private $filter;
    
    private $defaultValues;
    
    public function __construct(Table $oTable, $filter = null, $defaultValues = [])
    {
        $this->oTable = $oTable;
        $this->filter = $filter;
        $this->defaultValues = $defaultValues;
    }
    
    public function getTable()
    {
        return $this->oTable;
    }
    
    public function getFilter()
    {
        return $this->filter;
    }
    
    /**
     * Creates a new unsaved entity
     *
     * @return \PatroNet\Core\Entity\ActiveRecordEntity
     */
    public function create()
    {
        return $this->wrapActiveRecordToEntity($this->createActiveRecord());
    }
    
    /**
     * Gets an entity by it ID
     *
     * @param int $id
     * @return \PatroNet\Core\Entity\ActiveRecordEntity|null
     */
    public function get($id)
    {
        $oActiveRecord = $this->oTable->getFirst($this->getRawFilterByIdOrIds($id), null, null, Table::FETCH_ACTIVE);
        if (empty($oActiveRecord)) {
            return null;
        }
        return $this->wrapActiveRecordToEntity($oActiveRecord);
    }
    
    /**
     * Gets multiple entities by IDs
     *
     * @param int[] $idList
     * @param string[string] $order
     * @param mixed $limit
     * @return \PatroNet\Core\Entity\ActiveRecordEntity[]|Iterable
     */
    public function getAll($idList = null, $order = null, $limit = null)
    {
        return $this->getAllByRawFilter($this->getRawFilterByIdOrIds($idList), $order, $limit);
    }
    
    /**
     * Gets multiple entities by a filter
     *
     * @param mixed $filter
     * @param string[string] $order
     * @param mixed $limit
     * @return \PatroNet\Core\Entity\ActiveRecordEntity[]|Iterable
     */
    public function getAllByFilter($filter = null, $order = null, $limit = null)
    {
        return $this->getAllByRawFilter($this->getRawFilterByFilter($filter), $order, $limit);
    }
    
    private function getAllByRawFilter($filter, $order, $limit)
    {
        $self = $this;
        return $this->oTable->getAll($filter, $order, $limit, null, ResultSet::FETCH_CALLBACK, function ($oActiveRecord) use ($self) {
            return $self->wrapActiveRecordToEntity($oActiveRecord);
        }, Table::FETCH_ACTIVE);
    }
    
    /**
     * Removes an entity by its ID or by its name
     *
     * @param int $id
     * @return boolean
     */
    public function delete($id)
    {
        $oEntity = $this->get($id);
        if (!is_null($oEntity)) {
            return $oEntity->delete();
        } else {
            return true;
        }
    }
    
    /**
     * Removes multiple entities by IDs
     *
     * @param int[]|null $idList
     * @return boolean
     */
    public function deleteAll($idList = null)
    {
        foreach ($this->getAll($idList) as $oEntity) {
            if (!$oEntity->delete()) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Checks whether the entity exists
     *
     * @param int $id
     * @return boolean
     */
    public function exists($id)
    {
        return $this->oTable->existsAny($this->getRawFilterByIdOrIds($id));
    }

    /**
     * @param \PatroNet\Core\Database\ActiveRecord
     */
    protected function createActiveRecord()
    {
        $oActiveRecord = new ActiveRecord($this->oTable);
        foreach ($this->defaultValues as $key => $value) {
            $oActiveRecord[$key] = $value;
        }
        return $oActiveRecord;
    }
    
    /**
     * @param \PatroNet\Core\Database\ActiveRecord $oActiveRecord
     * @return \PatroNet\Core\Entity\ActiveRecordEntity
     */
    protected function wrapActiveRecordToEntity(ActiveRecord $oActiveRecord)
    {
        return new ActiveRecordEntity($oActiveRecord);
    }
    
    private function getRawFilterByIdOrIds($idOrIds)
    {
        if (is_null($idOrIds)) {
            return $this->filter;
        }
        
        $uniqueKey = $this->oTable->getUniqueKey();
        
        $filters = [];
        
        if (is_array($uniqueKey)) {
            $ids = (empty($idOrIds) || (isset($idOrIds[0]) && is_array($idOrIds[0]))) ? $idOrIds : [$idOrIds];
            foreach ($ids as $id) {
                $idFilter = [];
                $i = 0;
                foreach ($uniqueKey as $uniqueKeyField) {
                    $idFilter[$uniqueKeyField] = isset($id[$uniqueKeyField]) ? $id[$uniqueKeyField] : $id[$i];
                    $i++;
                }
                $filters[] = $idFilter;
            }
        } else {
            if (is_array($idOrIds)) {
                $filters[0][$uniqueKey] = ["in", $idOrIds];
            } else {
                $filters[0][$uniqueKey] = $idOrIds;
            }
        }
        
        if (is_null($this->filter)) {
            if (empty($filters)) {
                return null;
            } else if (count($filters) == 1) {
                return $filters[0];
            } else {
                return [[$filters]];
            }
        } else {
            return [[[$this->filter]], [$filters]];
        }
    }
    
    private function getRawFilterByFilter($filter)
    {
        if (is_null($this->filter)) {
            return $filter;
        } else if (is_null($filter)) {
            return $this->filter;
        } else {
            return [[[$this->filter]], [[$filter]]];
        }
    }
    
}


