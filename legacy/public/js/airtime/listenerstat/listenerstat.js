$(document).ready(function () {
  listenerstat_content = $("#listenerstat_content");
  ((dateStartId = "#his_date_start"),
    (timeStartId = "#his_time_start"),
    (dateEndId = "#his_date_end"),
    (timeEndId = "#his_time_end"));

  // set width dynamically
  var width = $("#listenerstat_content").width();
  width = width * 0.91;
  $("#listenerstat_content").find("#flot_placeholder").width(width);
  $("#listenerstat_content").find("#legend").width(width);

  getDataAndPlot();

  listenerstat_content.find("#his_submit").click(function () {
    var oRange = AIRTIME.utilities.fnGetScheduleRange(
      dateStartId,
      timeStartId,
      dateEndId,
      timeEndId,
    );
    var start = oRange.start;
    var end = oRange.end;
    getDataAndPlot(start, end);
  });
});

/**
 * Toggle a spinner overlay so the user knows the page is processing
 */
function toggleOverlay() {
  $("#flot_placeholder").toggleClass("processing");
}

function getDataAndPlot(startTimestamp, endTimestamp) {
  // Turn on the processing overlay
  toggleOverlay();

  // get data
  $.get(
    baseUrl + "Listenerstat/get-data",
    { start: startTimestamp, end: endTimestamp },
    function (data) {
      out = new Object();
      $.each(data, function (mpName, v) {
        plotData = new Object();
        plotData.data = new Array();
        $.each(v, function (i, ele) {
          plotData.label = mpName;
          var d = new Date(0);
          d.setUTCSeconds(ele.timestamp);
          plotData.data.push([d, ele.listener_count]);
        });
        out[mpName] = plotData;
      });
      plot(out);
      // Turn off the processing overlay
      toggleOverlay();
    },
  );
}

function plot(datasets) {
  var plot;
  data = null;
  function plotByChoice(doAll) {
    // largest date object that you can set
    firstTimestamp = new Date(8640000000000000);
    // smallest
    lastTimestamp = new Date(0);

    data = [];
    if (doAll != null) {
      $.each(datasets, function (key, val) {
        if (firstTimestamp.getTime() > val.data[0][0].getTime()) {
          firstTimestamp = val.data[0][0];
        }
        if (
          lastTimestamp.getTime() < val.data[val.data.length - 1][0].getTime()
        ) {
          lastTimestamp = val.data[val.data.length - 1][0];
        }
        data.push(val);
      });
    } else {
      $("#legend .legendCB").each(function () {
        if (this.checked) {
          data.push(datasets[this.id]);
          if (
            firstTimestamp.getTime() > datasets[this.id].data[0][0].getTime()
          ) {
            firstTimestamp = datasets[this.id].data[0][0];
          }
          if (
            lastTimestamp.getTime() <
            datasets[this.id].data[
              datasets[this.id].data.length - 1
            ][0].getTime()
          ) {
            lastTimestamp =
              datasets[this.id].data[datasets[this.id].data.length - 1][0];
          }
        } else {
          data.push({ label: this.id, data: [] });
        }
      });
    }

    numOfTicks = 10;
    tickSize =
      (lastTimestamp.getTime() - firstTimestamp.getTime()) / 1000 / numOfTicks;

    plot = $.plot($("#flot_placeholder"), data, {
      yaxis: {
        min: 0,
        tickDecimals: 0,
        color: "#d6d6d6",
        tickColor: "#d6d6d6",
      },
      xaxis: {
        mode: "time",
        timeformat: "%y/%m/%0d %H:%M",
        tickSize: [tickSize, "second"],
        color: "#d6d6d6",
        tickColor: "#d6d6d6",
      },
      grid: {
        hoverable: true,
        backgroundColor: { colors: ["#333", "#555"] },
      },
      series: {
        lines: {
          show: true,
          fill: 0.3,
        },
        points: { show: true },
      },
      legend: {
        container: $("#legend"),
        noColumns: 5,
        color: "#c0c0c0",
        labelFormatter: function (label, series) {
          var cb =
            '<input style="float:left;" class="legendCB" type="checkbox" ';
          if (series.data.length > 0) {
            cb += 'checked="true" ';
          }
          cb += 'id="' + label + '" /> ';
          cb += label;
          return cb;
        },
      },
    });

    function showTooltip(x, y, contents) {
      $('<div id="tooltip">' + contents + "</div>")
        .css({
          position: "absolute",
          display: "none",
          top: y + 5,
          left: x + 5,
          border: "1px solid #fdd",
          padding: "2px",
          "background-color": "#fee",
          opacity: 0.8,
        })
        .appendTo("body")
        .fadeIn(200);
    }

    var previousPoint = null;
    $("#flot_placeholder").bind("plothover", function (event, pos, item) {
      if (item) {
        if (previousPoint != item.dataIndex) {
          previousPoint = item.dataIndex;

          $("#tooltip").remove();
          var y = item.datapoint[1].toFixed(2);

          showTooltip(
            item.pageX,
            item.pageY,
            sprintf(
              $.i18n._("Listener Count on %s: %s"),
              item.series.label,
              Math.floor(y),
            ),
          );
        }
      } else {
        $("#tooltip").remove();
        previousPoint = null;
      }
    });

    $("#legend")
      .find("input")
      .click(function () {
        setTimeout(plotByChoice, 100);
      });
  }

  plotByChoice(true);
  oBaseDatePickerSettings = {
    dateFormat: "yy-mm-dd",
    //i18n_months, i18n_days_short are in common.js
    monthNames: i18n_months,
    dayNamesMin: i18n_days_short,
    onSelect: function (sDate, oDatePicker) {
      $(this).datepicker("setDate", sDate);
    },
  };

  oBaseTimePickerSettings = {
    showPeriodLabels: false,
    showCloseButton: true,
    closeButtonText: $.i18n._("Done"),
    showLeadingZero: false,
    defaultTime: "0:00",
    hourText: $.i18n._("Hour"),
    minuteText: $.i18n._("Minute"),
  };

  listenerstat_content.find(dateStartId).datepicker(oBaseDatePickerSettings);
  listenerstat_content.find(timeStartId).timepicker(oBaseTimePickerSettings);
  listenerstat_content.find(dateEndId).datepicker(oBaseDatePickerSettings);
  listenerstat_content.find(timeEndId).timepicker(oBaseTimePickerSettings);
}
