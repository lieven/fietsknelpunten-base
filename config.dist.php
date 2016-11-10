<?php // core configuration

$GLOBALS['config'] = array();

// databases
$GLOBALS['config']['databases'] = array();

// you should set up at least one database, called 'main'
$GLOBALS['config']['databases']['main'] = array();
$GLOBALS['config']['databases']['main']['host']            = 'localhost';
$GLOBALS['config']['databases']['main']['user']            = 'username';
$GLOBALS['config']['databases']['main']['pass']            = 'password';
$GLOBALS['config']['databases']['main']['database']        = 'database';



date_default_timezone_set('Europe/Brussels');