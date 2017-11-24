<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Entity\Repository;

interface JsonDataRepository extends Repository
{
    
    // XXX (credentials filter???)
    public function count($filter = null);
    
    // TODO: credentials
    public function getJsonDataList($filter = null, $orderBy = null, $limit = null, $entityViewParameters = null);
    
}