<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\SimpleTaskManager\Rest\DefaultJsonServiceControllerAdapter;
use PatroNet\SimpleTaskManager\Rest\DefaultRestRequest;
use PatroNet\SimpleTaskManager\Rest\DefaultAuthenticator;

error_reporting(E_ALL);
ini_set("display_errors", true);

require_once(__DIR__ . "/../lib/autoload.php");
require_once(__DIR__ . "/../app/autoload.php");

$oController = new DefaultJsonServiceControllerAdapter(
    new MainJsonService(),
    new DefaultAuthenticator()
);

$oController->handle(new DefaultRestRequest("api"))->send();
