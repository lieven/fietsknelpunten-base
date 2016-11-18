<?php // core functions

namespace Base;

use \Exception;


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


Config::Load();


function GetArg($inName, $inDefault = NULL, $inType = INPUT_GET, $inFilter = FILTER_DEFAULT)
{
	$result = filter_input($inType, $inName, $inFilter);
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


function Show($inText)
{
	echo htmlspecialchars($inText);
}

// Figure out resources folder path

if (!defined('RESOURCES_FOLDER'))
{
	$rootFolder = dirname($_SERVER['SCRIPT_FILENAME']) .'/';
	$currentFolder = __DIR__;
	$rootFolderLength = strlen($rootFolder);

	if (strncmp($rootFolder, $currentFolder, $rootFolderLength) === 0)
	{
		$relativePath = substr($currentFolder, $rootFolderLength);
		$end = strpos($relativePath, '/system');
	
		define('RESOURCES_FOLDER', substr($relativePath, 0, $end) . '/resources');
	}
	else
	{
		throw new Exception("Can't figure out resources folder");
	}
}


function ResourcePath($inFile)
{
	return RESOURCES_FOLDER .'/'. $inFile;
}

function ShowResourcePath($inFile)
{
	Show(ResourcePath($inFile));
}
