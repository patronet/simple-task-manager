<?php

namespace PatroNet\SimpleTaskManager\Model;

use PatroNet\Core\Entity\ActiveRecordEntity;
use PatroNet\Core\Database\ActiveRecord;


/**
 * Represents a task
 */
class Task extends ActiveRecordEntity
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

    /**
     * Gets default task repository
     *
     * @return TaskRepository
     */
    public static function getRepository()
    {
        if (is_null(self::$oRepository)) {
            self::$oRepository = new TaskRepository();
        }
        return self::$oRepository;
    }
    
}
