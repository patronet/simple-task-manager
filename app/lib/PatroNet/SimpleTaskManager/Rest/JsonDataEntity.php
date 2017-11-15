<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Entity\Entity;

interface JsonDataEntity extends Entity
{
    
    public function toJsonData($entityViewQueryData);
    
}