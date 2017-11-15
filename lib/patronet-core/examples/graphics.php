<?php

use \PatroNet\Core\Graphics\ImageDriver\GdImage as ImageHandler;
use \PatroNet\Core\Graphics\Color;
use \PatroNet\Core\Graphics\Font;

$oBaseColor = new Color("red");

$oImage = new ImageHandler(100, 100, $oBaseColor);

$oImage->setFigureOutline(true);
$oImage->setWeight(4);
$oImage->setColor($oBaseColor->lighten(0.5));
$oImage->setFillColor($oBaseColor->darken(0.5));
$oImage->drawRectangle(30, 30, 50, 50);

$oImage->setFont(new Font(__DIR__."/font/lighthouse.ttf"));
$oImage->setFontSize(21);
$oImage->drawText("Hello", 37, 60);

$oImage->flush("png", true);

$oImage->free();
