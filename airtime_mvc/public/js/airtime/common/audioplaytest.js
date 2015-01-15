
function isAudioSupported(mime){
    var audio = new Audio();

    var bMime = null;
    if (mime.indexOf("ogg") != -1 || mime.indexOf("vorbis") != -1) {
       bMime = 'audio/ogg; codecs="vorbis"'; 
    } else {
        bMime = mime;
    }

    //return a true of the browser can play this file natively, or if the
    //file is an mp3 and flash is installed (jPlayer will fall back to flash to play mp3s).
    //Note that checking the navigator.mimeTypes value does not work for IE7, but the alternative
    //is adding a javascript library to do the work for you, which seems like overkill....
    return (!!audio.canPlayType && audio.canPlayType(bMime) != "") || 
        (mime.indexOf("mp3") != -1 && navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) ||
        (mime.indexOf("mp4") != -1 && navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) ||
        (mime.indexOf("mpeg") != -1 && navigator.mimeTypes ["application/x-shockwave-flash"] != undefined);
}        
