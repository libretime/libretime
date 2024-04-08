/**
 *
 *   Full Calendar callback methods.
 *
 */

function scheduleRefetchEvents(json) {
  if (json.show_error == true) {
    alert($.i18n._("The show instance doesn't exist anymore!"));
  }
  if (json.show_id) {
    var dialog_id = parseInt($("#add_show_id").val(), 10);

    //if you've deleted the show you are currently editing, close the add show dialog.
    if (dialog_id === json.show_id) {
      $("#add-show-close").click();
    }
  }
  $("#schedule_calendar").fullCalendar("refetchEvents");
}

function makeTimeStamp(date) {
  var sy, sm, sd, h, m, s, timestamp;
  sy = date.getFullYear();
  sm = date.getMonth() + 1;
  sd = date.getDate();
  h = date.getHours();
  m = date.getMinutes();
  s = date.getSeconds();

  timestamp =
    sy +
    "-" +
    pad(sm, 2) +
    "-" +
    pad(sd, 2) +
    " " +
    pad(h, 2) +
    ":" +
    pad(m, 2) +
    ":" +
    pad(s, 2);
  return timestamp;
}

function dayClick(date, allDay, jsEvent, view) {
  // The show from will be preloaded if the user is admin or program manager.
  // Hence, if the user if DJ then it won't open anything.
  if (userType == "S" || userType == "A" || userType == "P") {
    var now, today, selected, chosenDate, chosenTime;

    now = adjustDateToServerDate(new Date(), serverTimezoneOffset);

    if (view.name === "month") {
      today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      selected = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    } else {
      today = new Date(
        now.getFullYear(),
        now.getMonth(),
        now.getDate(),
        now.getHours(),
        now.getMinutes(),
      );
      selected = new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate(),
        date.getHours(),
        date.getMinutes(),
      );
    }

    if (selected >= today) {
      var addShow = $(".add-button");

      //remove the +show button if it exists.
      if (addShow.length == 1) {
        var span = $(addShow).parent();

        $(span).next().remove();
        $(span).remove();
      }

      // get current duration value on the form
      var duration_string = $.trim($("#add_show_duration").val());
      var duration_info = duration_string.split(" ");
      var duration_h = 0;
      var duration_m = 0;
      if (duration_info[0] != null) {
        duration_h = parseInt(duration_info[0], 10);
      }
      if (duration_info[1] != null) {
        duration_m = parseInt(duration_info[1], 10);
      }
      // duration in milisec
      var duration = duration_h * 60 * 60 * 1000 + duration_m * 60 * 1000;

      var startTime_string;
      var startTime = 0;
      // get start time value on the form
      if (view.name === "month") {
        startTime_string = $("#add_show_start_time").val();
        var startTime_info = startTime_string.split(":");
        if (startTime_info.length == 2) {
          var start_time_temp =
            parseInt(startTime_info[0], 10) * 60 * 60 * 1000 +
            parseInt(startTime_info[1], 10) * 60 * 1000;
          if (!isNaN(start_time_temp)) {
            startTime = start_time_temp;
          }
        }
      } else {
        // if in day or week view, selected has all the time info as well
        // so we don't ahve to calculate it explicitly
        startTime_string =
          pad(selected.getHours(), 2) + ":" + pad(selected.getMinutes(), 2);
        startTime = 0;
      }

      // calculate endDateTime
      var endDateTime = new Date(selected.getTime() + startTime + duration);

      chosenDate =
        selected.getFullYear() +
        "-" +
        pad(selected.getMonth() + 1, 2) +
        "-" +
        pad(selected.getDate(), 2);
      var endDateFormat =
        endDateTime.getFullYear() +
        "-" +
        pad(endDateTime.getMonth() + 1, 2) +
        "-" +
        pad(endDateTime.getDate(), 2);

      //TODO: This should all be refactored into a proper initialize() function for the show form.
      $("#add_show_start_now-future").attr("checked", "checked");
      $("#add_show_start_now-now").removeProp("disabled");
      setupStartTimeWidgets(); //add-show.js
      $("#add_show_start_date").val(chosenDate);
      $("#add_show_end_date_no_repeat").val(endDateFormat);
      $("#add_show_end_date").val(endDateFormat);
      if (view.name !== "month") {
        var endTimeString =
          pad(endDateTime.getHours(), 2) +
          ":" +
          pad(endDateTime.getMinutes(), 2);
        $("#add_show_start_time").val(startTime_string);
        $("#add_show_end_time").val(endTimeString);
      }
      calculateShowColor();
      $("#schedule-show-when").show();

      openAddShowForm();
      makeAddShowButton();
      toggleAddShowButton();
    }
  }
}

function viewDisplay(view) {
  view_name = view.name;

  if (view.name === "agendaDay" || view.name === "agendaWeek") {
    var calendarEl = this;

    var select = $('<select class="schedule_change_slots input_select"/>')
      .append('<option value="1">' + $.i18n._("1m") + "</option>")
      .append('<option value="5">' + $.i18n._("5m") + "</option>")
      .append('<option value="10">' + $.i18n._("10m") + "</option>")
      .append('<option value="15">' + $.i18n._("15m") + "</option>")
      .append('<option value="30">' + $.i18n._("30m") + "</option>")
      .append('<option value="60">' + $.i18n._("60m") + "</option>")
      .change(function () {
        var slotMin = $(this).val();
        var opt = view.calendar.options;
        var date = $(calendarEl).fullCalendar("getDate");

        opt.slotMinutes = parseInt(slotMin);
        opt.events = getFullCalendarEvents;
        opt.defaultView = view.name;

        //re-initialize calendar with new slotmin options
        $(calendarEl)
          .fullCalendar("destroy")
          .fullCalendar(opt)
          .fullCalendar("gotoDate", date);

        //save slotMin value to db
        var url = baseUrl + "Schedule/set-time-interval/format/json";
        $.post(url, { timeInterval: slotMin });
      });

    var topLeft = $(view.element).find("table.fc-agenda-days > thead th:first");

    //select.width(topLeft.width())
    //    .height(topLeft.height());

    topLeft.empty().append(select);

    var slotMin = view.calendar.options.slotMinutes;
    $('.schedule_change_slots option[value="' + slotMin + '"]').attr(
      "selected",
      "selected",
    );
  }

  if (
    $("#add-show-form").length == 1 &&
    $("#add-show-form").css("display") == "none" &&
    $(".fc-header-left > span").length == 5
  ) {
    //userType is defined in bootstrap.php, and is derived from the currently logged in user.
    if (userType == "S" || userType == "A" || userType == "P") {
      makeAddShowButton();
    }
  }

  //save view name to db if it was changed
  if (calendarPref.timeScale !== view.name) {
    var url = baseUrl + "Schedule/set-time-scale/format/json";
    $.post(url, { timeScale: view.name });
    calendarPref.timeScale = view.name;
  }
}

function eventRender(event, element, view) {
  $(element).addClass("fc-show-instance-" + event.id);
  $(element).attr("data-show-id", event.showId);
  $(element).attr("data-show-linked", event.linked);
  $(element).data("event", event);

  //only put progress bar on shows that aren't being recorded.
  if (
    (view.name === "agendaDay" || view.name === "agendaWeek") &&
    event.record === 0
  ) {
    var div = $("<div/>");
    div
      .height("5px")
      .width("95%")
      .css("margin-top", "1px")
      .css("margin-left", "auto")
      .css("margin-right", "auto")
      .progressbar({
        value: event.percent,
      });

    $(element).find(".fc-event-content").append(div);
  }

  if (event.record === 0 && event.rebroadcast === 0) {
    if (view.name === "agendaDay" || view.name === "agendaWeek") {
      if (event.show_empty === 1) {
        if (event.linked) {
          $(element)
            .find(".fc-event-time")
            .before(
              '<span class="small-icon linked"></span><span class="small-icon show-empty"></span>',
            );
          // in theory a linked show shouldn't have an automatic playlist so adding this here
        } else if (event.show_has_auto_playlist === true) {
          $(element)
            .find(".fc-event-time")
            .before('<span class="small-icon autoplaylist"></span>');
        } else {
          $(element)
            .find(".fc-event-time")
            .before('<span class="small-icon show-empty"></span>');
        }
      } else if (event.show_partial_filled === true) {
        if (event.linked) {
          $(element)
            .find(".fc-event-time")
            .before(
              '<span class="small-icon linked"></span><span class="small-icon show-partial-filled"></span>',
            );
        } else if (event.show_has_auto_playlist === true) {
          $(element)
            .find(".fc-event-time")
            .before('<span class="small-icon autoplaylist"></span>');
        } else {
          $(element)
            .find(".fc-event-time")
            .before('<span class="small-icon show-partial-filled"></span>');
        }
      } else if (event.percent > 100) {
        if (event.linked) {
          $(element)
            .find(".fc-event-time")
            .before(
              '<span class="small-icon linked"></span><span class="small-icon show-overbooked"></span>',
            );
        } else if (event.show_has_auto_playlist === true) {
          $(element)
            .find(".fc-event-time")
            .before('<span class="small-icon autoplaylist"></span>');
        } else {
          $(element)
            .find(".fc-event-time")
            .before('<span class="small-icon show-overbooked"></span>');
        }
      } else {
        if (event.linked) {
          $(element)
            .find(".fc-event-time")
            .before('<span class="small-icon linked"></span>');
        } else if (event.show_has_auto_playlist === true) {
          $(element)
            .find(".fc-event-time")
            .before('<span class="small-icon autoplaylist"></span>');
        }
      }
    } else if (view.name === "month") {
      if (event.show_empty === 1) {
        if (event.linked) {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span class="small-icon linked"></span><span title="' +
                $.i18n._("Show is empty") +
                '" class="small-icon show-empty"></span>',
            );
        } else if (event.show_has_auto_playlist === true) {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span title="' +
                $.i18n._("Show has an automatic playlist") +
                '"class="small-icon autoplaylist"></span>',
            );
        } else {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span title="' +
                $.i18n._("Show is empty") +
                '" class="small-icon show-empty"></span>',
            );
        }
      } else if (event.show_partial_filled === true) {
        if (event.linked) {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span class="small-icon linked"></span><span title="' +
                $.i18n._("Show is partially filled") +
                '" class="small-icon show-partial-filled"></span>',
            );
        } else if (event.show_has_auto_playlist === true) {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span title="' +
                $.i18n._("Show has an automatic playlist") +
                '"class="small-icon autoplaylist"></span>',
            );
        } else {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span title="' +
                $.i18n._("Show is partially filled") +
                '" class="small-icon show-partial-filled"></span>',
            );
        }
      } else if (event.percent > 100) {
        if (event.linked) {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span class="small-icon linked"></span><span title="' +
                $.i18n._(
                  "Shows longer than their scheduled time will be cut off by a following show.",
                ) +
                '" class="small-icon show-overbooked"></span>',
            );
        } else if (event.show_has_auto_playlist === true) {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span title="' +
                $.i18n._("Show has an automatic playlist") +
                '"class="small-icon autoplaylist"></span>',
            );
        } else {
          $(element)
            .find(".fc-event-title")
            .after(
              '<span title="' +
                $.i18n._(
                  "Shows longer than their scheduled time will be cut off by a following show.",
                ) +
                '" class="small-icon show-overbooked"></span>',
            );
        }
      } else {
        if (event.linked) {
          $(element)
            .find(".fc-event-title")
            .after('<span class="small-icon linked"></span>');
        } else if (event.show_has_auto_playlist === true) {
          $(element)
            .find(".fc-event-title")
            .after('<span class="small-icon autoplaylist"></span>');
        }
      }
    }
  }

  //rebroadcast icon
  if (event.rebroadcast === 1) {
    if (view.name === "agendaDay" || view.name === "agendaWeek") {
      $(element)
        .find(".fc-event-time")
        .before('<span class="small-icon rebroadcast"></span>');
    } else if (view.name === "month") {
      $(element)
        .find(".fc-event-title")
        .after('<span class="small-icon rebroadcast"></span>');
    }
  }

  //now playing icon.
  var span = '<span class="small-icon now-playing"></span>';

  if (event.nowPlaying === true) {
    if (view_name === "agendaDay" || view_name === "agendaWeek") {
      $(element).find(".fc-event-time").before(span);
    } else if (view_name === "month") {
      $(element).find(".fc-event-title").after(span);
    }
  }
}

function eventAfterRender(event, element, view) {
  $(element)
    .find(".small-icon")
    .live("mouseover", function () {
      addQtipsToIcons($(this), event.id);
    });
}

function eventDrop(
  event,
  dayDelta,
  minuteDelta,
  allDay,
  revertFunc,
  jsEvent,
  ui,
  view,
) {
  var url = baseUrl + "Schedule/move-show/format/json";

  $.post(
    url,
    { day: dayDelta, min: minuteDelta, showInstanceId: event.id },
    function (json) {
      if (json.show_error == true) {
        alertShowErrorAndReload();
      }
      if (json.error) {
        alert(json.error);
        revertFunc();
      }

      //Workaround for cases where FullCalendar handles events over DST
      //time changes in a different way than Airtime does.
      //(Airtime preserves show duration, FullCalendar doesn't.)
      scheduleRefetchEvents(json);
    },
  );
}

function eventResize(
  event,
  dayDelta,
  minuteDelta,
  revertFunc,
  jsEvent,
  ui,
  view,
) {
  var url = baseUrl + "Schedule/resize-show/format/json";

  $.post(
    url,
    {
      day: dayDelta,
      min: minuteDelta,
      showId: event.showId,
      instanceId: event.id,
    },
    function (json) {
      if (json.show_error == true) {
        alertShowErrorAndReload();
      }
      if (json.error) {
        alert(json.error);
        revertFunc();
      }

      scheduleRefetchEvents(json);
    },
  );
}

function windowResize() {
  // 200 px for top dashboard and 50 for padding on main content
  // this calculation was copied from schedule.js line 326
  var mainHeight = $(window).height() - 200 - 24;
  $("#schedule_calendar").fullCalendar("option", "contentHeight", mainHeight);
}

function preloadEventFeed() {
  createFullCalendar({ calendarInit: calendarPref });
}

var initialLoad = true;
function getFullCalendarEvents(start, end, callback) {
  if (initialLoad) {
    initialLoad = false;
    callback(calendarEvents);
  } else {
    var url, start_date, end_date;

    start_date = makeTimeStamp(start);
    end_date = makeTimeStamp(end);
    url = baseUrl + "Schedule/event-feed";

    var d = new Date();
    $.post(
      url,
      { format: "json", start: start_date, end: end_date, cachep: d.getTime() },
      function (json) {
        callback(json.events);
        getUsabilityHint();
      },
    );
  }

  $(".fc-button").addClass("btn").addClass("btn-small");
  //$("span.fc-button > :button").addClass("btn btn-small");
}

/** This function adds and removes the current
 *  show icon
 */
function getCurrentShow() {
  var url = baseUrl + "Schedule/get-current-show/format/json";

  function addNowPlaying(json) {
    var $el,
      span = '<span class="small-icon now-playing"></span>';

    $(".now-playing").remove();

    if (json.current_show === true) {
      $el = $(".fc-show-instance-" + json.si_id);

      if (view_name === "agendaDay" || view_name === "agendaWeek") {
        $el.find(".fc-event-time").before(span);
      } else if (view_name === "month") {
        $el.find(".fc-event-title").after(span);
      }
    }

    setTimeout(getCurrentShow, 5000);
  }

  $.post(url, { format: "json" }, addNowPlaying);
}

function addQtipsToIcons(ele, id) {
  if ($(ele).hasClass("progress")) {
    $(ele).qtip({
      content: {
        text: $.i18n._("Uploading in progress..."),
      },
      position: {
        adjust: {
          resize: true,
          method: "flip flip",
        },
        at: "right center",
        my: "left top",
        viewport: $(window),
      },
      style: {
        classes: "ui-tooltip-dark file-md-long",
      },
      show: {
        ready: true, // Needed to make it show on first mouseover event
      },
    });
  } else if ($(ele).hasClass("show-empty")) {
    $(ele).qtip({
      content: {
        text: $.i18n._("This show has no scheduled content."),
      },
      position: {
        adjust: {
          resize: true,
          method: "flip flip",
        },
        at: "right center",
        my: "left top",
        viewport: $(window),
      },
      style: {
        classes: "ui-tooltip-dark file-md-long",
      },
      show: {
        ready: true, // Needed to make it show on first mouseover event
      },
    });
  } else if ($(ele).hasClass("show-partial-filled")) {
    $(ele).qtip({
      content: {
        text: $.i18n._("This show is not completely filled with content."),
      },
      position: {
        adjust: {
          resize: true,
          method: "flip flip",
        },
        at: "right center",
        my: "left top",
        viewport: $(window),
      },
      style: {
        classes: "ui-tooltip-dark file-md-long",
      },
      show: {
        ready: true, // Needed to make it show on first mouseover event
      },
    });
  } else if ($(ele).hasClass("show-overbooked")) {
    $(ele).qtip({
      content: {
        text: $.i18n._(
          "Shows longer than their scheduled time will be cut off by a following show.",
        ),
      },
      position: {
        adjust: {
          resize: true,
          method: "flip flip",
        },
        at: "right center",
        my: "left top",
        viewport: $(window),
      },
      style: {
        classes: "ui-tooltip-dark file-md-long",
      },
      show: {
        ready: true, // Needed to make it show on first mouseover event
      },
    });
  }
}
//Alert the error and reload the page
//this function is used to resolve concurrency issue
function alertShowErrorAndReload() {
  alert($.i18n._("The show instance doesn't exist anymore!"));
  window.location.reload();
}

$(document).ready(function () {
  preloadEventFeed();
  getCurrentShow();
});

var view_name;
