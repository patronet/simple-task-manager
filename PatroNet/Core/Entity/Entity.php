<?php

namespace PatroNet\Core\Entity;


/**
 * Interface for entity objects
 */
interface Entity
{
    
    /**
     * Gets the ID of the entity
     *
     * @return int
     */
    public function getId();
    
    /**
     * Gets the name of the entity
     *
     * @return string
     */
    public function getName();
    
    /**
     * Gets the label of the entity
     *
     * @return string
     */
    public function getLabel();
    
    /**
     * Gets the description of the entity
     *
     * @return string
     */
    public function getDescription();
    
    /**
     * Deletes the entity
     */
    public function delete();
    
    // FIXME/TODO
    // update
    // insert (uninserted entities)
    
}

