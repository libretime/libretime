
function isAudioSupported(mime){
    var audio = new Audio();

    var bMime = null;
    if (mime.indexOf("ogg") != -1 || mime.indexOf("vorbis") != -1) {
       bMime = 'audio/ogg; codecs="vorbis"'; 
    } else if (mime.indexOf("mp3") != -1) {
        bMime = "audio/mp3";
    } else if (mime.indexOf("mp4") != -1) {
        bMime = "audio/mp4";
    } else if (mime.indexOf("flac") != -1) {
        bMime = "audio/x-flac";
    }

    return !!bMime && !!audio.canPlayType && audio.canPlayType(bMime) != "";
}        
