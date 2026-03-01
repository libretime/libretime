$(document).ready(function () {
  var uploadProgress;
  var self = this;
  self.uploadFilter = "all";

  self.IMPORT_STATUS_CODES = {
    0: { message: $.i18n._("Successfully imported") },
    1: { message: $.i18n._("Pending import") },
    2: { message: $.i18n._("Import failed.") },
    UNKNOWN: { message: $.i18n._("Unknown") },
  };
  if (Object.freeze) {
    Object.freeze(self.IMPORT_STATUS_CODES);
  }

  Dropzone.options.addMediaDropzone = {
    url: "/rest/media",
    //clickable: false,
    acceptedFiles: acceptedMimeTypes.join(),
    addRemoveLinks: true,
    dictRemoveFile: $.i18n._("Remove"),
    maxFilesize: LIBRETIME_PLUPLOAD_MAX_FILE_SIZE, //Megabytes
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

      this.on("queuecomplete", function () {
        uploadProgress = false;
      });
    },
  };

  $(window).bind("beforeunload", function () {
    if (uploadProgress) {
      return sprintf(
        $.i18n._(
          "You are currently uploading files. %sGoing to another screen will cancel the upload process. %sAre you sure you want to leave the page?",
        ),
        "\n",
        "\n",
      );
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
      return (
        '<a class="deleteFileAction">' +
        $.i18n._("Delete from Library") +
        "</a>"
      );
    } else if (full.import_status == 1) {
      //No actions for pending files
      return $.i18n._("N/A");
    } else {
      //Failed downloads
      return '<a class="deleteFileAction">' + $.i18n._("Clear") + "</a>";
    }
  };

  $("#recent_uploads_table").on("click", "a.deleteFileAction", function () {
    //Grab the file object for the row that was clicked.
    // Some tips from the DataTables forums:
    //   fnGetData is used to get the object behind the row - you can also use
    //   fnGetPosition if you need to get the index instead
    file = $("#recent_uploads_table")
      .dataTable()
      .fnGetData($(this).closest("tr")[0]);

    $.ajax({
      type: "DELETE",
      url: "rest/media/" + file.id + "?csrf_token=" + $("#csrf").attr("value"),
      success: function (resp) {
        self.recentUploadsTable.fnDraw();
      },
      error: function () {
        alert(
          $.i18n._(
            "Error: The file could not be deleted. Please try again later.",
          ),
        );
      },
    });
  });

  self.setupRecentUploadsTable = function () {
    return $("#recent_uploads_table").dataTable({
      bJQueryUI: true,
      bProcessing: false,
      bServerSide: true,
      sAjaxSource: "/plupload/recent-uploads/format/json",
      sAjaxDataProp: "files",
      bSearchable: false,
      bInfo: true,
      //"sScrollY": "200px",
      bFilter: false,
      bSort: false,
      //"sDom": '<"H">frtip<"F"l>',
      sDom: '<"dataTables_scrolling"frt><"F"lip>',
      bPaginate: true,
      sPaginationType: "full_numbers",
      oLanguage: getDatatablesStrings({
        sEmptyTable: $.i18n._("No files have been uploaded yet."),
        sInfoEmpty: $.i18n._("Showing 0 to 0 of 0 uploads"),
        sInfo: $.i18n._("Showing _START_ to _END_ of _TOTAL_ uploads"),
        sInfoFiltered: $.i18n._("(filtered from _MAX_ total uploads)"),
      }),
      aoColumns: [
        { mData: "artist_name", sTitle: $.i18n._("Creator") },
        { mData: "track_title", sTitle: $.i18n._("Title") },
        {
          mData: "import_status",
          sTitle: $.i18n._("Import Status"),
          mRender: self.renderImportStatus,
        },
        { mData: "utime", sTitle: $.i18n._("Uploaded") },
        {
          mData: "id",
          sTitle: $.i18n._("Actions"),
          mRender: self.renderFileActions,
        },
      ],
      fnServerData: function (sSource, aoData, fnCallback) {
        /* Add some extra data to the sender */
        aoData.push({ name: "uploadFilter", value: self.uploadFilter });
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
            } else if (self.isRecentUploadsRefreshTimerActive) {
              self.stopRefreshingRecentUploads();
              self.recentUploadsTable.fnDraw();
            }

            // Update usability hint - in common.js
            getUsabilityHint();
          }
        });
      },
    });
  };

  $("#recent_uploads").addTitles("td");

  self.isRecentUploadsRefreshTimerActive = false;

  self.startRefreshingRecentUploads = function () {
    if (!self.isRecentUploadsRefreshTimerActive) {
      //Prevent multiple timers from running
      self.recentUploadsRefreshTimer = setInterval(function () {
        self.recentUploadsTable.fnDraw();
      }, 3000);
      self.isRecentUploadsRefreshTimerActive = true;
    }
  };

  self.stopRefreshingRecentUploads = function () {
    clearInterval(self.recentUploadsRefreshTimer);
    self.isRecentUploadsRefreshTimerActive = false;
  };

  $("#upload_status_all").click(function () {
    if (self.uploadFilter !== "all") {
      self.uploadFilter = "all";
      self.recentUploadsTable.fnPageChange(0).fnDraw();
    }
  });
  $("#upload_status_pending").click(function () {
    if (self.uploadFilter !== "pending") {
      self.uploadFilter = "pending";
      self.recentUploadsTable.fnPageChange(0).fnDraw();
    }
  });
  $("#upload_status_failed").click(function () {
    if (self.uploadFilter !== "failed") {
      self.uploadFilter = "failed";
      self.recentUploadsTable.fnPageChange(0).fnDraw();
    }
  });

  //Create the recent uploads table.
  self.recentUploadsTable = self.setupRecentUploadsTable();

  //$("#recent_uploads_table.div.fg-toolbar").prepend('<b>Custom tool bar! Text/images etc.</b>');

  $("#select_type").on("change", function () {
    var ttValue = $("#select_type").val();
    var ttText = $('#select_type option[value="' + ttValue + '"]').text();
    if (ttValue != "") {
      $("#upload_type").text(" " + ttText);
      $("#upload_type").css("color", "#ff611f");
    } else {
      $("#upload_type").text(" Tracks");
      $("#upload_type").css("color", "#ffffff");
    }
    Cookies.set("tt_upload", ttValue);
  });
});
