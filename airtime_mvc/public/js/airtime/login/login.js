$(window).load(function(){
    $("#username").focus();
    $("#locale").val($.cookie("airtime_locale")!== null?$.cookie("airtime_locale"):'en_CA');
});

$(document).ready(function() {
    $("#submit").click(function() {
        $.cookie("airtime_locale", $("#locale").val(), {path: '/'});
    });
});
