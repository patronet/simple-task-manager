<?php

namespace PatroNet\SimpleTaskManager\Model;

use PatroNet\Core\Entity\ActiveRecordEntity;
use PatroNet\Core\Database\ActiveRecord;
use PatroNet\SimpleTaskManager\Rest\JsonDataEntity;


/**
 * Represents a project
 */
class Project extends ActiveRecordEntity implements JsonDataEntity
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
     * Gets sprints of this project
     *
     * @param string[string] $order
     * @param mixed $limit
     * @return Sprint[]|\PatroNet\Core\Database\ResultSet
     */
    public function getSprints($order = null, $limit = null)
    {
    	return Sprint::getRepository()->getAllByFilter(["project_id" => $this->getId()], $order, $limit);
    }
    
    /**
     * Creates a new sprint associated to this project
     *
     * @return Sprint
     */
    public function createSprint()
    {
    	$oSprint = Sprint::getRepository()->create();
    	$oSprint->getActiveRecord()["project_id"] = $this->getId();
    	return $oSprint;
    }
    
    /**
     * Gets tasks of this project
     *
     * @param string[string] $order
     * @param mixed $limit
     * @return Task[]|\PatroNet\Core\Database\ResultSet
     */
    public function getTasks($order = null, $limit = null)
    {
        return Task::getRepository()->getAllByFilter(["project_id" => $this->getId()], $order, $limit);
    }
    
    /**
     * Gets standalone tasks of this project
     *
     * @param string[string] $order
     * @param mixed $limit
     * @return Task[]|\PatroNet\Core\Database\ResultSet
     */
    public function getStandaloneTasks($order = null, $limit = null)
    {
        return Task::getRepository()->getAllByFilter([
            "project_id" => $this->getId(),
            "has_sprint" => 0,
        ], $order, $limit);
    }
    
    /**
     * Creates a new task associated to this sprint
     *
     * @return Task
     */
    public function createTask()
    {
        $oTask = Task::getRepository()->create();
        $oTask->getActiveRecord()["project_id"] = $this->getId();
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
     * Deletes this project
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
        // XXX
        return [
            "project" => $this->getActiveRecord()->getRow(),
            "sprints" => Sprint::getRepository()->getJsonDataList(["project_id" => $this->getId()]),
        ];
    }
    
    /**
     * Gets default project repository
     *
     * @return Project\_Repository
     */
    public static function getRepository()
    {
        if (is_null(self::$oRepository)) {
            self::$oRepository = new Project\_Repository();
        }
        return self::$oRepository;
    }
    
}

namespace PatroNet\SimpleTaskManager\Model\Project;

use PatroNet\SimpleTaskManager\Application;
use PatroNet\Core\Database\ActiveRecord;
use PatroNet\Core\Entity\TableRepository;
use PatroNet\SimpleTaskManager\Model\Project;
use PatroNet\SimpleTaskManager\Rest\JsonDataRepository;
use PatroNet\SimpleTaskManager\Rest\JsonDataTableRepositoryTrait;

/**
 * @method Project create()
 * @method Project get(mixed $id)
 * @method Project[]|\PatroNet\Core\Database\ResultSet getAll(int[] $idList = null, string[string] $order = null, mixed $limit = null)
 * @method Project[]|\PatroNet\Core\Database\ResultSet getAllByFilter(mixed $filter = null, string[string] $order = null, mixed $limit = null)
 */
class _Repository extends TableRepository implements JsonDataRepository
{
    use JsonDataTableRepositoryTrait;
    
    public function __construct()
    {
        parent::__construct($oTable = Application::conn()->getTable("stm_project", "project_id", "project"));
        //$oTable->addRelation("[alias]", ["[table].[field]" => "[other table].[field]"], "[table name]");
    }
    
    /**
     * Creates a new unsaved project
     *
     * @return Project
     */
    public function create()
    {
        $oProject = parent::create();
        $oProject->getActiveRecord()["datetime_created"] = date("Y-m-d H:i:s");
        return $oProject;
    }
    
    /**
     * @param ActiveRecord $oActiveRecord
     * @return Project
     */
    protected function wrapActiveRecordToEntity(ActiveRecord $oActiveRecord)
    {
        return new Project($oActiveRecord);
    }
    
}