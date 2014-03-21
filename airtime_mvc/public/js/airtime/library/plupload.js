$(document).ready(function() {
	
    var uploader;
	var self = this;
	self.uploadFilter = "all";

	$("#plupload_files").pluploadQueue({
		// General settings
		runtimes        : 'gears, html5, html4',
		url             :  baseUrl+'rest/media',
		//chunk_size      : '5mb', //Disabling chunking since we're using the File Upload REST API now
		unique_names    : 'true',
		multiple_queues : 'true',
		filters : [
			{title: "Audio Files", extensions: "ogg,mp3,oga,flac,wav,m4a,mp4,opus"}
		]
	});

	uploader = $("#plupload_files").pluploadQueue();

	uploader.bind('FileUploaded', function(up, file, json) 
	{
		//Refresh the upload table:
		self.recentUploadsTable.fnDraw(); //Only works because we're using bServerSide
		//In DataTables 1.10 and greater, we can use .fnAjaxReload()
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
	
	self.renderImportStatus = function ( data, type, full ) {
		if (typeof data !== "number") {
			console.log("Invalid data type for the import_status.");
			return;
		}
		var statusStr = $.i18n._("Unknown");
		if (data == 0)
		{
			statusStr = $.i18n._("Successfully imported");
		} 
		else if (data == 1)
		{
			statusStr = $.i18n._("Pending import");
		}
	
         return statusStr;
    };
    
	self.renderFileActions = function ( data, type, full ) {
		return '<a class="deleteFileAction">Delete</a>';
    };
	 
    $("#recent_uploads_table").on("click", "a.deleteFileAction", function () {
    	//Grab the file object for the row that was clicked.
    	// Some tips from the DataTables forums:
        //   fnGetData is used to get the object behind the row - you can also use
        //   fnGetPosition if you need to get the index instead
    	file = $("#recent_uploads_table").dataTable().fnGetData($(this).closest("tr")[0]);
    	
    	$.ajax({
    		  type: 'DELETE',
    		  url: '/rest/media/' + file.id,
    		  success: function(resp) {
    			  self.recentUploadsTable.fnDraw();
    		  },
    		  error: function() {
    			  alert($.i18n._("Error: The file could not be deleted. Please try again later."));
    		  }
    		});
    });
    
	self.setupRecentUploadsTable = function() {
		recentUploadsTable = $("#recent_uploads_table").dataTable({
            "bJQueryUI": true,
			"bProcessing": false,
			"bServerSide": true,
			"sAjaxSource": '/Plupload/recent-uploads/format/json',
			"sAjaxDataProp": 'files',
			"bSearchable": false,
			"bInfo": true,
			//"sScrollY": "200px",
			"bFilter": false,
			"bSort": false,
			"sDom": '<"H"l>frtip',
			"bPaginate" : true,
            "sPaginationType": "full_numbers",
			"aoColumns": [
	   		   { "mData" : "artist_name", "sTitle" : $.i18n._("Creator") },
			   { "mData" : "track_title", "sTitle" : $.i18n._("Title") },
			   { "mData" : "import_status", "sTitle" : $.i18n._("Import Status"), 
			      "mRender": self.renderImportStatus
			   },
			   { "mData" : "utime", "sTitle" : $.i18n._("Uploaded") },
			   { "mData" : "id", "sTitle" : $.i18n._("Actions"),
				      "mRender": self.renderFileActions
			   }
			 ],
			 "fnServerData": function ( sSource, aoData, fnCallback ) {
				/* Add some extra data to the sender */
				aoData.push( { "name": "uploadFilter", "value": self.uploadFilter } );
				$.getJSON( sSource, aoData, function (json) { 
					fnCallback(json);
				} );
			 }
		});
		
		return recentUploadsTable;
	};
	
	$("#upload_status_all").click(function() {
		self.uploadFilter = "all";
		self.recentUploadsTable.fnDraw();
	});
	$("#upload_status_pending").click(function() {
		self.uploadFilter = "pending";
		self.recentUploadsTable.fnDraw();
	});
	$("#upload_status_failed").click(function() {
		self.uploadFilter = "failed";
		self.recentUploadsTable.fnDraw();
	});

	//Create the recent uploads table.
	self.recentUploadsTable = self.setupRecentUploadsTable();

	//$("#recent_uploads_table.div.fg-toolbar").prepend('<b>Custom tool bar! Text/images etc.</b>');
});
