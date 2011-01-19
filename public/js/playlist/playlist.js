var estimatedSchedulePosixTime = -1;
var schedulePosixTime;

var previousSongs;
var currentSong;
var nextSongs;

var currentElem;

function init(elemID) {
	var currentElem = $("#" + elemID).attr("style", "z-index: 1; width: 100%; left: 0px; right: 0px; bottom: 0px; color: white; min-height: 100px; background-color: #cc3300;");
	
	$('#progressbar').progressBar(0);

	getScheduleFromServer();
	updateProgressBarValue();
	
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

function secondsTimer(){
	estimatedSchedulePosixTime += 1000;
	updateProgressBarValue();
}

/* Called every 1 second. */
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
				//at the end of each song we are updating the
				//server time we have been estimating client-side
				//with the real server time.
				estimatedSchedulePosixTime = schedulePosixTime;
			}
		} else
			$('#progressbar').progressBar(0);
		updatePlaylist();
	}
	setTimeout(secondsTimer, 1000);
}

function getTrackInfo(song){
	return song.track_title + " - " + song.artist_name + " - " + song.album_title;
}

function updatePlaylist(){
	/* Column 0 update */
	$('#listen');
	$('#volume');
	
	/* Column 1 update */
	$('#show').empty();
	$('#playlist').empty();
	$('#host').empty();
	for (var i=0; i<currentSong.length; i++){
		$('#show').append(currentSong[i].show);
		$('#playlist').append(currentSong[i].playlist);
		$('#host').append(currentSong[i].host);
	}
	
	/* Column 2 update */
	$('#previous').empty();
	$('#current').empty();
	$('#next').empty();
	for (var i=0; i<previousSongs.length; i++){
		$('#previous').append(getTrackInfo(previousSongs[i]));
	}
	for (var i=0; i<currentSong.length; i++){
		$('#current').append(getTrackInfo(currentSongs[i]));
	}
	for (var i=0; i<nextSongs.length; i++){
		$('#next').append(getTrackInfo(nextSongs[i]));
	}
	
	/* Column 3 update */
	$('#start').empty();
	$('#end').empty();
	for (var i=0; i<currentSong.length; i++){
		$('#start').append(currentSong[i].starts);
		$('#end').append(currentSong[i].ends);
	}	
}

function calcAdditionalData(currentItem){
	for (var i=0; i<currentItem.length; i++){
		currentItem[i].songStartPosixTime = convertDateToPosixTime(currentItem[i].starts);
		currentItem[i].songEndPosixTime = convertDateToPosixTime(currentItem[i].ends);
		currentItem[i].songLengthMs = currentItem[i].songEndPosixTime - currentItem[i].songStartPosixTime;
	}
}

function prepareNextPlayingItem(obj){
	if (obj.next.length > 0){
		var nextItem = obj.next[0];
	}
}

function parseItems(obj){
	schedulePosixTime = convertDateToPosixTime(obj.schedulerTime);

	if (estimatedSchedulePosixTime == -1)
		estimatedSchedulePosixTime = schedulePosixTime;

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
	setTimeout(getScheduleFromServer, 5000);
}

$(document).ready(function() {
	init("nowplayingbar");
});
