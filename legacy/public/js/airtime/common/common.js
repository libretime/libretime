var previewWidth = 482,
  previewHeight = 110;

$(document).ready(function () {
  /* Removed as this is now (hopefully) unnecessary */
  //$("#Panel").stickyPanel({
  //    topPadding: 1,
  //    afterDetachCSSClass: "floated-panel",
  //    savePanelSpace: true
  //});

  //this statement tells the browser to fade out any success message after 5 seconds
  setTimeout(function () {
    $(".success").fadeOut("slow");
  }, 5000);
});

/*
 * i18n_months and i18n_days_short are used in jquery datepickers
 * which we use in multiple places
 */
var i18n_months = [
  $.i18n._("January"),
  $.i18n._("February"),
  $.i18n._("March"),
  $.i18n._("April"),
  $.i18n._("May"),
  $.i18n._("June"),
  $.i18n._("July"),
  $.i18n._("August"),
  $.i18n._("September"),
  $.i18n._("October"),
  $.i18n._("November"),
  $.i18n._("December"),
];

var i18n_months_short = [
  $.i18n._("Jan"),
  $.i18n._("Feb"),
  $.i18n._("Mar"),
  $.i18n._("Apr"),
  $.i18n._("May"),
  $.i18n._("Jun"),
  $.i18n._("Jul"),
  $.i18n._("Aug"),
  $.i18n._("Sep"),
  $.i18n._("Oct"),
  $.i18n._("Nov"),
  $.i18n._("Dec"),
];

var i18n_days_short = [
  $.i18n._("Su"),
  $.i18n._("Mo"),
  $.i18n._("Tu"),
  $.i18n._("We"),
  $.i18n._("Th"),
  $.i18n._("Fr"),
  $.i18n._("Sa"),
];

var HTTPMethods = Object.freeze({
  GET: "GET",
  POST: "POST",
  PUT: "PUT",
  PATCH: "PATCH",
  DELETE: "DELETE",
  OPTIONS: "OPTIONS",
});

var dateStartId = "#sb_date_start",
  timeStartId = "#sb_time_start",
  dateEndId = "#sb_date_end",
  timeEndId = "#sb_time_end";

function getDatatablesStrings(overrideDict) {
  var dict = {
    sEmptyTable: $.i18n._("No data available in table"),
    sInfo: $.i18n._("Showing _START_ to _END_ of _TOTAL_ entries"),
    sInfoEmpty: $.i18n._("Showing 0 to 0 of 0 entries"),
    sInfoFiltered: "", // $.i18n._("(filtered from _MAX_ total entries)"),
    sInfoPostFix: $.i18n._(""),
    sInfoThousands: $.i18n._(","),
    sLengthMenu: $.i18n._("Show _MENU_"),
    sLoadingRecords: $.i18n._("Loading..."),
    //"sProcessing":     $.i18n._("Processing..."),
    sProcessing: $.i18n._(""),
    sSearch: $.i18n._(""),
    sZeroRecords: $.i18n._("No matching records found"),
    oPaginate: {
      sFirst: "&laquo;",
      sLast: "&raquo;",
      sNext: "&rsaquo;",
      sPrevious: "&lsaquo;",
    },
    //"oPaginate": {
    //    "sFirst":    $.i18n._("First"),
    //    "sLast":     $.i18n._("Last"),
    //    "sNext":     $.i18n._("Next"),
    //    "sPrevious": $.i18n._("Previous")
    //},
    oAria: {
      sSortAscending: $.i18n._(": activate to sort column ascending"),
      sSortDescending: $.i18n._(": activate to sort column descending"),
    },
  };

  return $.extend({}, dict, overrideDict);
}

function adjustDateToServerDate(date, serverTimezoneOffset) {
  //date object stores time in the browser's localtime. We need to artificially shift
  //it to
  var timezoneOffset = date.getTimezoneOffset() * 60 * 1000;

  date.setTime(date.getTime() + timezoneOffset + serverTimezoneOffset * 1000);

  /* date object has been shifted to artificial UTC time. Now let's
   * shift it to the server's timezone */
  return date;
}

/**
 *handle to the jplayer window
 */
var _preview_window = null;

/**
 *Gets the info from the view when menu action play choosen and opens the jplayer window.
 */
function openAudioPreview(p_event) {
  p_event.stopPropagation();

  var audioFileID = $(this).attr("audioFile");
  var objId = $(".obj_id:first").attr("value");
  var objType = $(".obj_type:first").attr("value");
  var playIndex = $(this).parent().parent().attr("id");
  playIndex = playIndex.substring(4); //remove the spl_

  if (objType == "playlist") {
    open_playlist_preview(objId, playIndex);
  } else if (objType == "block") {
    open_block_preview(objId, playIndex);
  }
}

function open_audio_preview(type, id) {
  // The reason that we need to encode artist and title string is that
  // sometime they contain '/' or '\' and apache reject %2f or %5f
  // so the work around is to encode it twice.
  openPreviewWindow(
    baseUrl + "audiopreview/audio-preview/audioFileID/" + id + "/type/" + type,
    previewWidth,
    previewHeight,
  );
  _preview_window.focus();
}

/**
 *Opens a jPlayer window for the specified info, for either an audio file or playlist.
 *If audioFile, audioFileTitle, audioFileArtist is supplied the jplayer opens for one file
 *Otherwise the playlistID and playlistIndex was supplied and a playlist is played starting with the
 *given index.
 */
function open_playlist_preview(p_playlistID, p_playlistIndex) {
  if (p_playlistIndex == undefined)
    //Use a resonable default.
    p_playlistIndex = 0;

  if (_preview_window != null && !_preview_window.closed)
    _preview_window.playAllPlaylist(p_playlistID, p_playlistIndex);
  else
    openPreviewWindow(
      baseUrl +
        "audiopreview/playlist-preview/playlistIndex/" +
        p_playlistIndex +
        "/playlistID/" +
        p_playlistID,
      previewWidth,
      previewHeight,
    );
  _preview_window.focus();
}

function open_block_preview(p_blockId, p_blockIndex) {
  if (p_blockIndex == undefined)
    //Use a resonable default.
    p_blockIndex = 0;

  if (_preview_window != null && !_preview_window.closed)
    _preview_window.playBlock(p_blockId, p_blockIndex);
  else
    openPreviewWindow(
      baseUrl +
        "audiopreview/block-preview/blockIndex/" +
        p_blockIndex +
        "/blockId/" +
        p_blockId,
      previewWidth,
      previewHeight,
    );
  _preview_window.focus();
}

/**
 *Opens a jPlayer window for the specified info, for either an audio file or playlist.
 *If audioFile, audioFileTitle, audioFileArtist is supplied the jplayer opens for one file
 *Otherwise the playlistID and playlistIndex was supplied and a playlist is played starting with the
 *given index.
 */
function open_show_preview(p_showID, p_showIndex) {
  if (_preview_window != null && !_preview_window.closed)
    _preview_window.playAllShow(p_showID, p_showIndex);
  else
    openPreviewWindow(
      baseUrl +
        "audiopreview/show-preview/showID/" +
        p_showID +
        "/showIndex/" +
        p_showIndex,
      previewWidth,
      previewHeight,
    );
  _preview_window.focus();
}

function openPreviewWindow(url, w, h) {
  var dim = w && h ? "width=" + w + ",height=" + h + "," : "";
  // Hardcoding this here is kinda gross, but the alternatives aren't much better...
  _preview_window = window.open(
    url,
    $.i18n._("Audio Player"),
    dim + "scrollbars=yes",
  );
  return false;
}

function validateTimeRange() {
  var oRange,
    inputs = $(".sb-timerange > input"),
    start,
    end;

  oRange = AIRTIME.utilities.fnGetScheduleRange(
    dateStartId,
    timeStartId,
    dateEndId,
    timeEndId,
  );

  start = oRange.start;
  end = oRange.end;

  if (end >= start) {
    inputs.removeClass("error");
  } else {
    if (!inputs.hasClass("error")) {
      inputs.addClass("error");
    }
  }

  return {
    start: start,
    end: end,
    isValid: end >= start,
  };
}

// validate uploaded images
function validateImage(img, el) {
  // remove any existing error messages
  if ($("#img-err")) {
    $("#img-err").remove();
  }

  if (img.size > 2048000) {
    // 2MB - pull this from somewhere instead?
    // hack way of inserting an error message
    var err = $.i18n._("Selected file is too large");
    el.parent().after(
      "<ul id='img-err' class='errors'>" + "<li>" + err + "</li>" + "</ul>",
    );
    return false;
  } else if (validateMimeType(img.type) < 0) {
    var err = $.i18n._("File format is not supported");
    el.parent().after(
      "<ul id='img-err' class='errors'>" + "<li>" + err + "</li>" + "</ul>",
    );
    return false;
  }
  return true;
}

// validate image mime type
function validateMimeType(mime) {
  var extensions = [
    "image/jpeg",
    "image/png",
    "image/gif",
    // BMP?
  ];
  return $.inArray(mime, extensions);
}

function pad(number, length) {
  return sprintf("%'0" + length + "d", number);
}

function removeSuccessMsg() {
  var $status = $(".success");

  $status.fadeOut("slow", function () {
    $status.empty();
  });
}

function hideHint(h) {
  h.hide("slow").addClass("hidden");
}

function showHint(h) {
  h.show("slow").removeClass("hidden");
}

function getUsabilityHint() {
  var pathname = window.location.pathname;
  $.getJSON(
    baseUrl + "api/get-usability-hint",
    { format: "json", userPath: pathname },
    function (json) {
      var $hint_div = $(".usability_hint");
      var current_hint = $hint_div.html();
      if (json === "") {
        // there are no more hints to display to the user
        hideHint($hint_div);
      } else if (current_hint !== json) {
        // we only change the message if it is new
        if ($hint_div.is(":visible")) {
          hideHint($hint_div);
        }
        $hint_div.html(json);
        showHint($hint_div);
      } else {
        // hint is the same before we hid it so we just need to show it
        if ($hint_div.is(":hidden")) {
          showHint($hint_div);
        }
      }
    },
  );
}

// TODO: build this out so we can use it as a fallback in fail cases
function buildErrorDialog(message) {
  var el = $("<div id='error_dialog'></div>");
  el.text(message);
  $(document.body).append(el);
  $("#error_dialog").dialog({
    title: $.i18n._("Something went wrong!"),
    resizable: false,
    modal: true,
    width: "auto",
    height: "auto",
  });
}

/**
 * Add title attributes (whose values are their inner text) to all elements in the calling parent matching selector
 *
 * @param selector jQuery selector to search descendants
 * @returns {jQuery}
 */
jQuery.fn.addTitles = function (selector) {
  this.each(function () {
    // Put this in a mouseenter event handler so it's dynamic
    // (newly created elements will have the title applied on hover)
    $(this).on("mouseenter", selector, function () {
      $(this).attr("title", $(this).text());
    });
  });

  return this; // jQuery chaining
};

// XXX: Old code to pan selector text; keeping this around in case we want to use it later - Duncan
jQuery.fn.scrollText = function (selector) {
  this.each(function () {
    $(this).on("mouseenter", selector, function () {
      var sw = $(this)[0].scrollWidth - parseFloat($(this).css("textIndent")),
        iw = $(this).innerWidth();
      if (sw > iw) {
        $(this)
          .stop()
          .animate(
            {
              textIndent: "-" + (sw + 1 - iw) + "px",
            },
            sw * 8,
          );
      }
    });
    $(this).on("mouseleave", selector, function () {
      $(this).stop().animate(
        {
          textIndent: "0",
        },
        500,
      );
    });
  });

  return this;
};
