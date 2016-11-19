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
		
		// class mapping for modules
		'modules' => array
		(
			// e.g. module "login" would be mapped to the \App\LoginModule class
			'namespace' => '\App',
			
			// allow for manual mapping between module names and fully qualified class names
			'override' => array
			(
				// e.g. 'api' => '\MyApp\APIModuleV2'
			),
			
			// the name of the default module to be run, if none is set in the request
			'default' => 'example',
		)
	)
);


date_default_timezone_set('Europe/Brussels');
