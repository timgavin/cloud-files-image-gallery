<?php
	
	
	// include our config file
	require_once 'assets/config.php';
	
	
	
	// get the container name
	$container_name = (string) $_GET['gallery'];
	
	
	
	// get the container's privacy status
	$private = $db->get_var("SELECT private FROM galleries WHERE container='".$db->escape($container_name)."'");
	
	
	
	// get the filename in our local database. we'll compare them against the container later
	if($results = $db->get_results("SELECT g.name, g.container, p.id, p.photo FROM galleries g JOIN photos p ON g.id=p.gallery_id WHERE g.container='".$db->escape($container_name)."'")) {
		
		
		
		// obtain an Object Store service object from the $client (set in config.php)
		$objectStoreService = $client->objectStoreService(null, RS_REGION);
		
		
		
		// assign the container name to our object store service
		$container = $objectStoreService->getContainer($container_name);
		
		
		
		// list the objects (images) within the container
		$objects = $container->objectList();
		
		
		
		// create some empty arrays for storing our stuff
		$obs = $data = array();
		
		
		
		// loop through our objects and put the filename and url into the $urls array
		foreach($objects as $object) {
			
			// check if our gallery is private or public
			if($private == 1) {
				// private: get the temporary url, valid for 1 hour
				// note: to prevent files from auto-downloading in the browser, concatenate &inline to end of temporary URLs
				$url = $object->getTemporaryUrl(3600, 'GET').'&inline';
			} else {
				// public: get the permanent url (at this point we could now store this in our database too...)
				// note: we do not need to concat &inline on public URLs
				$url = $object->getPublicUrl();
			}
			
			// get the filename
			$filename = $object->getName();
			
			// put into array with filename as key and url as value
			$obs[$filename] = $url;
		}
		
		
		
		// loop through our database results so we can put the images into a new array, and to group the thumbnaila with their full-size images.
		// we're doing this because at some point we may want to add captions, which we'll have to store locally, and because
		// this is also a good intregity check, to make sure we're not printing anything that isn't in our local database
		
		$i=0;
		
		foreach($results as $key => $value) {
			
			
			// make sure the object name matches what we have in the database (`photo` field)
			if(array_key_exists($value->photo, $obs)) {
				
				
				// assign our large image
				$data[$value->photo]['lg'] = $obs[$value->photo];
				
				
				// assign our thumbnail...
				
				// create the thumbnail key for searching
				$tnkey = 'tn-'.$value->photo;
				
				
				// if the thumbnail key matches the $urls key, we have our thumbnail image
				if(array_key_exists($tnkey, $obs)) {
					$data[$value->photo]['tn'] = $obs[$tnkey];
				}
			
			
				// assign the row ID to the object for the delete button
				$data[$value->photo]['id'] = $value->id;
				
				
				// assign a name
				$data[$value->photo]['name'] = $value->photo;
				
				
				// assign the gallery name and container name
				$gallery_name = $value->name;
				$container_name = $value->container;
			}
			
			$i++;
		}
	
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
		<title>List Photos</title>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
		<link href="//cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/3.0.3a/ekko-lightbox.min.css" rel="stylesheet">
		<link href="//bootswatch.com/flatly/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		
		<div class="container" id="container">
		
			<div class="col-md-10 col-md-offset-1" id="content">
			
				<div id="messages"></div>
				
				
				<!-- loop through $data array and print each thumbnail with link to full-size -->
				<?php if(isset($results)): ?>
					
					
					<!-- header -->
					<div class="row">
						<h1 class="page-header">
							<?php echo $gallery_name ?>
							<a href="index.php"><button class="btn btn-sm btn-primary pull-right" style="margin-left:10px"><i class="fa fa-picture-o"></i> Galleries</button></a>
							<a href="upload-photos.php?gallery=<?php echo $container_name ?>"><button class="btn btn-sm btn-success pull-right"><i class="fa fa-upload"></i> Upload Photos</button></a>
						</h1>
					</div>
					
					
					
					<!-- display thumbnails -->
					<div class="row well">
						<?php foreach($data as $image): ?>
							<div class="col-xs-6 col-md-4" id="image-<?php echo $image['id'] ?>">
								<div class="thumbnail">
									<a href="<?php echo $image['lg'] ?>" data-toggle="lightbox" data-gallery="gallery">
										<img src="<?php echo $image['tn'] ?>" class="img-responsive img-rounded" alt="">
									</a>
									<div class="caption text-right">
										<button class="btn btn-xs btn-danger delete" data-id="<?php echo $image['id'] ?>" data-name="<?php echo $image['name'] ?>" data-cont="<?php echo $container_name ?>" data-toggle="tooltip" title="Delete photo"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				
				
				
				<!-- display message -->
				<?php else: ?>
					<div class="alert alert-danger">
						<p class="lead">This gallery is empty!</p>
						<p><a href="/upload-photos.php?gallery=<?php echo $container_name?>"><button class="btn btn-danger"><i class="fa fa-upload"></i> Upload Photos</button></a></p>
					</div>
				<?php endif; ?>
				
				
			</div>
		
		</div>
		
		<script src="//code.jquery.com/jquery.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/spin.js/2.0.1/spin.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/3.0.3a/ekko-lightbox.min.js"></script>
		<script src="/assets/js/site.js"></script>
	</body>
</html>