// function from http://stackoverflow.com/questions/1053902/
function convertToSlug(Text) {
	return Text
	.toLowerCase()
	.replace(/[^\w ]+/g,'')
	.replace(/ +/g,'-');
}


$(document).ready(function() {
	
	
	// bootstrap tooltips
	$('[data-toggle="tooltip"]').tooltip();
	
	
	
	// set spin.js options
	var opts = {
		length 	: 40,
		width 	: 10,
		radius 	: 30,
	};
	
	
	
	// show photos in modal (quick 'n dirty for this example)
	$(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
		event.preventDefault();
		$(this).ekkoLightbox();
	}); 
	
	
	
	// put focus on the input when 'create gallery' modal is shown
	$('#new-gallery').on('shown.bs.modal', function() {
		$('#modal-messages').removeClass('alert alert-danger').text('');
		$("#gallery_name").focus();
	});
	
	
	// reset the modal when hidden
	$('#new-gallery').on('hidden.bs.modal', function() {
		$('#modal-messages').removeClass('alert alert-danger').text('');
		$("#gallery_name").val('');
		$('#make_private').prop('checked', false);
	});
	
	
	
	// create a new gallery
	$('#create-gallery').submit(function(e) {
		
		e.preventDefault();
		
		var data = $("#create-gallery").serialize();
		var gallery_name = $('#gallery_name').val();
		
		if(gallery_name == 0) {
			$('#modal-messages').addClass('alert alert-danger').html('<strong>Please enter a gallery name</strong>');
			return false;
		}
		
		// start a spinner
		var target = document.getElementById('container');
		var spinner = new Spinner(opts).spin(target);
		
		$.post('/assets/ajax/create-gallery.php',data,function(result){
			
			if(result == 'success') {
				// redirect to upload page
				window.location.replace('upload-photos.php?gallery=' + convertToSlug(gallery_name));
			} else {
				$('#messages').addClass('alert alert-danger').text('Oops. Something went wrong and the gallery was not created.');
			}
			spinner.stop(target);
		});
	});
	
	
	
	// delete a photo
	$('.delete').click(function() {
		
		var object_name = $(this).data('name');
		var container_name = $(this).data('cont');
		var id = $(this).data('id');
		
		if(confirm('Are you sure? This can not be undone!')) {
			
			// start a spinner
			var target = document.getElementById('container');
			var spinner = new Spinner(opts).spin(target);
			
			$.post('/assets/ajax/delete-photo.php',{object:object_name, container:container_name, id:id},function(result){
				
				if(result == 'success') {
					// remove the thumbnail from the page
					$('#image-' + id).fadeOut();
				} else {
					$('#messages').addClass('alert alert-danger').text('Oops. Something went wrong and the photo was not deleted.');
				}
				spinner.stop(target);
			});
		}
	});
	
	
	
	// delete a gallery and all its contents
	$('.delete-gallery').click(function(e) {
		
		e.preventDefault();
		
		var container_name = $(this).data('cont');
		
		if(confirm('Are you sure? This can not be undone!')) {
			
			// start a spinner
			var target = document.getElementById('container');
			var spinner = new Spinner(opts).spin(target);
			
			$.post('/assets/ajax/delete-gallery.php',{container_name:container_name},function(result){
				
				if(result == 'success') {
					$('#gallery-' + container_name).fadeOut().remove();
				} else {
					$('#messages').addClass('alert alert-danger').text('Oops. Something went wrong and the gallery was not deleted.');
				}
				
				// display message if no galleries
				if($('#list li').length == 0) {
					$('#no-galleries').removeClass('hide');
				}
				
				spinner.stop(target);
			});
		}
	});
	

});