$(document).ready(function() {

    /* Removed as this is now (hopefully) unnecessary */
    //$("#Panel").stickyPanel({
    //    topPadding: 1,
    //    afterDetachCSSClass: "floated-panel",
    //    savePanelSpace: true
    //});

    //this statement tells the browser to fade out any success message after 5 seconds
    setTimeout(function(){$(".success").fadeOut("slow", function(){$(this).empty()});}, 5000);
});

/*
 * i18n_months and i18n_days_short are used in jquery datepickers
 * which we use in multiple places
 */
var i18n_months = [
    $.i18n._("January"),
    $.i18n._("February"),
    $.i18n._("March"),
    $.i18n._("April"),
    $.i18n._("May"),
    $.i18n._("June"),
    $.i18n._("July"),
    $.i18n._("August"),
    $.i18n._("September"),
    $.i18n._("October"),
    $.i18n._("November"),
    $.i18n._("December")
];

var i18n_months_short = [
    $.i18n._("Jan"),
    $.i18n._("Feb"),
    $.i18n._("Mar"),
    $.i18n._("Apr"),
    $.i18n._("May"),
    $.i18n._("Jun"),
    $.i18n._("Jul"),
    $.i18n._("Aug"),
    $.i18n._("Sep"),
    $.i18n._("Oct"),
    $.i18n._("Nov"),
    $.i18n._("Dec")
];

var i18n_days_short = [
    $.i18n._("Su"),
    $.i18n._("Mo"),
    $.i18n._("Tu"),
    $.i18n._("We"),
    $.i18n._("Th"),
    $.i18n._("Fr"),
    $.i18n._("Sa")
];

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
    var objId = $('#obj_id:first').attr('value');
    var objType = $('#obj_type:first').attr('value');
    var playIndex = $(this).parent().parent().attr('id');
    playIndex = playIndex.substring(4); //remove the spl_
    
    if (objType == "playlist") {
        open_playlist_preview(objId, playIndex);
    } else if (objType == "block") {
        open_block_preview(objId, playIndex);
    }
}

function open_audio_preview(type, id, audioFileTitle, audioFileArtist) {
    // we need to remove soundcloud icon from audioFileTitle
    var index = audioFileTitle.indexOf("<span class=");
    if(index != -1){
        audioFileTitle = audioFileTitle.substring(0,index);
    }
    // The reason that we need to encode artist and title string is that
    // sometime they contain '/' or '\' and apache reject %2f or %5f
    // so the work around is to encode it twice.
    openPreviewWindow(baseUrl+'audiopreview/audio-preview/audioFileID/'+id+'/audioFileArtist/'+encodeURIComponent(encodeURIComponent(audioFileArtist))+'/audioFileTitle/'+encodeURIComponent(encodeURIComponent(audioFileTitle))+'/type/'+type);
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
        openPreviewWindow(baseUrl+'audiopreview/playlist-preview/playlistIndex/'+p_playlistIndex+'/playlistID/'+p_playlistID);
    _preview_window.focus();
}

function open_block_preview(p_blockId, p_blockIndex) {
    if (p_blockIndex == undefined) //Use a resonable default.
        p_blockIndex = 0;
    
    if (_preview_window != null && !_preview_window.closed)
        _preview_window.playBlock(p_blockId, p_blockIndex);
    else
        openPreviewWindow(baseUrl+'audiopreview/block-preview/blockIndex/'+p_blockIndex+'/blockId/'+p_blockId);
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
        openPreviewWindow(baseUrl+'audiopreview/show-preview/showID/'+p_showID+'/showIndex/'+p_showIndex);
    _preview_window.focus();
}

function openPreviewWindow(url) {
    _preview_window = window.open(url, $.i18n._('Audio Player'), 'width=450,height=100,scrollbars=yes');
    return false;
}

function pad(number, length) {
    return sprintf("%'0"+length+"d", number);
}

function removeSuccessMsg() {
    var $status = $('.success');
    
    $status.fadeOut("slow", function(){$status.empty()});
}
