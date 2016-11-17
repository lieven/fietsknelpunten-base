<?php // core functions

if (defined('CONFIG_FILE') && stream_resolve_include_path(CONFIG_FILE))
{
	require_once(CONFIG_FILE);
}
else
{
	header('Content-Type: text/plain');
	die('Please create a config file (see config.dist.php) and define CONFIG_FILE');
}

if (get_magic_quotes_gpc())
{
	throw new Exception('Please disable magic quotes');
}


require_once(__DIR__ . '/module.php');
require_once(__DIR__ . '/abstractapimodule.php');
require_once(__DIR__ . '/view.php');
require_once(__DIR__ . '/database.php');



// fatal exception handling
function FatalExceptionHandler($inException)
{
	// TODO: make this nice
	header('Content-Type: text/plain');
	die('*** fatal exception: '. $inException->getMessage());
}

set_exception_handler('FatalExceptionHandler');



// load modules based on their class name
function __autoload($inClassName)
{
	try
	{
		$matches = array();
		
		if (preg_match('/^([a-zA-Z][a-zA-Z0-9_]*)Module$/', $inClassName, $matches))
		{
			// e.g. MyModule should be defined in mymodule.php
			$name = strtolower($matches[1]);
			
			$path = sprintf('%smodule.php', $name);;
			if (! stream_resolve_include_path($path))
				throw new Exception('Module not found: '. $inClassName);
			
			require_once $path;
			
			if (! class_exists($inClassName, false))
				throw new Exception("Module '$name' not defined in $path");
		}
		else
		{
			throw new Exception('Invalid class name: '. $inClassName);
		}
	}
	catch (Exception $e) // work around standard "not found"-handler
	{
		$code = 'class %s { public function __construct() { throw unserialize(stripslashes(\'%s\')); } }';
		eval(sprintf($code, $inClassName, addslashes(serialize($e))));
	}
}

function GetArg($inName, $inDefault = NULL, $inType = INPUT_GET, $inFilter = FILTER_DEFAULT)
{
	$result = filter_input($inType, $inName, $inFilter);
	if ($result === NULL)
	{
		$result = $inDefault;
	}
	return $result;
}

// Type-safe accessor for $GLOBALS['config'][$key1][$key2]...[$keyN]
function GetConfig(/* keys */)
{
	$result = NULL;
	
	if (isset($GLOBALS['config']))
	{
		$result =& $GLOBALS['config'];
		
		for ($i = 0, $n = func_num_args(); $i < $n; ++$i)
		{
			$key = func_get_arg($i);
			if (!is_string($key) || !isset($result[$key]))
			{
				$result = NULL;
				break;
			}
			
			$result =& $result[$key];
			
			if ($i + 1 < $n && !is_array($result))
			{
				$result = NULL;
				break;
			}
		}
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
