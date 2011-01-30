var estimatedSchedulePosixTime = -1;

var localRemoteTimeOffset;

var previousSongs = new Array();
var currentSong = new Array();
var nextSongs = new Array();

var currentElem;

var serverUpdateInterval = 5000;
var uiUpdateInterval = 200;

var songEndFunc;

var showStartPosixTime = 0;
var showEndPosixTime = 0;
var showLengthMs = 1;

/* boolean flag to let us know if we should prepare to execute a function
 * that flips the playlist to the next song. This flags purpose is to
 * make sure the function is only executed once*/
var nextSongPrepare = true;

/* Another script can register its function here
 * when it wishes to know when a song ends. */
function registerSongEndListener(func){
    songEndFunc = func;
}

function notifySongEndListener(){
    if (typeof songEndFunc == "function"){
        //create a slight pause in execution to allow the browser
        //to update the display.
        setTimeout(songEndFunc, 50);
    }
}

function getTrackInfo(song){
    var str = "";

    if (song.track_title != null)
        str += song.track_title;
    if (song.artist_name != null)
        str += " - " + song.artist_name;
    //if (song.album_title != null)
        //str += " - " + song.album_title;

    str += ","

    return str;
}

function secondsTimer(){
    var date = new Date();
    estimatedSchedulePosixTime = date.getTime() - localRemoteTimeOffset;
    updateProgressBarValue();
}

function updateGlobalValues(obj){
    showStartPosixTime = obj.showStartPosixTime;
    showEndPosixTime = obj.showEndPosixTime;
    showLengthMs = showEndPosixTime - showStartPosixTime;
}

function newSongStart(){
    nextSongPrepare = true;
    currentSong[0] = nextSongs.shift();
    updateGlobalValues(currentSong[0]);
    updatePlaybar();

    notifySongEndListener();
}

/* Called every "uiUpdateInterval" mseconds. */
function updateProgressBarValue(){
    if (estimatedSchedulePosixTime != -1){
        if (showStartPosixTime != 0){
            var showPercentDone = (estimatedSchedulePosixTime - showStartPosixTime)/showLengthMs*100;
            if (showPercentDone < 0 || showPercentDone > 100){
                showPercentDone = 0;
                $('#on-air-info').attr("class", "on-air-info off");
            } else {
                $('#on-air-info').attr("class", "on-air-info on");
            }
            $('#progress-show').attr("style", "width:"+showPercentDone+"%");
        }

        var songPercentDone = 0;
        if (currentSong.length > 0){
            songPercentDone = (estimatedSchedulePosixTime - currentSong[0].songStartPosixTime)/currentSong[0].songLengthMs*100;
            if (songPercentDone < 0 || songPercentDone > 100){
                songPercentDone = 0;
                currentSong = new Array();
            }
        }
        $('#progress-bar').attr("style", "width:"+songPercentDone+"%");

        //calculate how much time left to next song if there is any
        if (nextSongs.length > 0 && nextSongPrepare){
            if (nextSongs[0].songStartPosixTime - estimatedSchedulePosixTime < serverUpdateInterval){
                nextSongPrepare = false;
                setTimeout(newSongStart, nextSongs[0].songStartPosixTime - estimatedSchedulePosixTime);
            }
        }

        updatePlaybar();
    }
    setTimeout(secondsTimer, uiUpdateInterval);
}

function updatePlaybar(){
    /* Column 0 update */
    $('#previous').empty();
    $('#prev-length').empty();
    $('#current').text("Current:");
    $('#next').empty();
    $('#next-length').empty();
    if (previousSongs.length > 0){
        $('#previous').text(getTrackInfo(previousSongs[previousSongs.length-1]));
        $('#prev-length').text(convertToHHMMSSmm(previousSongs[previousSongs.length-1].songLengthMs));
    }
    if (currentSong.length > 0){
        $('#current').text(getTrackInfo(currentSong[0]));
    }
    if (nextSongs.length > 0){
        $('#next').text(getTrackInfo(nextSongs[0]));
        $('#next-length').text(convertToHHMMSSmm(nextSongs[0].songLengthMs));
    }

    $('#start').empty();
    $('#end').empty();
    $('#time-elapsed').empty();
    $('#time-remaining').empty();
    $('#song-length').empty();
    for (var i=0; i<currentSong.length; i++){
        $('#start').text(currentSong[i].starts.substring(currentSong[i].starts.indexOf(" ")+1));
        $('#end').text(currentSong[i].ends.substring(currentSong[i].starts.indexOf(" ")+1));

        /* Get rid of the millisecond accuracy so that the second counters for both
         * show and song change at the same time. */
        var songStartRoughly = parseInt(Math.round(currentSong[i].songStartPosixTime/1000))*1000;
        var songEndRoughly = parseInt(Math.round(currentSong[i].songEndPosixTime/1000))*1000;

        $('#time-elapsed').text(convertToHHMMSS(estimatedSchedulePosixTime - songStartRoughly));
        $('#time-remaining').text(convertToHHMMSS(songEndRoughly - estimatedSchedulePosixTime));
        $('#song-length').text(convertToHHMMSSmm(currentSong[i].songLengthMs));
    }

    /* Column 1 update */
    $('#playlist').text("Current Show:");
    for (var i=0; i<currentSong.length; i++){
        $('#playlist').text(currentSong[i].name);
    }
    $('#show-length').empty();
    if (estimatedSchedulePosixTime < showEndPosixTime){
        $('#show-length').text(convertDateToHHMMSS(showStartPosixTime) + " - " + convertDateToHHMMSS(showEndPosixTime));
    }

    /* Column 2 update */
    $('#time').text(convertDateToHHMMSS(estimatedSchedulePosixTime));
}

function calcAdditionalData(currentItem, bUpdateGlobalValues){
    for (var i=0; i<currentItem.length; i++){
        currentItem[i].songStartPosixTime = convertDateToPosixTime(currentItem[i].starts);
        currentItem[i].songEndPosixTime = convertDateToPosixTime(currentItem[i].ends);
        currentItem[i].songLengthMs = currentItem[i].songEndPosixTime - currentItem[i].songStartPosixTime;

        currentItem[i].showStartPosixTime = convertDateToPosixTime(currentItem[i].starts.substring(0, currentItem[i].starts.indexOf(" ")) + " " + currentItem[i].start_time);
        currentItem[i].showEndPosixTime = convertDateToPosixTime(currentItem[i].starts.substring(0, currentItem[i].starts.indexOf(" ")) + " " + currentItem[i].end_time);

        //check if there is a rollover past midnight
        if (currentItem[i].start_time > currentItem[i].end_time){
            //start_time is greater than end_time, so we rolled through midnight.
            currentItem[i].showEndPosixTime += (1000*3600*24); //add 24 hours
        }

        currentItem[i].showLengthMs = currentItem[i].showEndPosixTime - currentItem[i].showStartPosixTime;

        if (bUpdateGlobalValues){
            updateGlobalValues(currentItem[i]);
        }
    }
}

function parseItems(obj){
    var schedulePosixTime = convertDateToPosixTime(obj.schedulerTime);

    previousSongs = obj.previous;
    currentSong = obj.current;
    nextSongs = obj.next;

    calcAdditionalData(previousSongs, false);
    calcAdditionalData(currentSong, true);
    calcAdditionalData(nextSongs, false);

    if (estimatedSchedulePosixTime == -1){
        var date = new Date();
        localRemoteTimeOffset = date.getTime() - schedulePosixTime;
        estimatedSchedulePosixTime = schedulePosixTime;
    }
}

function getScheduleFromServer(){
    $.ajax({ url: "/Schedule/get-current-playlist/format/json", dataType:"json", success:function(data){
            parseItems(data.entries);
          }});
    setTimeout(getScheduleFromServer, serverUpdateInterval);
}

function init() {
    //begin producer "thread"
    getScheduleFromServer();

    //begin consumer "thread"
    updateProgressBarValue();
}

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

$(document).ready(function() {
    //initialize the playlist bar in the included playlist.js
    init();
});
