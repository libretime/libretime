$(document).ready(function() {
    var uploader;

	$("#plupload_files").pluploadQueue({
		// General settings
		runtimes : 'html5,html4',
		url : '/Plupload/upload/format/json',
		multiple_queues : 'true',
		filters : [
			{title: "Audio Files", extensions: "ogg,mp3"}
		]
	});

	uploader = $("#plupload_files").pluploadQueue();

	uploader.bind('FileUploaded', function(up, file, json) {
		var j = jQuery.parseJSON(json.response);

		if(j.error !== undefined) {  

			var row = $("<tr/>")
				.append('<td>' + file.name +'</td>')
				.append('<td>' + j.error.message + '</td>');
				
			$("#plupload_error").find("table").append(row);
		}
	});
	
	var uploadProgress = false;
	
	uploader.bind('QueueChanged', function(){
		uploadProgress = true;
	});
	
	uploader.bind('UploadComplete', function(){
		uploadProgress = false;
	});
	
	
	$(window).bind('beforeunload', function(){
		if(uploadProgress){
			if(!confirm("Are you sure you want to navigate away from the page?\nNavigating away from the page will cancel all the upload process.")){
				return false;
			}
		}
	});

});
