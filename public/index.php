<?php

namespace PatroNet\SimpleTaskManager;

use PatroNet\Core\Database\ResultSet;

error_reporting(E_ALL);
ini_set("display_errors", true);

require_once(__DIR__ . "/../lib/autoload.php");
require_once(__DIR__ . "/../app/autoload.php");

var_dump(Model\Project::getRepository()->getAll()->fetchAll(ResultSet::FETCH_ASSOC));
