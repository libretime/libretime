var AIRTIME = (function(AIRTIME) {
	
	if (AIRTIME.playerPreview === undefined) {
        AIRTIME.playerPreview = {};
    }
    var mod = AIRTIME.playerPreview,
    	playlistJPlayer;
    
    function changePlaylist(data) {
    	
    	if (data.hasDuration) {
    		$(".jp-duration").show();
    	}
    	else {
    		$(".jp-duration").hide();
    	}
    	
    	playlistJPlayer.setPlaylist(data.playlist);
    	//playlistJPlayer.option("autoPlay", true);
    	playlistJPlayer.play(0);
    }
    
    function fetchMedia(mediaId) {
    	var url = baseUrl+"audiopreview/media-preview";
    	
    	$.get(url, {format: "json", id: mediaId}, changePlaylist);
    }
    
    mod.previewMedia = function(mediaId) {
    	
    	fetchMedia(mediaId);
    };
    
    mod.initPlayer = function() {
    	
    	$.jPlayer.timeFormat.showHour = true;

    	playlistJPlayer = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_1",
            cssSelectorAncestor: "#jp_container_1"
        },
        [], //array of songs will be filled with json call
        {
            swfPath: baseUrl+"js/jplayer",
            supplied: "mp3, oga, m4a, wav, flac",
            //solution: "flash, html",
            preload: "none",
            wmode: "window",
            size: {
                width: "0px",
                height: "0px",
                cssClass: "jp-video-270p"
            },
            playlistOptions: {
                autoPlay: false,
                loopOnPrevious: false,
                shuffleOnLoop: true,
                enableRemoveControls: false,
                displayTime: 0,
                addTime: 0,
                removeTime: 0,
                shuffleTime: 0
            },
            ready: function(e) {
            	console.log("ready");
            	console.log(e);
            },
            error: function(e) {
            	console.log("error");
            	console.error(e);
            },
            play: function(e) {
            	var title = e.jPlayer.status.media.title,
            		artist = e.jPlayer.status.media.artist,
            		html;
            	
            	html = title + " <span class='jp-artist'>" + artist + "</span>";
            	
            	$(".jp-current").html(html);
            }
        });
    	

    	$( "#open_playlist" ).click(function() {
    	  $(".jp-playlist").toggleClass( "open" );
    	  $( this ).toggleClass( "selected" );
    	});

    };
    
return AIRTIME;
	
}(AIRTIME || {}));

$(document).ready(AIRTIME.playerPreview.initPlayer);