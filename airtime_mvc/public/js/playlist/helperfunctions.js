/* function to create popup window */
function popup(mylink){
    if (!window.focus)
        return true;
    var href;
    if (typeof(mylink) == 'string')
       href=mylink;
    else
       href=mylink.href;
    window.open(href, "player", 'width=300,height=100,scrollbars=yes');
    return false;
}


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

function convertToHHMMSSmm(timeInMS){
	var time = parseInt(timeInMS);
	
	var hours = parseInt(time / 3600000);
	time -= 3600000*hours;
		
	var minutes = parseInt(time / 60000);
	time -= 60000*minutes;
	
	var seconds = parseInt(time / 1000);
	time -= 1000*seconds;
	
	var ms = parseInt(time);
	
	hours = hours.toString();
	minutes = minutes.toString();
	seconds = seconds.toString();
	ms = ms.toString();
	
	if (hours.length == 1)
		hours = "0" + hours;
	if (minutes.length == 1)
		minutes = "0" + minutes;
	if (seconds.length == 1)
		seconds = "0" + seconds;
		
	if (ms.length == 3)
		ms = ms.substring(0, 2);
	else if (ms.length == 2)
		ms = "0" + ms.substring(0,1);
	else if (ms.length == 1)
		ms = "00";
		
	if (hours == "00")
		return minutes + ":" + seconds + "." + ms;
	else
		return hours + ":" + minutes + ":" + seconds+ "." + ms;
}

function convertDateToHHMM(epochTime){
	var d = new Date(epochTime);
	
	var hours = d.getUTCHours().toString();
	var minutes = d.getUTCMinutes().toString();
	
	if (hours.length == 1)
		hours = "0" + hours;
	if (minutes.length == 1)
		minutes = "0" + minutes;
        
	return hours + ":" + minutes;
}

function convertDateToHHMMSS(epochTime){
	var d = new Date(epochTime);
	
	var hours = d.getUTCHours().toString();
	var minutes = d.getUTCMinutes().toString();
	var seconds = d.getUTCSeconds().toString();
	
	if (hours.length == 1)
		hours = "0" + hours;
	if (minutes.length == 1)
		minutes = "0" + minutes;
	if (seconds.length == 1)
		seconds = "0" + seconds;
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

function getFileExt(filename){
    return filename.split('.').pop();
}

var currentAudioPreviewID = "";

function audioPreview(filename, elemID){

    var elems = $('.ui-icon.ui-icon-pause');
    elems.attr("class", "ui-icon ui-icon-play");

    if (currentAudioPreviewID == elemID){
         $('#jquery_jplayer_1').jPlayer('stop');
        currentAudioPreviewID = "";
        return;
    } else {
        currentAudioPreviewID = elemID;
    }

    var ext = getFileExt(filename);
    var uri = "/api/get-media/api_key/" + apiKey + "/file/" + filename;
    
    var media;
    var supplied;
    if (ext == "ogg"){
        media = {oga:uri};
        supplied = "oga";
    } else {
        media = {mp3:uri};
        supplied = "mp3";
    }

      //$('#jquery_jplayer_1').jPlayer('stop');
      $("#jquery_jplayer_1").jPlayer("destroy");
      $("#jquery_jplayer_1").jPlayer({
		ready: function () {
            //alert(media);
			$(this).jPlayer("setMedia", media).jPlayer("play");
		},
        swfPath: "/js/jplayer",
		supplied: supplied
      });

    //$('#jquery_jplayer_1').jPlayer('setMedia', media).jPlayer('play');
    $('#'+elemID+' div.list-item-container a span').attr("class", "ui-icon ui-icon-pause");
}
