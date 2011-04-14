$(document).ready(function() {
    var uploader;

	$("#plupload_files").pluploadQueue({
		// General settings
		runtimes : 'html5,html4',
		url : '/Plupload/upload/format/json',
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

});
