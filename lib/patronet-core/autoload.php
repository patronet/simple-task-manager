<?php

use PatroNet\Core\Autoload\Registrator;
use PatroNet\Core\Autoload\PathAutoloader;

require_once(__DIR__ . '/PatroNet/Core/Autoload/Autoloader.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/FileAutoloader.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/AutoloaderTrait.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/FileAutoloaderTrait.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/PathAutoloader.php');
require_once(__DIR__ . '/PatroNet/Core/Autoload/RegexPathAutoloader.php'); // XXX
require_once(__DIR__ . '/PatroNet/Core/Autoload/Registrator.php');

Registrator::register(new PathAutoloader('PatroNet\\Core', __DIR__ . '/PatroNet/Core'));

