<?php

use \PatroNet\Core\Content\Source;
use \PatroNet\Core\Content\FileStorage;
use \PatroNet\Core\Cache\ExpirableCacheSource;
use PatroNet\Core\Content\SourceTrait;

class TestSource implements Source
{
    
    use SourceTrait;
    
    public function get()
    {
        return "This content generated at " . date("Y-m-d H:i:s");
    }
    
}

$oCache = new ExpirableCacheSource(new TestSource(), new FileStorage(__DIR__."/tmp/test.cache"), 10);

echo $oCache->get();
