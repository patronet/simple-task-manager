<?php

use \PatroNet\Core\Database\ConnectionManager;
use \PatroNet\Core\Database\Connection;
use \PatroNet\Core\Database\ConnectionDriver\Pdo\Connection as PdoConnection;
use \PatroNet\Core\Database\PreparedStatement;
use \PatroNet\Core\Database\ResultSet;

$connectionUri = ". . .";

$oConnection = (new ConnectionManager())->create($connectionUri);
$oConnection->open();

if (!$oConnection->isOpen()) {
    die($oConnection->getPlatformErrorDescription());
}

$oPreparedStatement = $oConnection->prepare("SELECT `pn_uname` FROM `opey_users` WHERE `pn_uid`=:id");

$oPreparedStatement->bind(":id", 1);
var_dump($oPreparedStatement->execute()->getResultSet()->fetchAll());

echo "<hr />";

$oPreparedStatement->bind(":id", 4, PreparedStatement::PARAM_INT);
var_dump($oPreparedStatement->execute()->getResultSet()->fetchAll());

echo "<hr />";

var_dump($oPreparedStatement->execute([":id"=>3])->getResultSet()->fetchAll());

echo "<hr />";

var_dump($oPreparedStatement->execute([":id"=>[2, PreparedStatement::PARAM_INT]])->getResultSet()->fetchAll());
