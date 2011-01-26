var estimatedSchedulePosixTime = -1;
var schedulePosixTime;

var currentRemoteTimeOffset;

var previousSongs = new Array();
var currentSong = new Array();
var nextSongs = new Array();

var currentElem;

var updateInterval = 5000;

var songEndFunc;

function registerSongEndListener(func){
	songEndFunc = func;
}


function convertToHHMMSS(timeInMS){
	var time = parseInt(timeInMS);
	
	var hours = parseInt(time / 3600000);
	time -= 3600000*hours;
		
	var minutes = parseInt(time / 60000);
	time -= 60000*minutes;
	
	var seconds = parseInt(time / 1000);
	
	hours = "" + hours;
	minutes = "" + minutes;
	seconds = "" + seconds;
	
	if (hours.length == 1)
		hours = "0" + hours;
	if (minutes.length == 1)
		minutes = "0" + minutes;
	if (seconds.length == 1)
		seconds = "0" + seconds;
	return "" + hours + ":" + minutes + ":" + seconds;
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

function getTrackInfo(song){
	var str = "";
	
	if (song.track_title != null)
		str += song.track_title;
	if (song.artist_name != null)
		str += " - " + song.artist_name;
	if (song.album_title != null)
		str += " - " + song.album_title;
		
	return str;
}

function secondsTimer(){
	var date = new Date();
	estimatedSchedulePosixTime = date.getTime() - currentRemoteTimeOffset;
	updateProgressBarValue();
}

/* Called every 0.2 seconds. */
function updateProgressBarValue(){
	if (estimatedSchedulePosixTime != -1){
		if (currentSong.length > 0){
			var percentDone = (estimatedSchedulePosixTime - currentSong[0].songStartPosixTime)/currentSong[0].songLengthMs*100;
			if (percentDone <= 100){
				$('#progressbar').progressBar(percentDone);
			} else {
				if (nextSongs.length > 0){
					currentSong[0] = nextSongs.shift();
				} else {
					currentSong = new Array();
				}
				$('#progressbar').progressBar(0);
			}
			
			
			percentDone = (estimatedSchedulePosixTime - currentSong[0].showStartPosixTime)/currentSong[0].showLengthMs*100;
			//$('#showprogressbar').text(currentSong[0].showLengthMs);
			$('#showprogressbar').progressBar(percentDone);
		} else {
			$('#progressbar').progressBar(0);
			
			//calculate how much time left to next song if there is any
			if (nextSongs.length > 0){
				if (nextSongs[0].songStartPosixTime - estimatedSchedulePosixTime < updateInterval){
					setTimeout(temp, nextSongs[0].songStartPosixTime - estimatedSchedulePosixTime);
				}
			}
		}
		updatePlaylist();
	}
	setTimeout(secondsTimer, 200);
}

function temp(){
	currentSong[0] = nextSongs[0];
    updatePlaylist();
    
    songEndFunc();
}

function updatePlaylist(){
	/* Column 0 update */
	$('#listen');
	
	
	/* Column 1 update */
	for (var i=0; i<currentSong.length; i++){
		//alert (currentSong[i].playlistname);
		//$('#show').text(currentSong[i].show);
		$('#playlist').text(currentSong[i].name);
		//$('#host').text(currentSong[i].creator);
	}
	
	/* Column 2 update */
	for (var i=0; i<previousSongs.length; i++){
		$('#previous').text(getTrackInfo(previousSongs[i]));
	}
	for (var i=0; i<currentSong.length; i++){
		$('#current').text(getTrackInfo(currentSong[i]));
	}
	for (var i=0; i<nextSongs.length; i++){
		$('#next').text(getTrackInfo(nextSongs[i]));
	}
	
	/* Column 3 update */
	for (var i=0; i<currentSong.length; i++){
		$('#start').text(currentSong[i].starts.substring(currentSong[i].starts.indexOf(" ")+1));
		$('#end').text(currentSong[i].ends.substring(currentSong[i].starts.indexOf(" ")+1));
		$('#songposition').text(convertToHHMMSS(estimatedSchedulePosixTime - currentSong[i].songStartPosixTime));
		$('#songlength').text(currentSong[i].clip_length);
		$('#showposition').text(convertToHHMMSS(estimatedSchedulePosixTime - currentSong[i].showStartPosixTime));
		$('#showlength').text(convertToHHMMSS(currentSong[i].showEndPosixTime - currentSong[i].showStartPosixTime));
	}	
}

function calcAdditionalData(currentItem){
	for (var i=0; i<currentItem.length; i++){
		currentItem[i].songStartPosixTime = convertDateToPosixTime(currentItem[i].starts);
		currentItem[i].songEndPosixTime = convertDateToPosixTime(currentItem[i].ends);
		currentItem[i].songLengthMs = currentItem[i].songEndPosixTime - currentItem[i].songStartPosixTime;
		
		currentItem[i].showStartPosixTime = convertDateToPosixTime(currentItem[i].starts.substring(0, currentItem[i].starts.indexOf(" ")) + " " + currentItem[i].start_time);
		currentItem[i].showEndPosixTime = convertDateToPosixTime(currentItem[i].starts.substring(0, currentItem[i].starts.indexOf(" ")) + " " + currentItem[i].end_time);
		currentItem[i].showLengthMs = currentItem[i].showEndPosixTime - currentItem[i].showStartPosixTime;
	}
}

function parseItems(obj){
	schedulePosixTime = convertDateToPosixTime(obj.schedulerTime);

	if (estimatedSchedulePosixTime == -1){
		var date = new Date();
		currentRemoteTimeOffset = date.getTime() - schedulePosixTime;
		estimatedSchedulePosixTime = schedulePosixTime;
	}
	
	previousSongs = obj.previous;
	currentSong = obj.current;
	nextSongs = obj.next;

	calcAdditionalData(previousSongs);
	calcAdditionalData(currentSong);
	calcAdditionalData(nextSongs);
}

function getScheduleFromServer(){
	$.ajax({ url: "/Schedule/get-current-playlist/format/json", dataType:"json", success:function(data){
			parseItems(data.entries);
		  }});
	setTimeout(getScheduleFromServer, updateInterval);
}

function init(elemID) {
	var currentElem = $("#" + elemID).attr("style", "z-index: 1; width: 100%; left: 0px; right: 0px; bottom: 0px; color: black; min-height: 100px; background-color: #FEF1B5;");
	
	$('#progressbar').progressBar(0, {showText : false});
	$('#showprogressbar').progressBar(0, {showText : false, barImage:'/js/progressbar/images/progressbg_red.gif'});

	getScheduleFromServer();
	updateProgressBarValue();
	
}
