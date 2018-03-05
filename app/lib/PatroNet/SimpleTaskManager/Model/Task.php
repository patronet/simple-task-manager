<?php

namespace PatroNet\SimpleTaskManager\Model;

use PatroNet\Core\Entity\ActiveRecordEntity;
use PatroNet\Core\Database\ActiveRecord;
use PatroNet\SimpleTaskManager\Rest\JsonDataEntity;


/**
 * Represents a task
 */
class Task extends ActiveRecordEntity implements JsonDataEntity
{
    
    const STATUS_CREATED = 'created';
    const STATUS_PROGRESS = 'progress';
    const STATUS_PAUSED = 'paused';
    const STATUS_DEVELOPED = 'developed';
    const STATUS_READY = 'ready';
    const STATUS_ACCEPTED = 'accepted';
    
    private static $oRepository = null;
    
    
    public function __construct(ActiveRecord $oActiveRecord)
    {
        parent::__construct($oActiveRecord);
    }
    
    /**
     * Gets the project where this task is (if any)
     *
     * @return Project|null
     */
    public function getProject()
    {
        return Project::getRepository()->get($this->oActiveRecord["project_id"]);
    }
    
    /**
     * Gets the sprint where this task is (if any)
     *
     * @return Sprint|null
     */
    public function getSprint()
    {
    	return Sprint::getRepository()->get($this->oActiveRecord["sprint_id"]);
    }
    
    public function setStatus($status)
    {
        $this->oActiveRecord["status"] = $status;
        return $this;
    }
    
    public function getStatus()
    {
        return $this->oActiveRecord["status"];
    }
    
    public function setLabel($label)
    {
        $this->oActiveRecord["label"] = $label;
        return $this;
    }
    
    public function getLabel()
    {
        return $this->oActiveRecord["label"];
    }
    
    /**
     * Deletes this task
     * 
     * @return boolean
     */
    public function delete()
    {
    	// TODO
    	
        return parent::delete();
    }
    
    public function toJsonData($entityViewParameters)
    {
        return $this->getActiveRecord()->getRow();
    }
    
    /**
     * Gets default task repository
     *
     * @return Task\_Repository
     */
    public static function getRepository()
    {
        if (is_null(self::$oRepository)) {
            self::$oRepository = new Task\_Repository();
        }
        return self::$oRepository;
    }
    
}


namespace PatroNet\SimpleTaskManager\Model\Task;

use PatroNet\Core\Database\ActiveRecord;
use PatroNet\Core\Entity\TableRepository;
use PatroNet\SimpleTaskManager\Application;
use PatroNet\SimpleTaskManager\Model\Task;
use PatroNet\SimpleTaskManager\Rest\JsonDataRepository;
use PatroNet\SimpleTaskManager\Rest\JsonDataTableRepositoryTrait;


/**
 * @method Task create()
 * @method Task get(mixed $id)
 * @method Task[]|\PatroNet\Core\Database\ResultSet getAll(int[] $idList = null, string[string] $order = null, mixed $limit = null)
 * @method Task[]|\PatroNet\Core\Database\ResultSet getAllByFilter(mixed $filter = null, string[string] $order = null, mixed $limit = null)
 */
class _Repository extends TableRepository implements JsonDataRepository
{
    use JsonDataTableRepositoryTrait;
    
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