<?php

use \PatroNet\Core\Database\QueryBuilderDriver\Mysql\QueryBuilder as MysqlQueryBuilder;
use \PatroNet\Core\Database\CommonFilter;
use \PatroNet\Core\Database\StaticFilter;

header("Content-type: text/plain; charset=utf-8");

$oQueryBuilder = new MysqlQueryBuilder();
$oQueryBuilder
    ->select("*")
    ->from("table1", "t1")
    ->leftJoin("table2", "t2", ["t2.id" => "t1.t2_id"])
    ->where((new CommonFilter())
        ->addAnd("t1.datetime_created", ">", new DateTime())
        ->addAnd("t1.datetime_created", "<", (new DateTime())->add(new DateInterval("PT5M")))
    )
;

echo wordwrap($oQueryBuilder->generateQuery());
echo "\n----------------------------------------------------------------------\n\n";

$oQueryBuilder = new MysqlQueryBuilder();
$oQueryBuilder
    ->select(["a" => "t1.alma", "b" => "t2.korte"])
    ->from("table1", "t1")
    ->leftJoin("table2", "t2", ["t2.id" => "t1.t2_id"])
    ->where(new StaticFilter((new CommonFilter())
        ->addAnd("t1.visible", "1")
        ->addAnd("t1.datetime_created", ">", new DateTime())
        ->addAnd("t1.datetime_updated", ">", new DateTime())
        ->addOr("t1.datetime_checked", ">", new DateTime())
        ->toArray()
    ))
;

echo wordwrap($oQueryBuilder->generateQuery());
echo "\n----------------------------------------------------------------------\n\n";

$oQueryBuilder = new MysqlQueryBuilder();
$oQueryBuilder
    ->insert()
    ->into("table", ["alma", "korte", "cseresznye"])
    ->values(["ALMAval", "KORTEval", "CSERESZNYEval", "MEGGYval"])
;

echo wordwrap($oQueryBuilder->generateQuery());
echo "\n----------------------------------------------------------------------\n\n";

$oQueryBuilder = new MysqlQueryBuilder();
$oQueryBuilder
    ->insert()
    ->into("table")
    ->set(["a" => "aaaa", "b" => "bb", "c" => "ccc"])
;

echo wordwrap($oQueryBuilder->generateQuery());
echo "\n----------------------------------------------------------------------\n\n";

echo wordwrap($oQueryBuilder->generateQuery());
echo "\n----------------------------------------------------------------------\n\n";

$oQueryBuilder = new MysqlQueryBuilder();
$oQueryBuilder
    ->update("table")
    ->set([
        "a" => "aaaa",
        "b" => "bb",
        "c" => "ccc",
        "x" => new DateTime(),
        "y" => ["set", ["A", "B", "C'D"]],
        "z" => ["+", 4],
    ])
    ->where(["d" => "dddd"])
;

echo wordwrap($oQueryBuilder->generateQuery());
echo "\n----------------------------------------------------------------------\n\n";

