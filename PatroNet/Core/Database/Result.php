<?php

namespace PatroNet\Core\Database;


/**
 * Interface for database result handling
 */
interface Result extends ErrorStatus
{
    
    /**
     * Checks success
     *
     * @return boolean
     */
    public function isSuccess();
    
    /**
     * Gets the result set
     *
     * @return \PatroNet\Core\Database\ResultSet
     */
    public function getResultSet();
    
    /**
     * Gets last insert-id if any
     *
     * @return string|int|null
     */
    public function getLastInsertId();
    
}
