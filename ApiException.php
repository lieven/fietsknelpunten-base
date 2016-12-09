<?php

namespace Base;


use Exception;


class ApiException extends Exception
{
	const UNKNOWN_ERROR = 1;
	const UNKNOWN_ACTION = 2;
	const PARAMETER_REQUIRED = 3;
	const INVALID_PARAMETER = 4;
	const PERMISSION_DENIED = 5;
	
	public function __construct($inErrorCode, $inMessage = '')
	{
		parent::__construct($inMessage, $inErrorCode);	
	}
}