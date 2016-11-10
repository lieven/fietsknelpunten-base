<?php

require_once(__DIR__ . '/core.php');


// load module
$moduleName = GetVar('module', $GLOBALS['config']['default_module']);

if ($moduleName)
{
	$module =& Module::Get($moduleName);
	$module->run(GetVar('action', 'default'));
	exit;
}

throw new Exception('No module defined!');