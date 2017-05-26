<?php

use \PatroNet\Core\Content\UnicodeString;

$oStr = new UnicodeString("árvíztűrő tükörfúrógép");

header("Content-type: text/plain; charset=utf-8");
echo $oStr->substr(1, 4)->get();
