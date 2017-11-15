<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\SimpleTaskManager\Model\Project;
use PatroNet\Core\Request\ResponseBuilder;

error_reporting(E_ALL);
ini_set("display_errors", true);

require_once(__DIR__ . "/../lib/autoload.php");
require_once(__DIR__ . "/../app/autoload.php");

(new ResponseBuilder())->initJson(Project::getRepository()->getJsonDataList(["project_id" => ["in", [1, 4, 2]]], ["label" => "asc"], 2))->build()->send();
