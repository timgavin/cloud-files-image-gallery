<?php
	
	// include our config file
	require_once 'assets/config.php';
	
	// get the container/gallery name
	if(isset($_GET['gallery'])) {
		$container_name = (string) $_GET['gallery'];
		$gallery_name = $db->get_var("SELECT name FROM galleries WHERE container='".$db->escape($container_name)."'");
	}	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
		<title>Upload Photos into <?php echo $gallery_name ?></title>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
		<link href="//cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
		<link href="//bootswatch.com/flatly/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<style>
			.icon-lg {
				font-size: 3em;	
			}
			div#my-dropzone {
				border: 0;
				background: #efefef;
				border-radius: 5px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="col-md-10 col-md-offset-1" id="content">
				<h1 class="page-header">
					<i class="fa fa-cloud-upload"></i> <span class="text-primary"><?php echo $gallery_name ?></span>
					<a href="index.php"><button class="btn btn-sm btn-primary pull-right" style="margin-left:10px"><i class="fa fa-picture-o"></i> Browse Galleries</button></a>
					<a href="list-photos.php?gallery=<?php echo $container_name ?>"><button class="btn btn-sm btn-primary pull-right"><i class="fa fa-eye"></i> View Photos</button></a>
				</h1>		
				
				<!-- dropzone form -->
				<form>		
					<div class="dropzone dz-default dz-file-preview dz-clickable" id="my-dropzone">
						<div class="text-center">
							<label class="message dz-message text-center text-danger">
								<h1><i class="fa fa-cloud-upload icon-lg"></i><br>Drag Photos Here</h1>or tap to select files...
							</label>
						</div>
					</div>
				</form>
				
				<br>
				
				<div id="messages"></div>
				
				<!-- dropzone: upload and clear buttons -->
				<div class="row" id="buttons">
					<div class="col-xs-6">
						<div id="upload" class="hide">
							<button class="btn btn-info btn-block btn-lg" id="btn-upload"><i class="fa fa-upload"></i> Upload All Photos</button>
						</div>
					</div>
					<div class="col-xs-6">
						<div id="clear" class="hide">
						<button class="btn btn-danger btn-block btn-lg" id="btn-clear"><i class="fa fa-ban"></i> Clear All Photos</button>
						</div>
					</div>
				</div>
				
				
			</div>
		</div>
		<script src="//code.jquery.com/jquery.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.js"></script>
		<script>
			
			// we'll put our dropzone script here because we need to echo a php variable in the dropzone url...
			$(document).ready(function() {
				$('div#my-dropzone').dropzone({
					url: '/assets/php/upload.php?gallery=<?php echo $container_name ?>'
				})
			});
			
			Dropzone.autoDiscover = false;
			Dropzone.options.myDropzone = {
				
				autoProcessQueue : false,
				parallelUploads: 50,
				
				init : function() {
					
					var submitButton = document.querySelector('#btn-upload');
					
					myDropzone = this;
					
					
					// process all queued files
					submitButton.addEventListener('click', function() {
						myDropzone.processQueue();
					});
					
					
					
					// when adding files...
					this.on('addedfile', function() {
						$('#upload, #clear, #buttons').removeClass('hide').show();
						$('#messages').removeClass('alert alert-success alert-danger').hide();
					});
					
					
					
					// add a remove button to each image
					this.on('addedfile', function(file,maxFileSize) {
						var removeButton = Dropzone.createElement('<i class="fa fa-close text-danger"></i>');
						var _this = this;
						
						// Listen to the click event
						removeButton.addEventListener('click', function(e) {
							e.preventDefault();
							e.stopPropagation();
							_this.removeFile(file);
						});
						
						// Add the button to the file preview element.
						file.previewElement.appendChild(removeButton);
					});
					
					
					this.on('queuecomplete', function(file) {
						myDropzone.removeAllFiles();
						myDropzone.removeAllFiles(true);
						$('#messages').addClass('alert alert-success').html('<p class="text-center"><strong>Files Uploaded!</strong></p>').show();
						$('#buttons').hide();
						$('#upload, #clear').addClass('hide').hide();
					});
					
					$('#btn-clear').on('click', function() {
						myDropzone.removeAllFiles();
						myDropzone.removeAllFiles(true);
						$('#upload, #clear').addClass('hide').hide();
					});
				}
			};
		</script>
	</body>
</html>