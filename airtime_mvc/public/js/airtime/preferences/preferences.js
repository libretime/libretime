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

function resizeImg(ele){
	var img = $(ele);

	var width = ele.width;
    var height = ele.height;

    // resize img proportionaly
    if( width > height && width > 450){
    	var ratio = 450/width;
    	img.css("width", "450px");
    	var newHeight = height * ratio;
    	img.css("height", newHeight );

    }else if( width < height && height > 450){
    	var ratio = 450/height;
    	img.css("height", "450px");
    	var newWidth = width * ratio;
		img.css("width", newWidth );
    }else if( width == height && width > 450){
    	img.css("height", "450px");
		img.css("width", "450px" );
    }

}

$(document).ready(function() {
	var form = $("form");

	$('.collapsible-header').live('click',function() {
        $(this).next().toggle('fast');
        $(this).toggleClass("close");
        return false;
    }).next().hide();

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
    
    $("#link_to_privacy").live('click', function(){
        window.open("http://www.sourcefabric.org/en/about/policy/");
    });
    
    $('.toggle legend').click(function() {
        $('.toggle').toggleClass('closed');
        return false;
    }); 
});
