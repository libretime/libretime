function generateEmbedSrc()
{
    document.getElementById('embed_player_preview').textContent="";
}

function setupPlayer()
{
    MRP.insert({
        'url':"http://127.0.0.1:8000/airtime_128",
        'codec':"mp3",
        'volume':100,
        'jsevents':true,
        'autoplay':false,
        'buffering':5,
        'title':'test',
        'bgcolor':'#FFFFFF',
        'skin':-1,
        'width':180,
        'height':60
    });
}

window.onload = function() {
    setupPlayer();
    document.getElementById('player_display_track_metadata').onchange = generateEmbedSrc;
    document.getElementById('muses_play').click = MRP.play();
    document.getElementById('muses_stop').click = MRP.stop();
}
