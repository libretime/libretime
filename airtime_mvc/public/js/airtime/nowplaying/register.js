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
					$(this).dialog("close");  
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
})

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