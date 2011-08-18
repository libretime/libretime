function showErrorSections() {

    $(".errors").each(function(i){
        if($(this).length > 0){
            $(this).closest("div").show();
            $(window).scrollTop($(this).position().top);
            return false;
        }
    });
}

function buildStreamUrl(){
    
    $("input:[id$=-host], input:[id$=-port], input:[id$=-mount], select:[id$=-type]").change(function(){
        div = $(this).closest("div")
        host = div.find("input:[id$=-host]").val()
        port = div.find("input:[id$=-port]").val()
        mount = div.find("input:[id$=-mount]").val()
        type = div.find("select:[id$=-type]").val()
        div.find("#stream_url").html("http://"+host+":"+port+"/"+mount+"."+type)
        if($(this).attr('id').indexOf('type') != -1){
            div.find("#mount_ext").html("."+type)
        }
    })
}

$(document).ready(function() {
    
    $('.collapsible-header').click(function() {
        $(this).next().toggle('fast');
        $(this).toggleClass("close");
        return false;
    }).next().hide();
    
    showErrorSections()
    
    buildStreamUrl()
});