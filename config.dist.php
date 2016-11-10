<?php

// Make a copy of this file at config/config.php and edit the values below:


$GLOBALS['config'] = array
(
	// you should set up at least one database, called 'main'
	'databases' => array
	(
		'main' => array
		(
			'host' => 'localhost',
			'user' => 'username',
			'pass' => 'password',
			'database' => 'database'
		)
	),
	// you should create at least one module (see module.php)
	'default_module' => 'TODO'
);


date_default_timezone_set('Europe/Brussels');
