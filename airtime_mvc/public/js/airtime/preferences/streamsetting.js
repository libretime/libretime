function showErrorSections() {

    $(".errors").each(function(i){
        if($(this).length > 0){
            $(this).closest("div").show();
            $(window).scrollTop($(this).position().top);
            return false;
        }
    });
}
function rebuildStreamURL(ele){
    div = ele.closest("div")
    host = div.find("input:[id$=-host]").val()
    port = div.find("input:[id$=-port]").val()
    mount = div.find("input:[id$=-mount]").val()
    streamurl = ""
    if(div.find("select:[id$=-output]").val()=="icecast"){
        streamurl = "http://"+host
        if($.trim(port) != ""){
            streamurl += ":"+port
        }
        if($.trim(mount) != ""){
            streamurl += "/"+mount
        }
    }else{
        streamurl = "http://"+host+":"+port+"/"
    }
    div.find("#stream_url").html(streamurl)
}

$(document).ready(function() {
    // initial stream url
    $("dd[id=outputStreamURL-element]").each(function(){
        rebuildStreamURL($(this))
    })
    
    $("input:[id$=-host], input:[id$=-port], input:[id$=-mount]").keyup(function(){
        rebuildStreamURL($(this))
    })
    
    $("select:[id$=-output]").change(function(){
        rebuildStreamURL($(this))
    })
    
    $('.collapsible-header').click(function() {
        $(this).next().toggle('fast');
        $(this).toggleClass("close");
        return false;
    }).next().hide();
    
    $("select[id$=-output]").change(function(){
        if($(this).val() == 'shoutcast'){
            $(this).closest("div").find("#outputMountpoint-label").hide()
            $(this).closest("div").find("#outputMountpoint-element").hide()
        }else{
            $(this).closest("div").find("#outputMountpoint-label").show()
            $(this).closest("div").find("#outputMountpoint-element").show()
        }
    })
    
    showErrorSections()
    
    
});