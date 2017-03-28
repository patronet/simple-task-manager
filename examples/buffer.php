<?php

namespace PatroNet\Core\Content;

$oBuffer = new MemoryBuffer(10);
$oBuffer->write("almakortecseresznyemeggy");
var_dump($oBuffer->isEmpty(), $oBuffer->read());echo "<hr />";
$oBuffer->write("asdffdsaxxx");
var_dump($oBuffer->isEmpty(), $oBuffer->read());echo "<hr />";
$oBuffer->write("vv");
var_dump($oBuffer->isEmpty(), $oBuffer->read());echo "<hr />";
var_dump($oBuffer->isEmpty(), $oBuffer->readAll());echo "<hr />";
var_dump($oBuffer->isEmpty(), $oBuffer->read());echo "<hr />";
$oBuffer->write("bbbbb");
var_dump($oBuffer->isEmpty(), $oBuffer->read());echo "<hr />";
