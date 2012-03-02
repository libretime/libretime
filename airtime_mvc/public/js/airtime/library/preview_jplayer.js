

function audioPreview(filename, elemID){
    console.log("in the audio preview");
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
        supplied: supplied
    });

    $('#'+elemID+' div.list-item-container a span').attr("class", "ui-icon ui-icon-pause");
}

$(document).ready(function(){
    var filename = $(".filename").text();
    play(filename);
});

function play(filename){
    var uri = "/api/get-media/file/" + filename;
    var ext = getFileExt(filename);
    
    
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

