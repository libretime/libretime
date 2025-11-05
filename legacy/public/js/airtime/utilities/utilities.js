var AIRTIME = (function (AIRTIME) {
  var mod;

  if (AIRTIME.utilities === undefined) {
    AIRTIME.utilities = {};
  }
  mod = AIRTIME.utilities;

  mod.findViewportDimensions = function () {
    var viewportwidth, viewportheight;

    // the more standards compliant browsers (mozilla/netscape/opera/IE7) use
    // window.innerWidth and window.innerHeight
    if (typeof window.innerWidth != "undefined") {
      ((viewportwidth = window.innerWidth),
        (viewportheight = window.innerHeight));
    }
    // IE6 in standards compliant mode (i.e. with a valid doctype as the first
    // line in the document)
    else if (
      typeof document.documentElement != "undefined" &&
      typeof document.documentElement.clientWidth != "undefined" &&
      document.documentElement.clientWidth != 0
    ) {
      viewportwidth = document.documentElement.clientWidth;
      viewportheight = document.documentElement.clientHeight;
    }
    // older versions of IE
    else {
      viewportwidth = document.getElementsByTagName("body")[0].clientWidth;
      viewportheight = document.getElementsByTagName("body")[0].clientHeight;
    }

    return {
      width: viewportwidth,
      height: viewportheight,
    };
  };

  /*
   * Returns an object containing a unix timestamp in seconds for the start/end range
   *
   * @return Object {"start", "end", "range"}
   */
  mod.fnGetScheduleRange = function (
    dateStartId,
    timeStartId,
    dateEndId,
    timeEndId,
  ) {
    var start, end, time;

    start = $(dateStartId).val();
    start = start === "" ? null : start;

    time = $(timeStartId).val();
    time = time === "" ? "00:00" : time;

    if (start) {
      start = start + " " + time;
    }

    end = $(dateEndId).val();
    end = end === "" ? null : end;

    time = $(timeEndId).val();
    time = time === "" ? "00:00" : time;

    if (end) {
      end = end + " " + time;
    }

    return {
      start: start,
      end: end,
    };
  };

  return AIRTIME;
})(AIRTIME || {});
