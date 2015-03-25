function updateEmbedSrcParams()
{
    var $embedCodeParams = "?";
    var $streamMode = getStreamMode();
    if ($streamMode == "manual") {
        var $stream = $("input[name=player_stream_url]:radio:checked").val();
        $embedCodeParams += "stream="+$stream;
    } else if ($streamMode == "auto") {
        $embedCodeParams += "stream=auto";
    }
    $embedCodeParams += "\"";

    $("input[name=player_embed_src]").val(function(index, value) {
        return value.replace(/\?.*?"/, $embedCodeParams);
    });

    updatePlayerIframeSrc($("input[name=player_embed_src]").val());
}

function updatePlayerIframeSrc(iframe_text) {
    var $player_iframe = $("#player_form iframe");
    var player_iframe_src = iframe_text.match(/http.*?"/)[0].slice(0, -1);
    $player_iframe.attr('src', player_iframe_src);
}

function getStreamMode() {
    return $("input[name=player_stream_mode]:radio:checked").val();
}

$(document).ready(function() {

    $("#player_stream_mode-element").change(function() {
        var $streamMode = getStreamMode();
        if ($streamMode == "auto") {
            $("#player_stream_url-element input[type='radio']").attr("disabled", "disabled");
        } else if ($streamMode == "manual") {
            $("#player_stream_url-element input[type='radio']").removeAttr("disabled");

            $("input[name=player_stream_url]").each(function(i, obj) {
                if ($(this).parent().text().toLowerCase().indexOf("opus") >= 0) {
                    $(this).attr("disabled", "disabled");
                }
            });
        }

        updateEmbedSrcParams();
    });

    $("#player_stream_url-element").change(function() {
        updateEmbedSrcParams();
    });
});

