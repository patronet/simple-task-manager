<?php

namespace PatroNet\Core\Request;


class Error500Exception extends HttpException
{

	public function __construct($message = "")
    {
		parent::__construct(500, $message);
    }
    
}
