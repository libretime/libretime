$(document).ready(function(){
    
    function doNotShowPopup(){
        $.get("/Nowplaying/donotshowregistrationpopup");
    }

    var dialog = $("#register_popup");
    
    dialog.dialog({
        autoOpen: false,
        width: 500,
        resizable: false,
        modal: true,
        position:['center',50],
        close: doNotShowPopup,
        buttons: [
            {
            	id: "remind_me",
            	text: "Remind me in 1 week",
            	click: function() {
                    var url = '/Nowplaying/remindme';
                    $.ajax({
                        url: url,
                    });
                    $(this).dialog("close");
            	}
            },
            {
            	id: "help_airtime",
            	text: "Yes, help Airtime",
            	click: function() {
            	    $("#register-form").submit();
            	}
            }
         ]
    });
    
    var button = $("#help_airtime");
    button.attr('disabled', 'disabled').addClass('ui-state-disabled');
    dialog.dialog('open');
    

    $('.collapsible-header').live('click',function() {
        $(this).next().toggle('fast');
        $(this).toggleClass("close");
        return false;
    }).next().hide();
    
    $("#SupportFeedback").live('click', function(){
        var pub = $("#Publicise");
        var privacy = $("#Privacy");
        var button = $("#help_airtime");
        if( !$(this).is(':checked') ){
            pub.removeAttr("checked");
            pub.attr("disabled", true);
            $("#public-info").hide();
            button.attr('disabled', 'disabled' ).addClass('ui-state-disabled');
        }else{
            pub.removeAttr("disabled");
            if(privacy.is(':checked')){
                button.removeAttr('disabled').removeClass('ui-state-disabled');
            }
        }
    });

    var promote = $("#Publicise");
    promote.live('click', function(){
        if($(this).is(':checked')){
            $("#public-info").show();
        }else{
            $("#public-info").hide();
        }
    });
    if( promote.is(":checked")){
        $("#public-info").show();
    }
    
    $("#Privacy").live('click', function(){
    	var support = $("#SupportFeedback");
    	var button = $("#help_airtime");
        if($(this).is(':checked') && support.is(':checked')){
        	button.removeAttr('disabled').removeClass('ui-state-disabled');
        }else{
        	button.attr('disabled', 'disabled' ).addClass('ui-state-disabled');
        }
    });
    
    if($("#SupportFeedback").is(':checked') && $("#Privacy").is(':checked')){
        button.removeAttr('disabled').removeClass('ui-state-disabled');
    }else{
        button.attr('disabled', 'disabled' ).addClass('ui-state-disabled');
    }
    
    $('.toggle legend').live('click',function() {
        $('.toggle').toggleClass('closed');
        return false;
    }); 
});
        
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