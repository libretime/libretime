var AIRTIME = (function(AIRTIME) {
	
	if (AIRTIME.playerPreview === undefined) {
        AIRTIME.playerPreview = {};
    }
    var mod = AIRTIME.playerPreview,
    	playlistJPlayer;
    
    function addToPlaylist(data) {
    	var playNow = false;
    	
    	if (playlistJPlayer.playlist.length === 0) {
    		playNow = true;
    	}
    	
    	data.playlist.forEach(function(mediaObject, index, mediaArray) {

    		if (mod.isAudioSupported(mediaObject.mime)) {
    			playlistJPlayer.add(mediaObject, playNow);
        		playNow = false;
    		}
    	});
    }
    
    function fetchMedia(mediaId) {
    	var url = baseUrl+"audiopreview/media-preview";
    	
    	$.get(url, {format: "json", id: mediaId}, addToPlaylist);
    }
    
    mod.previewMedia = function(mediaId) {
    	
    	fetchMedia(mediaId);
    };
    
    mod.isAudioSupported = function(mime){
        var audio = new Audio();

        var bMime = null;
        if (mime.indexOf("ogg") != -1 || mime.indexOf("vorbis") != -1) {
           bMime = 'audio/ogg; codecs="vorbis"'; 
        } else {
            bMime = mime;
        }

        //return a true of the browser can play this file natively, or if the
        //file is an mp3 and flash is installed (jPlayer will fall back to flash to play mp3s).
        //Note that checking the navigator.mimeTypes value does not work for IE7, but the alternative
        //is adding a javascript library to do the work for you, which seems like overkill....
        return (!!audio.canPlayType && audio.canPlayType(bMime) != "") || 
            (mime.indexOf("mp3") != -1 && navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) ||
            (mime.indexOf("mp4") != -1 && navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) ||
            (mime.indexOf("mpeg") != -1 && navigator.mimeTypes ["application/x-shockwave-flash"] != undefined);
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
                shuffleOnLoop: false,
                enableRemoveControls: true,
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