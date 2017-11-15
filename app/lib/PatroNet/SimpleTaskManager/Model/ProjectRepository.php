<?php

namespace PatroNet\SimpleTaskManager\Model;

use PatroNet\Core\Database\ActiveRecord;
use PatroNet\Core\Entity\TableRepository;
use PatroNet\SimpleTaskManager\Application;


/**
 * @method Project create()
 * @method Project get(mixed $id)
 * @method Project[]|\PatroNet\Core\Database\ResultSet getAll(int[] $idList = null, string[string] $order = null, mixed $limit = null)
 * @method Project[]|\PatroNet\Core\Database\ResultSet getAllByFilter(mixed $filter = null, string[string] $order = null, mixed $limit = null)
 */
class ProjectRepository extends TableRepository
{
    
    public function __construct()
    {
        parent::__construct($oTable = Application::conn()->getTable("stm_project", "project_id", "project"));
        //$oTable->addRelation("[alias]", ["[table].[field]" => "[other table].[field]"], "[table name]");
    }

    /**
     * @param \PatroNet\Core\Database\ActiveRecord $oActiveRecord
     * @return Project
     */
    protected function wrapActiveRecordToEntity(ActiveRecord $oActiveRecord)
    {
        return new Project($oActiveRecord);
    }
    
}
