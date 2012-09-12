var audio = new Audio();
supportedAudio = {};
supportedAudio["audio/ogg"] = !!audio.canPlayType && audio.canPlayType('audio/ogg; codecs="vorbis"') != "";
supportedAudio["audio/mp3"] = !!audio.canPlayType && audio.canPlayType('audio/mp3') != "";
supportedAudio["audio/mp4"] = !!audio.canPlayType && audio.canPlayType('audio/mp4') != "";
supportedAudio["audio/x-flac"] = !!audio.canPlayType && audio.canPlayType('audio/x-flac') != "";

function isAudioSupported(mime){
    return mime in supportedAudio && supportedAudio[mime];
}        
