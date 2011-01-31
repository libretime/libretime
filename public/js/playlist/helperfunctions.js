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

function convertDateToHHMMSS(epochTime){
	var d = new Date(epochTime);
	
	var hours = d.getUTCHours().toString();
	var minutes = d.getUTCMinutes().toString();
	var seconds = d.getUTCSeconds().toString();

    //if (hours == "NaN")
        //alert("epochTime: " + epochTime);
	
	if (hours.length == 1)
		hours = "0" + hours;
	if (minutes.length == 1)
		minutes = "0" + minutes;
	if (seconds.length == 1)
		seconds = "0" + seconds;
	return hours + ":" + minutes + ":" + seconds;
}

function convertDateToPosixTime(s){
	var year = s.substring(0, 4);
	var month = s.substring(5, 7);
	var day = s.substring(8, 10);
	var hour = s.substring(11, 13);
	var minute = s.substring(14, 16);
	var sec = s.substring(17, 19);
	var msec = 0;
	if (s.length >= 20){
		msec = s.substring(20);
	}

	return Date.UTC(year, month, day, hour, minute, sec, msec);
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
    var uri = "/api/get-media/api_key/AAA/file/" + filename;
    
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
    $('#'+elemID).children("a").children().attr("class", "ui-icon ui-icon-pause");
}
