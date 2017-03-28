<?php

namespace PatroNet\Core\Graphics\ImageDriver;

use \PatroNet\Core\Graphics\Image;
use \PatroNet\Core\Graphics\AbstractImage;
use \PatroNet\Core\Graphics\Color;
use \PatroNet\Core\Graphics\Font;
use \PatroNet\Core\Graphics\Exception as ImageException;
use \Imagick;
use \ImagickDraw;
use \ImagickPixel;


// TODO: arc-ot (es egyebeket) ellenorizni, nincs-e olyan jelenseg, mint a gmagicknel volt
// TODO: linecap<->alphachanel
// TODO: set_pixel<->paint_pixel
/**
 * Image manipulator implemented with the ImageMagick extension
 */
class ImagickImage extends AbstractImage
{
    
    protected $draw_settings = null;
    
    protected $oImagick = null;
    
    
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
        return !is_null($this->oImagick);
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
    {try{
        if ($this->isOpen()) {
            $this->close();
        }
        if (is_null($bgcolor)) {
            $bgcolor = $this->behindColor;
        }
        $this->_init_draw_settings();
        $this->oImagick = $this->_create_image($width, $height, $bgcolor);
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be created");}}
    
    /**
     * Loads image from file
     *
     * @param string $file
     * @param string|null $type
     * @return self
     */
    public function loadFromFile($file, $type = null)
    {try{
        if (!is_file($file)) {
            return $this->_error("File not found");
        }
        $this->_init_draw_settings();
        $this->oImagick = new Imagick($file);
        $this->_save_filename($file);
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be loaded");}}
    
    /**
     * Clones another image
     *
     * @param \PatroNet\Core\Graphics\Image
     */
    public function cloneFrom(Image $oImage)
    {try{
        $this->_init_draw_settings();
        if ($oImage instanceof self) {
            $this->oImagick = clone $oImage->oImagick;
        } else {
            if (!$this->_clone_pixelbypixel_from($oImage)) {
                return $this->_error("Image can not be cloned");
            }
        }
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be cloned");}}
    
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
    {try{
        return $this->oImagick->getImageWidth();
    }catch(Exception $e){return 0;}}
    
    /**
     * Gets height of the image
     *
     * @return int
     */
    function getHeight()
    {try{
        return $this->oImagick->getImageHeight();
    }catch(Exception $e){return 0;}}
    
    /**
     * Sets the font
     *
     * @param \PatroNet\Core\Graphics\Font $font
     * @return self
     */
    public function setFont(Font $font)
    {try{
        $this->font = $font;
        $this->draw_settings->setFont($this->font->getFile());
        return $this;
    }catch(Exception $e){return $this->_error("Can not set font");}}
    
    /**
     * Sets the text size
     *
     * @param int $size
     * @return self
     */
    public function setFontSize($size)
    {try{
        $this->fontSize = $size;
        $this->draw_settings->setFontSize($size);
        return $this;
    }catch(Exception $e){return $this->_error("Can not set font size");}}
    
    /**
     * Sets the stroke color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setColor(Color $color)
    {try{
        $this->color = $color;
        $this->draw_settings->setStrokeColor($this->_create_color($color));
        $this->draw_settings->setStrokeOpacity($this->_create_opacity($color));
        return $this;
    }catch(Exception $e){return $this->_error("Can not set color");}}
    
    /**
     * Sets the fill color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setFillColor(Color $color)
    {try{
        $this->fillColor = $color;
        $this->draw_settings->setFillColor($this->_create_color($color));
        $this->draw_settings->setFillOpacity($this->_create_opacity($color));
        return $this;
    }catch(Exception $e){return $this->_error("Can not set fill color");}}
    
    /**
     * Enables or disables antialiasing
     *
     * @param boolean $enabled
     * @return self
     */
    public function setAntialiasing($enabled)
    {try{
        $this->antialiasing = $enabled;
        $this->draw_settings->setStrokeAntialias($enabled);
        $this->draw_settings->setTextAntialias($enabled);
        return $this;
    }catch(Exception $e){return $this->_error("Can not set antialiasing");}}
    
    /**
     * Sets stroke weight
     *
     * @param int $weight
     * @return self
     */
    public function setWeight($weight)
    {try{
        $this->weight = $weight;
        $this->draw_settings->setStrokeWidth($weight);
        return $this;
    }catch(Exception $e){return $this->_error("Can not set weight");}}
    
    /**
     * Sets round mode
     *
     * @param boolean $rounded
     * @return self
     */
    public function setRoundedPrimitives($rounded)
    {try{
        $this->roundedPrimitives = $rounded;
        $this->draw_settings->setStrokeLineCap($rounded?Imagick::LINECAP_ROUND:Imagick::LINECAP_BUTT);
        return $this;
    }catch(Exception $e){return $this->_error("Can not set rounded primitives");}}
    
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
    {try{
        if ($fill) {
            $oldimg = $this->oImagick;
            $imgpart = $oldimg->clone();
            $imgpart->cropImage($width, $height, $left, $top);
            $newimg = $this->_create_image($width, $height, $this->behindColor);
            $pasteleft = 0 - min(0, $left);
            $pastetop = 0 - min(0, $top);
            $newimg->compositeImage($imgpart, Imagick::COMPOSITE_DEFAULT, $pasteleft, $pastetop);
            $this->oImagick = $newimg;
            $this->_destroy_image($oldimg);
        } else {
            $this->oImagick->cropImage($width, $height, $left, $top);
        }
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be cut");}}
    
    /**
     * Resizes the image
     *
     * @param int $width
     * @param int $height
     * @param string $mode
     * @return self
     */
    public function resize($width, $height, $mode = Image::RESIZE_NORMAL)
    {try{
        if ($width == 0 && $height == 0) {
            return $this;
        }
        $image = $this->oImagick;
        $oldwidth = $image->getImageWidth();
        $oldheight = $image->getImageHeight();
        if ($width == $oldwidth && $height == $oldheight) {
            return $this;
        }
        $oldrel = $oldwidth / $oldheight;
        $refrel = $height ? $width / $height : 0;
        $filter = $this->resampling ? Imagick::FILTER_TRIANGLE : Imagick::FILTER_POINT;
        $blur = $this->resampling ? 0.7 : 0;
        if ($width == 0 || $height == 0 || in_array($mode, [
            Image::RESIZE_NORMAL, Image::RESIZE_THUMB, Image::RESIZE_THUMBRESIZE, Image::RESIZE_MIN
        ])) {
            if ($width == 0 || $height == 0 || $mode == Image::RESIZE_NORMAL) {
                $resizewidth = $width;
                $resizeheight = $height;
            } elseif ($mode == Image::RESIZE_MIN) {
                if ($refrel > $oldrel) {
                    $resizewidth = $width;
                    $resizeheight = 0;
                } else {
                    $resizewidth = 0;
                    $resizeheight = $height;
                }
            } else {
                if ($mode == Image::RESIZE_THUMB && $width >= $oldwidth && $height >= $oldheight) {
                    $resizewidth = $oldwidth;
                    $resizeheight = $oldheight;
                } else {
                    if ($refrel > $oldrel) {
                        $resizewidth = 0;
                        $resizeheight = $height;
                    } else {
                        $resizewidth = $width;
                        $resizeheight = 0;
                    }
                }
            }
            $image->resizeImage($resizewidth, $resizeheight, $filter, $blur);
        } elseif ($mode == Image::RESIZE_THUMBFILL || $mode == Image::RESIZE_THUMBRESIZEFILL) {
            if ($mode == Image::RESIZE_THUMBFILL && $width >= $oldwidth && $height >= $oldheight) {
                $resizewidth = $oldwidth;
                $resizeheight = $oldheight;
            } else {
                if ($refrel > $oldrel) {
                    $resizewidth = 0;
                    $resizeheight = $height;
                } else {
                    $resizewidth = $width;
                    $resizeheight = 0;
                }
            }
            $image->resizeImage($resizewidth, $resizeheight, $filter, $blur);
            $newimage = $this->_create_image($width, $height, $this->behindColor);
            $pasteleft = round(($width - $image->getImageWidth()) / 2);
            $newheight = round(($height - $image->getImageHeight()) / 2);
            $newimage->compositeImage($image, Imagick::COMPOSITE_DEFAULT, $pasteleft, $newheight);
            $this->oImagick = $newimage;
            $this->_destroy_image($image);
        } elseif ($mode == Image::RESIZE_MINCUT) {
            if ($refrel > $oldrel) {
                $cutwidth = $oldwidth;
                $cutheight = $oldwidth / $refrel;
            } else {
                $cutwidth = $oldheight * $refrel;
                $cutheight = $oldheight;
            }
            $cutleft = floor(($oldwidth - $cutwidth) / 2);
            $cuttop = floor(($oldheight - $cutheight) / 2);
            
            $image->cropImage($cutwidth, $cutheight, $cutleft, $cuttop);
            $image->resizeImage($width, $height, $filter, $blur);
        } else {
            return $this->_error("Unknown resizing mode");
        }
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be resized");}}
    
    /**
     * Flips the image horizontally
     *
     * @return self
     */
    public function flip()
    {try{
        $this->oImagick->flipImage();
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be flipped");}}
    
    /**
     * Flips the image vertically
     *
     * @return self
     */
    public function flop()
    {try{
        $this->oImagick->flopImage();
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be flopped");}}
    
    /**
     * Rotates the image
     *
     * @param int $angle
     * @return self
     */
    public function rotate($angle = 180)
    {try{
        $this->oImagick->rotateImage($this->_create_color($this->behindColor), $angle);
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be rotated");}}
    
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
    {try{
        $img = $this->oImagick;
        $pasteimg = $oImage->oImagick;
        $pasteleft = $left;
        $pastetop = $top;
        if ($extend) {
            $width = $img->getImageWidth();
            $height = $img->getImageHeight();
            $pastewidth = $pasteimg->getImageWidth();
            $pasteheight = $pasteimg->getImageHeight();
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
            $difftop = abs(min(0, $top));
            if ($newwidth>$width || $newheight > $height) {
                // TODO: erre talan van elegansabb megoldas is (referencia-csere nelkul)
                $targetimg = $this->_create_image($newwidth, $newheight, $this->behindColor);
                $targetimg->compositeImage($img, Imagick::COMPOSITE_DEFAULT, $diffleft, $difftop);
                $this->_destroy_image($img);
                $this->oImagick = $targetimg;
            }
        }
        if ($oImage instanceof self) {
            $this->oImagick->compositeImage($pasteimg, Imagick::COMPOSITE_DEFAULT, $pasteleft, $pastetop);
            return $this;
        } else {
            if ($this->_paste_pixelbypixel_from($oImage, $pasteleft, $pastetop)) {
                return $this;
            } else {
                return $this->_error("Image can not be pasted");
            }
        }
    }catch(Exception $e){return $this->_error("Image can not be pasted");}}
    
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
    {try{
        $aligns = [
            Image::ALIGN_LEFT => Imagick::ALIGN_LEFT,
            Image::ALIGN_CENTER => Imagick::ALIGN_CENTER,
            Image::ALIGN_RIGHT => Imagick::ALIGN_RIGHT,
        ];
        $oDraw = $this->_create_drawer();
        $oDraw->setStrokeWidth(0);
        $oDraw->setStrokeColor('none');
        $oDraw->setFillColor($this->_create_color($this->color));
        $oDraw->setTextAlignment($aligns[$align]);
        $this->oImagick->annotateImage($oDraw, $left, $top, $angle, $text);
        return $this;
    }catch(Exception $e){return $this->_error("Text can not be drawn");}}
    
    /**
     * Gets width of a text
     *
     * @param string $text
     * @param int|null $fontsize
     * @return self
     */
    public function getTextWidth($text, $fontsize = null)
    {try{
        $oDraw = $this->_create_drawer();
        if (!is_null($fontsize)) {
            $oDraw->setFontSize($fontsize);
        }
        $oDraw->setStrokeColor('none');
        $metrics = $this->oImagick->queryFontMetrics($oDraw, $text);
        return $metrics['textWidth'];
    }catch(Exception $e){return 0;}}
    
    /**
     * Gets height of upper case characters
     *
     * @param int|null $fontsize
     * @return self
     */
    public function getTextUpperHeight($fontsize = null)
    {try{
        $oDraw = $this->_create_drawer();
        if (!is_null($fontsize)) {
            $oDraw->setFontSize($fontsize);
        }
        $oDraw->setStrokeColor('none');
        $metrics = $this->oImagick->queryFontMetrics($oDraw, 'I');
        return $metrics['characterHeight'];
    }catch(Exception $e){return 0;}}
    
    /**
     * Draws a point
     *
     * @param int $left
     * @param int $top
     * @return self
     */
    public function drawPoint($left, $top)
    {try{
        $oDraw = $this->_create_drawer();
        $oDraw->setStrokeColor('none');
        $diff = $this->weight / 2;
        if ($this->weight == 1) {
            $oDraw->point($left, $top);
        } elseif (!$this->roundedPrimitives || $this->weight < 3) {
            $oDraw->rectangle($left - $diff, $top - $diff, $left + $diff - 1, $top + $diff - 1);
        } else {
            $oDraw->circle($left, $top, $left, $top + $diff);
        }
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Point can not be drawn");}}
    
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
    {try{
        $oDraw = $this->_create_drawer();
        $oDraw->setFillColor('none');
        $oDraw->line($left1, $top1, $left2, $top2);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Line can not be drawn");}}
    
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
    {try{
        $oDraw = $this->_create_drawer(true);
        $oDraw->rectangle($left, $top, $left + $width - 1, $top + $height - 1);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Rectangle can not be drawn");}}
    
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
    {try{
        $oDraw = $this->_create_drawer(true);
        if (is_null($verradius)) {
            $verradius = $radius;
        }
        $oDraw->roundRectangle($left, $top, $left + $width - 1, $top + $height - 1,$radius, $verradius);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Rounded rectangle can not be drawn");}}
    
    /**
     * Draws a circle
     *
     * @param int $left
     * @param int $top
     * @param int $radius
     * @return self
     */
    public function drawCircle($left, $top, $radius)
    {try{
        $oDraw = $this->_create_drawer(true);
        $oDraw->circle($left, $top, $left, $top + $radius);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Circle can not be drawn");}}
    
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
    {try{
        $oDraw = $this->_create_drawer(true);
        $rx = $width / 2;
        $ry = $height / 2;
        $oDraw->ellipse($left + $rx, $top + $ry, $rx, $ry, 0, 360);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Ellipse can not be drawn");}}
    
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
        return $this->drawEllipseArc($left - $radius, $top - $radius, $radius * 2, $radius * 2, $startangle, $endangle);
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
    {try{
        $oDraw = $this->_create_drawer();
        $oDraw->setFillColor('none');
        $oDraw->arc($left, $top, $left + $width, $top + $height, $startangle, $endangle);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Ellipse arc line can not be drawn");}}
    
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
    {try{
        $oDraw = $this->_create_drawer(true);
        if ($endangle < $startangle) {
            $endangle += 360;
        }
        $diff = $endangle-$startangle;
        $startpoint = $this->_get_ellipse_point($left, $top, $width, $height, $startangle);
        $endpoint = $this->_get_ellipse_point($left, $top, $width, $height, $endangle);
        $centerpoint = ['x' => $left + $width / 2, 'y' => $top + $height / 2];
        $tempstrokecolor = $oDraw->getStrokeColor();
        if ($diff == 180) {
            $oDraw->arc($left, $top, $left + $width, $top + $height, $startangle, $endangle);
        } elseif ($diff > 180) {
            $refpoint = $this->_get_ellipse_point($left, $top, $width, $height, $startangle + 180);
            $oDraw->setStrokeColor('none');
            $oDraw->polygon([
                $refpoint,
                $centerpoint,
                $endpoint,
            ]);
            $oDraw->setStrokeColor($tempstrokecolor);
            $oDraw->arc($left, $top, $left + $width, $top + $height, $startangle, $startangle + 180);
            $oDraw->arc($left, $top, $left + $width, $top + $height, $startangle + 180, $endangle);
        } else {
            $oDraw->arc($left, $top, $left + $width, $top + $height, $startangle, $endangle);
            if ($this->figureOutline) {
                $oDraw->setStrokeColor('none');
            }
            $oDraw->polygon([
                $startpoint,
                $centerpoint,
                $endpoint,
            ]);
        }
        if ($this->figureOutline) {
            $oDraw->setStrokeColor($tempstrokecolor);
            $oDraw->setStrokeLineCap(Imagick::LINECAP_ROUND);
            $oDraw->line($centerpoint['x'], $centerpoint['y'], $startpoint['x'], $startpoint['y']);
            $oDraw->line($centerpoint['x'], $centerpoint['y'], $endpoint['x'], $endpoint['y']);
        }
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return false;}}
    
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
    {try{
        $oDraw = $this->_create_drawer();
        $oDraw->setFillColor('none');
        $oDraw->bezier([
            ['x' => $left1, 'y' => $top1],
            ['x' => $left2, 'y' => $top2],
            ['x' => $left3, 'y' => $top3],
            ['x' => $left4, 'y' => $top4],
        ]);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Bezier curve can not be drawn");}}
    
    /**
     * Draws a polygon
     *
     * @param int[][] $left1
     * @return self
     */
    public function drawPolygon($points)
    {try{
        $oDraw = $this->_create_drawer(true);
        $oDraw->polygon($this->_create_pointarr($points));
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Polygon can not be drawn");}}
    
    /**
     * Runs fill from the given point
     *
     * @param int $left
     * @param int $top
     * @return self
     */
    public function fill($left, $top)
    {try{
        $oDraw = $this->_create_drawer();
        $oDraw->color($left, $top, Imagick::PAINT_FLOODFILL);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be filled");}}
    
    /**
     * Apply an effect
     *
     * @param string $effect
     * @param mixed $params
     * @return self
     */
    public function effect($effect, $params = false)
    {try{
        $res = $this->oImagick;
        switch ($effect) {
            case Image::EFFECT_GAMMA:
                $res->gammaImage($params);
                return $this;
            break;
            case Image::EFFECT_INVERT:
                $res->negateImage(false);
                return $this;
            break;
            default:
                return $this->_error("Unknown effect");
        }
    }catch(Exception $e){return $this->_error("Effect can not be applied");}}
    
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
        return $this->setPixel($left, $top, $color);
        // TODO: megkulonboztetni a set_pixel-tol
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
    {try{
        $oDraw = $this->_create_drawer();
        if (is_null($color)) {
            $color = $this->color;
        }
        $oDraw->setStrokeColor('none');
        $oDraw->setFillColor($this->_create_color($color));
        $oDraw->point($left, $top);
        $this->oImagick->drawImage($oDraw);
        return $this;
    }catch(Exception $e){return $this->_error("Pixel can not be drawn");}}
    
    /**
     * Gets a pixel's color
     *
     * @param int $left
     * @param int $top
     * @return \PatroNet\Core\Graphics\Color
     */
    public function getPixel($left, $top)
    {try{
        return array_values($this->oImagick->getImagePixelColor($left, $top)->getColor());
    }catch(Exception $e){return false;}}
    
    /**
     * Prints image content
     *
     * @param string $type
     * @param boolean $flushHeader
     */
    public function flush($type = 'png', $flushHeader = false)
    {try{
        if ($flushHeader) {
            $this->_flush_type_header($type);
        }
        $this->oImagick->setImageFormat($type);
        echo $this->oImagick;
        return true;
    }catch(Exception $e){return $this->_error("Image can not be flushed");}}
    
    /**
     * Gets image content as binary string
     *
     * @param string $type
     * @return string
     */
    public function get($type = 'png')
    {try{
        $this->oImagick->setImageFormat($type);
        return '' . $this->oImagick;
    }catch(Exception $e){return "";}}
    
    /**
     * Saves image to file
     *
     * @param string|null $file
     * @param string|null $type
     * @return self
     */
    public function save($file = null, $type = null)
    {try{
        $file = $this->_handle_tosave_filename($file);
        if (is_null($file)) {
            return false;
        }
        if (!is_null($type)) {
            $this->oImagick->setImageFormat($type);
        }
        $this->oImagick->writeImage($file);
        return $this;
    }catch(Exception $e){return $this->_error("Image can not be saved");}}
    
    /**
     * Frees allocated memory
     *
     * @return self
     */
    public function free()
    {try{
        if ($this->oImagick) {
            $this->_destroy_image($this->oImagick);
        }
        $this->oImagick = null;
        return $this;
    }catch(Exception $e){return $this->_error("Memory can not be clean");}}
    
    
    /* PRIVATES */
    
    protected function _create_image($width, $height, $bgcolor)
    {
        $img = new Imagick();
        $bgres = $this->_create_color($bgcolor);
        $opacity = $this->_create_opacity($bgcolor);
        if ($opacity == 1) {
            $img->newImage($width, $height, $bgres);
        } else {
            $img->newImage($width, $height, 'none');
            $oDraw = new ImagickDraw();
            $oDraw->setFillColor($bgres);
            $oDraw->setFillOpacity($opacity);
            $oDraw->rectangle(0, 0, $width - 1, $height - 1);
            $img->drawImage($oDraw);
        }
        return $img;
    }
    
    protected function _destroy_image($imgobj)
    {
        $imgobj->clear();
        $imgobj->destroy();
    }
    
    protected function _create_drawer($figuremethods = false) {
        $drawobj = clone $this->draw_settings;
        if ($figuremethods) {
            if (!$this->figureOutline) {
                $drawobj->setStrokeColor('none');
            }
            if (!$this->figureFill) {
                $drawobj->setFillColor('none');
            }
        }
        return $drawobj;
    }
    
    protected function _init_draw_settings()
    {
        if (is_null($this->draw_settings)) {
            $oDraw = new ImagickDraw();
            
            $oDraw->setFontSize($this->fontSize);
            $oDraw->setTextEncoding('UTF-8');
            
            $oDraw->setStrokeWidth($this->weight);
            $oDraw->setStrokeColor($this->_create_color($this->color));
            $oDraw->setStrokeOpacity($this->_create_opacity($this->color));
            $oDraw->setStrokeLineCap($this->roundedPrimitives ? Imagick::LINECAP_ROUND : Imagick::LINECAP_BUTT);
            
            $oDraw->setFillColor($this->_create_color($this->fillColor));
            $oDraw->setFillOpacity($this->_create_opacity($this->fillColor));
            
            $oDraw->setStrokeAntialias($this->antialiasing);
            $oDraw->setTextAntialias($this->antialiasing);
            
            $this->draw_settings = $oDraw;
        }
    }
    
    protected function _create_color($color)
    {
        return new ImagickPixel($this->_create_colorstring($color));
    }
    
    protected function _create_colorstring($color)
    {
        return "rgb(" . $color->getRed() . "," . $color->getGreen() . "," . $color->getBlue() . ")";
    }
    
    protected function _create_opacity($color)
    {
        return $color->getAlpha();
    }
    
    protected function _apply_figuremethods($drawobj)
    {
        if (!$this->figureOutline) {
            $drawobj->setStrokeColor('none');
        }
        if (!$this->figureFill) {
            $drawobj->setFillColor('none');
        }
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
                $x = array_shift($point);
                $y = array_shift($point);
                $result[] = ['x' => $x, 'y' => $y];
            }
        } else {
            $result = [];
            $c = count($numarr);
            for ($i = 0; $i < $c; $i += 2) {
                $result[] = ['x' => $numarr[$i] * 1, 'y' => $numarr[$i + 1] * 1];
            }
        }
        return $result;
    }
    
}

?>
