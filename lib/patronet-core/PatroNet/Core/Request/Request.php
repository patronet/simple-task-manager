<?php

namespace PatroNet\Core\Request;

// TODO: request-chain with isApplicable (...) or exception
/**
 * Interface for request objects
 */
interface Request
{
    
    /**
     * Returns with the request's HTTP method
     *
     * @return string
     */
    public function getMethod();
    
    /**
     * Returns with the request's path
     *
     * @return string
     */
    public function getPath();
    
    /**
     * Returns with the GET datas
     *
     * @return array
     */
    public function getGet();
    
    /**
     * Returns with the POST datas
     *
     * @return array
     */
    public function getPost();
    
}
