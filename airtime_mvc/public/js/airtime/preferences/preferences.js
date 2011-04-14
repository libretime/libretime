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
});

