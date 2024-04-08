function generatePartitions(partitions) {
  var rowTemplate =
    '<tr class="partition-info">' +
    '<td><span class="strong">' +
    $.i18n._("Disk") +
    " #%s</span>" +
    '<ul id="watched-dir-list-%s">' +
    "</ul>" +
    "</td>" +
    "<td>%sGB of %sGB</td>" +
    '<td colspan="3">' +
    '<div class="big">' +
    '<div class="diskspace" style="width:%s%%;">' +
    "</div>" +
    "</div>" +
    "<div>%s%% " +
    $.i18n._("in use") +
    "</div>" +
    "</td>" +
    "</tr>";

  $(".partition-info").remove();
  var lastElement = $("#partitions");
  for (var i = 0; i < partitions.length; i++) {
    var spaceUsed = partitions[i].totalSpace - partitions[i].totalFreeSpace;
    var totalSpace = partitions[i].totalSpace;
    var percUsed = sprintf("%01.1f", (spaceUsed / totalSpace) * 100);

    var spaceUsedGb = sprintf("%01.1f", spaceUsed / Math.pow(2, 30));
    var totalSpaceGb = sprintf("%01.1f", totalSpace / Math.pow(2, 30));

    var row = sprintf(
      rowTemplate,
      i + 1,
      i,
      spaceUsedGb,
      totalSpaceGb,
      percUsed,
      percUsed,
    );
    var tr = $(row);
    lastElement.after(tr);

    if (partitions[i].dirs) {
      var watched_dirs_ul = $("#watched-dir-list-" + i);
      for (var j = 0; j < partitions[i].dirs.length; j++) {
        watched_dirs_ul.append("<li>" + partitions[i].dirs[j] + "</li>");
      }
    }
    lastElement = tr;
  }
}

function success(data, textStatus, jqXHR) {
  var services = data.status.services;

  for (var key in services) {
    var s = services[key];
    if (s) {
      var children = $("#" + s.name).children();
      $(children[0]).text(s.name);

      var status_class = "not-available-icon";
      if (s.status == 0) {
        status_class = "checked-icon";
      } else if (s.status == 1) {
        status_class = "warning-icon";
      }

      $($(children[1]).children()[0]).attr("class", status_class);
      $(children[2]).text(
        sprintf(
          "%(days)sd %(hours)sh %(minutes)sm %(seconds)ss",
          convertSecondsToDaysHoursMinutesSeconds(s.uptime_seconds),
        ),
      );
      $(children[3]).text(s.cpu_perc);
      $(children[4]).text(
        sprintf("%01.1fMB (%s)", parseInt(s.memory_kb) / 1000, s.memory_perc),
      );
    }
  }
  if (data.status.partitions) {
    generatePartitions(data.status.partitions);
  }
  setTimeout(function () {
    updateStatus(false);
  }, 5000);
}

function updateStatus(getDiskInfo) {
  $.getJSON(
    baseUrl + "api/status/format/json/diskinfo/" + getDiskInfo,
    null,
    success,
  );
}

$(document).ready(function () {
  updateStatus(true);
});
