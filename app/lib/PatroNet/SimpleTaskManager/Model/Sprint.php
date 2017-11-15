<?php

namespace PatroNet\SimpleTaskManager\Model;

use PatroNet\Core\Entity\ActiveRecordEntity;
use PatroNet\Core\Database\ActiveRecord;
use PatroNet\SimpleTaskManager\Rest\JsonDataEntity;


/**
 * Represents a sprint
 */
class Sprint extends ActiveRecordEntity implements JsonDataEntity
{
    
    const STATUS_INITIAL = 'initial';
    const STATUS_PROGRESS = 'progress';
    const STATUS_CANCELED = 'canceled';
    const STATUS_COMPLETED = 'completed';
    
    private static $oRepository = null;
    
    
    public function __construct(ActiveRecord $oActiveRecord)
    {
        parent::__construct($oActiveRecord);
    }

    /**
     * Gets the project of this sprint
     *
     * @return Project
     */
    public function getProject()
    {
    	return Project::getRepository()->get($this->oActiveRecord["project_id"]);
    }
    
    /**
     * Gets tasks of this sprint
     *
     * @param string[string] $order
     * @param mixed $limit
     * @return Task[]|\PatroNet\Core\Database\ResultSet
     */
    public function getTasks($order = null, $limit = null)
    {
    	return Task::getRepository()->getAllByFilter(["task_id" => $this->getId()], $order, $limit);
    }
    
    /**
     * Creates a new task associated to this sprint
     *
     * @return Task
     */
    public function createTask()
    {
    	$oTask = Task::getRepository()->create();
    	$oTask->getActiveRecord()["sprint_id"] = $this->getId();
    	return $oTask;
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
     * Deletes this sprint
     * 
     * @return boolean
     */
    public function delete()
    {
    	// TODO
    	
        return parent::delete();
    }
    
    public function toJsonData($entityViewQueryData)
    {
        return $this->getActiveRecord()->getRow();
    }
    
    /**
     * Gets default sprint repository
     *
     * @return Sprint\_Repository
     */
    public static function getRepository()
    {
        if (is_null(self::$oRepository)) {
            self::$oRepository = new Sprint\_Repository();
        }
        return self::$oRepository;
    }
    
}

namespace PatroNet\SimpleTaskManager\Model\Sprint;

use PatroNet\Core\Database\ActiveRecord;
use PatroNet\Core\Entity\TableRepository;
use PatroNet\SimpleTaskManager\Application;
use PatroNet\SimpleTaskManager\Model\Sprint;
use PatroNet\SimpleTaskManager\Rest\JsonDataRepository;
use PatroNet\SimpleTaskManager\Rest\JsonDataTableRepositoryTrait;


/**
 * @method Sprint create()
 * @method Sprint get(mixed $id)
 * @method Sprint[]|\PatroNet\Core\Database\ResultSet getAll(int[] $idList = null, string[string] $order = null, mixed $limit = null)
 * @method Sprint[]|\PatroNet\Core\Database\ResultSet getAllByFilter(mixed $filter = null, string[string] $order = null, mixed $limit = null)
 */
class _Repository extends TableRepository implements JsonDataRepository
{
    use JsonDataTableRepositoryTrait;
    
    public function __construct()
    {
        parent::__construct($oTable = Application::conn()->getTable("stm_sprint", "sprint_id", "sprint"));
        //$oTable->addRelation("[alias]", ["[table].[field]" => "[other table].[field]"], "[table name]");
    }
    
    /**
     * @param ActiveRecord $oActiveRecord
     * @return Sprint
     */
    protected function wrapActiveRecordToEntity(ActiveRecord $oActiveRecord)
    {
        return new Sprint($oActiveRecord);
    }
    
}

