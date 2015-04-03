<?php
	
	// include our config file
	require_once 'assets/config.php';	

	// get our galleries/container names and photos from our local database
	$galleries = $db->get_results("SELECT g.id, g.name, g.container, g.private, COUNT(p.id) AS count, p.photo FROM galleries g LEFT JOIN photos p ON p.gallery_id = g.id GROUP BY g.id, g.name ORDER BY g.id");
	
	// obtain an Object Store service object from the $client (set in config.php)
	$objectStoreService = $client->objectStoreService(null, RS_REGION);
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
		<title>List Galleries</title>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
		<link href="//bootswatch.com/flatly/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		
		<div class="container" id="container">
			
			<div class="row">
				<div class="col-xs-12">
					<div id="messages"></div>
				</div>
			</div>
			
			<div class="row">
			
				<div class="col-md-10 col-md-offset-1" id="content">
				
					<h1 class="page-header">
						Your Galleries
						<button class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#new-gallery"><i class="fa fa-plus"></i> New Gallery</button>
					</h1>
				
				<?php if(isset($galleries)): ?>
					
					<!-- loop through the galleries array -->
					<div class="row">
						
						<?php foreach($galleries as $gallery): ?>
						
						
							<?php
								
								// we want to get a thumbnail image and display it
								
								// assign the container name to our object store service
								$container = $objectStoreService->getContainer($gallery->container);
								
								
								
								// list the objects (images) within the container
								$objects = $container->objectList();
								
								
								
								// create some empty arrays for storing our stuff
								$obs = $data = array();
								
								
								
								// loop through our objects and get a thumbnail image
								foreach($objects as $object) {
									
									// check if our gallery is private or public
									if($gallery->private == 1) {
										// private: get the temporary url, valid for 1 hour
										// note: to prevent files from auto-downloading in the browser, concatenate &inline to end of temporary URLs
										$thumbnail = $object->getTemporaryUrl(3600, 'GET').'&inline';
									} else {
										// public: get the permanent url (at this point we could now store this in our database too...)
										// note: we do not need to concat &inline on public URLs
										$thumbnail = $object->getPublicUrl();
									}
									
								}
								
								// a thumbnail doesn't exist, use this instead
								if(empty($thumbnail)) {
									$thumbnail = 'http://placehold.it/150';
								}
								
							?>
							
							<div class="col-xs-6 col-sm-3" id="list">
								
								<!-- list the item: give each <li> a unique id -->
								<div class="thumbnail" id="gallery-<?php echo $gallery->container ?>">
									
									
									<!-- open a link to view photos -->
									<a href="list-photos.php?gallery=<?php echo $gallery->container ?>">
										<img src="<?php echo $thumbnail ?>" class="img-responsive">
									</a>
									
									
									<div class="caption">
											
										<!-- print private & public status icons -->
										<div class="row">
											<div class="col-xs-12">
												<!-- print the gallery name -->
												<h4><?php echo $gallery->name ?></h4>
											</div>
										</div>
										
										
										<div class="row">
											
											<div class="col-xs-12">
												
												<!-- public/private status icons -->
												<a href="list-photos.php?gallery=<?php echo $gallery->container ?>">
														<?php if($gallery->private): ?>
														<button type="button" class="btn btn-default btn-xs push-left" data-toggle="tooltip" title="Private"><i class="fa fa-lock"></i></button>
													<?php else: ?>
														<button type="button" class="btn btn-default btn-xs push-left" data-toggle="tooltip" title="Public"><i class="fa fa-globe"></i></button>
													<?php endif; ?>
												</a>
												
												
												<!-- print number of photos in each gallery -->
												<a href="list-photos.php?gallery=<?php echo $gallery->container ?>">
													<button type="button" class="btn btn-default btn-xs push-left" data-toggle="tooltip" title="<?php echo $gallery->count ?> photos"><?php echo $gallery->count ?></button>
												</a>
										
												
												<!-- delete gallery button -->
												<button type="button" class="btn btn-danger btn-xs pull-right delete-gallery" data-toggle="tooltip" title="Delete" data-cont="<?php echo $gallery->container ?>"><i class="fa fa-trash"></i></button>
												
												
												<!-- upload photos button -->
												<a href="upload-photos.php?gallery=<?php echo slug($gallery->container) ?>">
													<button type="button" class="btn btn-success btn-xs pull-right" data-toggle="tooltip" title="Upload" style="margin-right:5px"><i class="fa fa-upload"></i></button>
												</a>
					
											</div>
											
										</div>
								
									</div>
								
								</div>
								
							</div>
					
						<?php endforeach; ?>
					
					</div>
					
				<?php endif; ?>
					
					<!-- 'no galleries exist' message -->
					<div class="row well<?php if(isset($galleries)): ?> hide<?php endif; ?>" id="no-galleries">
						<div class="col-xs-12">
							<h3><i class="fa fa-warning text-warning"></i> <span class="text-danger">You do not have any galleries</span></h3><br>
							<button class="btn btn-lg btn-success" data-toggle="modal" data-target="#new-gallery"><i class="fa fa-plus"></i> Create a Gallery</button>
						</div>
					</div>
				
				</div>
			
			</div>
			
		</div>
		
		<!-- this modal shows the 'add gallery' form -->
		<div class="modal fade" id="new-gallery" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<form id="create-gallery" method="post" accept-charset="utf-8">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title">New Gallery</h4>
						</div>
						<div class="modal-body">
							<div id="modal-messages"></div>
							<div id="form">
								<div class="form-group">
									<input type="text" name="gallery_name" id="gallery_name" class="form-control input-lg" placeholder="Gallery name...">
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="make_private" id="make_private" value="make_private"> Make Private
									</label>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Create</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<script src="//code.jquery.com/jquery.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/spin.js/2.0.1/spin.min.js"></script>
		<script src="/assets/js/site.js"></script>
	</body>
</html>