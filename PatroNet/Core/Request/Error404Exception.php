<?php

namespace PatroNet\Core\Request;


class Error404Exception extends HttpException
{
    public function __construct($message="")
    {
        parent::__construct(404, $message);
    }
}
