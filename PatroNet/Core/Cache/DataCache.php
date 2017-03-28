<?php

namespace PatroNet\Core\Cache;


/**
 * Interface for dataCache
 */
interface DataCache
{
	public function isValid();
	
    public function clear();  
}
