$(document).ready(function() {
    setSmartPlaylistEvents();
});

function setSmartPlaylistEvents() {
	var form = $('#smart-playlist-form');
	
	form.find('a[id="criteria_add"]').click(function(){
        var div = $('dd[id="sp_criteria-element"]').children('div:visible:last').next();

        div.show();
        div.children().removeAttr('disabled');
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
		
        list.find('div:visible:last').children().attr('disabled', 'disabled');
        list.find("div:visible:last")
            .find('[name^="sp_criteria"]').val(0).end()
            .find('[name^="sp_criteria_modifier"]').val(0).end()
            .find('[name^="sp_criteria_value"]').val('')
            .end().hide();

        list.next().show();
    });
	
    form.find('button[id="save_button"]').click(function(event){
        var playlist_type = form.find('input:radio[name=sp_type]:checked').val(),
            data = $('form').serializeArray(),
            static_action = 'Playlist/smart-playlist-generate',
            dynamic_action ='Playlist/smart-playlist-criteria-save',
            action,
            callback;
		
        if (playlist_type == "0") {
            action = static_action;
            callback = staticCallback;
        } else {
            action = dynamic_action;
            callback = dynamicCallback;
        }
        $.post(action, {format: "json", data: data}, callback);
    });
	
    form.find('dd[id="sp_type-element"]').change(function(){
        var playlist_type = $('input:radio[name=sp_type]:checked').val(),
            button_text;
        if (playlist_type == "0") {
            button_text = 'Generate';
        } else {
            button_text = 'Save';
        }
        $('button[id="save_button"]').text(button_text);    	
    });
	
}

function staticCallback() {
	
}

function dynamicCallback() {
	
}
