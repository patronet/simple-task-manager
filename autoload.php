<?php

use PatroNet\Core\Autoload\Registrator;
use PatroNet\Core\Autoload\Psr0Autoloader;

require_once(__DIR__ . '/PatroNet/Core/Autoload/Autoloader.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/FileAutoloader.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/AutoloaderTrait.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/FileAutoloaderTrait.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/Psr0Autoloader.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/Registrator.php');

Registrator::register(new Psr0Autoloader('PatroNet', __DIR__ . '/PatroNet'));

