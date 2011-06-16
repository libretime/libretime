$(document).ready(function(){
    $.get("/Preference/register", {format:"json"}, function(json){
        var dialog = $(json.dialog);

        dialog.dialog({
            autoOpen: false,
            width: 450,
            resizable: false,
            modal: true,
            position:'center',
            buttons: {
                "Remind me in 1 week": function() {
                    var url = '/Preference/remindme';
                    $.ajax({
                        url: url,
                    });
                    $(this).dialog("close");
                }, 
                "Yes, help Airtime": function() {
                    if($("#Publicise").is(':checked')){
                        if(validateFields()){
                            $("#register-form").submit();
                        }
                    }else{
                        $("#register-form").submit();
                    }
                } 
            }
        });

        dialog.dialog('open');
    })

    $('.collapsible-header').live('click',function() {
        $(this).next().toggle('fast');
        $(this).toggleClass("close");
        return false;
    }).next().hide();

    $("#SupportFeedback").live('click', function(){
        var pub = $("#Publicise");
        if( !$(this).is(':checked') ){
            pub.removeAttr("checked");
            pub.attr("disabled", true);
            $("#public-info").hide();
        }else{
            pub.removeAttr("disabled");
        }
    });

    $("#Publicise").live('click', function(){
        if($(this).is(':checked')){
            $("#public-info").show();
        }else{
            $("#public-info").hide();
        }
    });
});

function validateFields(){
    var stnName = $("#stnName");
    var phone = $("#Phone");
    var email = $("#Email");
    var city = $("#City");
    var description = $("#Description");
    
    var errors = new Array();
    
    errors[0] = displayError(stnName);
    errors[1] = displayError(phone);
    errors[2] = displayError(email);
    errors[3] = displayError(city);
    errors[4] = displayError(description);
  
    for( e in errors ){
        if(errors[e]){
            return false;
        }
    }
    return true;
}

function displayError(ele){
    var errorMsg = "Value is required and can't be empty";
    
    ele.parent().find("ul").remove();
    if($.trim(ele.val()) == ''){
        ele.parent().append("<ul class='errors'><li>"+errorMsg+"</li></ul>");
        return true;
    }
    return false;
}
        
function resizeImg(ele){

    var img = $(ele);

    var width = ele.width;
    var height = ele.height;

    // resize img proportionaly
    if( width > height && width > 430){
        var ratio = 430/width;
        img.css("width", "430px");
        var newHeight = height * ratio;
        img.css("height", newHeight );

    }else if( width < height && height > 430){
        var ratio = 430/height;
        img.css("height", "430px");
        var newWidth = width * ratio;
        img.css("width", newWidth );
    }else if( width == height && width > 430){
        img.css("height", "430px");
        img.css("width", "430px" );
    }	
}