$(window).load(function(){
    $("#username").focus()
    
    var captcha = $("#captcha-label").next()
    captcha.css("padding-left", (((captcha.parents('div:eq(0)').width()-captcha.width())/2+"px")));
})