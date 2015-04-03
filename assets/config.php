<?php
	
	// define our site root
	defined('ROOT') 	? null : define('ROOT', realpath(__DIR__.'/..'));
	
	
	
	// define our uploads directory - don't forget to set permissions!
	defined('UPLOADS') 	? null : define('UPLOADS', ROOT.'/uploads');
	
	
	
	// our database credentials
	defined('DB_HOST') 	? null : define('DB_HOST', 'localhost');
	defined('DB_USER') 	? null : define('DB_USER', '');											// <-- DB USERNAME
	defined('DB_PASS') 	? null : define('DB_PASS', '');											// <-- DB PASSWORD
	defined('DB_NAME') 	? null : define('DB_NAME', 'open-cloud');
	
	
	
	// instantiate a rackspace client
	require_once ROOT.'/assets/plugins/open-cloud/vendor/autoload.php';
	
	use OpenCloud\Rackspace;
	use Guzzle\Http\Exception\BadResponseException;
	
	$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
		'username' => 'YOUR CLOUD FILES USERNAME',												// <-- YOUR CLOUD FILES API USERNAME
		'apiKey'   => 'YOUR CLOUD FILES API KEY'												// <-- YOUR CLOUD FILES API KEY
	));
	
	
	
	// define rackspace container location and secret
	defined('RS_REGION') ? null : define('RS_REGION', 'DFW');									// <-- CONTAINER LOCATION
	
	
	
	// setup ezsql
	require_once ROOT.'/assets/plugins/ezSQL-master/shared/ez_sql_core.php';
	require_once ROOT.'/assets/plugins/ezSQL-master/mysqli/ez_sql_mysqli.php';
	$db = new ezSQL_mysqli(DB_USER,DB_PASS,DB_NAME,DB_HOST);
	$db->hide_errors();
	
	
	
	// include site classes & functions
	require_once ROOT.'/assets/php/functions.php';