<?php

use \PatroNet\Core\Content\BinaryString;
use \PatroNet\Core\Content\UnicodeString;
use \PatroNet\Core\Content\EncodedString;

$oStr = new UnicodeString("árvíztűrő tükörfúrógép");

header("Content-type: text/plain; charset=utf-8");
echo $oStr->substr(1, 4)->get();
