<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Entity\Repository;

interface JsonDataRepository extends Repository
{
    
    public function getJsonDataList($filter = null, $orderBy = null, $limit = null, $entityViewParameters = null);
    
}