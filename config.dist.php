<?php

// Copy this file to a readable but safe path, edit it and define Base\CONFIG_FILE before calling run.php

\Base\Config::Register
(
	array
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
		'modules' => array
		(
			'namespace' => '\\',
			'default' => 'TODO' // you should create at least one module (see Base/Module.php)
		)
	)
);


date_default_timezone_set('Europe/Brussels');
