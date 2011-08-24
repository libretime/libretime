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

function hideForShoutcast(ele){
    ele.closest("div").find("#outputMountpoint-label").hide()
    ele.closest("div").find("#outputMountpoint-element").hide()
    ele.closest("div").find("#outputUser-label").hide()
    ele.closest("div").find("#outputUser-element").hide()
}

function showForIcecast(ele){
    ele.closest("div").find("#outputMountpoint-label").show()
    ele.closest("div").find("#outputMountpoint-element").show()
    ele.closest("div").find("#outputUser-label").show()
    ele.closest("div").find("#outputUser-element").show()
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
            hideForShoutcast($(this))
        }else{
            showForIcecast($(this))
        }
    })
    
    $("select[id$=-output]").each(function(){
        if($(this).val() == 'shoutcast'){
            hideForShoutcast($(this))
        }
    })
    
    showErrorSections()
    
    
});