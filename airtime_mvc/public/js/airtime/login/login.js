$(window).load(function(){
    $("#username").focus();
    $("#locale").val($.cookie("airtime_locale")!== null?$.cookie("airtime_locale"):$.cookie("default_airtime_locale"));
});

$(document).ready(function() {
    $("#submit").click(function() {
        $.cookie("airtime_locale", $("#locale").val(), {path: '/'});
    });
});
