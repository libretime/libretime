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

function convertSecondsToDaysHoursMinutesSeconds(seconds){
    if (seconds < 0)
        seconds = 0;
    
    seconds = parseInt(seconds, 10);

    var days = parseInt(seconds / 86400);
    seconds -= days*86400;

    var hours = parseInt(seconds / 3600);
    seconds -= hours*3600;

    var minutes = parseInt(seconds / 60);
    seconds -= minutes*60;

    return {days:days, hours:hours, minutes:minutes, seconds:seconds}; 
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

function audioStream(){

    if ($("#jquery_jplayer_1").data("jPlayer") && $("#jquery_jplayer_1").data("jPlayer").status.paused != true){
        $('#jquery_jplayer_1').jPlayer('clearMedia');
        $('#jquery_jplayer_1').jPlayer('destroy');
        return;
    }
    
    var uri = "http://localhost:8000/airtime_128.ogg";
    var ext = getFileExt(uri);
    
    var media;
    var supplied;
    if (ext == "ogg"){
        media = {oga:uri};
        supplied = "oga";
    } else {
        media = {mp3:uri};
        supplied = "mp3";
    }

    $("#jquery_jplayer_1").jPlayer({
        ready: function () {
            $(this).jPlayer("setMedia", media).jPlayer("play");
        },
        swfPath: "/js/jplayer",
        supplied: supplied
    });
}

function playlistAudioPreviewEditor(filename, elemIndexString){
    
    elemIndex =parseInt(elemIndexString)+1;//increment the index as tags start from 1 not 0
    console.log("hello world with index "+elemIndex);
  
   
   var cueIn = $("dd[id^=spl_cue_in_"+elemIndex+"]").find('span').html();
   console.log(cueIn);
   
   var cueOut = $("dd[id^=spl_cue_out_"+elemIndex+"]").find('span').html();
   console.log("The cueOut is "+cueOut);
   
   var fadeIn = $("dd[id^=spl_fade_in_"+elemIndex+"]").find('span').html();
   if (fadeIn == undefined){ console.log("undefined fadein");  fadeIn = $("dd[id^=spl_fade_in_main]").find('span').html();}
   console.log("The fadeIn is "+fadeIn);
   
   var fadeInFileName = "";
   if (fadeIn != undefined && parseInt(fadeIn) > 0 ){
      //need to get the previous element in the playlist...but don't support previous playlist fading becuase thats not possible.
      
   }   
   console.log("The fadeInFileName is "+fadeInFileName);
   
   var fadeOut = $("dd[id^=spl_fade_out_"+elemIndex+"]").find('span').html();
   if (fadeOut == undefined){ console.log("undefined fadeout"); fadeOut = $("dd[id^=spl_fade_out_main]").find('span').html();}
   console.log("The fadeOut is "+fadeOut);
   
   var fadeOutFileName = "";
   if (fadeOut != undefined && parseInt(fadeOut) > 0 ){
      //need to get the next element in the playlist...but don't support next playlist fading becuase thats not possible.
      
   }
   console.log("The fadeOutFileName is "+fadeOutFileName);
   
   //Pop out a play list with cue in and cue out set.
   console.log(baseUrl+"Dashboard/audio-preview-player");
   //window.open(baseUrl+"Dashboard/audio-preview-player", "music player", "width=200,height=200");
   event.preventDefault();
   
   //Set the play button to pause.
   var elemID = "spl_"+elemIndexString;
   $('#'+elemID+' div.list-item-container a span').attr("class", "ui-icon ui-icon-pause");

}

function audioPreview(filename, elemID){

    var elems = $('.ui-icon.ui-icon-pause');
    elems.attr("class", "ui-icon ui-icon-play");

    if ($("#jquery_jplayer_1").data("jPlayer") && $("#jquery_jplayer_1").data("jPlayer").status.paused != true){
         $('#jquery_jplayer_1').jPlayer('stop');
        return;
    }

    var ext = getFileExt(filename);
    var uri = "/api/get-media/file/" + filename;
    
    var media;
    var supplied;
    if (ext == "ogg"){
        media = {oga:uri};
        supplied = "oga";
    } else {
        media = {mp3:uri};
        supplied = "mp3";
    }

    $("#jquery_jplayer_1").jPlayer("destroy");
    $("#jquery_jplayer_1").jPlayer({
        ready: function () {
            $(this).jPlayer("setMedia", media).jPlayer("play");
        },
        swfPath: "/js/jplayer",
        supplied: supplied,
        wmode:"window"
    });

    $('#'+elemID+' div.list-item-container a span').attr("class", "ui-icon ui-icon-pause");
}

function resizeImg(ele, targetWidth, targetHeight){
    var img = $(ele);

    var width = ele.width;
    var height = ele.height;

    // resize img proportionaly
    if( width > height && width > targetWidth){
        var ratio = targetWidth/width;
        img.css("width", targetHeight+"px");
        var newHeight = height * ratio;
        img.css("height", newHeight+"px");
    }else if( width < height && height > targetHeight){
        var ratio = targetHeight/height;
        img.css("height", targetHeight+"px");
        var newWidth = width * ratio;
        img.css("width", newWidth+"px");
    }else if( width == height && width > targetWidth){
        img.css("height", targetHeight+"px");
        img.css("width", targetWidth+"px" );
    }
}

function resizeToMaxHeight(ele, targetHeight){
    var img = $(ele);

    var width = ele.width;
    var height = ele.height;

    // resize img proportionaly
    if( height > targetHeight){
        var ratio = targetHeight/height;
        img.css("height", targetHeight+"px");
        var newWidth = width * ratio;
        img.css("width", newWidth+"px");
    }
}
