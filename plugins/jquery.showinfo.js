(function($){
 $.fn.airtimeShowSchedule = function(options) {

    var defaults = {
        updatePeriod: 20, //seconds
        sourceDomain: "http://localhost/", //where to get show status from
    };
    var options = $.extend(defaults, options);

    return this.each(function() {
        var obj = $(this);
        var sd;

        getServerData();

        function updateWidget(){
            var currentShow = sd.getCurrentShow();
            var nextShows = sd.getNextShows();

            var currentShowName = "";
            var nextShowName = ""

            if (currentShow.length > 0){
                currentShowName = currentShow[0].getName();
            }
            
            if (nextShows.length > 0){
                nextShowName = nextShows[0].getName();
            }

            tableString = "";
            tableString += "<h3>On air today</h3>";
            tableString += "<table width='100%' border='0' cellspacing='0' cellpadding='0' class='widget widget no-playing-list small'>"+
                "<tbody>";
            
            var shows=currentShow.concat(nextShows);
            
            obj.empty();
            for (var i=0; i<shows.length; i++){
                tableString +=
                "<tr>" +
                "<td class='time'>"+shows[i].getRange()+"</td>" +
                "<td><a href='#'>"+shows[i].getName()+"</a> <a href='#' class='listen'>Listen</a></td>" +
                "</tr>";
            }

            tableString += "</tbody></table>";
            
            obj.append(tableString);
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
            var currentShow = sd.getCurrentShow();
            var nextShows = sd.getNextShows();

            var showStatus = "Offline";
            var currentShowName = "";
            var timeElapsed = "";
            var timeRemaining = "";

            var nextShowName = "";
            var nextShowRange = "";

            if (currentShow.length > 0){
                showStatus = "On Air Now";
                currentShowName = currentShow[0].getName();

                timeElapsed = sd.getShowTimeElapsed(currentShow[0]);
                timeRemaining = sd.getShowTimeRemaining(currentShow[0]);
            }

            if (nextShows.length > 0){
                nextShowName = nextShows[0].getName();
                nextShowRange = nextShows[0].getRange();
            }

            obj.empty();
            obj.append("<a id='listenWadrLive'><span>Listen WADR Live</span></a>");
            obj.append("<h4>"+showStatus+" &gt;&gt;</h4>");
            obj.append("<ul class='widget no-playing-bar'>" +
                "<li class='current'>Current: "+currentShowName+
                "<span id='time-elapsed' class='time-elapsed'>"+timeElapsed+"</span>" +
                "<span id='time-remaining' class='time-remaining'>"+timeRemaining+"</span>"+
                "</li>" +
                "<li class='next'>Next: "+nextShowName+"<span>"+nextShowRange+"</span></li>" +
                "</ul>");

            //refresh the UI to update the elapsed/remaining time
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

/* ScheduleData class BEGIN */
function ScheduleData(data){
    this.data = data;
    this.estimatedSchedulePosixTime;

    this.currentShow = new Array();
    for (var i=0; i< data.currentShow.length; i++){
        this.currentShow[i] = new Show(data.currentShow[i]);
    }

    this.nextShows = new Array();
    for (var i=0; i< data.nextShow.length; i++){
        this.nextShows[i] = new Show(data.nextShow[i]);
    }
    

    this.schedulePosixTime = convertDateToPosixTime(data.schedulerTime);
    this.schedulePosixTime += parseInt(data.timezoneOffset)*1000;
    var date = new Date();
    this.localRemoteTimeOffset = date.getTime() - this.schedulePosixTime;
}


ScheduleData.prototype.secondsTimer = function(){
    var date = new Date();
    this.estimatedSchedulePosixTime = date.getTime() - this.localRemoteTimeOffset;
}

ScheduleData.prototype.getCurrentShow = function(){
    return this.currentShow;
}

ScheduleData.prototype.getNextShows = function() {
    return this.nextShows;
}

ScheduleData.prototype.getShowTimeElapsed = function(show) {
    this.secondsTimer();

    var showStart = convertDateToPosixTime(show.getStartTimestamp());
    return convertToHHMMSS(this.estimatedSchedulePosixTime - showStart);
};

ScheduleData.prototype.getShowTimeRemaining = function(show) {
    this.secondsTimer();

    var showEnd = convertDateToPosixTime(show.getEndTimestamp());
    return convertToHHMMSS(showEnd - this.estimatedSchedulePosixTime);
};
/* ScheduleData class END */

/* Show class BEGIN */
function Show(showData){
    this.showData = showData;
}

Show.prototype.getName = function(){
    return this.showData.name;
}
Show.prototype.getRange = function(){
    return getTime(this.showData.start_timestamp) + " - " + getTime(this.showData.end_timestamp);
}
Show.prototype.getStartTimestamp = function(){
    return this.showData.start_timestamp;
}
Show.prototype.getEndTimestamp = function(){
    return this.showData.end_timestamp;
}
/* Show class END */


function getTime(timestamp) {
    var time = timestamp.split(" ")[1].split(":");
    return time[0] + ":" + time[1];
};

/* Takes an input parameter of milliseconds and converts these into
 * the format HH:MM:SS */
function convertToHHMMSS(timeInMS){
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
function convertDateToPosixTime(s){
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
