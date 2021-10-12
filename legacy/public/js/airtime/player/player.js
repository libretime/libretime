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

    $embedCodeParams += "&title="+getPlayerTitle();

    $embedCodeParams += "\"";

    $("textarea[name=player_embed_src]").val(function(index, value) {
        return value.replace(/\?.*?"/, $embedCodeParams);
    });

    updatePlayerIframeSrc($("textarea[name=player_embed_src]").val());
}

function updatePlayerIframeSrc(iframe_text) {
    var $player_iframe = $("#player_form iframe");
    var player_iframe_src = iframe_text.match(/http.*?"/)[0].slice(0, -1);
    $player_iframe.attr('src', player_iframe_src);
}

function getStreamMode() {
    return $("input[name=player_stream_mode]:radio:checked").val();
}

function getPlayerTitle() {
    return $("input[name=player_title]").val();
}

$(document).ready(function() {

    $("#player_stream_url-element").hide();

    // stream mode change event
    $("#player_stream_mode-element").change(function() {
        var $streamMode = getStreamMode();

        if ($streamMode == "auto") {
            $("#player_stream_url-element").hide();

        } else if ($streamMode == "manual") {
            $("#player_stream_url-element").show();

            $("input[name=player_stream_url]").each(function(i, obj) {
                if ($(this).parent().text().toLowerCase().indexOf("opus") >= 0) {
                    $(this).attr("disabled", "disabled");
                }
            });
        }

        updateEmbedSrcParams();
    });

    // stream url change event
    $("#player_stream_url-element").change(function() {
        updateEmbedSrcParams();
    });

    // display title checkbox change event
    $("#player_display_title").change(function() {
        if ($(this).prop("checked")) {
            $("#player_title-label").show();
            $("#player_title-element").show();
        } else {
            $("#player_title-label").hide();
            $("#player_title-element").hide();
        }
        updateEmbedSrcParams();
    });

    // title textbox change event
    // setup before functions
    var typingTimer;
    var doneTypingInterval = 3000;

    // on keyup, start the countdown
    $("input[name=player_title]").keyup(function(){
        clearTimeout(typingTimer);
        typingTimer = setTimeout(updateEmbedSrcParams, doneTypingInterval);
    });

    // on keydown, clear the countdown
    $("input[name=player_title]").keydown(function(){
        clearTimeout(typingTimer);
    });
});
