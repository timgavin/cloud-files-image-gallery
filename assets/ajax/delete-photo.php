<?php
	
	// delete an image from our database and rackspace container via ajax request...
	
	
	
	// include our config file
	require_once '../config.php';
	
	
	
	// get the gallery/container name
	$container_name = (string) $_POST['container'];
	
	
	
	// get the object's name and ID
	$object_name = (string) $_POST['object'];
	$id = (int) $_POST['id'];
	
	
	
	// assign names to our thumbnail and full-size image objects so we can identify them
	$thumbnail = 'tn-'.$object_name;
	$fullsize  = $object_name;
	
	
	
	// delete from our container
	
	
	
	// obtain an Object Store service object from the $client (set in config.php)
	$objectStoreService = $client->objectStoreService(null, RS_REGION);
	
	
	
	// get the container
	$container = $objectStoreService->getContainer($container_name);
	
	
	
	// get the thumbnail image object
	$object = $container->getObject($thumbnail);
	
	
	
	// delete the thumbnail image object. (we *should* use a try/catch here...)
	$object->delete();
	
	
	
	// get the full-size image object
	$object = $container->getObject($fullsize);
	
	
	
	// delete the full-size image object. (we *should* use a try/catch here...)
	$object->delete();
	
	
	
	// delete record from our database
	if($db->query("DELETE FROM photos WHERE id=".$db->escape($id)." LIMIT 1") !== false) {
		die('success');
	}
	
	
	die('fail');