
function isAudioSupported(mime){
    var audio = new Audio();

    var bMime = null;
    if (mime.indexOf("ogg") != -1 || mime.indexOf("vorbis") != -1) {
       bMime = 'audio/ogg; codecs="vorbis"'; 
    } else {
        bMime = mime;
    }

    return !!bMime && !!audio.canPlayType && audio.canPlayType(bMime) != "";
}        
