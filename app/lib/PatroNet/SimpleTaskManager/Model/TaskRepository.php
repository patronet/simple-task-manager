<?php

namespace PatroNet\SimpleTaskManager\Model;

use PatroNet\Core\Database\ActiveRecord;
use PatroNet\Core\Entity\TableRepository;
use PatroNet\SimpleTaskManager\Application;


/**
 * @method Task create()
 * @method Task get(mixed $id)
 * @method Task[]|\PatroNet\Core\Database\ResultSet getAll(int[] $idList = null, string[string] $order = null, mixed $limit = null)
 * @method Task[]|\PatroNet\Core\Database\ResultSet getAllByFilter(mixed $filter = null, string[string] $order = null, mixed $limit = null)
 */
class TaskRepository extends TableRepository
{
    
    public function __construct()
    {
        parent::__construct($oTable = Application::conn()->getTable("stm_task", "task_id", "task"));
        //$oTable->addRelation("[alias]", ["[table].[field]" => "[other table].[field]"], "[table name]");
    }

    /**
     * @param \PatroNet\Core\Database\ActiveRecord $oActiveRecord
     * @return Task
     */
    protected function wrapActiveRecordToEntity(ActiveRecord $oActiveRecord)
    {
        return new Task($oActiveRecord);
    }
    
}
