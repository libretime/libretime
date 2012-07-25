$(document).ready(function() {
    setSmartPlaylistEvents();
});

function setSmartPlaylistEvents() {
    var form = $('#smart-playlist-form');
    
    /*
    sets.each(function(index, set){
        $(set).live('click', function(){
           if ($(this).next().hasClass('closed')) { 
               $(this).next().removeClass('closed sp-closed');
           } else {
               $(this).next().addClass('closed sp-closed');
           }
        });
    });
    */
    
    form.find('.criteria_add').live('click', function(){
        
        var div = $('dd[id="sp_criteria-element"]').children('div:visible:last').next();
        
        div.show();
        div.children().removeAttr('disabled');
        div = div.next();
        if (div.length === 0) {
            $(this).hide();
        }
        appendAddButton();
        removeButtonCheck();
    });
	
    form.find('a[id^="criteria_remove"]').live('click', function(){
        var curr = $(this).parent();
        var curr_pos = curr.index();
        var list = curr.parent();
        var list_length = list.find("div:visible").length;
        var count = list_length - curr_pos;
        var next = curr.next();
        var add_button = form.find('a[id="criteria_add"]');
        var item_to_hide;
        
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
            
            /* if only the current row has the extra criteria value,
             * then just remove the current row's extra criteria element
             */
            } else if (curr.find('[name^="sp_criteria_extra"]').attr("disabled") != "disabled"
                       && next.find('[name^="sp_criteria_extra"]').attr("disabled") == "disabled") {
                disableAndHideExtraField(curr.find(':first-child'), index);
                
            /* if only the next row has the extra criteria value,
             * then add the extra criteria element to current row
             * and assign next row's value to it
             */
            } else if (next.find('[name^="sp_criteria_extra"]').attr("disabled") != "disabled") {
                criteria_extra = next.find('[name^="sp_criteria_extra"]').val();
                enableAndShowExtraField(curr.find(':first-child'), index);
                curr.find('[name^="sp_criteria_extra"]').val(criteria_extra);
            }

            curr = next;
            next = curr.next();
        }
		
        /* Disable the last visible row since it holds the values the user removed
         * Reset the values to empty and resize the criteria value textbox
         * in case the row had the extra criteria textbox
         */
        item_to_hide = list.find('div:visible:last');
        item_to_hide.children().attr('disabled', 'disabled');
        item_to_hide.find('[name^="sp_criteria_field"]').val(0).end()
                    .find('[name^="sp_criteria_modifier"]').val(0).end()
                    .find('[name^="sp_criteria_value"]').val('');
        
        sizeTextBoxes(item_to_hide.find('[name^="sp_criteria_value"]'), 'sp_extra_input_text', 'sp_input_text');
        item_to_hide.hide();

        list.next().show();
        
        // always put '+' button on the last enabled row
        appendAddButton();
        // remove the 'x' button if only one row is enabled
        removeButtonCheck();
    });
	
    form.find('button[id="save_button"]').live("click", function(event){
        var data = $('form').serializeArray(),
            save_action = 'Playlist/smart-block-criteria-save',
            obj_id = $('input[id="obj_id"]').val();
        
        $.post(save_action, {format: "json", data: data, obj_id: obj_id}, function(data){
            callback(data, "save");
        });
    });
    
    form.find('button[id="generate_button"]').live("click", function(event){
        var data = $('form').serializeArray(),
            generate_action = 'Playlist/smart-block-generate',
            obj_id = $('input[id="obj_id"]').val();
		
        $.post(generate_action, {format: "json", data: data, obj_id: obj_id}, function(data){
            callback(data, "generate");
        });
    });
    
    form.find('button[id="shuffle_button"]').live("click", function(event){
        var data = $('form').serializeArray(),
            shuffle_action = 'Playlist/smart-block-shuffle',
            obj_id = $('input[id="obj_id"]').val();
		
        $.post(shuffle_action, {format: "json", data: data, obj_id: obj_id}, function(data){
            callback(data, "shuffle");
        });
    });
	
    form.find('dd[id="sp_type-element"]').live("change", function(){
        setupUI();  	
    });
    
    form.find('select[id^="sp_criteria"]:not([id^="sp_criteria_modifier"])').live("change", function(){
        var index_name = $(this).attr('id'),
            index_num = index_name.charAt(index_name.length-1);
        
        // disable extra field and hide the span
        disableAndHideExtraField($(this), index_num);
        populateModifierSelect(this);
    });
    
    form.find('select[id^="sp_criteria_modifier"]').live("change", function(){
        var criteria_value = $(this).next(),
            index_name = criteria_value.attr('id'),
            index_num = index_name.charAt(index_name.length-1);
        
        if ($(this).val() == 'is in the range') {
            enableAndShowExtraField(criteria_value, index_num);
        } else {
            disableAndHideExtraField(criteria_value, index_num);
        }
    });
    
    setupUI();
    appendAddButton();
    removeButtonCheck();
}

function setupUI() {
    var playlist_type = $('input:radio[name=sp_type]:checked').val();
    if ($('#obj_type').val() == 'block') {
        if (playlist_type == "0") {
            $('button[id="generate_button"]').show();
            $('button[id="shuffle_button"]').show();        
            //$('#spl_sortable').unblock();
            //$('#spl_sortable').css("position", "static");
            $('#spl_sortable').show();
        } else {
            $('button[id="generate_button"]').hide();
            $('button[id="shuffle_button"]').hide();
            /*
            $('#spl_sortable').block({
                message: "",
                theme: true,
                applyPlatformOpacityRules: false
            });
            */
            $('#spl_sortable').hide();
        }
    }
    
    $(".playlist_type_help_icon").qtip({
        content: {
            text: "A static playlist will save the criteria and generate the playlist content immediately." +
                  "This allows you to edit and view it in the Playlist Builder before adding it to a show.<br /><br />" +
                  "A dynamic playlist will only save the criteria. The playlist content will get generated upon " +
                  "adding it to a show. You will not be able to view and edit it in the Playlist Builder."
        },
        hide: {
            delay: 500,
            fixed: true
        },
        style: {
            border: {
                width: 0,
                radius: 4
            },
            classes: "ui-tooltip-dark ui-tooltip-rounded"
        },
        position: {
            my: "left bottom",
            at: "right center"
        },
    });
}

function enableAndShowExtraField(valEle, index) {
    var spanExtra = valEle.nextAll("#extra_criteria");
    spanExtra.children('#sp_criteria_extra_'+index).removeAttr("disabled");
    spanExtra.show();

    //make value input smaller since we have extra element now
    var criteria_val = $('#sp_criteria_value_'+index);
    sizeTextBoxes(criteria_val, 'sp_input_text', 'sp_extra_input_text');
}

function disableAndHideExtraField(valEle, index) {
    var spanExtra = valEle.nextAll("#extra_criteria");
    spanExtra.children('#sp_criteria_extra_'+index).val("").attr("disabled", "disabled");
    spanExtra.hide();
    
    //make value input larger since we don't have extra field now
    var criteria_value = $('#sp_criteria_value_'+index);
    sizeTextBoxes(criteria_value, 'sp_extra_input_text', 'sp_input_text');
}

function sizeTextBoxes(ele, classToRemove, classToAdd) {
    var form = $('#smart-playlist-form');
    if (ele.hasClass(classToRemove)) {
        ele.removeClass(classToRemove).addClass(classToAdd);
    }
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

function callback(data, type) {
    var form = $('#smart-playlist-form'),
    json = $.parseJSON(data);

    form.find('span[class="errors sp-errors"]').remove();
	
    if (json.result == "1") {
        form.find('.success').hide();
        $.each(json.errors, function(index, error){
            $.each(error.msg, function(index, message){
                $('#'+error.element).parent().append("<span class='errors sp-errors'>"+message+"</span>");
            });
        });
    } else {
        if (type == 'shuffle' || type == 'generate') {
            AIRTIME.playlist.fnOpenPlaylist(json);
            form = $('#smart-playlist-form');
            if (type == 'shuffle') {
                form.find('.success').text('Playlist shuffled');
            } else {
            	form.find('.success').text('Smart playlist generated and saved');
            }
    	    form.find('.success').show();
    	    form.find('#smart_playlist_options').removeClass("closed");
        } else {
            form.find('.success').text('Criteria saved');
            form.find('.success').show();
            
            /* Update number of files that meet criteria and change icon to success/warning
             * as appropriate. This is also done in the form but we do not pass the form
             * back on a 'Save' callback.
             */
            if (json.poolCount > 1) {
                $('#sp_pool_count').text(json.poolCount+' files meet the criteria');
                if ($('#sp_pool_count_icon').hasClass('sp-warning-icon')) {
                    $('#sp_pool_count_icon').removeClass('sp-warning-icon').addClass('checked-icon sp-checked-icon');  
                }
            } else if (json.poolCount == 1) {
                $('#sp_pool_count').text('1 file meets the criteria');
                if ($('#sp_pool_count_icon').hasClass('sp-warning-icon')) {
                    $('#sp_pool_count_icon').removeClass('sp-warning-icon').addClass('checked-icon sp-checked-icon');  
                }
            } else {
                $('#sp_pool_count').text('0 files meet the criteria');
                if ($('#sp_pool_count_icon').hasClass('checked-icon sp-checked-icon')) {
                    $('#sp_pool_count_icon').removeClass('checked-icon sp-checked-icon').addClass('sp-warning-icon');  
                }
            }
        }
        setTimeout('removeSuccessMsg()', 5000);
    }
}

function removeSuccessMsg() {
    var form = $('#smart-playlist-form');
    form.find('.success').text('');
    form.find('.success').hide();
}

function appendAddButton() {
    var add_button = "<a class='ui-button sp-ui-button-icon-only criteria_add'>" +
                     "<span class='ui-icon ui-icon-plusthick'></span></a>";
    var rows = $('#smart_playlist_options'),
        enabled = rows.find('select[name^="sp_criteria_field"]:enabled');

    rows.find('.criteria_add').remove();
    
    if (enabled.length > 1) {
        rows.find('select[name^="sp_criteria_field"]:enabled:last')
            .siblings('a[id^="criteria_remove"]')
            .after(add_button);
    } else {
        enabled.siblings('span[id="extra_criteria"]')
               .after(add_button);
    }
}

function removeButtonCheck() {
    var rows = $('dd[id="sp_criteria-element"]').children('div'),
        enabled = rows.find('select[name^="sp_criteria_field"]:enabled'),
        rmv_button = enabled.siblings('a[id^="criteria_remove"]');
    if (enabled.length == 1) {
        rmv_button.attr('disabled', 'disabled');
        rmv_button.hide();
    } else {
        rmv_button.removeAttr('disabled');
        rmv_button.show();
    }
}

var criteriaTypes = {
    0 : "",
    "album_title" : "s",
    "artist_name" : "s",
    "bit_rate" : "n",
    "bpm" : "n",
    "comments" : "s",
    "composer" : "s",
    "conductor" : "s",
    "utime" : "n",
    "mtime" : "n",
    "lptime" : "n",
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
