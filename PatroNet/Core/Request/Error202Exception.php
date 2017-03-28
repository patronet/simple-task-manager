<?php

namespace PatroNet\Core\Request;


class Error202Exception extends HttpException
{
	public function __construct($message="")
    {
		parent::__construct(202, $message);
    }
}
