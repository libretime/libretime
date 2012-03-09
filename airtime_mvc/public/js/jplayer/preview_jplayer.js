var playlist_jplayer;
var idToPostionLookUp;

$(document).ready(function(){
    var audioFileID = $('.audioFileID').text();
    var playlistID = $('.playlistID').text();
    var playlistIndex = $('.playlistIndex').text();
    console.log('in the ready');
    playlist_jplayer = new jPlayerPlaylist({
        jPlayer: "#jquery_jplayer_1",
        cssSelectorAncestor: "#jp_container_1"
    },[], //array of songs will be filled with below's json call
    {
        swfPath: "/js/jplayer",
        supplied: "mp3, oga",
        wmode: "window"
    });
    
    $.jPlayer.timeFormat.showHour = true;
    
    if (playlistID != undefined && playlistID !== "")
        playAll(playlistID, playlistIndex);
    else
        playOne(audioFileID);
    
});

/**
 * Sets up the jPlayerPlaylist to play.
 *  - Get the playlist info based on the playlistID give.
 *  - Update the playlistIndex to the position in the pllist to start playing.
 *  - Select the element played from and start playing. If playlist is null then start at index 0.
**/
function playAll(playlistID, playlistIndex) {
    var viewsPlaylistID = $('.playlistID').text();
    
    if ( idToPostionLookUp !== undefined && viewsPlaylistID == playlistID ) {
        play(playlistIndex);
    }else {
        idToPostionLookUp = Array();
        $.getJSON("/playlist/get-playlist/playlistID/"+playlistID, function(data){  // get the JSON array produced by my PHP
            var myPlaylist = new Array();
            var media;
            var index;
            for(index in data){
                if (data[index]['mp3'] != 'undefined'){
                    media = {title: data[index]['title'],
                            artist: data[index]['artist'],
                            mp3:data[index]['mp3']
                    };
                }else if (data[index]['ogg'] != 'undefined') {
                    media = {title: data[index]['title'],
                            artist: data[index]['artist'],
                            oga:data[index]['ogg']
                    };
                }
                myPlaylist[index] = media;
                
                idToPostionLookUp[data[index]['id']] = data[index]['position'];
            }
            playlist_jplayer.setPlaylist(myPlaylist);
            playlist_jplayer.option("autoPlay", true);
            play(playlistIndex);
        });
    }
}

function play(playlistIndex){
    playlistIndex = idToPostionLookUp[playlistIndex];
    playlist_jplayer.play(playlistIndex);
}

function playOne(audioFileID) {
    var playlist = new Array();
    var fileExtensioin = audioFileID.split('.').pop();
    
    if (fileExtensioin === 'mp3') {
        media = {title: $('.audioFileTitle').text() !== 'null' ?$('.audioFileTitle').text():"",
            artist: $('.audioFileArtist').text() !== 'null' ?$('.audioFileArtist').text():"",
            mp3:"/api/get-media/fileID/"+audioFileID
        };
    }else if (fileExtensioin === 'ogg' ) {
        media = {title: $('.audioFileTitle').text() != 'null' ?$('.audioFileTitle').text():"",
            artist: $('.audioFileArtist').text() != 'null' ?$('.audioFileArtist').text():"",
            oga:"/api/get-media/fileID/"+audioFileID
        };
    }
    playlist[0] = media;

    playlist_jplayer.setPlaylist(playlist);
    playlist_jplayer.option("autoPlay", true);
    playlist_jplayer.play(0);
}