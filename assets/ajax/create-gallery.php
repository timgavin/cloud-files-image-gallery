<?php
	
	// creates a new gallery via ajax request...
	
	
	
	// include our config file
	require_once '../config.php';

	
	
	// get the gallery name from our form
	$gallery_name = (string) $_POST['gallery_name'];
	
	
	
	
	// create the cloud files container name by slugging the gallery name
	$container_name = slug($gallery_name);
	
	
	
	// it would be a good idea to see if this gallery and container already exist, but we won't bother in this demonstration
	
	
	
	// obtain an Object Store service object from the $client (set in config.php)
	$objectStoreService = $client->objectStoreService(null, RS_REGION);
	
	
	
	// create the rackspace cloud files container
	$container = $objectStoreService->createContainer($container_name);
	
	
	
	// check if we're making this container private
	if(isset($_POST['make_private'])) {
		
		$private = '1';
		
	} else {
		
		$private = '0';
		
		
		// get the container
		$container = $objectStoreService->getContainer($container_name);
		
		
		// enable the cdn since the container is not private
		$container->enableCdn();
	}
	
	
	
	// insert the gallery into our database
	if($db->query("INSERT INTO galleries (name, container, private, created_at) VALUES ('".$db->escape($gallery_name)."', '".$db->escape($container_name)."', '".$db->escape($private)."', NOW())")) {
		die('success');
	}
	
	
	die('fail');