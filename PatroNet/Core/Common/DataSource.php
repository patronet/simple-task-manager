<?php

namespace PatroNet\Core\Common;


/**
 * Interface for data sources
 */
interface DataSource
{
    
    /**
     * Gets the data
     *
     * @return mixed
     */
    public function getData();
    
}
