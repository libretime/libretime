function setWatchedDirEvents() {
     $('#watchedFolder-selection').serverBrowser({
        onSelect: function(path) {
            $('#watchedFolder').val(path);
        },
        onLoad: function() {
            return $('#watchedFolder').val();
        },
        width: 500,
        height: 250,
        position: ['center', 'center'],
        //knownPaths: [{text:'Desktop', image:'desktop.png', path:'/home'}],
        knownPaths: [],
        imageUrl: 'img/icons/',
        systemImageUrl: 'img/browser/',
        handlerUrl: '/Preference/server-browse/format/json',
        title: 'Choose Folder to Watch',
        basePath: '/home',
        requestMethod: 'POST',
    });

    $('#watchedFolder-ok').click(function(){
        var url, chosen;

	    url = "/Preference/reload-watch-directory";
        chosen = $('#watchedFolder').val();

	    $.post(url,
		    {format: "json", dir: chosen},

		    function(json) {
                $("#watched-folder-section").empty();
                $("#watched-folder-section").append(json.subform);
                setWatchedDirEvents();
		    });
    });

    $('#watchedFolder-table').find('.ui-icon-close').click(function(){
        var row = $(this).parent();
        var folder = $(this).prev().text();

        url = "/Preference/remove-watch-directory";

	    $.post(url,
		    {format: "json", dir: folder},

		    function(json) {
			   row.remove();
		    });
    });
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

    showErrorSections();
    setWatchedDirEvents();

});

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


});


