(function($) {  
    // jQuery plugin definition  
    $.fn.playlistViewer = function(params) {  
        // merge default and user parameters  
        params = $.extend( {minlength: 0, maxlength: 99999}, params);  


    var $t;

    // traverse all nodes  
    return this.each(function() {
        // express a single node as a jQuery object  
        $t = $(this);  

        getScheduleFromServer();
        updateProgressBarValue();             
    });  

    function convertDateToPosixTime(s){
        var year = s.substring(0, 4);
        var month = s.substring(5, 7);
        var day = s.substring(8, 10);
        var hour = s.substring(11, 13);
        var minute = s.substring(14, 16);
        var sec = 0;
        var msec = 0;
        if (s.length >= 20){
        	sec = s.substring(17, 19);
			msec = s.substring(20);
		} else {
			sec = s.substring(17);
		}
		
        return Date.UTC(year, month, day, hour, minute, sec, msec);
    }

    var estimatedSchedulePosixTime = -1;
    var schedulePosixTime;

	var previousSongs;
    var currentSong;
    var nextSongs;
    
    function secondsTimer(){
        estimatedSchedulePosixTime += 1000;
        updateProgressBarValue();
    }

    function updateProgressBarValue(){
        alert(estimatedSchedulePosixTime);
    	if (estimatedSchedulePosixTime != -1){
	    	if (currentSong.length > 0){
		        var percentDone = (estimatedSchedulePosixTime - currentSong[0].songStartPosixTime)/currentSong[0].songLengthMs*100;
		        if (percentDone <= 100){
		            //$('#spaceused1').progressBar(percentDone);
		        } else {
		        	if (nextSongs.length > 0){
		            	currentSong[0] = nextSongs.shift();
		            } else {
		            	currentSong = new Array();
		            }
		            //$('#spaceused1').progressBar(0);
		            estimatedSchedulePosixTime = schedulePosixTime;
		        }
	        }
	        updatePlaylist();
	    }
        setTimeout(secondsTimer, 1000);
    }
    
    function createPlaylistElementString(song){
  		return "Start time: " + song.starts + "<br>" +
    			"End time: " + song.ends + "<br>" + 
    			"Clip length: " + song.clip_length + "<br>" + 
    			"Name: " + song.name + "<br>";   	
    }
    
    function updatePlaylist(){
    	$('#previous').empty();
    	$('#current').empty();
    	$('#next').empty();
    	for (var i=0; i<previousSongs.length; i++){
    		//var divElem = document.createElement('div');
    		//divElem.innerHTML = createPlaylistElementString(previousSongs[i]);
            $t.text(createPlaylistElementString(previousSongs[i])); 
    		//$('#previous').append(divElem);
    	}
     	for (var i=0; i<currentSong.length; i++){
    		//var divElem = document.createElement('div');
    		//divElem.innerHTML = createPlaylistElementString(currentSong[i]);
    		//$('#current').append(divElem);
            $t.text(createPlaylistElementString(currentSong[i]));
    	}    
    	for (var i=0; i<nextSongs.length; i++){
    		//var divElem = document.createElement('div');
    		//divElem.innerHTML = createPlaylistElementString(nextSongs[i]);
    		//$('#next').append(divElem);
            $t.text(createPlaylistElementString(nextSongs[i]));
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
        
        //updatePlaylist();
        //updateProgressBarValue();
    }

    function getScheduleFromServer(){
        $.ajax({ url: "http://localhost/Schedule/get-current-playlist/format/json", dataType:"json", success:function(data){
                parseItems(data.entries);
              }});
        setTimeout(getScheduleFromServer, 5000);
    }
 
    };  
})(jQuery);  
