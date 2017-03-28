<?php

namespace PatroNet\Core\Graphics\ImageDriver;

use \PatroNet\Core\Graphics\Image;
use \PatroNet\Core\Graphics\AbstractImage;
use \PatroNet\Core\Graphics\Color;
use \PatroNet\Core\Graphics\Font;
use \PatroNet\Core\Graphics\Exception as ImageException;
use \PatroNet\Core\Graphics\Misc\Charmap;
use \PatroNet\Core\Graphics\Misc\PhpImageIo;


/**
 * Image manipulator implemented in pure PHP
 */
class PhpImage extends AbstractImage
{
    
    protected $max_memory_size = 20000;
    protected $tempdir = "";
    protected $filename = 'phpgraphic.img';
    protected $_point2hpx = 0.45;
    protected $charmap = [];
    
    protected $resinfo = null; // FIXME...
    
    
    public function __construct()
    {
        parent::__construct();
        $this->tempdir = sys_get_temp_dir();
        $this->charmap = Charmap::getCharmap();
        $this->_load_from_args(func_get_args());
    }
    
    public function __destruct()
    {
        $this->close();
    }
    
    /**
     * Checks whether the image resource is open
     *
     * @return boolean
     */
    public function isOpen()
    {
        return !is_null($this->resinfo);
    }
    
    /**
     * Creates a new image
     *
     * @param int $width
     * @param int $height
     * @param \PatroNet\Core\Graphics\Color $bgcolor
     * @return self
     */
    public function create($width = 100, $height = 100, $bgcolor = null)
    {
        if ($this->isOpen()) {
            $this->close();
        }
        if (is_null($bgcolor)) {
            $bgcolor = $this->behindColor;
        }
        if (($newres = $this->_create_empty()) === false) {
            return $this->_error("Image can not be created");
        }
        $newres["width"] = $width;
        $newres["height"] = $height;
        $size = $width * $height;
        $seq = $this->_create_seq($bgcolor);
        $pointer = $newres["pointer"];
        for ($i = 0; $i < $size; $i++) {
            fwrite($pointer, $seq);
        }
        $this->resinfo = $newres;
        return $this;
    }
    
    /**
     * Loads image from file
     *
     * @param string $file
     * @param string|null $type
     * @return self
     */
    public function loadFromFile($file, $type = null)
    {
        if (!is_file($file)) {
            return $this->_error("File not found");
        }
        if (!empty($type)) {
            if ($type == 'jpg') {
                $type = 'jpeg';
            }
        } else {
            $mime = $this->_get_mime_force($file);
            if (preg_match('/^image\\/(.+)$/', $mime, $matches)) {
                $type = $matches[1];
                if (($pos = strrpos($type, '-')) !== false) {
                    $type = substr($type, $pos + 1);
                }
            } else {
                $type = 'png';
            }
        }
        $oReader = new PhpImageIo();
        if ($oReader->read($this, $type, $file)) {
            $this->_save_filename($file);
            return $this;
        } else {
            return $this->_error("Image can not be loaded");
        }
    }
    
    /**
     * Clones another image
     *
     * @param \PatroNet\Core\Graphics\Image
     */
    public function cloneFrom(Image $oImage)
    {
        if ($oImage instanceof self) {
            $oldres = $oImage->resinfo;
            $newfile = $this->_get_uniq_name();
            $copied = @copy($res['file'],$newfile);
            if (!$copied) {
                return $this->_error("Image can not be cloned");
            }
            if (($newpointer=@fopen($newfile,'rb+'))===false) {
                return $this->_error("Image can not be cloned");
            }
            $newres = [
                'width'     => $oldres['width'],
                'height'    => $oldres['height'],
                'file'      => $newfile,
                'pointer'   => $newpointer,
            ];
            $this->resinfo = $newres;
            return $this;
        } else {
            if ($this->_clone_pixelbypixel_from($oImage)) {
                return $this;
            } else {
                return $this->_error("Image can not be cloned");
            }
        }
    }
    
    /**
     * Creates a clone of this image
     *
     * @return self
     */
    public function duplicate()
    {
        return new self($this);
    }
    
    /**
     * Gets width of the image
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->resinfo['width'];
    }
    
    /**
     * Gets height of the image
     *
     * @return int
     */
    function getHeight()
    {
        return $this->resinfo['height'];
    }
    
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
    public function cut($left, $top, $width, $height, $fill = false)
    {
        $res = $this->resinfo;
        $out = $fill;
        if ($fill) {
            if ($left >= 0 && $top >= 0 && $left + $width <= $res["width"] && $top + $height <= $res["height"]) {
                $out = false;
            }
        }
        $cutstartx = max(0, left);
        $cutendx = min($res["width"], $left + $width) - 1;
        if ($cutendx < $cutstartx) {
            return $this;
        }
        $cutstarty = max(0, $top);
        $cutendy = min($res["height"],$top + $height) - 1;
        if ($cutendy < $cutstarty) {
            return $this;
        }
        $cutwidth = $cutendx-$cutstartx+1;
        $cutheight = $cutendy-$cutstarty+1;
        if ($out) {
            // TODO...
        } else {
            if (($newres = $this->_create_empty()) === false) {
                return $this->_error("Image can not be cut");
            }
            $newres["width"] = $cutwidth;
            $newres["height"] = $cutheight;
            $pointer = $res["pointer"];
            for ($y = $cutstarty; $y <= $cutendy; $y++) {
                for ($x = $cutstartx; $x <= $cutendx; $x++) {
                    $pos = ($x + $y * $res["width"]) * 4;
                    fseek($pointer, $pos);
                    $seq = fread($pointer, 4);
                    fwrite($newres["pointer"], $seq);
                }
            }
        }
        $this->resinfo = $newres;
        $this->_destroy_image($res);
        return $this;
    }
    
    /**
     * Resizes the image
     *
     * @param int $width
     * @param int $height
     * @param string $mode
     * @return self
     */
    public function resize($width, $height, $mode = Image::RESIZE_NORMAL)
    {
        // TODO
    }
    
    /**
     * Flips the image horizontally
     *
     * @return self
     */
    public function flip()
    {
        $res = $this->resinfo;
        $num = floor($res['height'] / 2);
        for ($y = 0; $y < $num; $y++) {
            $toprow = $this->_read_row($res, $y);
            $bottommrow = $this->_read_row($res, $res['height'] - $y - 1);
            $this->_write_row($res, $res['height'] - $y - 1, $toprow);
            $this->_write_row($res, $y, $bottommrow);
        }
        return $this;
    }
    
    /**
     * Flips the image vertically
     *
     * @return self
     */
    public function flop()
    {
        $res = $this->resinfo;
        for ($y = 0; $y < $res['height']; $y++) {
            $row = $this->_read_row($res, $y);
            $flipped_row = array_reverse($row);
            $this->_write_row($res, $y, $flipped_row);
        }
        return $this;
    }
    
    /**
     * Rotates the image
     *
     * @param int $angle
     * @return self
     */
    public function rotate($angle = 180)
    {
        // TODO
    }
    
    /**
     * Pastes another image into this image
     *
     * @param \PatroNet\Core\Graphics\Image $oImage
     * @param int $left
     * @param int $top
     * @param boolean $extend
     * @return self
     */
    public function pasteImage(Image $oImage, $left, $top, $extend = false)
    {
        if ($extend) {
            // TODO
        } else {
            if ($this->_paste_pixelbypixel_from($oImage, $left, $top)) {
                return $this;
            } else {
                return $this->_error("Image can not be pasted");
            }
        }
    }
    
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
    public function drawText($text, $left, $top, $angle = 0, $align = Image::ALIGN_LEFT)
    {
        $pxsize = $this->fontSize * $this->_point2hpx;
        $rad = PI() * $angle / 180;
        $xdiff = $pxsize * cos($rad);
        $ydiff = $pxsize * sin($rad);
        $len = mb_strlen($text, 'UTF-8');
        $height = $this->getTextUpperHeight();
        $charleft = $left - $height * sin($rad);
        $chartop = $top - $height * cos($rad);
        if ($align != Image::ALIGN_LEFT) {
            $width = $this->textwidth($text);
            $hor = $width * cos($rad);
            $ver = $width * sin($rad);
            if ($align == Image::ALIGN_RIGHT) {
                $charleft -= $hor;
                $chartop -= $ver;
            } elseif ($align == Image::ALIGN_CENTER) {
                $charleft -= round($hor / 2);
                $chartop -= round($ver / 2);
            }
        }
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $diff = $this->_draw_character($this->resinfo, $char, round($charleft), round($chartop), $angle);
            $charleft += $diff['x'];
            $chartop += $diff['y'];
        }
        return $this;
    }
    
    /**
     * Gets width of a text
     *
     * @param string $text
     * @param int|null $fontsize
     * @return self
     */
    public function getTextWidth($text, $fontsize = null)
    {
        if (is_null($fontsize)) {
            $fontsize = $this->fontSize;
        }
        $len = mb_strlen($text, 'UTF-8');
        $xlen = $this->_get_text_xlenght($text);
        return round($xlen*$this->_point2hpx * $fontsize);
    }
    
    /**
     * Gets height of upper case characters
     *
     * @param int|null $fontsize
     * @return self
     */
    public function getTextUpperHeight($fontsize = null)
    {
        if (is_null($fontsize)) {
            $fontsize = $this->fontSize;
        }
        return round($this->_point2hpx * $fontsize) + 1;
    }
    
    /**
     * Draws a point
     *
     * @param int $left
     * @param int $top
     * @return self
     */
    public function drawPoint($left, $top)
    {
        if ($this->weight == 1) {
            $this->paintPixel($left, $top, $this->color);
        } else {
            $res = $this->resinfo;
            $x = ceil($left - $this->weight / 2);
            $y = ceil($top - $this->weight / 2);
            $size = $this->weight;
            $color = $this->color;
            $antialiasing = $this->antialiasing;
            if ($this->roundedPrimitives) {
                $this->_ellipsefill($res, $x, $y, $size, $size, $color, $antialiasing);
            } else {
                $this->_rectanglefill($res, $x, $x + $size - 1, $y, $y + $size - 1, $color);
            }
        }
        return $this;
    }
    
    /**
     * Draws a line
     *
     * @param int $left1
     * @param int $top1
     * @param int $left1
     * @param int $top1
     * @return self
     */
    public function drawLine($left1, $top1, $left2, $top2)
    {
        if ($this->weight == 1) {
            if ($this->antialiasing) {
                $this->_antialiased_line($this->resinfo, $left1, $top1, $left2, $top2);
            } else 
                $this->_hard_line($this->resinfo, $left1, $top1, $left2, $top2);
            }
        else {
            // TODO: vastag vonal: rectangle illetve polygon (rounded?)
            $this->_hard_line($this->resinfo, $left1, $top1, $left2, $top2);   
        }
        return $this;
    }
    
    /**
     * Draws a rectangle
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @return self
     */
    public function drawRectangle($left, $top, $width, $height)
    {
        $sx = min($left, $left + $width - 1);
        $sy = min($top, $top + $height - 1);
        $ex = max($left, $left + $width - 1);
        $ey = max($top, $top + $height - 1);
        $res = $this->resinfo;
        if ($this->figureFill) {
            $this->_rectanglefill($res, $sx, $ex, $sy, $ey, $this->fillColor);
        }
        if ($this->figureOutline) {
            if ($this->weight>abs($width) || $this->weight>abs($height)) {
                
                $this->_rectanglefill($res,
                    floor($sx - $this->weight / 2), floor($sy - $this->weight / 2),
                    floor($ex + $this->weight / 2) - 1, floor($ey + $this->weight / 2) - 1,
                $this->fillColor);
                
            } else {
                
                $top_sx = floor($sx - $this->weight / 2);
                $top_sy = floor($sy - $this->weight / 2);
                $top_ex = floor($ex - $this->weight / 2);
                $top_ey = $top_sy + $this->weight - 1;
                $this->_rectanglefill($res, $top_sx, $top_ex, $top_sy, $top_ey, $this->color);
                
                $right_sx = floor($ex - $this->weight / 2) + 1;
                $right_sy = floor($sy - $this->weight / 2);
                $right_ex = $right_sx + $this->weight - 1;
                $right_ey = floor($ey - $this->weight / 2);
                $this->_rectanglefill($res, $right_sx, $right_ex, $right_sy, $right_ey, $this->color);
                
                $bottom_sx = floor($sx + $this->weight / 2);
                $bottom_sy = floor($ey - $this->weight / 2) + 1;
                $bottom_ex = floor($ex + $this->weight / 2);
                $bottom_ey = $bottom_sy + $this->weight - 1;
                $this->_rectanglefill($res, $bottom_sx, $bottom_ex, $bottom_sy, $bottom_ey, $this->color);
                
                $left_sx = floor($sx - $this->weight / 2);
                $left_sy = floor($sy + $this->weight / 2);
                $left_ex = $left_sx + $this->weight - 1;
                $left_ey = floor($ey + $this->weight / 2);
                $this->_rectanglefill($res, $left_sx, $left_ex, $left_sy, $left_ey, $this->color);
                
            }
        }
        return $this;
    }
    
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
    public function drawRoundedRectangle($left, $top, $width, $height, $radius = 0, $verradius = null)
    {
        // TODO
    }
    
    /**
     * Draws a circle
     *
     * @param int $left
     * @param int $top
     * @param int $radius
     * @return self
     */
    public function drawCircle($left, $top, $radius)
    {
        return $this->drawEllipse($left - $radius, $top - $radius, $radius * 2 + 1, $radius * 2 + 1);
    }
    
    /**
     * Draws an ellipse
     *
     * @param int $left
     * @param int $top
     * @param int $width
     * @param int $height
     * @return self
     */
    public function drawEllipse($left, $top, $width, $height)
    {
        $res = $this->resinfo;
        if ($this->figureFill) {
            $this->_ellipsefill($res, $left, $top, $width, $height, $this->fillColor, $this->_check_fill_antialiasing());
        }
        if ($this->figureOutline) {
            $this->_ellipseline($res, $left, $top, $width, $height, $this->weight, $this->color, $this->antialiasing);
        }
        return $this;
    }
    
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
    public function drawArcline($left, $top, $radius, $startangle, $endangle)
    {
        // TODO
    }
    
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
    public function drawArc($left, $top, $radius, $startangle, $endangle)
    {
        // TODO
    }
    
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
    public function drawEllipseArcline($left, $top, $width, $height, $startangle, $endangle)
    {
        // TODO
    }
    
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
    public function drawEllipseArc($left, $top, $width, $height, $startangle, $endangle)
    {
        // TODO
    }
    
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
    public function drawBezier($left1, $top1, $left2, $top2, $left3, $top3, $left4, $top4)
    {
        // TODO
    }
    
    /**
     * Draws a polygon
     *
     * @param int[][] $left1
     * @return self
     */
    public function drawPolygon($points)
    {
        $coords = $this->_create_pointarr($points);
        if ($this->figureFill) {
            $this->_polyfill($this->resinfo, $coords['x'], $coords['y'], $this->color, $this->antialiasing);
        }
        if ($this->figureOutline) {
            $this->_polyline($this->resinfo, $coords['x'], $coords['y'], $this->weight, $this->color, $this->antialiasing);
        }
        return $this;
    }
    
    /**
     * Runs fill from the given point
     *
     * @param int $left
     * @param int $top
     * @return self
     */
    public function fill($left, $top)
    {
        $res = $this->resinfo;
        $findcolor = $this->_read_pixel($res, $left, $top);
        if ($findcolor) {
            $this->_fill($res, $left, $top, $findcolor, $this->fillColor);
        }
        return $this;
    }
    
    /**
     * Apply an effect
     *
     * @param string $effect
     * @param mixed $params
     * @return self
     */
    public function effect($effect, $params = false)
    {
        $res = $this->resinfo;
        $hmax = $res["width"] - 1;
        $vmax = $res["height"] - 1;
        switch ($effect) {
            case Image::EFFECT_GAMMA:
                for ($y = 0; $y < $vmax; $y++) {
                    for ($x = 0; $x < $hmax; $x++) {
                        $oColor = $this->_read_pixel($res, $x, $y);
                        $oNewColor = $oColor->gamma($params);
                        $this->_write_pixel($res, $x, $y, $oNewColor);
                    }
                }
                return $this;
            case Image::EFFECT_INVERT:
                for ($y = 0; $y <= $vmax; $y++) {
                    for ($x = 0; $x <= $hmax; $x++) {
                        $oColor = $this->_read_pixel($res ,$x, $y);
                        $oNewColor = $oColor->invert();
                        $this->_write_pixel($res, $x, $y, $oNewColor);
                    }
                }
                return $this;
            default:
                return $this->_error("Unknown effect");
        }
    }
    
    /**
     * Paints on a specified pixel
     *
     * @param int $left
     * @param int $top
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function paintPixel($left, $top, $color = null)
    {
        if (is_null($color)) {
            $color = $this->color;
        }
        $this->_paint_pixel($this->resinfo, $left, $top, $color);
        return $this;
    }
    
    /**
     * Sets a specified pixel
     *
     * @param int $left
     * @param int $top
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setPixel($left, $top, $color = null)
    {
        if (is_null($color)) {
            $color = $this->color;
        }
        $this->_write_pixel($this->resinfo, $left,  $top, $color);
        return $this;
    }
    
    /**
     * Gets a pixel's color
     *
     * @param int $left
     * @param int $top
     * @return \PatroNet\Core\Graphics\Color
     */
    public function getPixel($left, $top)
    {
        return $this->_read_pixel($this->resinfo, $left, $top);
    }
    
    /**
     * Prints image content
     *
     * @param string $type
     * @param boolean $flushHeader
     */
    public function flush($type = 'png', $flushHeader = false)
    {
        if ($flushHeader) {
            $this->_flush_type_header($type);
        }
        $oWriter = new PhpImageIo();
        if ($oWriter->write($this, $type)) {
            return $this;
        } else {
            return $this->_error("Image can not be flushed");
        }
    }
    
    /**
     * Gets image content as binary string
     *
     * @param string $type
     * @return string
     */
    public function get($type = 'png')
    {
        ob_start();
        $this->flush($type, false);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    /**
     * Saves image to file
     *
     * @param string|null $file
     * @param string|null $type
     * @return self
     */
    public function save($file = null, $type = null)
    {
        $file = $this->_handle_tosave_filename($file);
        if (is_null($file)) {
            return $this->_error("Image can not be saved");
        }
        $default_type = 'png';
        if (is_null($type)) {
            $type = $default_type;
            if (($pos=strrpos($file, '.')) !== false) {
                $type = substr($file, $pos + 1);
            }
        }
        if ($type == 'jpg') {
            $type = 'jpeg';
        }
        $oWriter = new PhpImageIo();
        if ($oWriter->write($this, $type, $file)) {
            $this->_save_filename($file);
            return $this;
        } else {
            return $this->_error("Image can not be saved");
        }
    }
    
    /**
     * Frees allocated memory
     *
     * @return self
     */
    function free()
    {
        if ($this->resinfo) {
            $this->_destroy_image($this->resinfo);
        }
        $this->resinfo = null;
        return $this;
    }
    
    
    /* PRIVATES */
    
    protected function _create_empty()
    {
        $file = $this->_get_uniq_name();
        if (($creator = @fopen($file, 'w')) === false) {die($file);
            return false;
        }
        @fclose($creator);
        $pointer = @fopen($file, 'rb+');
        if ($pointer === false) {
            return false;
        }
        return [
            'file'      => $file,
            'pointer'   => $pointer,
            'width'     => 0,
            'height'    => 0,
        ];
    }
    
    protected function _destroy_image($res)
    {
        @fclose($res["pointer"]);
        @unlink($res["file"]);
        $res["width"] = 0;
        $res["height"] = 0;
        $res["pointer"] = null;
        $res["file"] = "";
    }
    
    protected function _read_pixel($res, $left, $top)
    {
        if ($left < 0 || $top < 0 || $left > $res["width"] - 1 || $top > $res["height"] - 1) {
            return $this->behindColor();
        }
        $pointer = $res["pointer"];
        $pos = ($top * $res["width"] + $left) * 4;
        fseek($pointer, $pos);
        $seq = fread($pointer, 4);
        return new Color(ord($seq[0]), ord($seq[1]), ord($seq[2]), ord($seq[3]) / 200);
    }
    
    protected function _write_pixel($res, $left, $top, $color)
    {
        if ($left < 0 || $top < 0 || $left > $res["width"] - 1 || $top > $res["height"] - 1) {
            return;
        }
        $pointer = $res["pointer"];
        $pos = ($top * $res["width"] + $left) * 4;
        $seq = $this->_create_seq($color);
        fseek($pointer, $pos);
        @fwrite($pointer, $seq);
    }
    
    protected function _paint_pixel($res, $left, $top, $color)
    {
        if ($left < 0 || $top < 0 || $left > $res["width"] - 1 || $top > $res["height"] - 1) {
            return;
        }
        $pointer = $res["pointer"];
        $pos = ($top * $res["width"] + $left) * 4;
        if ($color->hasTransparency()) {
            $refcolor = $this->_read_pixel($res, $left, $top);
            $color = $refcolor->coverWith($color);
        }
        $seq = $this->_create_seq($color);
        fseek($pointer, $pos);
        @fwrite($pointer, $seq);
    }
    
    protected function _check_fill_antialiasing() {
        return ($this->antialiasing && (!$this->figureOutline || (isset($this->color[3]) && $this->color[3] < 1)));
    }
    
    protected function _create_seq($color)
    {
        $seq = chr($color->getRed()) . chr($color->getGreen()) . chr($color->getBlue()) . chr(round($color->getAlpha() * 200));
        return $seq;
    }
    
    protected function _hard_line($res, $left1, $top1, $left2, $top2)
    {
        if ($top2==$top1) {
            $step = ($left2 < $left1) ? -1 : 1;
            for ($x = $left1; $x != $left2 + $step; $x += $step) {
                $this->_paint_pixel($res, $x, $top1, $this->color);
            }
        } else {
            $scale = ($left2 - $left1) / ($top2 - $top1);
            if (abs($scale) < 1) {
                $step = ($top2 < $top1) ? -1 : 1;
                for ($y = $top1; $y != $top2+$step; $y += $step) {
                    $x = round($left1 + (($y-$top1) * $scale));
                    $this->_paint_pixel($res, $x, $y, $this->color);
                }
            } else {
                $step = ($left2 < $left1) ? -1 : 1;
                for ($x = $left1; $x != $left2 + $step; $x += $step) {
                    $y = round($top1 + ($x - $left1) / $scale);
                    $this->_paint_pixel($res, $x, $y, $this->color);
                }
            }
        }
    }
    
    protected function _antialiased_line($res, $left1, $top1, $left2, $top2)
    {
        if ($top2 == $top1) {
            $step = ($left2 < $left1) ? -1 : 1;
            for ($x = $left1; $x != $left2 + $step; $x += $step) {
                $this->_paint_pixel($res, $x, $top1, $this->color);
            }
        } else {
            $xdiff = $left2 - $left1;
            $ydiff = $top2 - $top1;
            $scale = $xdiff / $ydiff;
            if (abs($scale) < 1) {
                $radangle = atan($scale);
                $step = ($top2 < $top1) ? -1 : 1;
                $size = 1 / cos($radangle);
                $diff = tan($radangle);
                $length = $diff + $size;
                $corrlength = 1 + ($length - 1) / $size;
                $height = $size / $corrlength;
                $wing = $corrlength / 2;
                for ($y = $top1; $y != $top2 + $step; $y += $step) {
                    $floatx = $left1 + ($y - $top1) * $scale;
                    $x = floor($floatx);
                    $cpos = $floatx - $x + 0.5;
                    $start = $cpos - $wing;
                    $end = $cpos + $wing;
                    $leftopacity = ($start<0) ? abs($start) * $height : 0;
                    $rightopacity = ($end>1) ? ($end-1) * $height : 0;
                    $opacity = $size - $leftopacity - $rightopacity;
                    if ($leftopacity) {
                        $leftcolor = $this->_scale_alpha($this->color, $leftopacity);
                        $this->_paint_pixel($res, $x-1, $y, $leftcolor);
                    }
                    if ($rightopacity) {
                        $rightcolor = $this->_scale_alpha($this->color, $rightopacity);
                        $this->_paint_pixel($res, $x+1, $y, $rightcolor);
                    }
                    $color = $this->_scale_alpha($this->color, $opacity);
                    $this->_paint_pixel($res, $x, $y, $color);
                }
            } else {
                $scale = $ydiff / $xdiff;
                $radangle = atan($scale);
                $step = ($left2 < $left1) ? -1 : 1;
                $size = 1 / cos($radangle);
                $diff = tan($radangle);
                $length = $diff + $size;
                $corrlength = 1 + ($length - 1) / $size;
                $width = $size / $corrlength;
                $wing = $corrlength / 2;
                for ($x = $left1; $x != $left2 + $step; $x += $step) {
                    $floaty = $top1 + ($x - $left1) * $scale;
                    $y = floor($floaty);
                    $cpos = $floaty - $y + 0.5;
                    $start = $cpos - $wing;
                    $end = $cpos + $wing;
                    $upopacity = ($start < 0) ? abs($start) * $width : 0;
                    $downopacity = ($end > 1) ? ($end - 1) * $width : 0;
                    $opacity = $size - $upopacity - $downopacity;
                    if ($upopacity) {
                        $upcolor = $this->_scale_alpha($this->color, $upopacity);
                        $this->_paint_pixel($res, $x, $y - 1, $upcolor);
                    }
                    if ($downopacity) {
                        $downcolor = $this->_scale_alpha($this->color, $downopacity);
                        $this->_paint_pixel($res, $x, $y + 1, $downcolor);
                    }
                    $color = $this->_scale_alpha($this->color, $opacity);
                    $this->_paint_pixel($res, $x, $y, $color);
                }
            }
        }
    }
    
    
    protected function _draw_character($res, $utf8_char, $left, $top, $angle)
    {
        if (!isset($this->charmap[$utf8_char])) {
            return false;
        }
        $struct = $this->charmap[$utf8_char];
        $pxsize = $this->fontSize * $this->_point2hpx;
        $letterwidth = $pxsize * $struct[0];
        $rad = PI() * $angle / 180;
        $rs = count($struct);
        for ($i = 1; $i < $rs; $i++) {
            $poly = $struct[$i];
            $num = count($poly);
            if ($num < 4 || $num % 2) {
                continue;
            }
            for ($j = 0; $j < $num - 2; $j += 2) {
                $p1 = $this->_rotate_point($poly[$j], $poly[$j + 1], $angle);
                $p2 = $this->_rotate_point($poly[$j + 2], $poly[$j + 3], $angle);
                $this->_hard_line(
                    $res,
                    round($left  +$p1[0] * $pxsize), round($top + $p1[1] * $pxsize),
                    round($left + $p2[0] * $pxsize), round($top + $p2[1] * $pxsize)
                );
            }
        }
        $result['x'] = $letterwidth * cos($rad);
        $result['y'] = $letterwidth * sin($rad);
        return $result;
    }
    
    
    protected function _rotate_point($x, $y, $angle)
    {
        if ($angle % 360 == 0 || ($x == 0 && $y == 0)) {
            return [$x, $y];
        }
        $oldrad = $y ? atan($x / $y):PI() / 2;
        $diffrad = PI() * $angle / 180;
        $sin = sin($oldrad);
        $d = $sin ? $x / sin($oldrad) : $y;
        $newrad = $oldrad - $diffrad;
        return [$d * sin($newrad), $d * cos($newrad)];
    }
    
    
    protected function _get_text_xlenght($text)
    {
        $default = 1;
        $length = mb_strlen($text, 'UTF-8');
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $utf8_char = mb_substr($text, $i, 1, 'UTF-8');
            if (!isset($this->charmap[$utf8_char])) {
                $result += $defult;
            } else {
                $result += $this->charmap[$utf8_char][0];
            }
        }
        return $result;
    }
    
    
    protected function _get_uniq_name()
    {
        return $this->tempdir . "/" . $this->_create_unique_name($this->tempdir, $this->filename);
    }
    
    
    protected function _scale_alpha($color, $scale)
    {
        return new Color(
            $color->getRed(),
            $color->getGreen(),
            $color->getBlue(),
            $color->getAlpha() * $scale
        );
    }
    
    
    protected function _rectanglefill($res, $startx, $endx, $starty, $endy, $color)
    {
        for ($y = $starty; $y <= $endy; $y++) {
            for ($x = $startx; $x <= $endx; $x++) {
                $this->_paint_pixel($res, $x, $y, $color);
            }
        }
    }
    
    
    protected function _ellipsefill($res, $left, $top, $width, $height, $color, $antialiasing)
    {
        for ($y = 0; $y < $height; $y++) {
            $row = $top + $y;
            $refh = ($height / 2 - $y) * $width / $height;
            $s = sqrt($width * $width / 4 - ($refh * $refh));
            if ($antialiasing) {
                $start = ceil($width / 2 - $s);
                $end = floor($width / 2 + $s);
                $mod = fmod($s - fmod($width / 2, 1), 1);
                if ($s < $width / 2 - 1 && $mod > 0.3) {
                    $scale = ($mod > 0.6) ? 0.5 : 0.25;
                    $drawcolor = $this->_scale_alpha($color, $scale);
                    $this->_paint_pixel($res, $start + $left - 1, $row, $drawcolor);
                    $this->_paint_pixel($res, $end + $left + 1, $row, $drawcolor);
                } elseif ($s > $width / 2 - 0.5) {
                    $drawcolor = $this->_scale_alpha($color, ($s > $width / 2 - 0.1) ? 0.5 : 0.25);
                    $this->_paint_pixel($res, $start + $left - 1, $row, $drawcolor);
                    $this->_paint_pixel($res, $end + $left + 1, $row, $drawcolor);
                }
            } else {
                $start = round($width / 2 - $s);
                $end = round($width / 2 + $s);
            }
            for ($x = $start; $x <= $end; $x++) {
                $this->_paint_pixel($res, $left + $x, $row, $color);
            }
        }
    }
    
    
    protected function _ellipseline($res, $left, $top, $width, $height, $weight, $color, $antialiasing)
    {
        // TODO ...
    }
    
    
    protected function _polyfill($res, $xcoords, $ycoords, $color, $antialiasing)
    {
        $enable_fill_antialiasing = $this->_check_fill_antialiasing();
        $mintop = min($ycoords);
        $maxtop = max($ycoords);
        for ($y = $mintop; $y <= $maxtop; $y++) {
            $crosspoints = $this->_cross_polygon($y, $xcoords, $ycoords);
            $num = count($crosspoints);
            for ($i = 0; $i < $num; $i += 2) {
                if ($enable_fill_antialiasing) {
                    $start = ceil($crosspoints[$i]);
                    $end = floor($crosspoints[$i + 1]);
                    // TODO: ??? (csak a meredekseg jo... ???)
                } else {
                    $start = round($crosspoints[$i]);
                    $end = round($crosspoints[$i + 1]);
                }
                for ($x = $start; $x <= $end; $x++) {
                    $this->_paint_pixel($res, $x, $y, $this->fillColor);
                }
            }
        }
    }
    
    
    protected function _polyline($res,$xcoords, $ycoords, $weight, $color, $antialiasing)
    {
        // TODO...
    }
    
    
    protected function _cross_polygon($y, $xcoords, $ycoords)
    {
        $num = count($ycoords);
        $ycoords[] = $ycoords[0];
        $xcoords[] = $xcoords[0];
        $result = [];
        for ($i = 0; $i < $num; $i++) {
            if (min($ycoords[$i], $ycoords[$i + 1]) < $y && max($ycoords[$i], $ycoords[$i + 1]) >= $y) {
                $xdiff = $xcoords[$i + 1] - $xcoords[$i];
                $ydiff = $ycoords[$i + 1] - $ycoords[$i];
                $p = ($y == $ycoords[$i]) ? 0 : ($y - $ycoords[$i]) / $ydiff;
                $x = $xcoords[$i] + $xdiff * $p;
                $result[] = $x;
            }
        }
        sort($result);
        return $result;
    }
    
    
    protected function _create_pointarr($custom)
    {
        if (is_string($custom)) {
            $numarr = explode(",", $custom);
        } elseif (is_array($custom)) {
            $numarr = array_values($custom);
        } else {
            return false;
        }
        if (is_array($numarr[0])) {
            $result = [];
            foreach ($numarr as $point) {
                ksort($point);
                $lefts[] = array_shift($point);
                $tops[] = array_shift($point);
            }
        } else {
            $result = [];
            $num = count($numarr);
            for ($i = 0; $i < $num; $i += 2) {
                $lefts[] = $numarr[$i] * 1;
                $tops[] = $numarr[$i + 1] * 1;
            }
        }
        return ['x' => $lefts, 'y' => $tops];
    }
    
    
    protected function _read_row($res, $y) {
        $result = [];
        for ($x=0; $x < $res['width']; $x++) {
            $result[] = $this->_read_pixel($res, $x, $y);
        }
        return $result;
    }
    
    
    protected function _write_row($res, $y, $row) {
        for ($x = 0; $x < $res['width']; $x++) {
            $result[] = $this->_write_pixel($res, $x, $y, $row[$x]);
        }
    }
    
    // FIXME: jobb algoritmus: korvonal letapogatasa (egy hatarpont megkeresese, majd abbol kigyo)
    //          (while ciklus, semmi rekurzio)
    //          -> metszet-kitoltes (mint a polygonnal) (semmi rekurzio)
    // gond: nagy tomb
    // MASIK: csigavonallal!!
    protected function _fill($res, $x, $y, $findcolor, $fillcolor) {
        $xcolor = $this->_read_pixel($res, $x, $y);
        if ($xcolor->equals($findcolor)) {
            $this->_write_pixel($res, $x, $y, $fillcolor);
            $this->_fill($res, $x + 1, $y, $findcolor, $fillcolor);
            $this->_fill($res, $x - 1, $y, $findcolor, $fillcolor);
            $this->_fill($res, $x, $y + 1, $findcolor, $fillcolor);
            $this->_fill($res, $x, $y - 1, $findcolor, $fillcolor);
        }
    }
    
}
?>
