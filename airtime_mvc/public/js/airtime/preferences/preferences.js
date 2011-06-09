$(document).ready(function() {

	var form = $("form");

    form.find("h3").click(function(){
        var h3 = $(this);
        h3.next().toggle();

        if(h3.hasClass("close")) {
            h3.removeClass("close");
        }
        else {
            h3.addClass("close");
        }
    });
    
    $('#Register').click(function(event){
    	event.preventDefault();
    	$.get("/Preference/register", {format:"json"}, function(json){
	    	var dialog = $(json.dialog);
	    	
	    	dialog.dialog({
	    		autoOpen: false,
	    		title: 'Register Airtime',
	    		width: 400,
	    		height: 500,
	    		modal: true,
	    		buttons: {"Ok": function() {
	    			dialog.remove();
	    		}}
	    	});
	
	    	dialog.dialog('open');
	    	
	    	var form = $("form");

	        form.find("h3").click(function(){
	            var h3 = $(this);
	            h3.next().toggle();

	            if(h3.hasClass("close")) {
	                h3.removeClass("close");
	            }
	            else {
	                h3.addClass("close");
	            }
	        });
    	})
    })
});

