<?php

require_once(__DIR__ . '/core.php');


// load module
$GLOBALS['module_name'] = GetVar('module', $GLOBALS['config']['default_module']);
if ($GLOBALS['module_name'])
{
	$GLOBALS['module'] =& Module::Get($GLOBALS['module_name']);
	$GLOBALS['module']->run(GetVar('action', 'default'));
	exit;
}

throw new Exception('No module defined!');