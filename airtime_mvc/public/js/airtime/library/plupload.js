$(document).ready(function() {
	
    var uploader;

	$("#plupload_files").pluploadQueue({
		// General settings
		runtimes        : 'gears, html5, html4',
		//url             :  baseUrl+'Plupload/upload/format/json',
		url             :  baseUrl+'rest/media',
		//chunk_size      : '5mb', //Disabling chunking since we're using the File Upload REST API now
		unique_names    : 'true',
		multiple_queues : 'true',
		filters : [
			{title: "Audio Files", extensions: "ogg,mp3,oga,flac,wav,m4a,mp4,opus"}
		]
	});

	uploader = $("#plupload_files").pluploadQueue();

	uploader.bind('FileUploaded', function(up, file, json) {

		/*
		var j = jQuery.parseJSON(json.response);

		console.log(json.response);
		if (j.error !== undefined) {
			var row = $("<tr/>")
				.append('<td>' + file.name +'</td>')
				.append('<td>' + j.error.message + '</td>');

			$("#plupload_error").find("table").append(row);
			$("#plupload_error table").css("display", "inline-table");
		} else {
			//FIXME: This should just update something in the GUI, not communicate with the backend -- Albert
			/*
		    var tempFileName = j.tempfilepath;
		    $.get(baseUrl+'Plupload/copyfile/format/json/name/'+
		          encodeURIComponent(file.name)+'/tempname/' +
		          encodeURIComponent(tempFileName), function(jr){
		        if(jr.error !== undefined) {
		            var row = $("<tr/>")
		                .append('<td>' + file.name +'</td>')
		                .append('<td>' + jr.error.message + '</td>');

		            $("#plupload_error").find("table").append(row);
		            $("#plupload_error table").css("display", "inline-table");
		        }
		    });
		}*/
	});
	
	var uploadProgress = false;
	
	uploader.bind('QueueChanged', function(){
        uploadProgress = (uploader.files.length > 0)
 	});
	
	uploader.bind('UploadComplete', function(){
		uploadProgress = false;
	});
	
	$(window).bind('beforeunload', function(){
		if(uploadProgress){
            return sprintf($.i18n._("You are currently uploading files. %sGoing to another screen will cancel the upload process. %sAre you sure you want to leave the page?"),
                    "\n", "\n");
		}
	});

});
