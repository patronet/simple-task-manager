<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\Core\Entity\ActiveRecordEntity;
use PatroNet\Core\Database\ActiveRecord;


/**
 * Represents a project
 */
class Project extends ActiveRecordEntity
{
    
    const STATUS_CREATED = 'created';
    const STATUS_ORDERED = 'ordered';
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

    /**
     * Gets default project repository
     *
     * @return ProjectRepository
     */
    public static function getRepository()
    {
        if (is_null(self::$oRepository)) {
            self::$oRepository = new ProjectRepository();
        }
        return self::$oRepository;
    }
    
}
