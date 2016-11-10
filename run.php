<?php

require_once(__DIR__ . '/core.php');


// load module
$moduleName = GetArg('module', $GLOBALS['config']['default_module']);

if ($moduleName)
{
	$module =& Module::Get($moduleName);
	$module->run(GetArg('action', 'default'));
	exit;
}

throw new Exception('No module defined!');