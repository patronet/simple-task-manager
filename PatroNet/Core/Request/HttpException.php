<?php

namespace PatroNet\Core\Request;


class HttpException extends \Exception
{
	protected $status;
	
	public function __construct($status, $message)
    {
		parent::__construct($message);
		
		$this->status = $status;
		$this->message = $message;
    }
	
	public function getStatus()
	{
		return $this->status;
	}
	
}
