<?php

use \PatroNet\Core\Database\ConnectionManager;
use \PatroNet\Core\Database\Table;
use \PatroNet\Core\Database\ResultSet;

$connectionUri = ". . .";

$oConnection = (new ConnectionManager())->create($connectionUri);
$oConnection->open();

$oTable = new Table($oConnection, "opey_modules", "pn_id");
$oTable->addRelation("l", ["l.id" => "self.oeLayout"], "opey_arculat_param");

var_dump(count($oTable));

echo "<hr size='5' color='#FF0000' />";

foreach ($oTable->getAll(["pn_name" => ["^", "C"]], ["pn_name" => "asc"], 4) as $row) {
    var_dump($row);
    echo "<hr />";
}
echo "<hr size='5' color='#FF0000' />";

foreach ($oTable->getAll(["pn_name" => ["^", "C"]], null, 2)->setFetchMode(ResultSet::FETCH_NUM) as $row) {
    var_dump($row);
    echo "<hr />";
}

echo "<hr size='5' color='#FF0000' />";

var_dump($oTable->get(["pn_id" => 119, "pn_type" => 1]));

echo "<hr size='5' color='#FF0000' />";

var_dump($oTable->getColumn("pn_name", ["pn_name" => ["^", "A"]], ["pn_name" => "desc"])->fetchAll());

echo "<hr size='5' color='#FF0000' />";

var_dump($oTable->getAll(["pn_name" => ["^", "A"]], ["pn_name" => "desc"], 3, ["self.pn_id", "self.pn_name", "l.id", "l.Name"])->fetchAll());

