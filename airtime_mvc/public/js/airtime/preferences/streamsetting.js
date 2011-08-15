$(document).ready(function() {

    $('#change_setting').click(function(){
        var url;
        
        url = "/Preference/change-stream-setting";

        $.post(url,
            {format: "json"}
        );
    });

});