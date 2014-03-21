$(document).ready(function() {
	
    var uploader;
	var self = this;

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

		var j = jQuery.parseJSON(json.response);
		console.log(j);
		console.log(file.name);

		self.recentUploadsTable.fnDraw(); //Only works because we're using bServerSide
		//In DataTables 1.10 and greater, we can use .fnAjaxReload()

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
        uploadProgress = (uploader.files.length > 0);
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
	
	self.setupRecentUploadsTable = function() {
		return recentUploadsTable = $("#recent_uploads_table").dataTable({
            "bJQueryUI": true,
			"bProcessing": false,
			"bServerSide": true,
			"sAjaxSource": '/Plupload/recent-uploads/format/json',
			"sAjaxDataProp": 'files',
			"bSearchable": false,
			"bInfo": true,
			"sScrollY": "200px",
			"bFilter": false,
			"bSort": false,
			"sDom": '<"H"l>frtip',
			"bPaginate" : true,
            "sPaginationType": "full_numbers",
			"aoColumns": [
	   		   { "mData" : "artist_name", "sTitle" : $.i18n._("Creator") },
			   { "mData" : "track_title", "sTitle" : $.i18n._("Title") },
			   { "mData" : "state", "sTitle" : $.i18n._("Import Status")},
			   { "mData" : "utime", "sTitle" : $.i18n._("Uploaded") }
			 ]
		});
	};
	self.recentUploadsTable = self.setupRecentUploadsTable();

	$("#recent_uploads_table.div.fg-toolbar").prepend('<b>Custom tool bar! Text/images etc.</b>');

});
