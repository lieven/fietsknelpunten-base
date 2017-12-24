<?php // core functions

namespace Base;

use Exception;


// fatal exception handling
function FatalExceptionHandler($inException)
{
	// TODO: make this nice
	header('Content-Type: text/plain');
	die('*** fatal exception: '. $inException->getMessage());
}

set_exception_handler('\Base\FatalExceptionHandler');


function AutoLoad($inClassName)
{
	$path = dirname(__DIR__) .'/'. str_replace('\\', '/', $inClassName) . '.php';
	
	if (file_exists($path))
	{
		require($path);
	}
}

spl_autoload_register('\Base\AutoLoad');


function GetArg($inName, $inDefault = NULL, $inType = array(INPUT_POST, INPUT_GET), $inFilter = FILTER_DEFAULT)
{
	$result = NULL;
	
	if (is_array($inType))
	{
		foreach ($inType as $type)
		{
			$result = filter_input($type, $inName, $inFilter);
			if ($result !== NULL)
			{
				break;
			}
		}
	}
	else
	{
		$result = filter_input($inType, $inName, $inFilter);
	}
	
	if ($result === NULL)
	{
		$result = $inDefault;
	}
	return $result;
}


function GetHeader($inHeaderName)
{
	$key = 'HTTP_' . strtoupper(str_replace('-', '_', $inHeaderName));
	return isset($_SERVER[$key]) ? $_SERVER[$key] : NULL;
}
