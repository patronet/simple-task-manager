<?php

namespace PatroNet\SimpleTaskManager\Model;

use PatroNet\Core\Entity\ActiveRecordEntity;
use PatroNet\Core\Database\ActiveRecord;


/**
 * Represents a sprint
 */
class Sprint extends ActiveRecordEntity
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

    /**
     * Gets default sprint repository
     *
     * @return SprintRepository
     */
    public static function getRepository()
    {
        if (is_null(self::$oRepository)) {
            self::$oRepository = new SprintRepository();
        }
        return self::$oRepository;
    }
    
}
