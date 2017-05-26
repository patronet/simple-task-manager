<?php

use PatroNet\Core\Autoload\Registrator;
use PatroNet\Core\Autoload\Psr0Autoloader;

Registrator::register(new Psr0Autoloader('PatroNet\\SimpleTaskManager', __DIR__ . '/lib/PatroNet/SimpleTaskManager'));
