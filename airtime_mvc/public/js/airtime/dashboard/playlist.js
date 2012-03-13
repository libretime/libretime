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
var airtime_on_air = false;

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
    if (currentSong !== null){
        songPercentDone = (estimatedSchedulePosixTime - currentSong.songStartPosixTime)/currentSong.songLengthMs*100;
        if (songPercentDone < 0 || songPercentDone > 100){
            songPercentDone = 0;        
            currentSong = null;
        } else {
            var airtime_connection = $("#airtime_connection") 
            if (currentSong.media_item_played == "t" && currentShow.length > 0){
                //$('#on-air-info').attr("class", "on-air-info on");
                airtime_connection.attr("class", "source-connection on");
                airtime_on_air = true;
            }
            else{
                //$('#on-air-info').attr("class", "on-air-info off");
                airtime_on_air = false;
                airtime_connection.attr("class", "source-connection off");
            }
            $('#progress-show').attr("class", "progress-show");
        }
    } else {
        $('#on-air-info').attr("class", "on-air-info off");
        $("#airtime_connection").attr("class", "source-connection off");
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
    $('#previous').empty();
    $('#prev-length').empty();
    $('#current').html("Current: <span style='color:red; font-weight:bold'>Nothing Scheduled</span>");
    $('#next').empty();
    $('#next-length').empty();
    if (previousSong !== null){
        $('#previous').text(previousSong.name+",");
        $('#prev-length').text(convertToHHMMSSmm(previousSong.songLengthMs));
    }
    if (currentSong !== null){
        if (currentSong.record == "1")
            $('#current').html("<span style='color:red; font-weight:bold'>Recording: </span>"+currentSong.name+",");
        else
            $('#current').text(currentSong.name+",");
    }

    if (nextSong !== null){
        $('#next').text(nextSong.name+",");
        $('#next-length').text(convertToHHMMSSmm(nextSong.songLengthMs));
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

function changeLineToWhite(div){
    div.css("background", "#FFFFFF")
}

function changeLineToRed(div){
    div.css("background", "#D40000")
}

function ColorOnSourceConnection(div){
    div.attr("class", "source-connection on");
    changeLineToRed(div.parent().find(".line-to-switch"))
}

function ColorOnSourceDisconnection(div){
    div.attr("class", "source-connection off");
    changeLineToWhite(div.parent().find(".line-to-switch"))
}

function parseSourceStatus(obj){
    var live_div = $("#live_dj_connection")
    var master_div = $("#master_dj_connection")
    
    if(obj.live_dj_source == false){
        ColorOnSourceDisconnection(live_div)
    }else{
        ColorOnSourceConnection(live_div)
    }
    
    if(obj.master_dj_source == false){
        ColorOnSourceDisconnection(master_div)
    }else{
        ColorOnSourceConnection(master_div)
    }
}

function parseSwitchStatus(obj){
    var live_div = $("#live_dj_connection")
    var master_div = $("#master_dj_connection")
    
    if(obj.live_dj_source == "on" && obj.master_dj_source == "off"){
        //changeLineToRed(live_div.parent().find(".line-to-on-air"))
        live_dj_on_air = true;
    }else{
        //changeLineToWhite(live_div.parent().find(".line-to-on-air"))
        live_dj_on_air = false;
    }
    
    if(obj.master_dj_source == "on"){
        //changeLineToRed(master_div.parent().find(".line-to-on-air"))
        master_dj_on_air = true;
    }else{
        master_dj_on_air = false;
        //changeLineToWhite(master_div.parent().find(".line-to-on-air"))
    }
    $("#live_dj.source-switch-button").find("span").html(obj.live_dj_source)
    $("#master_dj.source-switch-button").find("span").html(obj.master_dj_source)
}

function controlOnAirLight(){
    if(airtime_on_air || live_dj_on_air || master_dj_on_air){
        $('#on-air-info').attr("class", "on-air-info on");
    }else{
        $('#on-air-info').attr("class", "on-air-info off");
    }
}

function controlSwitchLight(){
    var live_div = $("#live_dj_connection")
    var master_div = $("#master_dj_connection")
    var airtime_div = $("#airtime_connection")
    
    if(airtime_on_air && !live_dj_on_air && !master_dj_on_air){
        changeLineToRed(airtime_div.parent().find(".line-to-on-air"))
        changeLineToWhite(live_div.parent().find(".line-to-on-air"))
        changeLineToWhite(master_div.parent().find(".line-to-on-air"))
    }else if(live_dj_on_air && !master_dj_on_air){
        changeLineToWhite(airtime_div.parent().find(".line-to-on-air"))
        changeLineToRed(live_div.parent().find(".line-to-on-air"))
        changeLineToWhite(master_div.parent().find(".line-to-on-air"))
    }else if(master_dj_on_air){
        changeLineToWhite(airtime_div.parent().find(".line-to-on-air"))
        changeLineToWhite(live_div.parent().find(".line-to-on-air"))
        changeLineToRed(master_div.parent().find(".line-to-on-air"))
    }else{
        changeLineToWhite(airtime_div.parent().find(".line-to-on-air"))
        changeLineToWhite(live_div.parent().find(".line-to-on-air"))
        changeLineToWhite(master_div.parent().find(".line-to-on-air"))
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

function setSwitchListener(){
    $(".source-switch-button").click(function(){
        var sourcename = $(this).attr('id')
        var status_span = $(this).find("span")
        var status = status_span.html()
        var _class = $(this).parent().find("div.source-connection").attr("class")
        var source_connection_status = false
        
        if(_class.indexOf("off") > 0){
            source_connection_status = false
        }else{
            source_connection_status = true
        }
        
        if(source_connection_status){
            $.get("/Dashboard/switch-source/format/json/sourcename/"+sourcename+"/status/"+status, function(data){
                status_span.html(data.status)
            });
        }else{
            alert("The source is not connected to Airtime!")
        }
    })
}

var stream_window = null;

function init() {
    //begin producer "thread"
    getScheduleFromServer();
    
    //begin consumer "thread"
    secondsTimer();

    setupQtip();
    
    setSwitchListener();
    
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
