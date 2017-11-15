<?php

namespace PatroNet\Core\Request;


/**
 * Default request implementation
 */
class NormalRequest implements Request
{
    
    protected $method;
    protected $path;
    protected $get;
    protected $post;
    
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = preg_replace('#^/|/$#', '', $_SERVER['SCRIPT_NAME']); // FIXME
        $this->get = $_GET;
        $this->post = $_POST;
    }
    
    /**
     * Returns with the request's HTTP method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Returns with the request's path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Returns with the GET datas
     *
     * @return array
     */
    public function getGet()
    {
        return $this->get;
    }
    
    /**
     * Returns with the POST datas
     *
     * @return array
     */
    public function getPost()
    {
        return $this->post;
    }
    
}
