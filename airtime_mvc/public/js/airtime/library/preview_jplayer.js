$(document).ready(function(){
    var fileID = $(".fileID").text();
    play(fileID);
});

function play(fileID){
    var uri = "/api/get-media/fileID/" + fileID;
    var ext = getFileExt(fileID);
    
    
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
    $.jPlayer.timeFormat.showHour = true;
    $("#jquery_jplayer_1").jPlayer({
        ready: function () {
            
            $(this).jPlayer("setMedia", media).jPlayer("play");
            
        },
        swfPath: "/js/jplayer",
        cssSelectorAncestor: '#jp_container_1',
        wmode: "window"
    });
    
}

