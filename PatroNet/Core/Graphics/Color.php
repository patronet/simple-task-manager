<?php

namespace PatroNet\Core\Graphics;


// TODO getHexValue(), getRgbValue(), __toString() ...
/**
 * Color handler class
 */
class Color
{
    
    protected $red = 0;
    
    protected $green = 0;
    
    protected $blue = 0;
    
    protected $alpha = 1;
    
    protected $predefinedValues = [
        "transparent" => [0, 0, 0, 0],
        "black" => [0, 0, 0, 1],
        "white" => [0, 0, 0, 1],
        "red" => [255, 0, 0, 1],
        "green" => [0, 255, 0, 1],
        "blue" => [0, 0, 255, 1],
    ];
    
    /**
     * @param \PatroNet\Core\Graphics\Color|string|array|int $param1
     * @param int $param2
     * @param int $param3
     * @param int $param4
     */
    public function __construct()
    {
        $args = func_get_args();
        $argc = func_num_args();
        if ($argc == 1) {
            if (is_string($args[0])) {
                if (array_key_exists($args[0], $this->predefinedValues)) {
                    $value = $this->predefinedValues[$args[0]];
                    $this->red = $value[0];
                    $this->green = $value[1];
                    $this->blue = $value[2];
                    $this->alpha = $value[3];
                } else {
                    // TODO: parse color string
                }
            } elseif ($args[0] instanceof Color) {
                $this->red = $args[0]->red;
                $this->green = $args[0]->green;
                $this->blue = $args[0]->blue;
                $this->alpha = $args[0]->alpha;
            }
        } elseif ($argc >= 3) {
            $this->red = $args[0];
            $this->green = $args[1];
            $this->blue = $args[2];
            $this->alpha = ($argc > 3) ? $args[3] : 1;
        }
    }
    
    /**
     * Checks whether another color is the same as this
     *
     * @param \PatroNet\Core\Graphics\Color $oColor
     * @return boolean
     */
    public function equals(Color $oColor)
    {
        return (
            $this->red == $oColor->red &&
            $this->green == $oColor->green &&
            $this->blue == $oColor->blue &&
            $this->alpha == $oColor->alpha
        );
    }
    
    /**
     * Gets the red component
     *
     * @return int
     */
    public function getRed()
    {
        return $this->red;
    }
    
    /**
     * Gets the green component
     *
     * @return int
     */
    public function getGreen()
    {
        return $this->green;
    }
    
    /**
     * Gets the blue component
     *
     * @return int
     */
    public function getBlue()
    {
        return $this->blue;
    }
    
    /**
     * Gets the alpha component
     *
     * @return float
     */
    public function getAlpha()
    {
        return $this->alpha;
    }
    
    /**
     * Checks whether the color is transparent
     *
     * @return boolean
     */
    public function hasTransparency()
    {
        return ($this->alpha < 1);
    }
    
    /**
     * Checks whether the color is fully transparent
     *
     * @return boolean
     */
    public function isFullyTransparent()
    {
        return ($this->alpha == 0);
    }
    
    /**
     * Create a color mix with cover algorithm
     *
     * @param \PatroNet\Core\Graphics\Color $oColor
     * @return self
     */
    public function coverWith(Color $oColor)
    {
        if ($oColor->alpha == 1) {
            return $oColor;
        }
        $refalpha = $this->alpha * (1 - $oColor->alpha);
        $targetalpha = $oColor->alpha + $refalpha;
        return new Color(
            round(($oColor->red * $oColor->alpha + $this->red * $refalpha) / $targetalpha),
            round(($oColor->green * $oColor->alpha + $this->green * $refalpha) / $targetalpha),
            round(($oColor->blue * $oColor->alpha + $this->blue * $refalpha) / $targetalpha),
            $targetalpha
        );
    }
    
    /**
     * Create a color mix with weighted mix algorithm
     *
     * @param \PatroNet\Core\Graphics\Color $oColor
     * @param int $selfWeight
     * @param int $withWeight
     * @return self
     */
    public function mixWith(Color $oColor, $selfWeight = 1, $withWeight = 1)
    {
        $sumWeight = $selfWeight + $withWeight;
        return new Color(
            round((($this->red * $selfWeight) + ($oColor->red * $selfWeight)) / $sumWeight),
            round((($this->green * $selfWeight) + ($oColor->green * $selfWeight)) / $sumWeight),
            round((($this->blue * $selfWeight) + ($oColor->blue * $selfWeight)) / $sumWeight),
            (($this->alpha * $selfWeight) + ($oColor->alpha * $selfWeight)) / $sumWeight
        );
    }
    
    /**
     * Inverts the color
     *
     * @return self
     */
    public function invert()
    {
        return new Color(
            255 - $this->red,
            255 - $this->green,
            255 - $this->blue,
            $this->alpha
        );
    }
    
    // TODO
    /**
     * Applies gamma correction
     *
     * @todo
     * @return self
     */
    public function gamma()
    {
        return new Color(
            pow($this->red / 255, 1 / $params) * 255,
            pow($this->green / 255, 1 / $params) * 255,
            pow($this->blue / 255, 1 / $params) * 255,
            $this->alpha
        );
    }
    
    /**
     * Darkens the image
     *
     * @param int $value
     * @return self
     */
    public function darken($value)
    {
        $value = max(0, min(1, $value));
        $scale = 1 - $value;
        return new Color(
            round($this->red * $scale),
            round($this->green * $scale),
            round($this->blue * $scale),
            $this->alpha
        );
    }
    
    // FIXME: multiple algorithms...
    /**
     * Lightens the image
     *
     * @param int $value
     * @return self
     */
    public function lighten($value)
    {
        $value = max(0, min(1, $value));
        return new Color(
            $this->red + round((255 - $this->red) * $value),
            $this->green + round((255 - $this->green) * $value),
            $this->blue + round((255 - $this->blue) * $value),
            $this->alpha
        );
    }
    
}
