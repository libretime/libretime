 var AIRTIME_API_VERSION = "1.1";

(function($){
 $.fn.airtimeShowSchedule = function(options) {

    var defaults = {
        updatePeriod: 20, //seconds
        sourceDomain: "http://localhost/", //where to get show status from
        text: {onAirToday:"On air today"},
        showLimit: 5
    };
    options = $.extend(true, defaults, options);
    options.sourceDomain = addEndingBackslash(options.sourceDomain);

    return this.each(function() {
        var obj = $(this);
        var sd;

        getServerData();

        function updateWidget(){
            var currentShow = sd.getCurrentShow();
            var nextShows = sd.getNextShows();
            var shows = currentShow.length == 0 ? nextShows : currentShow.concat(nextShows);

            tableString = "";
            tableString += "<h3>" + options.text.onAirToday + "</h3>";
            tableString += "<table width='100%' border='0' cellspacing='0' cellpadding='0' class='widget widget now-playing-list small'>"+
                "<tbody>";
            
            for (var i=0; i<shows.length; i++){
                tableString +=
                "<tr>" +
                "<td class='time'>"+shows[i].getRange()+"</td>";

                var url = shows[i].getURL();
                if (url.length > 0) {
                    tableString += "<td><a href='" + shows[i].getURL() + "'>" + shows[i].getName() + "</a></td></tr>";
                } else {
                    tableString += "<td>" + shows[i].getName() + "</td></tr>";
                }  
            }
            tableString += "</tbody></table>";
            
            obj.empty();
            obj.append(tableString);
        }

        function processData(data){
            checkWidgetVersion(data);
            sd = new ScheduleData(data);
            updateWidget();
        }

        function airtimeScheduleJsonpError(jqXHR, textStatus, errorThrown){
        }

        function getServerData(){
            $.ajax({url: options.sourceDomain + "api/live-info/", 
                    data: {type:"endofday",limit: options.showLimit}, 
                    dataType: "jsonp", 
                    success:function(data) {
                        processData(data);
                    }, 
                    error: airtimeScheduleJsonpError});
            setTimeout(getServerData, options.updatePeriod*1000);
        }
    });
 };
})(jQuery);


(function($){
 $.fn.airtimeLiveInfo = function(options) {

    var defaults = {
        updatePeriod: 5, //seconds
        sourceDomain: "http://localhost/", //where to get show status from
        text: {onAirNow:"On Air Now", offline:"Offline", current:"Current", next:"Next"}
    };
    options = $.extend(true, defaults, options);    
    options.sourceDomain = addEndingBackslash(options.sourceDomain);

    return this.each(function() {
        var obj = $(this);
        var sd = null;
        getServerData();

        //refresh the UI to update the elapsed/remaining time
        setInterval(updateWidget, 1000);

        function updateWidget(){

            if (sd == null){
                return;
            }
            
            var currentShow = sd.getCurrentShow();
            var nextShows = sd.getNextShows();

            var showStatus = options.text.offline;
            var currentShowName = "";
            var timeElapsed = "";
            var timeRemaining = "";

            var nextShowName = "";
            var nextShowRange = "";

            if (currentShow.length > 0){
                showStatus = options.text.onAirNow;
                currentShowName = currentShow[0].getName();

                timeElapsed = sd.getShowTimeElapsed(currentShow[0]);
                timeRemaining = sd.getShowTimeRemaining(currentShow[0]);
            }

            if (nextShows.length > 0){
                nextShowName = nextShows[0].getName();
                nextShowRange = nextShows[0].getRange();
            }

            obj.empty();
            obj.append("<h4>"+showStatus+" &gt;&gt;</h4>");
            obj.append("<ul class='widget now-playing-bar'>" +
                "<li class='current'>"+options.text.current+": "+currentShowName+
                "<span id='time-elapsed' class='time-elapsed'>"+timeElapsed+"</span>" +
                "<span id='time-remaining' class='time-remaining'>"+timeRemaining+"</span>"+
                "</li>" +
                "<li class='next'>"+options.text.next+": "+nextShowName+"<span>"+nextShowRange+"</span></li>" +
                "</ul>");
        }

        function processData(data){
            checkWidgetVersion(data);
            sd = new ScheduleData(data);
        }

        function airtimeScheduleJsonpError(jqXHR, textStatus, errorThrown){
        }

        function getServerData(){
            $.ajax({url: options.sourceDomain + "api/live-info/", 
                    data: {type:"interval",limit:"5"}, 
                    dataType: "jsonp", 
                    success: function(data) {
                        processData(data);
                    }, 
                    error: airtimeScheduleJsonpError});
            setTimeout(getServerData, options.updatePeriod*1000);
        }
    });
 };
})(jQuery);

(function($){
    $.fn.airtimeLiveTrackInfo = function(options) {

       var defaults = {
           updatePeriod: 5, //seconds
           sourceDomain: "http://localhost/", //where to get show status from
           text: {onAirNow:"On Air Now", offline:"Offline", current:"Current", next:"Next"}
       };
       options = $.extend(true, defaults, options);    
       options.sourceDomain = addEndingBackslash(options.sourceDomain);

       return this.each(function() {
           var obj = $(this);
           var sd = null;
           getServerData();

           //refresh the UI to update the elapsed/remaining time
           setInterval(updateWidget, 1000);

           function updateWidget(){
               if (sd == null){
                   return;
               }
               
               var currentShow = sd.getCurrentShow();
               var nextShows = sd.getNextShows();

               var showStatus = options.text.offline;
               var currentShowName = "";
               var timeElapsed = "";
               var timeRemaining = "";

               var nextShowName = "";
               var nextShowRange = "";

               if (currentShow.length > 0){
                   showStatus = options.text.onAirNow;
                   currentShowName = currentShow[0].getName();

                   timeElapsed = sd.getShowTimeElapsed(currentShow[0]);
                   timeRemaining = sd.getShowTimeRemaining(currentShow[0]);
               }

               if (nextShows.length > 0){
                   nextShowName = nextShows[0].getName();
                   nextShowRange = nextShows[0].getRange();
               }

               obj.empty();
               obj.append("<span id='status-current-show' style='display:inline'>"+showStatus+" &gt;&gt;&nbsp;"+currentShowName+"</span>" +
                       "<span class='current' id='time-elapsed' class='time-elapsed'>"+timeElapsed+"</span>" +
                       "<span class='current' id='time-remaining' class='time-remaining'>"+timeRemaining+"</span>");
               obj.append("<ul class='widget now-playing-bar'>" +
                   "<li class='current track-metadata'>"+options.text.current+": "+sd.currentTrack.getTitle()+"</li>" +
                   "<li class='next track-metadata'>"+options.text.next+": "+sd.nextTrack.getTitle()+"</span></li>" +                   
                   "</ul>");
           }

           function processData(data){
               checkWidgetVersion(data);
               sd = new ScheduleData(data);
           }

           function airtimeScheduleJsonpError(jqXHR, textStatus, errorThrown){
           }

           function getServerData(){
               $.ajax({url: options.sourceDomain + "api/live-info/", 
                       data: {type:"interval",limit:"5"}, 
                       dataType: "jsonp", 
                       success: function(data) {
                           processData(data);
                       }, 
                       error: airtimeScheduleJsonpError});
               setTimeout(getServerData, options.updatePeriod*1000);
           }
       });
    };
   })(jQuery);

(function($){
 $.fn.airtimeWeekSchedule = function(options) {

    var defaults = {
        sourceDomain: "http://localhost/", //where to get show status from
        updatePeriod: 600,
        dowText:{monday:"Monday", tuesday:"Tuesday", wednesday:"Wednesday",thursday:"Thursday", friday:"Friday", saturday:"Saturday",sunday:"Sunday", nextmonday:"Next Monday", nexttuesday:"Next Tuesday",nextwednesday:"Next Wednesday", nextthursday:"Next Thursday",nextfriday:"Next Friday", nextsaturday:"Next Saturday", nextsunday:"NextSunday"},
        miscText: {time:"Time", programName:"Program Name", details:"Details", readMore:"Read More"}
    };
    options = $.extend(true, defaults, options);
    options.sourceDomain = addEndingBackslash(options.sourceDomain);
    
    return this.each(function() {
        var obj = $(this);
        obj.empty();

        obj.attr("class", "ui-tabs");

        var dow = ["monday", "tuesday", "wednesday", "thursday", "friday","saturday", "sunday", "nextmonday", "nexttuesday", "nextwednesday","nextthursday", "nextfriday", "nextsaturday", "nextsunday"];

        var date = new Date();
        //subtract 1 because javascript date function returns
        //sunday as 0 based, but we want Monday to be 0-based.
        var todayInt = (date.getDay()-1);
        if (todayInt < 0)
            todayInt += 7;

        var html = '<ul>';
        for (var i=0; i<dow.length; i++){
            html += '<li'+ (i==todayInt?' class="ui-tabs-selected ui-state-active"':'')+'><a href="#'+dow[i]+'">'+options.dowText[dow[i]]+'</a></li>';
        }
        html += '</ul>';

        for (var i=0; i<dow.length; i++){
            html += '<div id="'+dow[i]+'" class="ui-tabs-hide"></div>'
        }
        obj.append(html);
        getServerData();

        function updateWidget(data){
            for (var i=0; i<dow.length; i++){
                var html = 
                  '<table class="widget widget now-playing-list">'+
                    '<colgroup>'+
                      '<col width="150" />'+
                      '<col width="350" />'+
                      '<col width="240" />'+
                    '</colgroup>'+
                    '<thead>'+
                      '<tr>'+
                        '<td>'+options.miscText.time+'</td>'+
                        '<td>'+options.miscText.programName+'</td>'+
                        '<td>'+options.miscText.details+'</td>'+
                      '</tr>'+
                    '</thead>'+
                    '<tfoot>'+
                      '<tr>'+
                        '<td></td>'+
                      '</tr>'+
                    '</tfoot>'+
                    '<tbody>';
                var daySchedule = data[dow[i]];
                for (var j=0; j<daySchedule.length; j++){
                    var url = daySchedule[j].url;
                    html +=
                      '<tr>'+
                        '<td>'+getTime(daySchedule[j].start_timestamp)+ " - " + getTime(daySchedule[j].end_timestamp)+'</td>'+
                        '<td>'+
                          '<h4>'+daySchedule[j].name+'</h4>'+
                        '</td>'+
                        '<td>'+
                          '<ul>'+
                            '<li>'+(url.length > 0 ? '<a href="'+url+'">'+options.miscText.readMore+'</a>':'')+'</li>'+
                          '</ul>'+
                        '</td>'+
                      '</tr>';
                }
                html +=
                    '</tbody>'+
                  '</table>';
                  
                $("#"+dow[i]).empty();
                $("#"+dow[i]).append(html);
            }
        }

        function processData(data){
            checkWidgetVersion(data);
            updateWidget(data);
        }

        function airtimeScheduleJsonpError(jqXHR, textStatus, errorThrown){
        }

        function getServerData(){
            $.ajax({ url: options.sourceDomain + "api/week-info/", dataType:"jsonp", success:function(data){
                        processData(data);
                  }, error:airtimeScheduleJsonpError});
            setTimeout(getServerData, options.updatePeriod*1000);
        }
    });
 };
})(jQuery);

function addEndingBackslash(str){
    if (str.charAt(str.length-1) != '/')
        return str+'/';
    else return str;
}

/* ScheduleData class BEGIN */
function ScheduleData(data){
    this.data = data;
    this.estimatedSchedulePosixTime;

    this.currentShow = new Array();
    if(data.currentShow != undefined) {
        for (var i=0; i< data.currentShow.length; i++){
            this.currentShow[i] = new Show(data.currentShow[i]);
        }
    }

    this.nextShows = new Array();
    if(data.nextShow != undefined) {
        for (var i=0; i< data.nextShow.length; i++) {
            this.nextShows[i] = new Show(data.nextShow[i]);
        }
    }
    
    this.currentTrack = new AudioTrack(data.current);
    this.nextTrack = new AudioTrack(data.next);

    this.schedulePosixTime = convertDateToPosixTime(data.schedulerTime);
    //this.schedulePosixTime += parseInt(data.timezoneOffset, 10)*1000;
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

Show.prototype.getURL = function(){
    return this.showData.url;
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

/* AudioTrack class BEGINS */
function AudioTrack(trackData){
    this.trackData = trackData;
}

AudioTrack.prototype.getTitle = function(){
    if (this.trackData === null) return "";
    return this.trackData.name;
}
/* AudioTrack class ENDS */

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

	return Date.UTC(year, month-1, day, hour, minute, sec, msec);
}

/* Checks the incomming data's widget version tag.
*  The current widget version is 1.
*     -If the value returned is equal to 1 do nothing.
*     -If the value doesn't exist or it is great then 1 throw error warning the user they should upgrade their airtime install.
*     -If the value is less then 1 warn the user that they should upgrade the javascript to a newer version.
*/
function checkWidgetVersion(data){
   
    var airtimeServerWidgetVersion = data['AIRTIME_API_VERSION'];
    
    if (undefined === airtimeServerWidgetVersion || airtimeServerWidgetVersion >  AIRTIME_API_VERSION )
        throw "The version of widgets you are using is out of date with the Airtime installation, please update your widgets javascript file. (Airtime widget API version is "+airtimeServerWidgetVersion+", this widget's API version is "+AIRTIME_API_VERSION+")";
    else if (airtimeServerWidgetVersion < AIRTIME_API_VERSION )
        throw "The Airtime server has a different version than this widget supports. Please get the correct widget version for your Airtime installation. (Airtime widget API version is "+airtimeServerWidgetVersion+", this widget's API version is "+AIRTIME_API_VERSION+")";
    
}
