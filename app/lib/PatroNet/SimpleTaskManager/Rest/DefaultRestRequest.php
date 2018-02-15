<?php

namespace PatroNet\SimpleTaskManager\Rest;

use PatroNet\Core\Request\Request;

class DefaultRestRequest implements Request
{
    
    private $path;
    
    private $method;
    
    private $get;
    
    private $post;
    
    public function __construct($prefix = null)
    {
        $this->get = $_GET;
        
        if (array_key_exists("_method", $_GET)) {
            $this->method = $_GET["_method"];
            unset($this->get["_method"]);
        } else {
            $this->method = $_SERVER["REQUEST_METHOD"];
        }
        
        if (array_key_exists("_path", $_GET)) {
            $this->path = $this->cleanPath($_GET["_path"]);
            unset($this->get["_path"]);
        } else {
            $this->path = $this->cleanPath($_SERVER["REDIRECT_URL"]);
        }
        
        if (!is_null($prefix)) {
            $this->path = preg_replace('@^' . preg_quote($prefix, '@') . '/*@', '', $this->path);
        }
        
        $this->post = $_POST;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getGet()
    {
        return $this->get;
    }

    public function getPost()
    {
        return $this->post;
    }
    
    private function cleanPath($path)
    {
        $path = preg_replace('@/+@', '/', $path);
        $path = preg_replace('@(^/|/$)@', '', $path);
        return $path;
    }
    
}