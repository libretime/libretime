function showErrorSections() {

    if($("soundcloud-settings .errors").length > 0) {
        $("#soundcloud-settings").show();
        $(window).scrollTop($("soundcloud-settings .errors").position().top);
    }
    if($("#support-settings .errors").length > 0) {
        $("#support-settings").show();
        $(window).scrollTop($("#support-settings .errors").position().top);
    }
}

$(document).ready(function() {
    var form = $("form");

    $("#SupportFeedback").click( function(){
        var pub = $("#Publicise");
        if( !$(this).is(':checked') ){
            pub.removeAttr("checked");
            pub.attr("disabled", true);
        }else{
            pub.removeAttr("disabled");
        }
    });
    
    var promote = $("#Publicise");
    if(!$("#SupportFeedback").is(':checked')){
        promote.removeAttr("checked");
        promote.attr("disabled", true);
    }else{
        promote.removeAttr("disabled");
    }
    
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

    showErrorSections();
    
    $('.toggle legend').click(function() {
        $('.toggle').toggleClass('closed');
        return false;
    }); 
    
    $("#Logo").change(function(ev){
    	var content, res, logoEl;
    	
    	content = $(this).val();
    	res = content.match(/(jpg|jpeg|png|gif)$/gi);
    	logoEl = $("#Logo-element");
    	
    	//not an accepted image extension.
    	if (!res) {
    		var ul, li; 
    			
    		ul = logoEl.find('.errors');
    		li = $("<li/>").append($.i18n._("Image must be one of jpg, jpeg, png, or gif"));
    		
    		//errors ul has already been created.
    		if (ul.length > 0) {
    			ul.empty()
    				.append(li);
    		}
    		else {
    			logoEl
    				.append('<ul class="errors"></ul>')
    				.find(".errors")
    					.append(li);
    		}
    		
    		$(this).val("");
    	}
    	else {
    		logoEl.find(".errors").remove();
    	}
    });
});
