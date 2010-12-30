(function($) {
    // jQuery plugin definition
    $.fn.playlistViewer = function(params) {
        var cc = this;
        cc.estimatedSchedulePosixTime = -1;
        cc.schedulePosixTime;

        cc.previousSongs;
        cc.currentSong;
        cc.nextSongs;
        
        cc.currentElem;

        // traverse all nodes
        return this.each(function() {
            // express a single node as a jQuery object
            cc.currentElem = $(this);
            
            var prevDiv = document.createElement('div');
            prevDiv.setAttribute("id", "previous");
            $(cc.currentElem).append(prevDiv);
            
            var currDiv = document.createElement('div');
            currDiv.setAttribute("id", "current");
            $(cc.currentElem).append(currDiv);
        
            var nextDiv = document.createElement('div');
            nextDiv.setAttribute("id", "next");
            $(cc.currentElem).append(nextDiv);
            
            $('#spaceused1').progressBar(0);

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

        function secondsTimer(){
            cc.estimatedSchedulePosixTime += 1000;
            updateProgressBarValue();
        }

        function updateProgressBarValue(){
            if (cc.estimatedSchedulePosixTime != -1){
                if (cc.currentSong.length > 0){
                    var percentDone = (cc.estimatedSchedulePosixTime - cc.currentSong[0].songStartPosixTime)/cc.currentSong[0].songLengthMs*100;
                    if (percentDone <= 100){
                        $('#spaceused1').progressBar(percentDone);
                    } else {
                        if (nextSongs.length > 0){
                            cc.currentSong[0] = cc.nextSongs.shift();
                        } else {
                            cc.currentSong = new Array();
                        }
                        $('#spaceused1').progressBar(0);
                        cc.estimatedSchedulePosixTime = cc.schedulePosixTime;
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
            for (var i=0; i<cc.previousSongs.length; i++){
                var divElem = document.createElement('div');
                divElem.innerHTML = createPlaylistElementString(cc.previousSongs[i]);
                $('#previous').append(divElem);
                //cc.currentElem.html(createPlaylistElementString(previousSongs[i]));
            }
            for (var i=0; i<cc.currentSong.length; i++){
                var divElem = document.createElement('div');
                divElem.innerHTML = createPlaylistElementString(cc.currentSong[i]);
                $('#current').append(divElem);
                //cc.currentElem.html(createPlaylistElementString(currentSong[i]));
            }
            for (var i=0; i<cc.nextSongs.length; i++){
                var divElem = document.createElement('div');
                divElem.innerHTML = createPlaylistElementString(cc.nextSongs[i]);
                $('#next').append(divElem);
                //cc.currentElem.html(createPlaylistElementString(nextSongs[i]));
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
            cc.schedulePosixTime = convertDateToPosixTime(obj.schedulerTime);

            if (cc.estimatedSchedulePosixTime == -1)
                cc.estimatedSchedulePosixTime = cc.schedulePosixTime;

            cc.previousSongs = obj.previous;
            cc.currentSong = obj.current;
            cc.nextSongs = obj.next;

            calcAdditionalData(cc.previousSongs);
            calcAdditionalData(cc.currentSong);
            calcAdditionalData(cc.nextSongs);
        }

        function getScheduleFromServer(){
            $.ajax({ url: "http://localhost/Schedule/get-current-playlist/format/json", dataType:"json", success:function(data){
                    parseItems(data.entries);
                  }});
            setTimeout(getScheduleFromServer, 5000);
        }

    };
})(jQuery);
