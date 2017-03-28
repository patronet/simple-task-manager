<?php

namespace PatroNet\Core\Graphics;


// TODO: method chaining + Exceptions
// TODO: color object, font object (...)
// TODO: rectangle, roundrectangle, ellipse, ellipse_arcline, ellipse_arc:
//    negativ width/height eseten a vegpontnal +1 (negativ ertekek kezeleset teljesen vegigvezetni)
/**
 * Abstract class for image manipulator classes
 */
abstract class AbstractImage implements Image
{
    
    protected $errorMode;
    protected $savedFilename;
    protected $font;
    protected $fontSize; // in points
    protected $color;
    protected $fillColor;
    protected $behindColor;
    protected $antialiasing;
    protected $resampling;
    protected $weight;
    protected $roundedPrimitives;
    protected $figureFill;
    protected $figureOutline;
    
    public function __construct()
    {
        $this->errorMode = Image::ERROR_CONTINUE_WARNING;
        $this->savedFilename = null;
        $this->fontSize = 10; // in points
        $this->color = new Color(0, 0, 0);
        $this->fillColor = new Color(255, 255, 255);
        $this->behindColor = new Color(0, 0, 0);
        $this->antialiasing = true;
        $this->resampling = false;
        $this->weight = 1;
        $this->roundedPrimitives = true;
        $this->figureFill = true;
        $this->figureOutline = false;
    }
    
    /**
     * Creates a new image
     *
     * @param int $width
     * @param int $height
     * @param \PatroNet\Core\Graphics\Color $bgcolor
     * @return boolean
     */
    public function open($width = 100, $height = 100, $bgcolor = null)
    {
        try {
            $this->create($width, $height, $bgcolor);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Frees allocated memory
     *
     * Same as free().
     *
     * @return self
     */
    public function close()
    {
        try {
            $this->free();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
        /**
     * Sets the error handling mode
     *
     * @param string $mode
     * @return self
     */
    public function setErrorMode($mode)
    {
        $this->errorMode = $mode;
        return $this;
    }
    
    // FIXME
    /**
     * Sets the font
     *
     * @param \PatroNet\Core\Graphics\Font $font
     * @return self
     */
    public function setFont(Font $oFont)
    {
        $this->font = $oFont;
        return $this;
    }
    
    /**
     * Sets the text size
     *
     * @param int $size
     * @return self
     */
    public function setFontSize($size)
    {
        $this->fontSize = $size;
        return $this;
    }
    
    /**
     * Sets the stroke color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setColor(Color $color)
    {
        $this->color = $color;
        return $this;
    }
    
    /**
     * Sets the fill color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setFillColor(Color $color)
    {
        $this->fillColor = $color;
        return $this;
    }
    
    /**
     * Sets the behind color
     *
     * @param \PatroNet\Core\Graphics\Color $color
     * @return self
     */
    public function setBehindColor(Color $color)
    {
        $this->behindColor = $color;
        return $this;
    }
    
    /**
     * Enables or disables antialiasing
     *
     * @param boolean $enabled
     * @return self
     */
    public function setAntialiasing($enabled)
    {
        $this->antialiasing = $enabled;
        return $this;
    }
    
    /**
     * Enables or disables resize resampling
     *
     * @param boolean $enabled
     * @return self
     */
    public function setResampling($enabled)
    {
        $this->resampling = $enabled;
        return $this;
    }
    
    /**
     * Sets stroke weight
     *
     * @param int $weight
     * @return self
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }
    
    /**
     * Sets round mode
     *
     * @param boolean $rounded
     * @return self
     */
    public function setRoundedPrimitives($rounded)
    {
        $this->roundedPrimitives = $rounded;
        return $this;
    }
    
    /**
     * Sets figure filling on or off
     *
     * @param boolean $fill
     * @return self
     */
    public function setFigureFill($fill)
    {
        $this->figureFill = $fill;
        return $this;
    }
    
    /**
     * Sets figure stroke drawing on or off
     *
     * @param boolean $drawline
     * @return self
     */
    public function setFigureOutline($drawline)
    {
        $this->figureOutline = $drawline;
        return $this;
    }
    
    
    // MISC
    
    protected function _save_filename($filename)
    {
        $this->savedFilename = $filename;
    }
    
    protected function _handle_tosave_filename($filename)
    {
        if (preg_match('/^([^~]|\\\\~^)*$/', $filename)) {
            return $filename;
        }
        if (is_null($this->savedFilename)) {
            // FIXME / TODO
        }
        if ($filename == "~") {
            return $this->savedFilename;
        }
        $savedDirectory = dirname($this->savedFilename);
        $savedFileFullname = basename($this->savedFilename);
        $pos = strrpos($savedFileFullname, ".");
        if ($pos === false) {
            $savedFileMainname = $savedFileFullname;
            $savedFileExtension = "png";
        } else {
            $savedFileMainname = substr($savedFileFullname, 0, $pos);
            $savedFileExtension = substr($savedFileFullname, $pos + 1);
        }
        // TODO: quote replacements
        $newFilename = preg_replace('#^~/#', $savedDirectory . '/', $filename);
        $newFilename = preg_replace('#(/|\\.)~\\.([^./]*)$#', '$1' . $savedFileMainname . '.$2', $newFilename);
        $newFilename = preg_replace('#^~\\.([^./]*)$#', $savedDirectory . '/' . $savedFileMainname . '.$1', $newFilename);
        $newFilename = preg_replace('#\\.~$#', '.' . $savedFileExtension, $newFilename);
        $newFilename = preg_replace('#/~$#', '/' . $savedFileFullname , $newFilename);
        // FIXME: save?
        return $newFilename;
    }
    
    protected function _error($message)
    {
        switch ($this->errorMode) {
            case Image::ERROR_CONTINUE_WARNING:
                trigger_error($message, E_USER_WARNING);
                return $this;
            case Image::ERROR_CONTINUE_SILENT:
                return $this;
            case Image::ERROR_RETURNFALSE:
                return false;
            case Image::ERROR_EXCEPTION:
                throw new Exception($message);
            default:
                return $this;
        }
    }
    
    protected function _load_from_args($args)
    {
        if (!empty($args)) {
            if (is_object($args[0]) && $args[0] instanceof Image) {
                $this->cloneFrom($args[0]);
            } elseif (is_string($args[0])) {
                $file = $args[0];
                $type = isset($args[1]) ? $args[1] : null;
                $this->loadFromFile($file, $type);
            } elseif (is_int($args[0])) {
                $width = $args[0];
                $height = isset($args[1]) ? $args[1] : null;
                $bgcolor = isset($args[2]) ? $args[2] : null;
                if (is_string($bgcolor)) {
                    $bgcolor = new Color($bgcolor);
                }
                $this->create($width, $height, $bgcolor);
            }
        }
    }
    
    protected function _clone_pixelbypixel_from(Image $oImage)
    {
        $this->open($oImage->getWidth(), $oImage->getHeight());
        $this->_paste_pixelbypixel_from($oImage, 0, 0);
        return true;
    }
    
    protected function _paste_pixelbypixel_from(Image $oImage, $left, $top)
    {
        $width = min($this->getWidth(), $left + $oImage->getWidth());
        $height = min($this->getHeight(), $top + $oImage->getHeight());
        $pasteleft = max(0, $left);
        $pastetop = max(0, $top);
        for ($row = $pastetop; $row < $height; $row++) {
            for ($col = $pasteleft; $col < $width; $col++) {
                $this->setPixel($col, $row, $oImage->getPixel($col - $left, $row - $top));
            } 
        }
        return true;
    }
    
    protected function _get_ellipse_point($left, $top, $width, $height, $angle)
    {
        $rad = PI() * $angle / 180;
        $xdiff = cos($rad) * ($width / 2);
        $ydiff = sin($rad) * ($height / 2);
        $result = ['x' => $left + ($width / 2) + $xdiff, 'y' => $top + ($height / 2) + $ydiff];
        return $result;
    }
    
    protected function _fix_raster_dimensions($left, $top, $width, $height)
    {
        if ($width < 0) {
            $left = $left + $width + 1;
            $width = abs($width);
        }
        if ($height < 0) {
            $top = $top + $height + 1;
            $height = abs($height);
        }
        return [$left, $top, $width, $height];
    }
    
    protected function _get_mime_by_ext($ext) {
        $ext2mime = [
            "txt"   => "text/plain",
            "jpg"   => "image/jpeg",
            "png"   => "image/png",
            "gif"   => "image/gif",
            "bmp"   => "image/bmp",
            "htm"   => "text/html",
            "html"  => "text/html",
            "css"   => "text/css",
            "php"   => "application/x-php",
            // ...
        ];
        return array_key_exists($ext, $ext2mime) ? $ext2mime[$ext] : "";
    }

    protected function _get_mime($file) {
        if (function_exists("finfo_open")) {
            $res = finfo_open(FILEINFO_MIME_TYPE);
            $result = finfo_file($res, $file);
            finfo_close($res);
            return $result;
        } elseif (function_exists("mime_content_type") && ($result = @mime_content_type($file)) !== false) {
            return $result;
        }
        return false;
    }

    protected function _get_mime_force($file) {
        if ($result = $this->_get_mime($file)) {
            return $result;
        }
        $filename = basename($file);
        if (($p = strrpos($filename, ".")) === false) {
            return "";
        }
        $ext = substr($filename, $p + 1);
        return $this->_get_mime_by_ext($ext);
    }
    
    protected function _create_unique_name($path, $filename) {
        $p = strrpos($filename, ".");
        if ($p === false) {
            $start = $filename;
            $end = "";
        } else {
            $start = substr($filename, 0, $p);
            $end = substr($filename, $p);
        }
        $newname = $filename;
        for ($i = 1; file_exists("$path/$newname"); $i++) {
            $newname = $start . "_" . $i . $end;
        }
        return $newname;
    }
    
    protected function _flush_type_header($type) {
        if ($type == 'jpg') {
            $type = 'jpeg';
        }
        header("Content-type: image/" . $type);
    }
        
}

?>
