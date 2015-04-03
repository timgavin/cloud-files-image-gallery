<?php
	
	// upload a photo...
	
	
	
	// include our config file
	require_once '../config.php';
	
	
	
	// include the rackspace autoload file
	require_once '../plugins/open-cloud/vendor/autoload.php';
	
	
	
	// get the gallery/container name
	$container_name = (string) $_GET['gallery'];
	
	
	
	// get the gallery ID
	$gallery_id = $db->get_var("SELECT id FROM galleries WHERE container='".$db->escape($container_name)."'");
	
	
	
	// create a unique 32 character hash for our image file
	$hash = generate_hash(32);
	
	
	
	// process uploads
	// we're using ImageMagick. replace with your own...
	
	
	
	// get the file name and info
	$tmp  	= $_FILES['file']['tmp_name'];
	$info   = getimagesize($tmp);
	$width  = $info[0];
	$height = $info[1];
	
	
	
	// get the file's extension
	if($info[2] == 1) {
		$ext = '.gif';
	}
	if($info[2] == 2) {
		$ext = '.jpg';
	}
	if($info[2] == 3) {
		$ext = '.png';
	}
	
	
	
	// resize the full-size image (if needed)
	$image = new Imagick($tmp);
	if($width > $height) {
		if($width > 1920) {
			$image->thumbnailImage(1920, 0);
		}
	}
	elseif($height > $width) {
		if($height > 1280) {
		$image->thumbnailImage(0, 1280);
	}
	}
	elseif($width == $height) {
		if($height > 1280) {
		$image->thumbnailImage(0, 1280);
	}
	}
	$image->stripImage();
	$image->writeImage(UPLOADS.'/'.$hash.$ext);
	
	
	
	// resize to create a thumbnail
	$image = new Imagick($tmp);
	if($width > $height) {
		if($width > 150) {
			$image->thumbnailImage(150, 0);
		}
	}
	elseif($height > $width) {
		if($height > 200) {
			$image->thumbnailImage(150, 0);
		}
	}
	elseif($width == $height) {
		if($height > 200) {
			$image->thumbnailImage(0, 200);
		}
	}
	$image->stripImage();
	$image->writeImage(UPLOADS.'/tn-'.$hash.$ext);
	
	
	
	// assign new filenames to variables for later
	$large = $hash.$ext;
	$tn    = 'tn-'.$hash.$ext;
	
	
	
	// done processing uploads
	
	// now prepare for uploading local file to cloud files...
	
	
	
	// obtain an Object Store service object from the $client (set in config.php)
	$objectStoreService = $client->objectStoreService(null, RS_REGION);
	
	
	
	// get the container
	$container = $objectStoreService->getContainer($container_name);
	
	
	
	// we're uploading many files at once, so we put them into an array
	$objects = array(
		array(
		    'name' => $large,
		    'path' => UPLOADS.'/'.$large
		),
		array(
		    'name' => $tn,
		    'path' => UPLOADS.'/'.$tn
		)
	);
	
	
	
	// upload the images!
	
	try {
		$object = $container->uploadObjects($objects);
	} catch(BadResponseException $e) {
		echo $e->getMessage();
		echo $e->getRequest();  // this will pretty print the underlying HTTP request
		echo $e->getResponse(); // ... and the corresponding response
	}
	
	
	
	// insert the image's filename.ext into our database
	$db->query("INSERT INTO photos (gallery_id, photo, container, created_at) VALUES (".$db->escape($gallery_id).", '".$db->escape($large)."', '".$db->escape($container_name)."', NOW())");
	
	
	
	// clean up: delete the local files
	// comment out if you want to keep the local images - not recommended for private containers
	unlink(UPLOADS.'/'.$tn);
	unlink(UPLOADS.'/'.$large);
