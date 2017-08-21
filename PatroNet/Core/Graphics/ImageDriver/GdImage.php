<?php

namespace PatroNet\Core\Graphics\ImageDriver;

use PatroNet\Core\Graphics\Image;
use PatroNet\Core\Graphics\AbstractImage;
use PatroNet\Core\Graphics\Color;
use PatroNet\Core\Graphics\Misc\PhpImageIo;


// FIXME: antialiasing?
// TODO: alpha <-> antialiasing
// TODO: roundrectangle
//        * a gorbitett szelek arc-ja nem illeszkedik a fill ellipszisere
/**
 * Image manipulator implemented with the GD extension
 */
class GdImage extends AbstractImage
{
    
    protected $emulated_formats = ["bmp"];
    
    protected $_color_lastres = null;
    protected $_fillcolor_lastres = null;
    protected $_behindcolor_lastres = null;
    protected $_rendering_lastres = null;
    
    protected $_color_allocated = null;
    protected $_fillcolor_allocated = null;
    protected $_behindcolor_allocated = null;
    
    protected $_brushres = null;
    protected $_brushres_lastsize = 0;
    protected $_brushres_lastcolor = null;
    
    protected $_gd_version = null;
    
    protected $_point2px = 1.333;
    
    protected $gdres = null;
    
    
    public function __construct()
    {
        parent::__construct();
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
        return !is_null($this->gdres);
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
        if (($res = $this->_create_image($width, $height, $bgcolor)) === false) {
            return $this->_error("Image can not be created");
        }
        $this->gdres = $res;
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
        $res = false;
        $funcname = false;
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
        if (in_array($type, $this->emulated_formats)) {
            // FIXME
            $oReader = new PhpImageIo();
            if ($oReader->read($this, $type, $file) === false) {
                return $this->_error("Image can not be loaded");
            }
            $this->_save_filename($file);
            return $this;
        } else {
            $funcname = 'imagecreatefrom' . $type;
            if ($funcname && function_exists($funcname)) {
                $res = @$funcname($file);
            }
            if (!$res) {
                $content = file_get_contents($file);
                $res = @imagecreatefromstring($content);
            }
        }
        if (!$res) {
            return $this->_error("Image can not be loaded");
        }
        $this->gdres = $res;
        $this->_save_filename($file);
        return $this;
    }
    
    /**
     * Clones another image
     *
     * @param \PatroNet\Core\Graphics\Image
     */
    public function cloneFrom(Image $oImage)
    {
        if ($oImage instanceof self) {
            $oldres = $oImage->gdres;
            $width = imagesx($oldres);
            $height = imagesy($oldres);
            if (!$newres=@imagecreatetruecolor($width, $height)) {
                return $this->_error("Image can not be initialized");
            }
            if (!@imagecopy($newres, $oldres,0, 0, 0, 0, $width, $height)) {
                return $this->_error("Image can not be cloned");
            }
            $this->gdres = $newres;
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
        return imagesx($this->gdres);
    }
    
    /**
     * Gets height of the image
     *
     * @return int
     */
    function getHeight()
    {
        return imagesy($this->gdres);
    }
    
    /**
     * Sets the stroke color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    function setColor(Color $color)
    {
        $this->_color_lastres = null;
        $this->color = $color;
        return $this;
    }
    
    /**
     * Sets the fill color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    function setFillColor(Color $color)
    {
        $this->_fillcolor_lastres = null;
        $this->fillColor = $color;
        return $this;
    }
    
    /**
     * Sets the behind color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    function setBehindColor(Color $color)
    {
        $this->_behindcolor_lastres = null;
        $this->behindColor = $color;
        return $this;
    }
    
    /**
     * Enables or disables antialiasing
     *
     * @param boolean $enabled
     * @return self
     */
    function setAntialiasing($enabled)
    {
        $this->_rendering_lastres = null;
        $this->antialiasing = $enabled;
        return $this;
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
        $oldres = $this->gdres;
        $oldwidth = imagesx($oldres);
        $oldheight = imagesy($oldres);
        $nopart = ($left >= $oldwidth || $top >= $oldheight || $left + $width <= 0 || $top + $height <= 0);
        if (!$fill && $nopart) {
            return $this;
        }
        $cutleft = max($left, 0);
        $cuttop = max($top, 0);
        $cutwidth = min($left + $width, $oldwidth) - $cutleft;
        $cutheight = min($top + $height, $oldheight) - $cuttop;
        if ($fill) {
            if (!$newres = $this->_create_image($width, $height, $this->behindColor)) {
                return $this->_error("Image can not be cut");
            }
            $pasteleft = abs(min($left, 0));
            $pastetop = abs(min($top, 0));
        } else {
            $newres = @imagecreatetruecolor($cutwidth, $cutheight);
            $pasteleft = 0;
            $pastetop = 0;
        }
        if (!$newres) {
            return $this->_error("Image can not be cut");
        }
        if (!@imagecopy($newres, $oldres, $pasteleft, $pastetop, $cutleft, $cuttop, $cutwidth, $cutheight)) {
            return $this->_error("Image can not be cut");
        }
        @imagedestroy($oldres);
        $this->gdres = $newres;
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
        if ($width == 0 && $height == 0) {
            return $this;
        }
        $oldres = $this->gdres;
        $oldwidth = imagesx($oldres);
        $oldheight = imagesy($oldres);
        if ($width == $oldwidth && $height == $oldheight) {
            return $this;
        }
        $oldrel = $oldwidth / $oldheight;
        $refrel = $height ? $width / $height : 0;
        if ($width == 0 || $height == 0 || in_array($mode, [
            Image::RESIZE_NORMAL, Image::RESIZE_THUMB, Image::RESIZE_THUMBRESIZE, Image::RESIZE_MIN
        ])) {
            if ($width != 0 && $height != 0) {
                if ($mode == Image::RESIZE_THUMB) {
                    if ($oldwidth > $width || $oldheight > $height) {
                        if ($refrel > $oldrel) {
                            $width = 0;
                        } else {
                            $height = 0;
                        }
                    } else {
                        return $this;
                    }
                } elseif ($mode == Image::RESIZE_THUMBRESIZE) {
                    if ($refrel > $oldrel) {
                        $width = 0;
                    } else {
                        $height = 0;
                    }
                } elseif ($mode == Image::RESIZE_MIN) {
                    if ($refrel < $oldrel) {
                        $width = 0;
                    } else {
                        $height = 0;
                    }
                }
            }
            if ($width == 0) {
                $newheight = $height;
                $newwidth = max(1, floor($height * $oldrel));
            } elseif ($height == 0) {
                $newheight = max(1, floor($width / $oldrel));
                $newwidth = $width;
            } else {
                $newwidth = $width;
                $newheight = $height;
            }
            if (!$newres = @imagecreatetruecolor($newwidth, $newheight)) {
                return $this->_error("Image can not be resized");
            }
            if ($this->resampling) {
                if (!@imagecopyresampled($newres, $oldres, 0, 0, 0, 0, $newwidth, $newheight, $oldwidth, $oldheight)) {
                    return $this->_error("Image can not be resized");
                }
            } else {
                if (!@imagecopyresized($newres, $oldres, 0, 0, 0, 0, $newwidth, $newheight, $oldwidth, $oldheight)) {
                    return $this->_error("Image can not be resized");
                }
            }
            @imagedestroy($oldres);
            $this->gdres = $newres;
            return $this;
        } elseif ($mode == Image::RESIZE_THUMBFILL || $mode == Image::RESIZE_THUMBRESIZEFILL) {
            if (!($newres = $this->_create_image($width, $height, $this->behindColor))) {
                return $this->_error("Image can not be resized");
            }
            if ($mode == Image::RESIZE_THUMBFILL && $oldwidth <= $width && $oldheight <= $height) {
                $newwidth = $oldwidth;
                $newheight = $oldheight;
            } else {
                if ($refrel > $oldrel) {
                    $newwidth = max(1, floor($height * $oldrel));
                    $newheight = $height;
                } else {
                    $newwidth = $width;
                    $newheight = max(1, floor($width / $oldrel));
                }
            }
            $pasteleft = round(($width - $newwidth) / 2);
            $pastetop = round(($height - $newheight) / 2);
            if ($this->resampling) {
                if (!@imagecopyresampled($newres, $oldres, $pasteleft, $pastetop, 0, 0, $newwidth, $newheight, $oldwidth, $oldheight)) {
                    return $this->_error("Image can not be resized");
                }
            } else {
                if (!@imagecopyresized($newres, $oldres, $pasteleft, $pastetop, 0, 0, $newwidth, $newheight, $oldwidth, $oldheight)) {
                    return $this->_error("Image can not be resized");
                }
            }
            @imagedestroy($oldres);
            $this->gdres = $newres;
            return $this;
        } elseif ($mode == Image::RESIZE_MINCUT) {
            if (!($newres = @imagecreatetruecolor($width, $height))) {
                return $this->_error("Image can not be resized");
            }
            if ($refrel > $oldrel) {
                $cutwidth = $oldwidth;
                $cutheight = $oldwidth / $refrel;
            } else {
                $cutwidth = $oldheight * $refrel;
                $cutheight = $oldheight;
            }
            $cutleft = floor(($oldwidth - $cutwidth) / 2);
            $cuttop = floor(($oldheight - $cutheight) / 2);
            if ($this->resampling) {
                if (!@imagecopyresampled($newres, $oldres, 0, 0, $cutleft, $cuttop, $width, $height, $cutwidth, $cutheight)) {
                    return $this->_error("Image can not be resized");
                }
            } else {
                if (!@imagecopyresized($newres, $oldres, 0, 0, $cutleft, $cuttop, $width, $height, $cutwidth, $cutheight)) {
                    return $this->_error("Image can not be resized");
                }
            }
            @imagedestroy($oldres);
            $this->gdres = $newres;
            return $this;
        } else {
            return $this->_error("Unknown resizing mode");
        }
    }
    
    /**
     * Flips the image horizontally
     *
     * @return self
     */
    function flip()
    {
        $oldres = $this->gdres;
        $width = imagesx($oldres);
        $height= imagesy($oldres);
        $newres = @imagecreatetruecolor($width, $height);
        if (!$newres) {
            return $this->_error("Image can not be flipped");
        }
        if (!@imagecopyresampled($newres, $oldres, 0, 0, 0, $height-1, $width, $height, $width, -$height)) {
            return $this->_error("Image can not be flipped");
        }
        $this->gdres = $newres;
        @imagedestroy($oldres);
        return $this;
    }
    
    /**
     * Flips the image vertically
     *
     * @return self
     */
    function flop()
    {
        $oldres = $this->gdres;
        $width = imagesx($oldres);
        $height= imagesy($oldres);
        $newres = @imagecreatetruecolor($width, $height);
        if (!$newres) {
            return $this->_error("Image can not be flopped");
        }
        if (!@imagecopyresampled($newres, $oldres, 0, 0, $width-1, 0, $width, $height, -$width, $height)) {
            return $this->_error("Image can not be flopped");
        }
        $this->gdres = $newres;
        @imagedestroy($oldres);
        return $this;
    }
    
    /**
     * Rotates the image
     *
     * @param int $angle
     * @return self
     */
    function rotate($angle = 180)
    {
        $angle = $angle % 360;
        if ($angle == 0) {
            return $this;
        }
        $oldres = $this->gdres;
        if (!function_exists("imagerotate")) {
            // FIXME/TODO
            if ($angle < 135 || $angle > 225) {
                return $this->_error("Image can not be rotated");
            }
            $width = imagesx($oldres);
            $height = imagesy($oldres);
            $newres = @imagecreatetruecolor($width, $height);
            if (!$newres) {
                return $this->_error("Image can not be rotated");
            }
            if (!@imagecopyresampled($newres, $oldres, 0, 0, $width-1, $height-1, $width, $height, -$width, -$height)) {
                return $this->_error("Image can not be rotated");
            }
            $this->gdres = $newres;
            @imagedestroy($oldres);
            return $this;
        }
        $bgres = $this->_prepare_behindcolor();
        $tempres = @imagerotate($oldres, -$angle, $bgres);
        if (!$tempres) {
            return $this->_error("Image can not be rotated");
        }
        
        // create new image because after imagerotate can not use transparency
        $newwidth = imagesx($tempres);
        $newheight = imagesy($tempres);
        if (!$newres = @imagecreatetruecolor($newwidth, $newheight)) {
            return $this->_error("Image can not be rotated");
        }
        if (!@imagecopy($newres, $tempres, 0, 0, 0, 0, $newwidth, $newheight)) {
            return $this->_error("Image can not be rotated");
        }
        
        $this->gdres = $newres;
        @imagedestroy($oldres);
        return $this;
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
        $img = $this->gdres;
        $pastewidth = $oImage->getWidth();
        $pasteheight = $oImage->getHeight();
        $pasteleft = $left;
        $pastetop = $top;
        if ($extend) {
            $width = imagesx($this->gdres);
            $height = imagesy($this->gdres);
            if (is_null($left)) {
                $left = $width;
            }
            if (is_null($top)) {
                $top = $height;
            }
            $pasteleft = max(0, $left);
            $pastetop = max(0, $top);
            $newwidth = max($left + $pastewidth, $width, $width - $left);
            $newheight = max($top + $pasteheight, $height, $height - $top);
            $diffleft = abs(min(0, $left));
            $difftop = abs(min(0 ,$top));
            if ($newwidth > $width || $newheight > $height) {
                $targetimg = $this->_create_image($newwidth, $newheight, $this->behindColor);
                if (!@imagecopy($targetimg, $this->gdres, $diffleft, $difftop, 0, 0, $width, $height)) {
                    return $this->_error("Image can not be pasted");
                }
                @imagedestroy($this->gdres);
                $this->gdres = $targetimg;
            }
        }
        if ($oImage instanceof self) {
            if (@imagecopy($this->gdres, $oImage->gdres, $pasteleft, $pastetop, 0, 0, $pastewidth, $pasteheight)) {
                return $this;
            } else {
                return $this->_error("Image can not be pasted");
            }
        } else {
            if ($this->_paste_pixelbypixel_from($oImage, $pasteleft, $pastetop)) {
                return  $this;
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
        $this->_prepare_rendering();
        $cres = $this->_prepare_color();
        $fontfile = $this->font->getFile();
        $gdversion = $this->_get_gd_version();
        $res = $this->gdres;
        $xsize = ($gdversion < 2) ? $this->fontSize : $this->fontSize / $this->_point2px;
        if ($align != Image::ALIGN_LEFT) {
            $box = imagettfbbox($xsize, $angle, $fontfile, $text);
            $hor = $box[2] - $box[0];
            $ver = $box[3] - $box[1];
            if ($align == Image::ALIGN_RIGHT) {
                $left -= $hor;
                $top -= $ver;
            } elseif ($align==Image::ALIGN_CENTER) {
                $left -= round($hor / 2);
                $top -= round($ver / 2);
            }
        }
        if (@imagettftext($res, $xsize, -$angle, $left, $top, $cres, $fontfile, $text)) {
            return $this;
        } else {
            return $this->_error("Text can not be drawn");
        }
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
        $gdversion = $this->_get_gd_version();
        $xsize = ($gdversion < 2)?$fontsize : $fontsize / $this->_point2px;
        $fontfile = $this->font->getFile();
        $box = imagettfbbox($xsize, 0, $fontfile, $text);
        $width = $box[2] - $box[0] + 1;
        return $width;
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
        $gdversion = $this->_get_gd_version();
        $xsize = ($gdversion < 2) ? $fontsize : $fontsize / $this->_point2px;
        $fontfile = $this->font->getFile();
        $box = imagettfbbox($xsize, 0, $fontfile, 'I');
        return $box[1] - $box[7];
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
        $res = $this->gdres;
        $this->_prepare_rendering();
        $cres = $this->_prepare_color();
        if ($this->weight == 1) {
            if (@imagesetpixel($res, $left, $top, $cres)) {
                return $this;
            } else {
                return $this->_error("Point can not be drawn");
            }
        } else {
            if ($this->roundedPrimitives) {
                if (!@imagefilledarc($res, $left, $top, $this->weight, $this->weight, 0, 360, $cres, IMG_ARC_PIE)) {
                    return $this->_error("Point can not be drawn");
                }
                if ($this->antialiasing) {
                    // TODO ...
                }
            } else {
                $rectleft = round($left - $this->weight / 2);
                $recttop = round($top - $this->weight / 2);
                if (@imagefilledrectangle($res, $rectleft, $recttop, $rectleft + $this->weight, $recttop + $this->weight, $cres)) {
                    return $this;
                } else {
                    return $this->_error("Point can not be drawn");
                }
            }
        }
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
        $res = $this->gdres;
        $this->_prepare_rendering();
        $cres = $this->_prepare_color();
        if ($this->weight == 1) {
            if (@imageline($res, $left1, $top1, $left2, $top2, $cres)) {
                return $this;
            } else {
                return $this->_error("Line can not be drawn");
            }
        }
        if ($left1 == $left2) {
            $rectleft = round($left1 - $this->weight / 2);
            if (!@imagefilledrectangle($res, $rectleft, $top1, $rectleft, $top2)) {
                return $this->_error("Line can not be drawn");
            }
        } elseif ($top1 == $top2) {
            $recttop = round($top1 - $this->weight / 2);
            if (!@imagefilledrectangle($res, $left1, $recttop, $left2, $recttop)) {
                return $this->_error("Line can not be drawn");
            }
        } else {
            $w = $left1 - $left2;
            $h = $top1 - $top2;
            $verdiff = ($this->weight / 2) * ($h / sqrt($w * $w + $h * $h));
            $hordiff = $verdiff * $w / $h;
            if (!@imagefilledpolygon($res, [
                $left1 + $verdiff, $top1 - $hordiff,
                $left2 + $verdiff, $top2 - $hordiff,
                $left2 - $verdiff, $top2 + $hordiff,
                $left1 - $verdiff, $top1 + $hordiff,
            ], 4, $cres)) {
                return $this->_error("Line can not be drawn");
            }
            if ($this->antialiasing) {
                @imagepolygon($res, [
                    $left1 + $verdiff, $top1 - $hordiff,
                    $left2 + $verdiff, $top2 - $hordiff,
                    $left2 - $verdiff, $top2 + $hordiff,
                    $left1 - $verdiff, $top1 + $hordiff,
                ], 4, $cres);
            }
        }
        if ($this->roundedPrimitives) {
            @imagefilledarc($res, $left1, $top1, $this->weight, $this->weight, 0, 360, $cres, IMG_ARC_PIE);
            @imagefilledarc($res, $left2, $top2, $this->weight, $this->weight, 0, 360, $cres, IMG_ARC_PIE);
        }
        if ($this->antialiasing) {
            // TODO ...
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
        if (!$this->figureFill && !$this->figureOutline) {
            return $this;
        }
        list($left, $top, $width, $height) = $this->_fix_raster_dimensions($left, $top, $width, $height);
        $left2 = $left + $width - 1;
        $top2 = $top + $height - 1;
        $filled = false;
        $res = $this->gdres;
        $this->_prepare_rendering();
        if ($this->figureFill) {
            $bgres = $this->_prepare_fillcolor();
            if (!@imagefilledrectangle($res, $left, $top, $left2, $top2, $bgres)) {
                return $this->_error("Rectangle can not be drawn");
            }
            $filled = true;
        }
        if ($this->figureOutline) {
            $outlined = false;
            $cres = $this->_prepare_color();
            if ($this->weight==1) {
                if (@imagerectangle($res, $left, $top, $left2, $top2, $cres)) {
                    $outlined = true;
                }
            } else {
                if ($this->weight>min($width,$height)) {
                    $xw = $this->weight/2;
                    $outlined = @imagefilledrectangle($res, $left - $xw, $top - $xw, $left2 + $xw, $top2 + $xw, $cres);
                } else {
                    $sign = (($left2 - $left) * ($top2 - $top) < 0) ? - 1 : 1;
                    $leftdiff = ($this->weight - 1) / 2;
                    $topdiff = $sign * $leftdiff;
                    $p1 = [$left - $leftdiff, $top + $topdiff];
                    $p2 = [$left2 - $leftdiff + 1, $top - $topdiff];
                    $p3 = [$left2 + $leftdiff + 1, $top2 - $topdiff + 1];
                    $p4 = [$left + $leftdiff, $top2 + $topdiff + 1];
                    $outlined = @imagefilledrectangle($res, $p1[0], $p1[1], $p2[0] - 1, $p2[1], $cres);
                    @imagefilledrectangle($res, $p2[0], $p2[1], $p3[0], $p3[1] - 1, $cres);
                    @imagefilledrectangle($res, $p3[0], $p3[1], $p4[0] + 1, $p4[1], $cres);
                    @imagefilledrectangle($res, $p4[0], $p4[1], $p1[0], $p1[1] + 1, $cres);
                }
            }
        }
        if (!$filled && !$outlined) {
            return $this->_error("Rectangle can not be drawn");
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
        if (!$this->figureFill && !$this->figureOutline) {
            return $this;
        }
        if ($radius==0) {
            return $this->drawRectangle($left, $top, $width, $height);
        }
        if (is_null($verradius)) {
            $verradius = $radius;
        }
        list($left, $top, $width, $height) = $this->_fix_raster_dimensions($left, $top, $width, $height);
        $res = $this->gdres;
        $radius = min($radius, floor($width / 2));
        $verradius = min($verradius, floor($width / 2));
        $filled = false;
        if ($this->figureFill) {
            $bgres = $this->_prepare_fillcolor();
            if ($width > $radius * 2) {
                $filled = @imagefilledrectangle($res, $left + $radius, $top, $left + $width - $radius - 1,$top + $height - 1, $bgres);
            } else {
                $filled = true;
            }
            if ($height > $verradius * 2) {
                @imagefilledrectangle($res, $left, $top + $verradius, $left + $radius - 1, $top + $height - $verradius - 1, $bgres);
                @imagefilledrectangle($res, $left + $width - $radius, $top + $verradius, $left + $width - 1, $top + $height - $verradius - 1, $bgres);
            }
            @imagefilledellipse($res, $left + $radius, $top + $verradius, $radius * 2, $verradius * 2, $bgres);
            @imagefilledellipse($res, $left + $radius, $top + $height - $verradius - 1, $radius * 2, $verradius * 2, $bgres);
            @imagefilledellipse($res, $left + $width - $radius - 1, $top + $verradius, $radius * 2, $verradius * 2, $bgres);
            @imagefilledellipse($res, $left + $width - $radius - 1, $top + $height - $verradius - 1, $radius * 2, $verradius * 2, $bgres);
            if ($this->antialiasing) {
                // TODO...
            }
        }
        if ($this->figureOutline) {
            if ($this->weight==1) {
                $cres = $this->_prepare_color();
            } else {
                $this->_prepare_brush();
                $cres = IMG_COLOR_BRUSHED;
            }
            $this->_imageantialias($res, false);
            if ($width > $radius * 2) {
                $outlined = @imageline($res, $left + $radius, $top, $left + $width - $radius - 1, $top, $cres);
                @imageline($res, $left + $radius, $top + $height - 1, $left + $width - $radius - 1,$top + $height - 1, $cres);
            } else {
                $outlined = true;
            }
            if ($height > $verradius * 2) {
                @imageline($res, $left, $top + $verradius, $left, $top + $height - $verradius - 1, $cres);
                @imageline($res, $left + $width - 1, $top + $verradius, $left + $width - 1, $top + $height - $verradius - 1, $cres);
            }
            $this->_imageantialias($res, $this->antialiasing);
            
            @imagearc($res, $left + $radius - 1, $top + $verradius-1, $radius * 2, $verradius * 2, 180, 270, $cres);
            @imagearc($res, $left + $radius - 1, $top + $height-$verradius, $radius * 2, $verradius * 2, 90, 180, $cres);
            @imagearc($res, $left + $width - $radius, $top + $verradius - 1, $radius * 2, $verradius * 2, -90, 0, $cres);
            @imagearc($res, $left + $width - $radius, $top + $height - $verradius, $radius * 2, $verradius * 2, 0, 90, $cres);
            
            if ($this->antialiasing) {
                // TODO ... 
            }
        }
        if (!$filled && !$outlined) {
            return $this->_error("Rounded rectangle can not be drawn");
        }
        return $this;
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
        if (!$this->figureFill && !$this->figureOutline) {
            return $this;
        }
        $centerleft = $left + ($width / 2);
        $centertop = $top + ($height / 2);
        $filled = false;
        $res = $this->gdres;
        $this->_prepare_rendering();
        if ($this->figureFill) {
            $bgres = $this->_prepare_fillcolor();
            if (!@imagefilledellipse($res, $centerleft, $centertop, $width, $height, $bgres)) {
                return $this->_error("Ellipse can not be drawn");
            }
            $filled = true;
            if ($this->antialiasing && !$this->figureOutline) {
                // TODO ...
            }
        }
        $outlined = false;
        if ($this->figureOutline) {
            $cres = $this->_prepare_color();
            if ($this->weight == 1) {
                $outlined = @imageellipse($res, $centerleft, $centertop, $width, $height, $cres);
            } else {
                if ($this->_prepare_brush()) {
                    $outlined = @imageellipse($res, $centerleft, $centertop, $width, $height, IMG_COLOR_BRUSHED);
                }
            }
            if ($this->antialiasing) {
                // TODO ... 
            }
        }
        if (!$filled && !$outlined) {
            return $this->_error("Ellipse can not be drawn");
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
        return $this->drawEllipseArcline($left - $radius, $top - $radius, $radius * 2, $radius * 2, $startangle, $endangle);
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
        return $this->drawEllipseArc($left - $radius, $top - $radius, $radius * 2, $radius *2, $startangle, $endangle);
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
        $res = $this->gdres;
        $cres = $this->_prepare_color();
        $this->_prepare_rendering();
        $cleft = $left + round($width / 2);
        $ctop = $top + round($height / 2);
        if ($this->weight == 1) {
            if (!@imagearc($res, $cleft, $ctop, $width, $height, $startangle, $endangle, $cres)) {
                return $this->_error("Ellipse arc line can not be drawn");
            }
        } else {
            $this->_prepare_brush();
            if (!@imagearc($res, $cleft, $ctop, $width, $height, $startangle, $endangle, IMG_COLOR_BRUSHED)) {
                return $this->_error("Ellipse arc line can not be drawn");
            }
        }
        if ($this->antialiasing) {
            // TODO ... 
        }
        return $this;
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
        if (!$this->figureFill && !$this->figureOutline) {
            return $this;
        }
        $res = $this->gdres;
        $this->_prepare_rendering();
        $cleft = $left+round($width/2);
        $ctop = $top+round($height/2);
        $filled = false;
        if ($this->figureFill) {
            $bgres = $this->_prepare_fillcolor();
            if (!@imagefilledarc($res, $cleft, $ctop, $width, $height, $startangle, $endangle, $bgres, IMG_ARC_PIE)) {
                return $this->_error("Ellipse arc can not be drawn");
            }
            $filled = true;
        }
        if ($this->figureOutline) {
            if ($this->weight == 1) {
                $cres = $this->_prepare_color();
            } else {
                $this->_prepare_brush();
                $cres = IMG_COLOR_BRUSHED;
            }
            $outlined = !@imagefilledarc($res, $cleft, $ctop, $width, $height, $startangle, $endangle, $cres, IMG_ARC_PIE | IMG_ARC_EDGED | IMG_ARC_NOFILL);
            if ($this->antialiasing) {
                // TODO...
            }
        }
        if (!$filled && !$outlined) {
            return $this->_error("Ellipse arc can not be drawn");
        }
        return $this;
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
        $lastx = $left1;
        $lasty = $top1;
        $steps = 40;
        $step = 1 / $steps;
        $res = $this->gdres;
        $this->_prepare_rendering();
        if ($this->weight == 1) {
            $cres = $this->_prepare_color();
        } else {
            $this->_imageantialias($res, false);
            $this->_prepare_brush();
            $cres = IMG_COLOR_BRUSHED;
        }
        for ($t = $step; $t<1; $t += $step) {
            $rt = 1-$t;
            $x = ($rt * $rt*$rt * $left1) + (3 * $t * $rt * $rt * $left2) + (3 * $t * $t * $rt * $left3) + ($t * $t * $t * $left4);
            $y = ($rt * $rt * $rt * $top1) + (3 * $t * $rt * $rt * $top2) + (3 * $t * $t * $rt * $top3) + ($t * $t * $t * $top4);
            if (!@imageline($res, $lastx, $lasty, $x, $y, $cres)) { // FIXME: weight...
                return $this->_error("Bezier curve can not be drawn");
            }
            $lastx = $x;
            $lasty = $y;
        }
        if (!@imageline($res, $lastx, $lasty, $left4, $top4, $cres)) { // FIXME: weight...
            return $this->_error("Bezier curve can not be drawn");
        }
        if ($this->weight > 1) {
            $this->_imageantialias($res, $this->antialiasing);
        }
        return $this;
    }
    
    /**
     * Draws a polygon
     *
     * @param int[][] $left1
     * @return self
     */
    public function drawPolygon($points)
    {
        if (!$this->figureFill && !$this->figureOutline) {
            return $this;
        }
        $res = $this->gdres;
        $this->_prepare_rendering();
        $filled = false;
        $point_arr = $this->_create_pointarr($points);
        $pnum = count($point_arr) / 2;
        if ($this->figureFill) {
            $bgres = $this->_prepare_fillcolor();
            if (!@imagefilledpolygon($res, $point_arr, $pnum, $bgres)) {
                return $this->_error("Polygon can not be drawn");
            }
            $filled = true;
            if ($this->antialiasing && !$this->figureOutline) {
                @imagepolygon($res, $point_arr, $pnum, $bgres);
            }
        }
        $outlined = false;
        if ($this->figureOutline) {
            $cres = $this->_prepare_color();
            if ($this->weight == 1) {
                $outlined = @imagepolygon($res, $point_arr, $pnum, $cres);
            } else {
                $this->_imageantialias($res, false);
                if ($this->_prepare_brush()) {
                    $outlined = @imagepolygon($res, $point_arr, $pnum, IMG_COLOR_BRUSHED);
                }
                $this->_imageantialias($res, $this->antialiasing);
                if ($this->antialiasing) {
                    // TODO ... 
                }
            }
        }
        if (!$filled && !$outlined) {
            return $this->_error("Polygon can not be drawn");
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
        $res = $this->gdres;
        $fcres = $this->_prepare_fillcolor();
        if (@imagefill($res, $left, $top, $fcres)) {
            return $this;
        } else {
            return $this->_error("Image can not be filled");
        }
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
        $res = $this->gdres;
        $hmax = imagesx($res) - 1;
        $vmax = imagesy($res) - 1;
        switch ($effect) {
            case Image::EFFECT_GAMMA:
                if (@imagegammacorrect($res, 1.0, $params)) {
                    return $this;
                } else {
                    return $this->_error("Gamma correction can not be applied");
                }
            case Image::EFFECT_INVERT:
                if (function_exists('imagefilter')) {
                    if (@imagefilter($res, IMG_FILTER_NEGATE)) {
                        return $this;
                    } else {
                        return $this->_error("Image can not be inverted");
                    }
                } else {
                    for ($y = 0; $y <= $vmax; $y++) {
                        for ($x = 0; $x <= $hmax; $x++) {
                            $oColor = $this->_read_pixel($res, $x, $y);
                            $oNewColor = $oColor->invert();
                            $this->_write_pixel($res, $x, $y, $oNewColor);
                        }
                    }
                    return $this;
                }
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
        $res = $this->gdres;
        if (is_null($color)) {
            $cres = $this->_prepare_color();
        } else {
            $cres = $this->_create_color($res, $color);
        }
        @imagealphablending($res, true);
        if (@imagesetpixel($res, $left, $top, $cres)) {
            return $this;
        } else {
            return $this->_error("Pixel can not be drawn");
        }
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
        $res = $this->gdres;
        if (is_null($color)) {
            $cres = $this->_prepare_color();
        } else {
            $cres = $this->_create_color($res, $color);
        }
        @imagealphablending($res, false);
        if (@imagesetpixel($res, $left, $top, $cres)) {
            return $this;
        } else {
            return $this->_error("Pixel can not be drawn");
        }
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
        return $this->_read_pixel($this->gdres, $left, $top);
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
        if (in_array($type, $this->emulated_formats)) {
            $oWriter = new PhpImageIo();
            if ($oWriter->write($this, $type)) {
                return $this;
            } else {
                return $this->_error("Image can not be flushed");
            }
        } else {
            if ($type == 'jpg') {
                $type = 'jpeg';
            }
            $funcname = 'image' . $type;
            if (!function_exists($funcname)) {
                return $this->_error("Image can not be flushed");
            }
            $res = $this->gdres;
            @imagesavealpha($res, true);
            if (@$funcname($res)) {
                return $this;
            } else {
                return $this->_error("Image can not be flushed");
            }
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
            return $this->_error("File name must be specified");
        }
        $default_type = 'png';
        if (is_null($type)) {
            $type = $default_type;
            if (($pos = strrpos($file, '.')) !== false) {
                $type = substr($file, $pos + 1);
            }
        }
        if ($type == 'jpg') {
            $type = 'jpeg';
        }
        if (in_array($type, $this->emulated_formats)) {
            $oWriter = new PhpImageIo();
            if ($oWriter->write($this, $type, $file)) {
                return $this;
            } else {
                return $this->_error("Image can not be saved");
            }
        } else {
            if (function_exists('image' . $type)) {
                $funcname = 'image' . $type;
            } else {
                $funcname = 'image' . $default_type;
            }
            $res = $this->gdres;
            @imagesavealpha($res, true);
            if (@$funcname($res, $file)) {
                return $this;
            } else {
                return $this->_error("Image can not be saved");
            }
        }
    }
    
    /**
     * Frees allocated memory
     *
     * @return self
     */
    public function free()
    {
        if ($this->gdres && !@imagedestroy($this->gdres)) {
            return $this->_error("Memory can not be clean");
        }
        $this->gdres = null;
        return $this;
    }
    
    
    /* PRIVATES */
    
    protected function _get_gd_version()
    {
        if (is_null($this->_gd_version)) {
            if (function_exists('gd_info')) {
                $info = gd_info();
                preg_match('/\d+(\.\d+)?/', $info['GD Version'], $match);
                $version = $match[0] * 1;
                return $version;
            }
        }
        return $this->_gd_version;
    }
    
    protected function _create_image($width, $height, $bgcolor)
    {
        if (!$res=@imagecreatetruecolor($width, $height)) {
            return false;
        }
        $bgres = $this->_create_color($res, $bgcolor);
        if (!$bgcolor->hasTransparency()) {
            @imagefill($res, 0, 0, $bgres);
        } else {
            @imagesavealpha($res, true);
            @imagealphablending($res, false);
            @imagefill($res, 0 ,0, $bgres);
        }
        @imagealphablending($res, true);
        return $res;
    }
    
    protected function _prepare_color()
    {
        $res = $this->gdres;
        if ($this->_color_lastres != $res) {
            $this->_color_allocated = $this->_create_color($res, $this->color);
            $this->_color_lastres = $res;
        }
        return $this->_color_allocated;
    }
    
    protected function _prepare_fillcolor()
    {
        $res = $this->gdres;
        if ($this->_fillcolor_lastres != $res) {
            $this->_fillcolor_allocated = $this->_create_color($res, $this->fillColor);
            $this->_fillcolor_lastres = $res;
        }
        return $this->_fillcolor_allocated;
    }
    
    protected function _prepare_behindcolor()
    {
        $res = $this->gdres;
        if ($this->_behindcolor_lastres != $res) {
            $this->_behindcolor_allocated = $this->_create_color($res, $this->behindColor);
            $this->_behindcolor_lastres = $res;
        }
        return $this->_behindcolor_allocated;
    }
    
    protected function _prepare_rendering()
    {
        $res = $this->gdres;
        if ($this->_rendering_lastres != $res) {
            $this->_imageantialias($res, $this->antialiasing);
        }
        return true;
    }
    
    protected function _prepare_brush()
    {
        $res = $this->gdres;
        if ($this->weight != $this->_brushres_lastsize || $this->color != $this->_brushres_lastcolor) {
            $w = ($this->weight % 2) ? $this->weight : $this->weight + 1;
            $bgcolor = ($this->_brushres_lastcolor && $this->_brushres_lastcolor->getRed() == 0) ? new Color(1, 0, 0, 0) : new Color(0, 0, 0, 0);
            $newbrushres = $this->_create_image($w, $w, new Color("transparent"));
            @imagealphablending($newbrushres, true);
            $cres = $this->_create_color($newbrushres, $this->color);
            @imagefilledellipse($newbrushres, $this->weight / 2, $this->weight / 2, $this->weight, $this->weight, $cres);
            $this->_brushres = $newbrushres;
            $this->_brushres_lastsize = $this->weight;
            $this->_brushres_lastcolor = $this->color;
        }
        if (!@imagesetbrush($res, $this->_brushres)) {
            return false;
        }
        return $this->_brushres;
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
                $result[] = array_shift($point);
                $result[] = array_shift($point);
            }
        } else {
            $result = [];
            foreach ($numarr as $item) {
                $result[] = $item * 1;
            }
        }
        return $result;
    }
    
    protected function _create_color($res, $color)
    {
        return ($color->hasTransparency())
            ?@imagecolorallocatealpha(
                $res, $color->getRed(),
                $color->getGreen(),
                $color->getBlue(),
                ceil((1 - $color->getAlpha()) * 128) - 1
            )
            :@imagecolorallocate($res, $color->getRed(), $color->getGreen(), $color->getBlue())
        ;
    }
    
    protected function _imageantialias($res, $enabled)
    {
        if (function_exists("imageantialias")) {
            @imageantialias($res, $enabled);
        }
    }
    
    protected function _read_pixel($res, $left, $top)
    {
        $gdversion = $this->_get_gd_version();
        $colorat = @imagecolorat($res, $left, $top);
        if ($gdversion < 2) {
            $rgb = @imagecolorsforindex($res, $colorat);
            $red = $rgb['red'];
            $green = $rgb['green'];
            $blue = $rgb['blue'];
            isset($rgb['alpha']) ? $rgb['alpha'] : 1;
        } else {
            $red = ($colorat >> 16) & 0xFF;
            $green = ($colorat >> 8) & 0xFF;
            $blue = $colorat & 0xFF;
            $alpha = 1; // FIXME
        }
        return new Color($red, $green, $blue, $alpha);
    }
    
    protected function _write_pixel($res, $left, $top, $color) {
        $cres = $this->_create_color($res, $color);
        @imagealphablending($res, false);
        $rseult = @imagesetpixel($res, $left, $top, $cres);
        @imagealphablending($res, false);
        return $result;
    }
    
}
?>
