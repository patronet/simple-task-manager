<?php

use PatroNet\Core\Autoload\Registrator;
use PatroNet\Core\Autoload\PathAutoloader;

Registrator::register(new PathAutoloader('PatroNet\\SimpleTaskManager', __DIR__ . '/lib/PatroNet/SimpleTaskManager'));
