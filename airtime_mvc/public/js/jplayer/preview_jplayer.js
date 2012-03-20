var _playlist_jplayer;
var _idToPostionLookUp;

/**
 *When the page loads the ready function will get all the data it can from the hidden span elements
 *and call one of three functions depending on weather the window was open to play an audio file,
 *or a playlist or a show.
 */
$(document).ready(function(){
        
    _playlist_jplayer = new jPlayerPlaylist({
        jPlayer: "#jquery_jplayer_1",
        cssSelectorAncestor: "#jp_container_1"
    },[], //array of songs will be filled with below's json call
    {
        swfPath: "/js/jplayer",
        supplied:"oga, mp3, m4v",
        size: {
            width: "0px",
            height: "0px",
            cssClass: "jp-video-270p"
        },
        wmode: "window"
    });
    
    
    $.jPlayer.timeFormat.showHour = true;
    
    var audioFileID = $('.audioFileID').text();
    var playlistID = $('.playlistID').text();
    var playlistIndex = $('.playlistIndex').text();
    var showID = $('.showID').text();
    var showIndex = $('.showIndex').text();

    if (playlistID != "" && playlistID !== ""){
        playAllPlaylist(playlistID, playlistIndex);
    }else if (audioFileID != "") {
        playOne(audioFileID);
    }else if (showID != "") {
        playAllShow(showID, showIndex);
    }
});

/**
 * Sets up the jPlayerPlaylist to play.
 *  - Get the playlist info based on the playlistID give.
 *  - Update the playlistIndex to the position in the pllist to start playing.
 *  - Select the element played from and start playing. If playlist is null then start at index 0.
**/
function playAllPlaylist(p_playlistID, p_playlistIndex) {
    var viewsPlaylistID = $('.playlistID').text();
    
    if ( _idToPostionLookUp !== undefined && viewsPlaylistID == p_playlistID ) {
        play(p_playlistIndex);
    }else {
        buildplaylist("/audiopreview/get-playlist/playlistID/"+p_playlistID, p_playlistIndex);
    }
}

/**
 * Sets up the show to play.
 *  checks with the show id given to the show id on the page/view
 *      if the show id and the page or views show id are the same it means the user clicked another
 *          file in the same show, so don't refresh the show content tust play the track from the preloaded show.
 *      if the the ids are different they we'll need to get the show's context so create the uri
 *      and call the controller.
**/
function playAllShow(p_showID, p_index) {
    
    var viewsShowID = $('.showID').text();
    if ( _idToPostionLookUp !== undefined && viewsShowID == p_showID ) {
        play(p_index);
    }else {
        buildplaylist("/audiopreview/get-show/showID/"+p_showID, p_index);
    }
}

/**
 * This function will call the AudiopreviewController to get the contents of either a show or playlist
 * Looping throught the returned contents and creating media for each track.
 *
 * Then trigger the jplayer to play the list.
 */
function buildplaylist(p_url, p_playIndex) {
    _idToPostionLookUp = Array();
    $.getJSON(p_url, function(data){  // get the JSON array produced by my PHP
        var myPlaylist = new Array();
        var media;
        var index;
        for(index in data){
            
            if (data[index]['element_mp3'] != undefined){
                media = {title: data[index]['element_title'],
                        artist: data[index]['element_artist'],
                        mp3:"/api/get-media/fileID/"+data[index]['element_mp3']
                };
            }else if (data[index]['element_oga'] != undefined) {
                media = {title: data[index]['element_title'],
                        artist: data[index]['element_artist'],
                        oga:"/api/get-media/fileID/"+data[index]['element_oga']
                };
            }
            myPlaylist[index] = media;
            
            _idToPostionLookUp[data[index]['element_id']] = data[index]['element_position'];
        }
        
        _playlist_jplayer.setPlaylist(myPlaylist);
        _playlist_jplayer.option("autoPlay", true);
        play(p_playIndex);
    });
}

/**
 *Function simply plays the given index, for playlists index can be different so need to look up the
 *right index.
 */
function play(p_playlistIndex){
    playlistIndex = _idToPostionLookUp[p_playlistIndex];
    //_playlist_jplayer.select(playlistIndex);
    _playlist_jplayer.play(playlistIndex);
}

/**
 * Playing one audio track occurs from the library. This function will create the media, setup
 * jplayer and play the track.
 */
function playOne(p_audioFileID) {
    var playlist = new Array();
    var fileExtensioin = p_audioFileID.split('.').pop();
    console.log(p_audioFileID);
    if (fileExtensioin === 'mp3') {
        media = {title: $('.audioFileTitle').text() !== 'null' ?$('.audioFileTitle').text():"",
            artist: $('.audioFileArtist').text() !== 'null' ?$('.audioFileArtist').text():"",
            mp3:"/api/get-media/fileID/"+p_audioFileID
        };
    }else if (fileExtensioin === 'ogg' ) {
        media = {title: $('.audioFileTitle').text() != 'null' ?$('.audioFileTitle').text():"",
            artist: $('.audioFileArtist').text() != 'null' ?$('.audioFileArtist').text():"",
            oga:"/api/get-media/fileID/"+p_audioFileID
        };
    }
    _playlist_jplayer.option("autoPlay", true);
    console.log(media);
    playlist[0] = media;
    //_playlist_jplayer.setPlaylist(playlist); --if I use this the player will call _init on the setPlaylist and on the ready
    _playlist_jplayer._initPlaylist(playlist);
    _playlist_jplayer.play(0);
}