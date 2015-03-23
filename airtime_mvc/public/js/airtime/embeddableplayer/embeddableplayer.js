function updateEmbedSrcParams()
{
    var $embedCodeParams = "?";
    var $streamMode = getStreamMode();
    if ($streamMode == "b") {
        var $stream = $("input[name=player_stream_url]:radio:checked").val();
        $embedCodeParams += "stream-mode=b&stream="+$stream;
    } else if ($streamMode == "a") {
        $embedCodeParams += "stream-mode=a";
    }
    $embedCodeParams += "\"";

    $("input[name=player_embed_src]").val(function(index, value) {
        return value.replace(/\?.*?"/, $embedCodeParams);
    });
}

function getStreamMode() {
    return $("input[name=player_stream_mode]:radio:checked").val();
}

$(document).ready(function() {

    $("#player_stream_mode-element").change(function() {
        var $streamMode = getStreamMode();
        if ($streamMode == "a") {
            $("#player_stream_url-element input[type='radio']").attr("disabled", "disabled");
        } else if ($streamMode == "b") {
            $("#player_stream_url-element input[type='radio']").removeAttr("disabled");

            $("input[name=player_stream_url]").each(function(i, obj) {
                if ($(this).parent().text().indexOf("opus") >= 0) {
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

