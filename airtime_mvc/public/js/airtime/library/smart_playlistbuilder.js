$(document).ready(function() {
    setSmartPlaylistEvents();
});

function setSmartPlaylistEvents() {
	var form = $('#smart-playlist-form');
	
	form.find('a[id="criteria_add"]').click(function(){
        var div = $('dd[id="sp_criteria-element"]').children('div:visible:last').next();

        div.show();
        div = div.next();
        if(div.length === 0) {
            $(this).hide();
        }
	});
	
	form.find('a[id^="criteria_remove"]').click(function(){
        var curr = $(this).parent();
        var curr_pos = curr.index();
        var list = curr.parent();
        var list_length = list.find("div:visible").length;
        var count = list_length - curr_pos;
        var next = curr.next();
		
       for(var i=0; i<=count; i++) {
            var criteria = next.find('[name^="sp_criteria"]').val();
            curr.find('[name^="sp_criteria"]').val(criteria);
            var modifier = next.find('[name^="sp_criteria_modifier"]').val();
            curr.find('[name^="sp_criteria_modifier"]').val(modifier);
            var criteria_value = next.find('[name^="sp_criteria_value"]').val();
            curr.find('[name^="sp_criteria_value"]').val(criteria_value);

            curr = next;
            next = curr.next();
        }
		
        list.find("div:visible:last")
            .find('[name^="sp_criteria"]').val('').end()
            .find('[name^="sp_criteria_modifier"]').val('').end()
            .find('[name^="sp_criteria_value"]').val('')
            .end().hide();

        list.next().show();
    });
	
}
