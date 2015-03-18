window.onload = function() {
    document.getElementById('player_display_track_metadata').onchange = generateEmbedSrc;
}

function generateEmbedSrc()
{
    document.getElementById('embed_player_preview').textContent="";
}

