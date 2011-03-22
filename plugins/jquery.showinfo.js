(function($){
 $.fn.airtimeShowSchedule = function(options) {

    var defaults = {
        updatePeriod: 5, //seconds
    };
    var options = $.extend(defaults, options);

    return this.each(function() {
        var obj = $(this);

        obj.append("<h3>On air today</h3>");
        obj.append(
            "<table width='100%' border='0' cellspacing='0' cellpadding='0' class='widget widget no-playing-list small'>"+
            "<tbody><tr>" +
            "<td class='time'>13:15 - 13:30</td>" +
            "<td><a href='#'>Program name</a> <a href='#' class='listen'>Listen</a></td>" +
            "</tr>"+
            "<tr>"+
            "<td class='time'>13:15 - 13:30</td>"+
            "<td><a href='#'>Lorem ipsum dolor</a></td>"+
            "</tr>"+
            "</tbody></table>");
    });
 };
})(jQuery);


(function($){
 $.fn.airtimeLiveInfo = function(options) {

    var defaults = {
        updatePeriod: 5, //seconds
        sourceDomain: "http://localhost/", //where to get show status from
        audioStreamSource: "" //where to get audio stream from
    };
    var options = $.extend(defaults, options);

    return this.each(function() {
        var obj = $(this);
        var sd;
        getServerData();

        function updateWidget(){
            var currentShow = sd.getCurrentShowName();
            var timeRemaining = sd.getCurrentShowTimeRemaining();
            var timeElapsed = sd.getCurrentShowTimeElapsed();
            var showStatus = sd.getCurrentShowStatus();

            var nextShow = sd.getNextShowName();
            var nextShowRange = sd.getNextShowRange();

            obj.empty();
            obj.append("<a id='listenWadrLive'><span>Listen WADR Live</span></a>");
            obj.append("<h4>"+showStatus+" &gt;&gt;</h4>");
            obj.append("<ul class='widget no-playing-bar'>" +
                "<li class='current'>"+currentShow+ "<span id='time-elapsed' class='time-elapsed'>"+timeElapsed+"</span>" +
                "<span id='time-remaining' class='time-remaining'>"+timeRemaining+"</span></li>" +
                "<li class='next'>"+nextShow+"<span>"+nextShowRange+"</span></li>" +
                "</ul>");

            //refresh the UI
            setTimeout(updateWidget, 1000);
        }

        function processData(data){
            sd = new ScheduleData(data);
            updateWidget();
        }

        function getServerData(){
            $.ajax({ url: options.sourceDomain + "api/live-info/", dataType:"jsonp", success:function(data){
                        processData(data);
                  }, error:function(jqXHR, textStatus, errorThrown){}});
            setTimeout(getServerData, defaults.updatePeriod*1000);
        }
    });
 };
})(jQuery);

/* The rest of this file is the ScheduleData class */
function ScheduleData(data){
    this.data = data;
    this.estimatedSchedulePosixTime;

    this.schedulePosixTime = this.convertDateToPosixTime(data.schedulerTime);
    this.schedulePosixTime += parseInt(data.timezoneOffset)*1000;
    var date = new Date();
    this.localRemoteTimeOffset = date.getTime() - this.schedulePosixTime;
}


ScheduleData.prototype.secondsTimer = function(){
    var date = new Date();
    this.estimatedSchedulePosixTime = date.getTime() - this.localRemoteTimeOffset;
}

ScheduleData.prototype.getCurrentShowName = function() {
    var currentShow = this.data.currentShow;
    if (currentShow.length > 0){
        return "Current: " + currentShow[0].name;
    } else {
        return "";
    }
};

ScheduleData.prototype.getCurrentShowStatus = function() {
    var currentShow = this.data.currentShow;
    if (currentShow.length > 0){
        return "On Air Now";
    } else {
        return "Offline";
    }
};

ScheduleData.prototype.getNextShowName = function() {
    var nextShow = this.data.nextShow;
    if (nextShow.length > 0){
        return "Next: " + nextShow[0].name;
    } else {
        return "";
    }
};

ScheduleData.prototype.getNextShowRange = function() {
    var nextShow = this.data.nextShow;
    if (nextShow.length > 0){
        return this.getTime(nextShow[0].start_timestamp) + " - " + this.getTime(nextShow[0].end_timestamp);
    } else {
        return "";
    }
};

ScheduleData.prototype.getCurrentShowTimeElapsed = function() {
    this.secondsTimer();
    var currentShow = this.data.currentShow;
    if (currentShow.length > 0){
        var showStart = this.convertDateToPosixTime(currentShow[0].start_timestamp);
        return this.convertToHHMMSS(this.estimatedSchedulePosixTime - showStart);
    } else {
        return "";
    }
};

ScheduleData.prototype.getCurrentShowTimeRemaining = function() {
    this.secondsTimer();
    var currentShow = this.data.currentShow;
    if (currentShow.length > 0){
        var showEnd = this.convertDateToPosixTime(currentShow[0].end_timestamp);
        return this.convertToHHMMSS(showEnd - this.estimatedSchedulePosixTime);
    } else {
        return "";
    }
};

ScheduleData.prototype.getTime = function(timestamp) {
    return timestamp.split(" ")[1];
};


/* Takes an input parameter of milliseconds and converts these into
 * the format HH:MM:SS */
ScheduleData.prototype.convertToHHMMSS = function(timeInMS){
	var time = parseInt(timeInMS);

	var hours = parseInt(time / 3600000);
	time -= 3600000*hours;

	var minutes = parseInt(time / 60000);
	time -= 60000*minutes;

	var seconds = parseInt(time / 1000);

	hours = hours.toString();
	minutes = minutes.toString();
	seconds = seconds.toString();

	if (hours.length == 1)
		hours = "0" + hours;
	if (minutes.length == 1)
		minutes = "0" + minutes;
	if (seconds.length == 1)
		seconds = "0" + seconds;
	if (hours == "00")
		return minutes + ":" + seconds;
	else
		return hours + ":" + minutes + ":" + seconds;
}

/* Takes in a string of format similar to 2011-02-07 02:59:57,
 * and converts this to epoch/posix time. */
ScheduleData.prototype.convertDateToPosixTime = function(s){
    var datetime = s.split(" ");

    var date = datetime[0].split("-");
    var time = datetime[1].split(":");

	var year = date[0];
	var month = date[1];
	var day = date[2];
	var hour = time[0];
	var minute = time[1];
    var sec = 0;
    var msec = 0;

    if (time[2].indexOf(".") != -1){
        var temp = time[2].split(".");
        sec = temp[0];
        msec = temp[1];
    } else
        sec = time[2];

	return Date.UTC(year, month, day, hour, minute, sec, msec);
}
