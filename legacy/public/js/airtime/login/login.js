$(window).load(function() {
    $("#username").focus();
});

$(document).ready(function() {
    $("#submit").click(function() {
        Cookies.set('airtime_locale', $('#locale').val(), {path: '/'});
    });
});
