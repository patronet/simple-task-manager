<?php

namespace PatroNet\Core\Common;

/**
 * Interface for controllers
 */
interface Controller
{
    
    /**
     * Processes query data from a GET request
     *
     * @return \PatroNet\Core\Request\Response
     */
    public function get($page, $get = []);
    
    /**
     * Processes query data from a POST request
     *
     * @return \PatroNet\Core\Request\Response
     */
    public function post($page, $post, $get = []);
    
}
