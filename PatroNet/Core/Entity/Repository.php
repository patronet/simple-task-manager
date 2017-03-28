<?php

namespace \PatroNet\Core\Entity;


/**
 * Interface for entity repositories
 */
interface Repository
{
    
    const SELECTOR_ID = "id";
    
    const SELECTOR_NAME = "name";
    
    // FIXME/TODO (xpath, css selector etc...)
    
    
    /**
     * Gets an entity by its ID or by its name
     *
     * @param mixed $idOrName
     * @param string $selector
     * @return \PatroNet\Core\Entity\Entity|null
     */
    public function get($idOrName, $selector = self::SELECTOR_ID);
    
    /**
     * Gets multiple entities by IDs or names
     *
     * @param mixed[] $idOrNameList
     * @param string $selector
     * @return \PatroNet\Core\Entity\Entity[]
     */
    public function getAll($idOrNameList = array(), $selector = self::SELECTOR_ID);
    
    /**
     * Removes an entity by its ID or by its name
     *
     * @param mixed $idOrName
     * @param string $selector
     */
    public function delete($idOrName, $selector = self::SELECTOR_ID);
    
    /**
     * Removes multiple entities by IDs or names
     *
     * @param mixed[] $idOrNameList
     * @param string $selector
     */
    public function deleteAll($idOrNameList = array(), $selector = self::SELECTOR_ID);
    
    /**
     * Checks whether the entity exists
     *
     * @param mixed $idOrName
     * @param string $selector
     * @return boolean
     */
    public function exists($idOrName, $selector = self::SELECTOR_ID);
    
}

