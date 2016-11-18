<?php

namespace Base;

use \Exception;


require_once(__DIR__ . '/core.php');


// load module
$moduleName = GetArg('module', Config::Get('modules', 'default'));

if ($moduleName)
{
	$module =& Module::Get($moduleName);
	$module->run(GetArg('action', 'default'));
	exit;
}

throw new Exception('No module defined!');