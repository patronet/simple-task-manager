<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\SimpleTaskManager\Rest\DefaultJsonServiceControllerAdapter;
use PatroNet\Core\Request\NormalRequest;

error_reporting(E_ALL);
ini_set("display_errors", true);

require_once(__DIR__ . "/../lib/autoload.php");
require_once(__DIR__ . "/../app/autoload.php");

(new DefaultJsonServiceControllerAdapter(new MainJsonService()))->handle(new NormalRequest())->send();
