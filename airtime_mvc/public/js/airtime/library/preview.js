var audio_preview_window_p = null;

function playlistAudioPreviewEditor(filename, elemIndexString){
    
   elemIndex =parseInt(elemIndexString)+1;//increment the index as tags start from 1 not 0
    
   var cueIn = $("dd[id^=spl_cue_in_"+elemIndex+"]").find('span').html();
   console.log(cueIn);
   
   var cueOut = $("dd[id^=spl_cue_out_"+elemIndex+"]").find('span').html();
   console.log("The cueOut is "+cueOut);
   
   var fadeIn = $("dd[id^=spl_fade_in_"+elemIndex+"]").find('span').html();
   if (fadeIn == undefined){ console.log("undefined fadein");  fadeIn = $("dd[id^=spl_fade_in_main]").find('span').html();}
   console.log("The fadeIn is "+fadeIn);
   
   var fadeInFileName = "";
   if (fadeIn != undefined && parseInt(fadeIn) > 0 ){
      //need to get the previous element in the playlist...but don't support previous playlist fading becuase thats not possible.
      
   }   
   console.log("The fadeInFileName is "+fadeInFileName);
   
   var fadeOut = $("dd[id^=spl_fade_out_"+elemIndex+"]").find('span').html();
   if (fadeOut == undefined){ console.log("undefined fadeout"); fadeOut = $("dd[id^=spl_fade_out_main]").find('span').html();}
   console.log("The fadeOut is "+fadeOut);
   
   var fadeOutFileName = "";
   if (fadeOut != undefined && parseInt(fadeOut) > 0 ){
      //need to get the next element in the playlist...but don't support next playlist fading becuase thats not possible.
      
   }
   console.log("The fadeOutFileName is "+fadeOutFileName);
   
   //Pop out a play list with cue in and cue out set.
   open_player();
   
   //Set the play button to pause.
   var elemID = "spl_"+elemIndexString;
   $('#'+elemID+' div.list-item-container a span').attr("class", "ui-icon ui-icon-pause");

}

function open_audio_preview_old(filename, index) {
   console.log("hello world 2 "+filename+" help?");
   url = 'Playlist/audio-preview-player/filename/'+filename+'/index/'+index;
   //$.post(baseUri+'Playlist/audio-preview-player', {fileName: fileName, cueIn: cueIn, cueOut: cueOut, fadeIn: fadeIn, fadeInFileName: fadeInFileName, fadeOut: fadeOut, fadeOutFileName: fadeOutFileName})
   if (audio_preview_window == null || audio_preview_window.closed){
      console.log("opening : "+baseUrl+url);
      
      audio_preview_window = window.open(url, 'Audio Player', 'width=400,height=95');

   } else if (!audio_preview_window.closed) {
      console.log("refreshing : "+baseUrl+url);      
      audio_preview_window.play(filename);
    } else {
      console.log("something else : "+baseUrl+url);
    }
    
   //Set the play button to pause.
   var elemID = "spl_"+elemIndexString;
   $('#'+elemID+' div.list-item-container a span').attr("class", "ui-icon ui-icon-pause");
   
    return false;
}

$('#library_type').bind('click', function(){
   console.log(data);
});