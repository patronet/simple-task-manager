<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\Core\Request\NormalRequest;

error_reporting(E_ALL);
ini_set("display_errors", true);

require_once(__DIR__ . "/../lib/patronet-core/autoload.php");
require_once(__DIR__ . "/../app/autoload.php");

(new ServiceController())->handle(new NormalRequest())->send();
