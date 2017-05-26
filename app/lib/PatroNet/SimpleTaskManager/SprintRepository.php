<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\Core\Database\ActiveRecord;
use PatroNet\Core\Entity\TableRepository;
use PatroNet\SimpleTaskManager\Application;


/**
 * @method Sprint create()
 * @method Sprint get(mixed $id)
 * @method Sprint[]|\PatroNet\Core\Database\ResultSet getAll(int[] $idList = null, string[string] $order = null, mixed $limit = null)
 * @method Sprint[]|\PatroNet\Core\Database\ResultSet getAllByFilter(mixed $filter = null, string[string] $order = null, mixed $limit = null)
 */
class SprintRepository extends TableRepository
{
    
    public function __construct()
    {
        parent::__construct($oTable = Application::conn()->getTable("stm_sprint", "sprint_id", "sprint"));
        //$oTable->addRelation("[alias]", ["[table].[field]" => "[other table].[field]"], "[table name]");
    }

    /**
     * @param \PatroNet\Core\Database\ActiveRecord $oActiveRecord
     * @return Sprint
     */
    protected function wrapActiveRecordToEntity(ActiveRecord $oActiveRecord)
    {
        return new Sprint($oActiveRecord);
    }
    
}
