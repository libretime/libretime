$(document).ready(function () {

    var uploadProgress;
    var self = this;
    self.uploadFilter = "all";

    self.IMPORT_STATUS_CODES = {
        0: {message: $.i18n._("Successfully imported")},
        1: {message: $.i18n._("Pending import")},
        2: {message: $.i18n._("Import failed.")},
        UNKNOWN: {message: $.i18n._("Unknown")}
    };
    if (Object.freeze) {
        Object.freeze(self.IMPORT_STATUS_CODES);
    }

    Dropzone.options.addMediaDropzone = {
        url: '/rest/media',
        //clickable: false,
        acceptedFiles: acceptedMimeTypes.join() + ",.flac",
        addRemoveLinks: true,
        dictRemoveFile: $.i18n._("Remove"),
        init: function () {
            this.on("sending", function (file, xhr, data) {
                data.append("csrf_token", $("#csrf").val());
            });

            this.on("addedfile", function (file, xhr, data) {
                var el = $(file.previewElement);
                uploadProgress = true;
                el.find(".dz-remove").prependTo(el.find(".dz-details"));
                el.find(".dz-error-message").appendTo(el.find(".dz-error-mark"));
            });

            this.on("success", function (file, xhr, data) {
                //Refresh the upload table:
                self.recentUploadsTable.fnDraw(); //Only works because we're using bServerSide
                //In DataTables 1.10 and greater, we can use .fnAjaxReload()
            });
            this.on("complete", function() {
                uploadProgress = false;
            });
        }
    };

    /*
     var uploader = new plupload.Uploader({
     runtimes: 'html5, flash, html4',
     browse_button: 'pickfiles',
     container: $("#container"),
     url :  baseUrl+'rest/media',
     filters : [
     {title: "Audio Files", extensions: "ogg,mp3,oga,flac,wav,m4a,mp4,opus,aac,oga,mp1,mp2,wma,au"}
     ],
     multipart_params : {
     "csrf_token" : $("#csrf").attr('value')
     },

     init: {
     PostInit: function() {
     document.getElementById('filelist').innerHTML = '';

     document.getElementById('uploadfiles').onclick = function() {
     uploader.start();
     return false;
     };
     },

     FilesAdded: function(up, files) {
     plupload.each(files, function(file) {
     document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
     });
     },

     UploadProgress: function(up, file) {
     document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
     },

     Error: function(up, err) {
     document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
     }
     }
     });

     uploader.init();
     */


    /*
     $("#plupload_files").pluploadQueue({
     // General settings
     runtimes        : 'gears, html5, html4',
     url             :  baseUrl+'rest/media',
     //chunk_size      : '5mb', //Disabling chunking since we're using the File Upload REST API now
     unique_names    : 'true',
     multiple_queues : 'true',
     filters : [
     {title: "Audio Files", extensions: "ogg,mp3,oga,flac,wav,m4a,mp4,opus,aac,oga,mp1,mp2,wma,au"}
     ],
     multipart_params : {
     "csrf_token" : $("#csrf").attr('value'),
     }
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
     });*/

    $(window).bind('beforeunload', function () {
        if (uploadProgress) {
            return sprintf($.i18n._("You are currently uploading files. %sGoing to another screen will cancel the upload process. %sAre you sure you want to leave the page?"),
                "\n", "\n");
        }
    });

    self.renderImportStatus = function (data, type, full) {
        if (typeof data !== "number") {
            console.log("Invalid data type for the import_status.");
            return;
        }
        var statusStr = self.IMPORT_STATUS_CODES.UNKNOWN.message;
        var importStatusCode = data;
        if (self.IMPORT_STATUS_CODES[importStatusCode]) {
            statusStr = self.IMPORT_STATUS_CODES[importStatusCode].message;
        }

        return statusStr;
    };

    self.renderFileActions = function (data, type, full) {
        if (full.import_status == 0) {
            return '<a class="deleteFileAction">' + $.i18n._('Delete from Library') + '</a>';
        } else if (full.import_status == 1) {
            //No actions for pending files
            return $.i18n._('N/A');
        } else { //Failed downloads
            return '<a class="deleteFileAction">' + $.i18n._('Clear') + '</a>';
        }
    };

    $("#recent_uploads_table").on("click", "a.deleteFileAction", function () {
        //Grab the file object for the row that was clicked.
        // Some tips from the DataTables forums:
        //   fnGetData is used to get the object behind the row - you can also use
        //   fnGetPosition if you need to get the index instead
        file = $("#recent_uploads_table").dataTable().fnGetData($(this).closest("tr")[0]);

        $.ajax({
            type: 'DELETE',
            url: 'rest/media/' + file.id + "?csrf_token=" + $("#csrf").attr('value'),
            success: function (resp) {
                self.recentUploadsTable.fnDraw();
            },
            error: function () {
                alert($.i18n._("Error: The file could not be deleted. Please try again later."));
            }
        });
    });

    self.setupRecentUploadsTable = function () {
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
            //"sDom": '<"H">frtip<"F"l>',
            "sDom": 'frt<"F"lip>',
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            "oLanguage": getDatatablesStrings({
                "sEmptyTable": $.i18n._("No files have been uploaded yet."),
                "sInfoEmpty": $.i18n._("Showing 0 to 0 of 0 uploads"),
                "sInfo": $.i18n._("Showing _START_ to _END_ of _TOTAL_ uploads"),
                "sInfoFiltered": $.i18n._("(filtered from _MAX_ total uploads)"),
            }),
            "aoColumns": [
                {"mData": "artist_name", "sTitle": $.i18n._("Creator")},
                {"mData": "track_title", "sTitle": $.i18n._("Title")},
                {
                    "mData": "import_status", "sTitle": $.i18n._("Import Status"),
                    "mRender": self.renderImportStatus
                },
                {"mData": "utime", "sTitle": $.i18n._("Uploaded")},
                {
                    "mData": "id", "sTitle": $.i18n._("Actions"),
                    "mRender": self.renderFileActions
                }
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                /* Add some extra data to the sender */
                aoData.push({"name": "uploadFilter", "value": self.uploadFilter});
                $.getJSON(sSource, aoData, function (json) {
                    fnCallback(json);
                    if (json.files) {
                        var areAnyFileImportsPending = false;
                        for (var i = 0; i < json.files.length; i++) {
                            //console.log(file);
                            var file = json.files[i];
                            if (file.import_status == 1) {
                                areAnyFileImportsPending = true;
                            }
                        }
                        if (areAnyFileImportsPending) {
                            //alert("pending uploads, starting refresh on timer");
                            self.startRefreshingRecentUploads();
                        } else {
                            self.stopRefreshingRecentUploads();
                        }

                        // Update usability hint - in common.js
                        getUsabilityHint();
                    }
                });
            }
        });

        return recentUploadsTable;
    };

    self.startRefreshingRecentUploads = function () {
        if (self.isRecentUploadsRefreshTimerActive()) { //Prevent multiple timers from running
            return;
        }
        self.recentUploadsRefreshTimer = setInterval("self.recentUploadsTable.fnDraw()", 3000);
    };

    self.isRecentUploadsRefreshTimerActive = function () {
        return (self.recentUploadsRefreshTimer != null);
    };

    self.stopRefreshingRecentUploads = function () {
        clearInterval(self.recentUploadsRefreshTimer);
        self.recentUploadsRefreshTimer = null;
    };

    $("#upload_status_all").click(function () {
        self.uploadFilter = "all";
        self.recentUploadsTable.fnDraw();
    });
    $("#upload_status_pending").click(function () {
        self.uploadFilter = "pending";
        self.recentUploadsTable.fnDraw();
    });
    $("#upload_status_failed").click(function () {
        self.uploadFilter = "failed";
        self.recentUploadsTable.fnDraw();
    });

    //Create the recent uploads table.
    self.recentUploadsTable = self.setupRecentUploadsTable();

    //$("#recent_uploads_table.div.fg-toolbar").prepend('<b>Custom tool bar! Text/images etc.</b>');
});
