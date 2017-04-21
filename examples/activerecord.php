<?php

use \PatroNet\Core\Database\ConnectionManager;
use \PatroNet\Core\Database\Table;
use \PatroNet\Core\Database\ActiveRecord;


ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../autoload.php");


$connectionUri = ". . .";

$oConnection = (new ConnectionManager())->create($connectionUri);
$oConnection->open();

$oTable = new Table($oConnection, "opey_modules", "pn_id");
$oTable->addRelation("l", ["l.id" => "self.oeLayout"], "opey_arculat_param");

$oActiveRecord = $oTable->getActive(119);

$oActiveRecord["pn_name"] = "Changed name";

var_dump($oActiveRecord);

echo "<hr size='5' color='#FF0000' />";

var_dump($oActiveRecord->getRow(ActiveRecord::DATALEVEL_LOADED));

echo "<hr />";

var_dump($oActiveRecord->getRow(ActiveRecord::DATALEVEL_CHANGES));

echo "<hr />";

var_dump($oActiveRecord->getRow(ActiveRecord::DATALEVEL_MERGED));

echo "<hr />";

var_dump(iterator_to_array($oActiveRecord));

echo "<hr size='5' color='#FF0000' />";

var_dump($oTable->getAll(["pn_name" => ["^", "A"]], ["pn_name" => "desc"], 3, ["self.pn_id", "self.pn_name", "l.id", "l.Name"], Table::FETCH_ACTIVE));

