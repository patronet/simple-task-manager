<?php

namespace PatroNet\Core\Graphics\Misc;

use PatroNet\Core\Graphics\Image;
use PatroNet\Core\Graphics\Color;


// TODO: mas formatumok... (lehetseges?)
/**
 * Image IO in pure PHP
 */
class PhpImageIo
{
    
    protected $oImage = null;
    protected $filename = null;
    
    /**
     * Loads image from file
     *
     * @param \PatroNet\Core\Graphics\Image $oImage
     * @param string $type
     * @param string $filename
     */
    public function read(Image $oImage, $type, $filename)
    {
        $method = '_read_' . $type;
        if (!method_exists($this, $method)) {
            return false;
        }
        $this->oImage = $oImage;
        $this->filename = $filename;
        $result = $this->$method();
        $this->oImage = null;
        return $result;
    }
    
    // TODO: külön kiírató metódus
    // TODO: image response
    /**
     * Saves or prints image
     *
     * @param \PatroNet\Core\Graphics\Image $oImage
     * @param string $type
     * @param string|false $filename
     */
    public function write(Image $oImage, $type, $filename = false)
    {
        $method = '_write_' . $type;
        if (!method_exists($this, $method)) {
            return false;
        }
        $this->oImage = $oImage;
        $this->filename = $filename;
        $result = $this->$method();
        $this->oImage = null;
        return $result;
    }
    
    protected function _create($width, $height)
    {
        $this->oImage->open($width, $height, array(0, 0, 0));
    }
    
    protected function _width() {
        return $this->oImage->getWidth();
    }
    
    protected function _height() {
        return $this->oImage->getHeight();
    }
    
    protected function _get($x, $y)
    {
        return $this->oImage->getPixel($x, $y);
    }
    
    protected function _set($x, $y, $color)
    {
        return $this->oImage->setPixel($x, $y, $color);
    }
    
    
    // BMP FORMAT
    
    protected function _read_bmp()
    {
        if (($res = @fopen($this->filename, 'rb')) === false) {
            return false;
        }
        
        $header = fread($res, 54);
        $header = unpack(
            'c2identifier/Vfile_size/Vreserved/Vbitmap_data/Vheader_size/'
                . 'Vwidth/Vheight/vplanes/vbits_per_pixel/Vcompression/Vdata_size/'
                . 'Vh_resolution/Vv_resolution/Vcolors/Vimportant_colors',
            $header
        );
        
        if ($header['identifier1'] != 66 || $header['identifier2'] != 77) {
            return false;
        }
        
        if ($header['bits_per_pixel'] != 24) {
            return false;
        }
        
        $width2 = ceil((3*$header['width']) / 4) * 4;
        
        $width = $header['width'];
        $height = $header['height'];
        
        $this->_create($width, $height);
        
        for ($y=$height-1; $y>=0; $y--) {
            $row = fread($res, $width2);
            $pixels = str_split($row, 3);
            for ($x=0; $x<$width; $x++)
            {
                $str = $pixels[$x];
                $this->_set($x, $y, new Color(ord($str[2]), ord($str[1]), ord($str[0])));
            }
        }
        fclose($res);
    }
    
    protected function _write_bmp()
    {
        $width = $this->_width();
        $height = $this->_height();
        $wpad = str_pad('', $width%4, "\0");
        $headlen = 54;
        
        $size = $headlen+$height*($width+$wpad);
        
        $header['identifier']       = 'BM';
        
        $header['file_size']        = pack("V", $size);
        $header['reserved']         = pack("V", 0);
        $header['bitmap_data']      = pack("V", 54);
        $header['header_size']      = pack("V", 40);
        $header['width']            = pack("V", $width);
        $header['height']           = pack("V", $height);
        
        $header['planes']           = pack("v", 1);
        $header['bits_per_pixel']   = pack("v", 24);
        
        $header['compression']      = pack("V", 0);
        $header['data_size']        = pack("V", 0);
        $header['h_resolution']     = pack("V", 0);
        $header['v_resolution']     = pack("V", 0);
        $header['colors']           = pack("V", 0);
        $header['important_colors'] = pack("V", 0);
        
        if (!$this->filename) {
            foreach ($header as $h) {
                echo $h;
            }
            for ($y = $height-1; $y >= 0; $y--) {
                for ($x = 0; $x < $width; $x++) {
                    $color = $this->_get($x, $y);
                    $seq = chr($color->getBlue()) . chr($color->getGreen()) . chr($color->getRed());
                    echo $seq;
                }
                echo $wpad;
            }
        } else {
            if (($res = @fopen($this->filename, "wb")) === false) {
                return false;
            }
            
            foreach ($header as $h) {
                fwrite($res, $h);
            }
            
            for ($y = $height-1; $y >= 0; $y--) {
                for ($x = 0; $x < $width; $x++) {
                    $color = $this->_get($x, $y);
                    $seq = chr($color->getBlue()) . chr($color->getGreen()) . chr($color->getRed());
                    fwrite($res, $seq);
                }
                fwrite($res, $wpad);
            }
            
            fclose($res);
        }
        
        return true;
    }
    
}

?>
