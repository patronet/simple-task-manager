<?php

namespace PatroNet\Core\Database;


/**
 * Interface for SQL error handlers
 */
interface ErrorStatus
{
    
    /**
     * Gets the standard SQL state code
     *
     * @return string|int|null
     */
    public function getSqlState();
    
    /**
     * Gets the platform speicfic error code
     *
     * @return string|int|null
     */
    public function getPlatformErrorCode();
    
    /**
     * Gets the platform speicfic error message
     *
     * @return string|null
     */
    public function getPlatformErrorDescription();
    
}
