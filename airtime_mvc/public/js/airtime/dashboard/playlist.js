var estimatedSchedulePosixTime = null;
var localRemoteTimeOffset = null;

var previousSong = null;
var currentSong = null;
var nextSong = null;

var currentShow = new Array();
var nextShow = new Array();

var currentElem;

var serverUpdateInterval = 5000;
var uiUpdateInterval = 200;

var master_dj_on_air = false;
var live_dj_on_air = false;
var scheduled_play_on_air = false;
var scheduled_play_source = false;

//var timezoneOffset = 0;

//set to "development" if we are developing :). Useful to disable alerts
//when entering production mode. 
var APPLICATION_ENV = "";

/* boolean flag to let us know if we should prepare to execute a function
 * that flips the playlist to the next song. This flags purpose is to
 * make sure the function is only executed once*/
var nextSongPrepare = true;
var nextShowPrepare = true;

function secondsTimer(){
    if (localRemoteTimeOffset !== null){
        var date = new Date();
        estimatedSchedulePosixTime = date.getTime() - localRemoteTimeOffset;
        updateProgressBarValue();
        updatePlaybar();
        controlOnAirLight();
        controlSwitchLight();
    }
    setTimeout(secondsTimer, uiUpdateInterval);
}

function newSongStart(){
    nextSongPrepare = true;
    currentSong = nextSong;
    nextSong = null;

    if (typeof notifySongStart == "function")   
        notifySongStart();
    
}

function nextShowStart(){
    nextShowPrepare = true;
    currentShow[0] = nextShow.shift();

    //call function in nowplayingdatagrid.js
    if (typeof notifyShowStart == "function")
        notifyShowStart(currentShow[0]);
}

/* Called every "uiUpdateInterval" mseconds. */
function updateProgressBarValue(){
    var showPercentDone = 0;
    if (currentShow.length > 0){
        showPercentDone = (estimatedSchedulePosixTime - currentShow[0].showStartPosixTime)/currentShow[0].showLengthMs*100;
        if (showPercentDone < 0 || showPercentDone > 100){
            showPercentDone = 0;
            currentShow = new Array();
            currentSong = null;
        }
    }
    $('#progress-show').attr("style", "width:"+showPercentDone+"%");

    var songPercentDone = 0;
    var scheduled_play_div = $("#scheduled_play_div")
    var scheduled_play_line_to_switch = scheduled_play_div.parent().find(".line-to-switch")
    
    if (currentSong !== null){
        songPercentDone = (estimatedSchedulePosixTime - currentSong.songStartPosixTime)/currentSong.songLengthMs*100;
        if (songPercentDone < 0 || songPercentDone > 100){
            songPercentDone = 0;        
            currentSong = null;
        } else { 
            if (currentSong.media_item_played == "t" && currentShow.length > 0){
                scheduled_play_line_to_switch.attr("class", "line-to-switch on");
                scheduled_play_div.addClass("ready")
                scheduled_play_source = true;
            }
            else{
                scheduled_play_source = false;
                scheduled_play_line_to_switch.attr("class", "line-to-switch off");
                scheduled_play_div.removeClass("ready")
            }
            $('#progress-show').attr("class", "progress-show");
        }
    } else {
        scheduled_play_source = false;
        scheduled_play_line_to_switch.attr("class", "line-to-switch off");
        scheduled_play_div.removeClass("ready")
        $('#progress-show').attr("class", "progress-show-error");
        
    }
    $('#progress-bar').attr("style", "width:"+songPercentDone+"%");

    //calculate how much time left to next song if there is any
    if (nextSong !== null && nextSongPrepare){
        var diff = nextSong.songStartPosixTime - estimatedSchedulePosixTime;
        if (diff < serverUpdateInterval){
            
            //sometimes the diff is negative (-100ms for example). Still looking
            //into why this could sometimes happen.
            if (diff < 0)
                diff=0;
                
            nextSongPrepare = false;
            setTimeout(newSongStart, diff);
        }
    }
    
    //calculate how much time left to next show if there is any
    if (nextShow.length > 0 && nextShowPrepare){
        var diff = nextShow[0].showStartPosixTime - estimatedSchedulePosixTime;
        if (diff < serverUpdateInterval){
            if (diff < 0)
                diff=0;
                
            nextShowPrepare = false;
            setTimeout(nextShowStart, diff);
        }
    }
}

function updatePlaybar(){
    /* Column 0 update */
    if (previousSong !== null){
        $('#previous').text(previousSong.name+",");
        $('#prev-length').text(convertToHHMMSSmm(previousSong.songLengthMs));
    }else{
        $('#previous').empty();
        $('#prev-length').empty();
    }
    if (currentSong !== null){
        if (currentSong.record == "1")
            $('#current').html("<span style='color:red; font-weight:bold'>Recording: </span>"+currentSong.name+",");
        else
            $('#current').text(currentSong.name+",");
    }else{
        if (master_dj_on_air) {
            $('#current').html("Current: <span style='color:red; font-weight:bold'>Master DJ On Air</span>");
        } else if (live_dj_on_air) {
            $('#current').html("Current: <span style='color:red; font-weight:bold'>Live DJ On Air</span>");
        } else {
            $('#current').html("Current: <span style='color:red; font-weight:bold'>Nothing Scheduled</span>");
        }
    }

    if (nextSong !== null){
        $('#next').text(nextSong.name+",");
        $('#next-length').text(convertToHHMMSSmm(nextSong.songLengthMs));
    }else{
        $('#next').empty();
        $('#next-length').empty();
    }

    $('#start').empty();
    $('#end').empty();
    $('#time-elapsed').empty();
    $('#time-remaining').empty();
    $('#song-length').empty();
    if (currentSong !== null){
        $('#start').text(currentSong.starts.split(' ')[1]);
        $('#end').text(currentSong.ends.split(' ')[1]);

        /* Get rid of the millisecond accuracy so that the second counters for both
         * show and song change at the same time. */
        var songStartRoughly = parseInt(Math.round(currentSong.songStartPosixTime/1000))*1000;
        var songEndRoughly = parseInt(Math.round(currentSong.songEndPosixTime/1000))*1000;

        $('#time-elapsed').text(convertToHHMMSS(estimatedSchedulePosixTime - songStartRoughly));
        $('#time-remaining').text(convertToHHMMSS(songEndRoughly - estimatedSchedulePosixTime));
        $('#song-length').text(convertToHHMMSSmm(currentSong.songLengthMs));
    }
    /* Column 1 update */
    $('#playlist').text("Current Show:");
    var recElem = $('.recording-show');
    if (currentShow.length > 0){
        $('#playlist').text(currentShow[0].name);
        (currentShow[0].record == "1") ? recElem.show(): recElem.hide();
    } else {
        recElem.hide();
    }

    $('#show-length').empty();
    if (currentShow.length > 0){
        //$('#show-length').text(convertDateToHHMM(currentShow[0].showStartPosixTime + timezoneOffset) + " - " + convertDateToHHMM(currentShow[0].showEndPosixTime + timezoneOffset));
        $('#show-length').text(convertDateToHHMM(currentShow[0].showStartPosixTime) + " - " + convertDateToHHMM(currentShow[0].showEndPosixTime));
    }

    /* Column 2 update */
    //$('#time').text(convertDateToHHMMSS(estimatedSchedulePosixTime + timezoneOffset));
    $('#time').text(convertDateToHHMMSS(estimatedSchedulePosixTime));
}

function calcAdditionalData(currentItem){
    currentItem.songStartPosixTime = convertDateToPosixTime(currentItem.starts);
    currentItem.songEndPosixTime = convertDateToPosixTime(currentItem.ends);
    currentItem.songLengthMs = currentItem.songEndPosixTime - currentItem.songStartPosixTime;
}

function calcAdditionalShowData(show){
    if (show.length > 0){
        show[0].showStartPosixTime = convertDateToPosixTime(show[0].start_timestamp);
        show[0].showEndPosixTime = convertDateToPosixTime(show[0].end_timestamp);
        show[0].showLengthMs = show[0].showEndPosixTime - show[0].showStartPosixTime;
    }
}

function parseItems(obj){
    APPLICATION_ENV = obj.env;
        
    $('#time-zone').text(obj.timezone);

    previousSong = obj.previous;
    currentSong = obj.current;
    nextSong = obj.next;

    if (previousSong !== null)
        calcAdditionalData(previousSong);
    if (currentSong !== null)
        calcAdditionalData(currentSong);
    if (nextSong !== null)
        calcAdditionalData(nextSong);
    
    currentShow = obj.currentShow;
    nextShow = obj.nextShow;
    
    calcAdditionalShowData(obj.currentShow);
    calcAdditionalShowData(obj.nextShow);

    var schedulePosixTime = convertDateToPosixTime(obj.schedulerTime);
    //timezoneOffset = parseInt(obj.timezoneOffset)*1000;
    var date = new Date();
    localRemoteTimeOffset = date.getTime() - schedulePosixTime;
}

function parseSourceStatus(obj){
    var live_div = $("#live_dj_div")
    var master_div = $("#master_dj_div")
    var live_li = live_div.parent()
    var master_li = master_div.parent()
    
    if(obj.live_dj_source == false){
        live_li.find(".line-to-switch").attr("class", "line-to-switch off")
        live_div.removeClass("ready")
    }else{
        live_li.find(".line-to-switch").attr("class", "line-to-switch on")
        live_div.addClass("ready")
    }
    
    if(obj.master_dj_source == false){
        master_li.find(".line-to-switch").attr("class", "line-to-switch off")
        master_div.removeClass("ready")
    }else{
        master_li.find(".line-to-switch").attr("class", "line-to-switch on")
        master_div.addClass("ready")
    }
}

function parseSwitchStatus(obj){
    
    if(obj.live_dj_source == "on" && obj.master_dj_source == "off"){
        live_dj_on_air = true;
    }else{
        live_dj_on_air = false;
    }
    
    if(obj.master_dj_source == "on"){
        master_dj_on_air = true;
    }else{
        master_dj_on_air = false;
    }
    
    if(obj.scheduled_play == "on"){
        scheduled_play_on_air = true;
    }else{
        scheduled_play_on_air = false;
    }
    
    var scheduled_play_switch = $("#scheduled_play.source-switch-button")
    var live_dj_switch = $("#live_dj.source-switch-button")
    var master_dj_switch = $("#master_dj.source-switch-button")
    
    scheduled_play_switch.find("span").html(obj.scheduled_play)
    if(scheduled_play_on_air){
        scheduled_play_switch.addClass("active")
    }else{
        scheduled_play_switch.removeClass("active")
    }
    
    live_dj_switch.find("span").html(obj.live_dj_source)
    if(live_dj_on_air){
        live_dj_switch.addClass("active")
    }else{
        live_dj_switch.removeClass("active")
    }
    
    master_dj_switch.find("span").html(obj.master_dj_source)
    if(master_dj_on_air){
        master_dj_switch.addClass("active")
    }else{
        master_dj_switch.removeClass("active")
    }
}

function controlOnAirLight(){
    if((scheduled_play_on_air && scheduled_play_source)|| live_dj_on_air || master_dj_on_air){
        $('#on-air-info').attr("class", "on-air-info on");
    }else{
        $('#on-air-info').attr("class", "on-air-info off");
    }
}

function controlSwitchLight(){
    var live_li= $("#live_dj_div").parent()
    var master_li = $("#master_dj_div").parent()
    var scheduled_play_li = $("#scheduled_play_div").parent()
    
    if((scheduled_play_on_air && scheduled_play_source) && !live_dj_on_air && !master_dj_on_air){
        scheduled_play_li.find(".line-to-on-air").attr("class", "line-to-on-air on")
        live_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
        master_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
    }else if(live_dj_on_air && !master_dj_on_air){
        scheduled_play_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
        live_li.find(".line-to-on-air").attr("class", "line-to-on-air on")
        master_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
    }else if(master_dj_on_air){
        scheduled_play_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
        live_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
        master_li.find(".line-to-on-air").attr("class", "line-to-on-air on")
    }else{
        scheduled_play_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
        live_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
        master_li.find(".line-to-on-air").attr("class", "line-to-on-air off")
    }
}

function getScheduleFromServer(){
    $.ajax({ url: "/Schedule/get-current-playlist/format/json", dataType:"json", success:function(data){
                parseItems(data.entries);
                parseSourceStatus(data.source_status);
                parseSwitchStatus(data.switch_status);
          }, error:function(jqXHR, textStatus, errorThrown){}});
    setTimeout(getScheduleFromServer, serverUpdateInterval);
}

function setupQtip(){
    var qtipElem = $('#about-link');

    if (qtipElem.length > 0){
        qtipElem.qtip({
            content: $('#about-txt').html(),
            show: 'mouseover',
            hide: { when: 'mouseout', fixed: true },
            position: {
                corner: {
                    target: 'center',
                    tooltip: 'topRight'
                }
            },
             style: {
                border: {
                   width: 0,
                   radius: 4
                },
                name: 'light' // Use the default light style
             }
        });
    }
}

function setSwitchListener(ele){
    var sourcename = $(ele).attr('id')
    var status_span = $(ele).find("span")
    var status = status_span.html()
    
    $.get("/Dashboard/switch-source/format/json/sourcename/"+sourcename+"/status/"+status, function(data){
        if(data.error){
            alert(data.error);
        }else{
        	if(data.status == "ON"){
        		$(ele).addClass("active");
        	}else{
        		$(ele).removeClass("active");
        	}
            status_span.html(data.status)
        }
    });
}

function kickSource(ele){
    var sourcename = $(ele).attr('id')
    
    $.get("/Dashboard/disconnect-source/format/json/sourcename/"+sourcename, function(data){
        if(data.error){
            alert(data.error);
        }
    });
}

var stream_window = null;

function init() {
    //begin producer "thread"
    getScheduleFromServer();
    
    //begin consumer "thread"
    secondsTimer();

    setupQtip();
    
    $('.listen-control-button').click(function() {
        if (stream_window == null || stream_window.closed)
            stream_window=window.open(baseUrl+"Dashboard/stream-player", 'name', 'width=400,height=178');
        stream_window.focus();
        return false;
    });
}

$(document).ready(function() {
    if ($('#master-panel').length > 0)
        init();
});
