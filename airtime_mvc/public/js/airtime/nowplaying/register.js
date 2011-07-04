$(document).ready(function(){
	var dialogGlob;
	
	function doNotShowPopup(){
		$.get("/Preference/donotshowpopup");
	}
	
    $.get("/Preference/register", {format:"json"}, function(json){
        var dialog = $(json.dialog);
        dialogGlob = dialog;
        
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
	                    var url = '/Preference/remindme';
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
	                	if($("#Publicise").is(':checked')){
	                        if(validateFields()){
	                            $("#register-form").submit();
	                        }
	                    }else{
	                        $("#register-form").submit();
	                    }
                	}
                }
             ]
        });
        
        var button = $("#help_airtime");
        button.attr('disabled', 'disabled').addClass('ui-state-disabled');
        dialog.dialog('open');
    })

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

    $("#Publicise").live('click', function(){
        if($(this).is(':checked')){
            $("#public-info").show();
        }else{
            $("#public-info").hide();
        }
    });
    
    $("#Privacy").live('click', function(){
    	var support = $("#SupportFeedback");
    	var button = $("#help_airtime");
        if($(this).is(':checked') && support.is(':checked')){
        	button.removeAttr('disabled').removeClass('ui-state-disabled');
        }else{
        	button.attr('disabled', 'disabled' ).addClass('ui-state-disabled');
        }
    });
    
    $("#link_to_whos_using").live('click', function(){
        window.open("http://sourcefabric.org/en/products/airtime_whosusing");
    });
    
    $("#link_to_privacy").live('click', function(){
        window.open("http://www.sourcefabric.org/en/about/policy/");
    });
    
    $('.toggle legend').live('click',function() {
        $('.toggle').toggleClass('closed');
        return false;
    }); 
});

function validateFields(){
    var stnName = $("#stnName");
    
    var errors = new Array();
    
    errors[0] = displayError(stnName);
  
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