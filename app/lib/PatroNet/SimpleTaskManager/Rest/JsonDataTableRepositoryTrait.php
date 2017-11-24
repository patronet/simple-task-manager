<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Database\ResultSet;
use PatroNet\Core\Database\Table;

trait JsonDataTableRepositoryTrait
{
    
    public function getJsonDataList($filter = null, $orderBy = null, $limit = null, $entityViewParameters = null)
    {
        $fetchCallback = function ($oActiveRecord) use ($entityViewParameters) {
            $oEntity = $this->wrapActiveRecordToEntity($oActiveRecord);
            if ($oEntity instanceof JsonDataEntity) {
                return $oEntity->toJsonData($entityViewParameters);
            } else {
                return $oActiveRecord->getRow();
            }
        };
        // XXX filterable interface?
        return $this->getTable()->getAll($filter, $orderBy, $limit, null, ResultSet::FETCH_CALLBACK, $fetchCallback, Table::FETCH_ACTIVE)->fetchAll();
    }
    
}