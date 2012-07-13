$(document).ready(function() {
    setSmartPlaylistEvents();
});

function setSmartPlaylistEvents() {
    var form = $('#smart-playlist-form');
	
    form.find('a[id="criteria_add"]').live("click", function(){
        var div = $('dd[id="sp_criteria-element"]').children('div:visible:last').next(),
            add_button = $(this);
        
        div.show();
        div.find('a[id^="criteria_remove"]').after(add_button);
        div.children().removeAttr('disabled');
        div = div.next();
        if (div.length === 0) {
            $(this).hide();
        }
    });
	
    form.find('a[id^="criteria_remove"]').live("click", function(){
        var curr = $(this).parent();
        var curr_pos = curr.index();
        var list = curr.parent();
        var list_length = list.find("div:visible").length;
        var count = list_length - curr_pos;
        var next = curr.next();
        var add_button = form.find('a[id="criteria_add"]');
        
        //remove error message from current row, if any
        var error_element = curr.find('span[class="errors sp-errors"]');
        if (error_element.is(':visible')) {
            error_element.remove();
        }

       /* assign next row to current row for all rows below and including
        * the row getting removed
        */
       for (var i=0; i<count; i++) {
            var criteria = next.find('[name^="sp_criteria_field"]').val();
            curr.find('[name^="sp_criteria_field"]').val(criteria);
            
            var modifier = next.find('[name^="sp_criteria_modifier"]').val();
            populateModifierSelect(curr.find('[name^="sp_criteria_field"]'));
            curr.find('[name^="sp_criteria_modifier"]').val(modifier);
            
            var criteria_value = next.find('[name^="sp_criteria_value"]').val();
            curr.find('[name^="sp_criteria_value"]').val(criteria_value);
            
            var id = curr.find('[name^="sp_criteria"]').attr('id');
            var index = id.charAt(id.length-1); 
            /* if current and next row have the extra criteria value
             * (for 'is in the range' modifier), then assign the next
             * extra value to current and remove that element from
             * next row
             */
            if (curr.find('[name^="sp_criteria_extra"]').attr("disabled") != "disabled"
                && next.find('[name^="sp_criteria_extra"]').attr("disabled") != "disabled") {
            	
                var criteria_extra = next.find('[name^="sp_criteria_extra"]').val();
                curr.find('[name^="sp_criteria_extra"]').val(criteria_extra);
                disableAndHideExtraField(next.find(':first-child'), index+1);
            	/*next.find('[name^="sp_criteria_extra"]').remove();
                next.find('span[id="sp_criteria_extra_label"]').remove();*/
                
            
            /* if only the current row has the extra criteria value,
             * then just remove the current row's extra criteria element
             */
            } else if (curr.find('[name^="sp_criteria_extra"]').attr("disabled") != "disabled"
                       && next.find('[name^="sp_criteria_extra"]').attr("disabled") == "disabled") {
                disableAndHideExtraField(curr.find(':first-child'), index);
                /*curr.find('[name^="sp_criteria_extra"]').remove();
                curr.find('span[id="sp_criteria_extra_label"]').remove();*/
                
            /* if only the next row has the extra criteria value,
             * then add the extra criteria element to current row
             * and assign next row's value to it
             */
            } else if (next.find('[name^="sp_criteria_extra"]').attr("disabled") != "disabled") {
                criteria_extra = next.find('[name^="sp_criteria_extra"]').val();
                enableAndShowExtraField(curr.find(':first-child'), index);
                /*curr.find('[name^="sp_criteria_value"]')
                    .after($('<input type="text" class="input_text">')
                    .attr('id', 'sp_criteria_extra_'+index_num)
                    .attr('name', 'sp_criteria_extra_'+index_num)).after('<span id="sp_criteria_extra_label"> to </span>');*/
                curr.find('[name^="sp_criteria_extra"]').val(criteria_extra);
            }

            curr = next;
            next = curr.next();
        }
		
        list.find('div:visible:last').children().attr('disabled', 'disabled');
        list.find('div:visible:last')
            .find('[name^="sp_criteria"]').val(0).end()
            .find('[name^="sp_criteria_modifier"]').val(0).end()
            .find('[name^="sp_criteria_value"]').val('');
        
        if (list.find('div:visible:last').children().hasClass('criteria_add')) {
            list.find('div:visible:last').find('.criteria_add').remove();
        }
        list.find('div:visible:last').hide();

        list.next().show();
        
        // always put 'add' button on the last row
        if (list.find('div:visible').length > 1) {
            list.find('div:visible:last').find('a[id^="criteria_remove"]')
                                         .after("<a href='#' id='criteria_add' class='criteria_add'>Add</a>");
        } else {
            list.find('div:visible:last').find('[name^="sp_criteria_value"]')
                                         .after("<a href='#' id='criteria_add' class='criteria_add'>Add</a>");
        }
    });
	
    form.find('button[id="save_button"]').live("click", function(event){
        var playlist_type = form.find('input:radio[name=sp_type]:checked').val(),
            data = $('form').serializeArray(),
            static_action = 'Playlist/smart-playlist-generate',
            dynamic_action ='Playlist/smart-playlist-criteria-save',
            action,
            callback,
            playlist_id = $('input[id="pl_id"]').val();
		
        if (playlist_type == "0") {
            action = static_action;
            callback = staticCallback;
        } else {
            action = dynamic_action;
            callback = dynamicCallback;
        }
        $.post(action, {format: "json", data: data, pl_id: playlist_id}, callback);
    });
	
    form.find('dd[id="sp_type-element"]').live("change", function(){
        var playlist_type = $('input:radio[name=sp_type]:checked').val(),
            button_text;
        if (playlist_type == "0") {
            button_text = 'Generate';
        } else {
            button_text = 'Save';
        }
        $('button[id="save_button"]').text(button_text);    	
    });
    
    form.find('select[id^="sp_criteria"]:not([id^="sp_criteria_modifier"])').live("change", function(){
        var index_name = $(this).attr('id'),
            index_num = index_name.charAt(index_name.length-1);
        
        // disalbe extra field and hide the span
        disableAndHideExtraField($(this), index_num);
        /*if ($('#sp_criteria_extra_'+index_num).length > 0) {
            $('#sp_criteria_extra_'+index_num).remove();
            $('#sp_criteria_extra_label_'+index_num).remove();
        }*/
        populateModifierSelect(this);
    });
    
    form.find('select[id^="sp_criteria_modifier"]').live("change", function(){
        var criteria_value = $(this).next(),
            index_name = criteria_value.attr('id'),
            index_num = index_name.charAt(index_name.length-1);
        
        if ($(this).val() == 'is in the range') {
            enableAndShowExtraField(criteria_value, index_num);
            /*after($('<input type="text" class="input_text">')
                          .attr('id', 'sp_criteria_extra_'+index_num)
                          .attr('name', 'sp_criteria_extra_'+index_num)).after('<span id="sp_criteria_extra_label_'+index_num+'"> to </span>');*/
        
        } else {
            disableAndHideExtraField(criteria_value, index_num);
            /*if ($('#sp_criteria_extra_'+index_num).length > 0) {
            	$('#sp_criteria_extra_'+index_num).remove();
            	$('#sp_criteria_extra_label_'+index_num).remove();
            }*/
        }
    });
	
}

function enableAndShowExtraField(valEle, index) {
    var spanExtra = valEle.nextAll("#extra_criteria");
    spanExtra.children('#sp_criteria_extra_'+index).removeAttr("disabled");
    spanExtra.show();
}

function disableAndHideExtraField(valEle, index) {
    var spanExtra = valEle.nextAll("#extra_criteria");
    spanExtra.children('#sp_criteria_extra_'+index).val("").attr("disabled", "disabled");
    spanExtra.hide();
}

function populateModifierSelect(e) {
    var criteria = $(e).val(),
        criteria_type = criteriaTypes[criteria],
        div = $(e);
    
    $(e).next().children().remove();

    if (criteria_type == 's') {
        $.each(stringCriteriaOptions, function(key, value){
            div.next().append($('<option></option>')
                      .attr('value', key)
                      .text(value));
        });
    } else {
        $.each(numericCriteriaOptions, function(key, value){
            div.next().append($('<option></option>')
                      .attr('value', key)
                      .text(value));
        });
    }
}

function staticCallback(data) {
	var form = $('#smart-playlist-form');
	form.find('span[class="errors sp-errors"]').remove();
	var json = $.parseJSON(data);
	if (json.result == "1") {
	    $.each(json.errors, function(index, error){
            $.each(error.msg, function(index, message){
                $('#'+error.element).parent().append("<span class='errors sp-errors'>"+message+"</span>");
            });
        });
    }
}

function dynamicCallback(json) {
	var form = $('#smart-playlist-form');
	form.find('span[class="errors sp-errors"]').remove();
	var json = $.parseJSON(data);
	if (json.result == "1") {
        $.each(json.errors, function(index, error){
            $.each(error.msg, function(index, message){
                $('#'+error.element).parent().append("<span class='errors sp-errors'>"+message+"</span>");
            });
        });
    }
}

var criteriaTypes = {
    0 : "",
    "album_title" : "s",
    "artist_name" : "s",
    "bit_rate" : "n",
    "bmp" : "n",
    "comments" : "s",
    "composer" : "s",
    "conductor" : "s",
    "utime" : "n",
    "mtime" : "n",
    "disc_number" : "n",
    "genre" : "s",
    "isrc_number" : "s",
    "label" : "s",
    "language" : "s",
    "length" : "n",
    "lyricist" : "s",
    "mood" : "s",
    "name" : "s",
    "orchestra" : "s",
    "radio_station_name" : "s",
    "rating" : "n",
    "sample_rate" : "n",
    "soundcloud_id" : "n",
    "track_title" : "s",
    "track_num" : "n",
    "year" : "n"               
};

var stringCriteriaOptions = {
    "0" : "Select modifier",
    "contains" : "contains",
    "does not contain" : "does not contain",
    "is" : "is",
    "is not" : "is not",
    "starts with" : "starts with",
    "ends with" : "ends with"
};
    
var numericCriteriaOptions = {
    "0" : "Select modifier",
    "is" : "is",
    "is not" : "is not",
    "is greater than" : "is greater than",
    "is less than" : "is less than",
    "is in the range" : "is in the range"
};
