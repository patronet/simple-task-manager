<?php

namespace PatroNet\Core\Graphics;

use PatroNet\Core\Common\Resource;

// TODO @throws \PatroNet\Core\Exception
// FIXME: $color -> Color $oColor
/**
 * Interface for image manipulator classes
 */
interface Image extends Resource
{
    
    const RESIZE_NORMAL = 'normal';
    const RESIZE_THUMB = 'thumb'; // maximum dim
    const RESIZE_THUMBFILL = 'thumbfill'; // maximum dim with fill
    const RESIZE_THUMBRESIZE = 'thumbresize'; // maximum dim and force resize
    const RESIZE_THUMBRESIZEFILL = 'thumbresizefill'; // maximum dim and force resize with fill
    const RESIZE_MINCUT = 'mincut'; // minimum dim and cut center middle
    const RESIZE_MIN = 'min'; // minimum dim
    
    const ALIGN_LEFT = 'left';
    const ALIGN_CENTER = 'center';
    const ALIGN_RIGHT = 'right';
    
    const EFFECT_GAMMA = 'gamma';
    const EFFECT_INVERT = 'invert';
    
    // FIXME
    const ERROR_CONTINUE_WARNING = 'continue_warning';
    const ERROR_CONTINUE_SILENT = 'continue';
    const ERROR_RETURNFALSE = 'false';
    const ERROR_EXCEPTION = 'exception';
    
    /**
     * Creates a new image
     *
     * @param int $width
     * @param int $height
     * @param \PatroNet\Core\Graphics\Color $bgcolor
     * @return boolean
     */
    public function open($width = 100, $height = 100, $bgcolor = null);
    
    /**
     * Creates a new image
     *
     * @param int $width
     * @param int $height
     * @param \PatroNet\Core\Graphics\Color $bgcolor
     * @return self
     */
    public function create($width = 100, $height = 100, $bgcolor = null);
    
    /**
     * Loads image from file
     *
     * @param string $file
     * @param string|null $type
     * @return self
     */
    public function loadFromFile($file, $type = null);
    
    /**
     * Clones another image
     *
     * @param \PatroNet\Core\Graphics\Image
     */
    public function cloneFrom(Image $oImage); // TODO
    
    /**
     * Creates a clone of this image
     *
     * @return self
     */
    public function duplicate();
    
    /**
     * Gets width of the image
     *
     * @return int
     */
    public function getWidth();
    
    /**
     * Gets height of the image
     *
     * @return int
     */
    public function getHeight();
    
    /**
     * Sets the error handling mode
     *
     * @param string $mode
     * @return self
     */
    public function setErrorMode($mode);
    
    /**
     * Sets the font
     *
     * @param \PatroNet\Core\Graphics\Font $font
     * @return self
     */
    public function setFont(Font $font);
    
    /**
     * Sets the text size
     *
     * @param int $size
     * @return self
     */
    public function setFontSize($size);
    
    /**
     * Sets the stroke color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setColor(Color $color);
    
    /**
     * Sets the fill color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setFillColor(Color $color);
    
    /**
     * Sets the behind color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setBehindColor(Color $color);
    
    /**
     * Enables or disables antialiasing
     *
     * @param boolean $enabled
     * @return self
     */
    public function setAntialiasing($enabled);
    
    /**
     * Enables or disables resize resampling
     *
     * @param boolean $enabled
     * @return self
     */
    public function setResampling($enabled);
    
    /**
     * Sets stroke weight
     *
     * @param int $weight
     * @return self
     */
    public function setWeight($weight);
    
    // FIXME/TODO: round modes (round, roundcap, square, squarecap, cross etc.)
    /**
     * Sets round mode
     *
     * @param boolean $rounded
     * @return self
     */
    public function setRoundedPrimitives($rounded);
    
    /**
     * Sets figure filling on or off
     *
     * @param boolean $fill
     * @return self
     */
    public function setFigureFill($fill);
    
    /**
     * Sets figure stroke drawing on or off
     *
     * @param boolean $drawline
     * @return self
     */
    public function setFigureOutline($drawline);
    
    /**
     * Reduce the image to a cut of the original image
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @param boolean $fill
     * @return self
     */
    public function cut($left, $top, $width, $height, $fill = false);
    
    /**
     * Resizes the image
     *
     * @param int $width
     * @param int $height
     * @param string $mode
     * @return self
     */
    public function resize($width, $height, $mode = self::RESIZE_NORMAL);
    
    /**
     * Flips the image horizontally
     *
     * @return self
     */
    public function flip();
    
    /**
     * Flips the image vertically
     *
     * @return self
     */
    public function flop();
    
    /**
     * Rotates the image
     *
     * @param int $angle
     * @return self
     */
    public function rotate($angle = 180); // FIXME: rad?
    
    /**
     * Pastes another image into this image
     *
     * @param \PatroNet\Core\Graphics\Image $oImage
     * @param int $left
     * @param int $top
     * @param boolean $extend
     * @return self
     */
    public function pasteImage(Image $oImage, $left, $top, $extend = false);
    
    /**
     * Draws text
     *
     * @param string $text
     * @param int $left
     * @param int $top
     * @param int $angle
     * @param string $align
     * @return self
     */
    public function drawText($text, $left, $top, $angle = 0, $align = self::ALIGN_LEFT);
    
    /**
     * Gets width of a text
     *
     * @param string $text
     * @param int|null $fontsize
     * @return self
     */
    public function getTextWidth($text, $fontsize = null);
    
    /**
     * Gets height of upper case characters
     *
     * @param int|null $fontsize
     * @return self
     */
    public function getTextUpperHeight($fontsize = null);
    
    /**
     * Draws a point
     *
     * @param int $left
     * @param int $top
     * @return self
     */
    public function drawPoint($left, $top);
    
    /**
     * Draws a line
     *
     * @param int $left1
     * @param int $top1
     * @param int $left1
     * @param int $top1
     * @return self
     */
    public function drawLine($left1, $top1, $left2, $top2);
    
    /**
     * Draws a rectangle
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @return self
     */
    public function drawRectangle($left, $top, $width, $height);
    
    /**
     * Draws a rectangle with rounded corners
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @param int $radius
     * @param int|null $verradius
     * @return self
     */
    public function drawRoundedRectangle($left, $top, $width, $height, $radius = 0, $verradius = null);
    
    /**
     * Draws a circle
     *
     * @param int $left
     * @param int $top
     * @param int $radius
     * @return self
     */
    public function drawCircle($left, $top, $radius);
    
    /**
     * Draws an ellipse
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @return self
     */
    public function drawEllipse($left, $top, $width, $height);
    
    /**
     * Draws part of circle line
     *
     * @param int $left
     * @param int $top
     * @param int $radius
     * @param int $startangle
     * @param int $endangle
     * @return self
     */
    public function drawArcline($left, $top, $radius, $startangle, $endangle); // FIXME
    
    /**
     * Draws a circle sector
     *
     * @param int $left
     * @param int $top
     * @param int $radius
     * @param int $startangle
     * @param int $endangle
     * @return self
     */
    public function drawArc($left, $top, $radius, $startangle, $endangle); // FIXME
    
    /**
     * Draws part of ellipse line
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @param int $startangle
     * @param int $endangle
     * @return self
     */
    public function drawEllipseArcline($left, $top, $width, $height, $startangle, $endangle); // FIXME
    
    /**
     * Draws an ellipse sector
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @param int $startangle
     * @param int $endangle
     * @return self
     */
    public function drawEllipseArc($left, $top, $width, $height, $startangle, $endangle); // FIXME
    
    /**
     * Draws bezier curve
     *
     * @param int $left1
     * @param int $top1
     * @param int $left2
     * @param int $top2
     * @param int $left3
     * @param int $top3
     * @param int $left4
     * @param int $top4
     * @return self
     */
    public function drawBezier($left1, $top1, $left2, $top2, $left3, $top3, $left4, $top4);
    
    /**
     * Draws a polygon
     *
     * @param int[][] $left1
     * @return self
     */
    public function drawPolygon($points);
    
    /**
     * Runs fill from the given point
     *
     * @param int $left
     * @param int $top
     * @return self
     */
    public function fill($left, $top);
    
    /**
     * Apply an effect
     *
     * @param string $effect
     * @param mixed $params
     * @return self
     */
    public function effect($effect, $params = false); // FIXME
    
    /**
     * Paints on a specified pixel
     *
     * @param int $left
     * @param int $top
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function paintPixel($left, $top, $color = null);
    
    /**
     * Sets a specified pixel
     *
     * @param int $left
     * @param int $top
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setPixel($left, $top, $color = null);
    
    /**
     * Gets a pixel's color
     *
     * @param int $left
     * @param int $top
     * @return \PatroNet\Core\Graphics\Color
     */
    public function getPixel($left, $top);
    
    /**
     * Prints image content
     *
     * @param string $type
     * @param boolean $flushHeader
     */
    public function flush($type = 'png', $flushHeader = false);
    
    /**
     * Gets image content as binary string
     *
     * @param string $type
     * @return string
     */
    public function get($type = 'png');
    
    /**
     * Saves image to file
     *
     * @param string|null $file
     * @param string|null $type
     * @return self
     */
    public function save($file = null, $type = null); // TODO: settings (jpg etc.)
    
    /**
     * Frees allocated memory
     *
     * @return self
     */
    public function free();
    
}
