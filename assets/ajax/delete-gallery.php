<?php
	
	// delete a gallery, container, and all its objects via ajax request...
	
	
	
	// include our config file
	require_once '../config.php';
	
	
	
	// get the container name from our form
	$container_name = (string) $_POST['container_name'];
	
	
	
	// obtain an Object Store service object from the $client (set in config.php)
	$objectStoreService = $client->objectStoreService(null, RS_REGION);
	
	
	
	// get the container
	$container = $objectStoreService->getContainer($container_name);
	
	
	
	// recursively delete the container and all its objects
	try {
		$container->delete(true);
		$deleted = true;
	} catch(BadResponseException $e) {
		echo $e->getMessage();
		echo $e->getRequest();  // this will pretty print the underlying HTTP request
		echo $e->getResponse(); // ... and the corresponding response
	}
	
	
	
	if(isset($deleted)) {
		
		
		// now delete the gallery from our database...
		
		
		// get our gallery id
		$gallery_id = $db->get_var("SELECT id FROM galleries WHERE container='".$db->escape($container_name)."'");
		
		
		
		// start a transaction incase something goes wrong
		$db->query('BEGIN');
		
		
		
		// delete the gallery record
		if($db->query("DELETE FROM galleries WHERE id=".$db->escape($gallery_id)." LIMIT 1") !== false) {
			
			
			
			// delete all photo records that are assigned to that gallery id
			if($db->query("DELETE FROM photos WHERE gallery_id=".$db->escape($gallery_id)) !== false) {
				
				
				
				if($db->query('COMMIT') !== false) {
				    
				    // transaction was successful
				    die('success');
				
				} else {
				    
				    // transaction failed, rollback
				    $db->query('ROLLBACK');
				    
				}
			}
		}
	}
	
	die('fail');