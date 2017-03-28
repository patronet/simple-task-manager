<?php

namespace PatroNet\Core\Template\Driver;

use \PatroNet\Core\Content\SourceTrait;
use \PatroNet\Core\Template\FileTemplate;


/**
 * Template class for the Twig templating system
 */
class TwigTemplate implements FileTemplate
{
    
    use SourceTrait;
    
    static protected $INITED = false;
    
    static protected $oTwigEnvironment = null;
    
    protected $file;
    
    protected $oTwigTemplate = null;
    
    protected $variables = [];
    
    protected $debugEnabled = false;
    
    /**
     * Gets the main Twig environment class
     *
     * @return \Twig_Environment
     */
    static public function getTwigEnvironment()
    {
        if (is_null(self::$oTwigEnvironment)) {
            require_once('includes/templating/twig/Twig/Autoloader.php');
            \Twig_Autoloader::register();
            $oTwigLoader = new \Twig_Loader_Filesystem();
            $oTwigLoader->addPath(".");
            self::$oTwigEnvironment = new \Twig_Environment($oTwigLoader, [
                "cache" => $this->cacheDirectory,
                "auto_reload" => true,
            ]);
        }
        return self::$oTwigEnvironment;
    }
    
    /**
     * @param string $file
     */
    public function __construct($file, $cacheDirectory = "/tmp") // XXX
    {
        $this->file = $file;
        $this->cacheDirectory = $cacheDirectory;
    }
    
    /**
     * Gets the Twig template object
     *
     * @return \Twig_TemplateInterface
     */
    public function getTwigTemplate()
    {
        if (is_null($this->oTwigTemplate)) {
            $this->oTwigTemplate = self::getTwigEnvironment()->loadTemplate($this->file);
        }
        return $this->oTwigTemplate;
    }
    
    /**
     * Gets the associated file name
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Enables or disables debug mode
     *
     * @param boolean $enable
     * @return self
     */
    public function setDebug($enable = true)
    {
        $this->debugEnabled = $enable;
        return $this;
    }
    
    /**
     * Assigns a template variable
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function assign($variable, $value)
    {
        $this->variables[$variable] = $value;
        return $this;
    }
    
    /**
     * Assigns multiple template variables
     *
     * @param array $variables
     * @return self
     */
    public function assignAll($variables)
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }
    
    /**
     * Gets the text content
     *
     * @return string
     */
    public function get()
    {
        try {
            return $this->getTwigTemplate()->render($this->variables);
        } catch (\Exception $oException) {
            if ($this->debugEnabled) {
                return $oException->getMessage();
            } else {
                return "";
            }
        }
    }
    
    /**
     * Flushes the text content
     */
    public function flush()
    {
        try {
            $this->getTwigTemplate()->display($this->variables);
        } catch (\Exception $oException) {
            if ($this->debugEnabled) {
                echo $oException->getMessage();
            }
        }
    }
    
}
