$(document).ready(function() {

    $("#Panel").stickyPanel({
	    topPadding: 1,
	    afterDetachCSSClass: "floated-panel",
	    savePanelSpace: true
    });
});

function adjustDateToServerDate(date, serverTimezoneOffset){
    //date object stores time in the browser's localtime. We need to artificially shift 
    //it to 
    var timezoneOffset = date.getTimezoneOffset()*60*1000;
    
    date.setTime(date.getTime() + timezoneOffset + serverTimezoneOffset*1000);
    
    /* date object has been shifted to artificial UTC time. Now let's
     * shift it to the server's timezone */
    return date;
}

/**
 *handle to the jplayer window
 */
var _preview_window = null;

/**
 *Gets the info from the view when menu action play choosen and opens the jplayer window.
 */
function openAudioPreview(p_event) {
    p_event.stopPropagation();
    
    var audioFileID = $(this).attr('audioFile');
    var playlistID = $('#pl_id:first').attr('value');
    var playlistIndex = $(this).parent().parent().attr('id');
    playlistIndex = playlistIndex.substring(4); //remove the spl_
    
    open_playlist_preview(playlistID, playlistIndex);
}

function open_audio_preview(audioFileID, audioFileTitle, audioFileArtist) {
    openPreviewWindow('audiopreview/audio-preview/audioFileID/'+audioFileID+'/audioFileArtist/'+audioFileArtist+'/audioFileTitle/'+audioFileTitle);
    _preview_window.focus();
}
/**
 *Opens a jPlayer window for the specified info, for either an audio file or playlist.
 *If audioFile, audioFileTitle, audioFileArtist is supplied the jplayer opens for one file
 *Otherwise the playlistID and playlistIndex was supplied and a playlist is played starting with the
 *given index.
 */
function open_playlist_preview(p_playlistID, p_playlistIndex) {
    if (p_playlistIndex == undefined) //Use a resonable default.
        p_playlistIndex = 0;
    
    
    if (_preview_window != null && !_preview_window.closed)
        _preview_window.playAllPlaylist(p_playlistID, p_playlistIndex);
    else
        openPreviewWindow('audiopreview/playlist-preview/playlistIndex/'+p_playlistIndex+'/playlistID/'+p_playlistID);
    _preview_window.focus();
}

/**
 *Opens a jPlayer window for the specified info, for either an audio file or playlist.
 *If audioFile, audioFileTitle, audioFileArtist is supplied the jplayer opens for one file
 *Otherwise the playlistID and playlistIndex was supplied and a playlist is played starting with the
 *given index.
 */
function open_show_preview(p_showID, p_showIndex) {
    if (_preview_window != null && !_preview_window.closed)
        _preview_window.playAllShow(p_showID, p_showIndex);
    else 
        openPreviewWindow('audiopreview/show-preview/showID/'+p_showID+'/showIndex/'+p_showIndex);
    _preview_window.focus();
}

function openPreviewWindow(url) {
    //$.post(baseUri+'Playlist/audio-preview-player', {fileName: fileName, cueIn: cueIn, cueOut: cueOut, fadeIn: fadeIn, fadeInFileName: fadeInFileName, fadeOut: fadeOut, fadeOutFileName: fadeOutFileName})
    _preview_window = window.open(url, 'Audio Player', 'width=450,height=800');
    //Set the play button to pause.
    //var elemID = "spl_"+elemIndexString;
    //$('#'+elemID+' div.list-item-container a span').attr("class", "ui-icon ui-icon-pause");
    return false;
}