

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
    //var filename = $(".jp_audio_0").attr("src");
    play(filename);
    
    
});

function play(filename){
   console.log("in the play function! "+filename);
    
    var uri = "/api/get-media/file/" + filename+"/api_key/H7CSH1RH1YH2W3KFAKCZ";
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
    /**
    $("#jquery_jplayer_1").jPlayer().bind($.jPlayer.event.play, function(event){
        console.log("playing xxx");
        //console.log(this.htmlElement.media.currentTime)
        //$("#jquery_jplayer_1").jPlayer("playHead", event.jPlayer.status.seekPercent);
    });
    $("#jquery_jplayer_1").jPlayer().bind($.jPlayer.event.seeking, function(event){
        console.log("hello 123");
        //console.log(this.htmlElement.media.currentTime)
        //$("#jquery_jplayer_1").jPlayer("playHead", event.jPlayer.status.seekPercent);
    });
    $("#jquery_jplayer_1").jPlayer().bind($.jPlayer.event.seeked, function(event){
        console.log("hello 456");
        //console.log(this.htmlElement.media.currentTime)
        //$("#jquery_jplayer_1").jPlayer("playHead", event.jPlayer.status.seekPercent);
    });
    $("#jquery_jplayer_1").jPlayer().bind($.jPlayer.event.volumechange, function(event){
        console.log("hello 666");
        //console.log(this.htmlElement.media.currentTime)
        //$("#jquery_jplayer_1").jPlayer("playHead", event.jPlayer.status.seekPercent);
    });
    $(".jp-seek-bar").click(function(){
        console.log("seek bar clicked");
        console.log($("#currentTime"));
        //console.log(this.htmlElement.media.seekable)
        //console.log($(".jp-play-bar").attr("style"));
        //$("#jquery_jplayer_1").jPlayer("play", 40);
    });
    
     $(".jp-seek-bar").click(function(){
        console.log("hi");
        //console.log(this.htmlElement.media.seekable)
        //console.log($(".jp-play-bar").attr("style"));
        //$("#jquery_jplayer_1").jPlayer("playHead", "50%");
    });
    $(".jp-play-bar").click(function(){
        console.log("bye");
    });
    $(".jp-progres").click(function(){
        console.log("no");
    });
    $("#combo-box").change(function(eventObject){
        var elem = $("#combo-box option:selected");
        setjPlayer(elem.attr("data-url"), elem.attr("data-type"), elem.attr("server-type"));
    });
    **/
    
    
}

