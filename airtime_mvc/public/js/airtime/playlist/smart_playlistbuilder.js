$(document).ready(function() {
    setSmartPlaylistEvents();
});

function setSmartPlaylistEvents() {
    var form = $('#smart-playlist-form');
    
    /********** ADD CRITERIA ROW **********/
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
    
    /********** ADD MODIFIER ROW **********/
    form.find('a[id^="modifier_add"]').live('click', function(){
        var id = $(this).attr('id'),
            row_index = id.charAt(id.length-1),
            mod_index,
            criteria_value = $(this).siblings('select[name^="sp_criteria_field"]').val();
        
        //get index for the new modifier row
        if ($(this).parent().find('select[name^="sp_criteria_modifier_'+row_index+'_"]').length == 0) {
            mod_index = 0;
        } else {
            var last_mod = $(this).parent().find('select[name^="sp_criteria_modifier_'+row_index+'_"]:last');
            var last_mod_id = last_mod.attr('id');
            mod_index = parseInt(last_mod_id.substr(last_mod_id.length-1))+1;
        }
        
        //make new modifier row
        var newRow = $(this).parent().clone(),
            newRowCrit = newRow.find('select[name^="sp_criteria_field"]'),
            newRowMod = newRow.find('select[name^="sp_criteria_modifier"]'),
            newRowVal = newRow.find('input[name^="sp_criteria_value"]'),
            newRowExtra = newRow.find('input[name^="sp_criteria_extra"]'),
            newRowRemove = newRow.find('a[id^="criteria_remove"]');

        //remove error msg
        if (newRow.children().hasClass('errors sp-errors')) {
            newRow.find('span[class="errors sp-errors"]').remove();
        }
        
        //hide the critieria field select box
        newRowCrit.addClass('sp-invisible');
        
        //append modifier index to the new modifier row
        newRowCrit.attr('name', 'sp_criteria_field_'+row_index+'_'+mod_index);
        newRowCrit.attr('id', 'sp_criteria_field_'+row_index+'_'+mod_index);
        newRowCrit.val(criteria_value);
        newRowMod.attr('name', 'sp_criteria_modifier_'+row_index+'_'+mod_index);
        newRowMod.attr('id', 'sp_criteria_modifier_'+row_index+'_'+mod_index);
        newRowMod.val('0');
        newRowVal.attr('name', 'sp_criteria_value_'+row_index+'_'+mod_index);
        newRowVal.attr('id', 'sp_criteria_value_'+row_index+'_'+mod_index);
        newRowVal.val('');
        newRowExtra.attr('name', 'sp_criteria_extra_'+row_index+'_'+mod_index);
        newRowExtra.attr('id', 'sp_criteria_extra_'+row_index+'_'+mod_index);
        newRowExtra.val('');
        disableAndHideExtraField(newRowVal);
        sizeTextBoxes(newRowVal, 'sp_extra_input_text', 'sp_input_text');
        newRowRemove.attr('id', 'criteria_remove_'+row_index+'_'+mod_index);
        
        //remove the 'criteria add' button from new modifier row
        newRow.find('.criteria_add').remove();
        
        $(this).parent().after(newRow);
        appendAddButton();
        appendModAddButton();
        removeButtonCheck();
    });
	
    /********** REMOVE ROW **********/
    form.find('a[id^="criteria_remove"]').live('click', function(){
        var curr = $(this).parent();
        var curr_pos = curr.index();
        var list = curr.parent();
        var list_length = list.find("div:visible").length;
        var count = list_length - curr_pos;
        var next = curr.next();
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
            
            var id = curr.find('[name^="sp_criteria"]').attr('id'),
                delimiter = '_',
                start = 3,
                tokens = id.split(delimiter).slice(start),
                index = tokens.join(delimiter);
             
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
                    .find('[name^="sp_criteria_value"]').val('').end()
                    .find('[name^="sp_criteria_extra"]').val('');
        
        sizeTextBoxes(item_to_hide.find('[name^="sp_criteria_value"]'), 'sp_extra_input_text', 'sp_input_text');
        item_to_hide.hide();

        list.next().show();
        
        // always put '+' button on the last enabled row
        appendAddButton();
        // always put '+' button on the last modifier row
        appendModAddButton();
        
        reindexElements();
        // remove the 'x' button if only one row is enabled
        removeButtonCheck();
    });
	
    /********** SAVE ACTION **********/
    form.find('button[id="save_button"]').live("click", function(event){
        var data = $('form').serializeArray(),
            save_action = 'Playlist/smart-block-criteria-save',
            obj_id = $('input[id="obj_id"]').val();
        enableLoadingIcon();
        $.post(save_action, {format: "json", data: data, obj_id: obj_id}, function(data){
            callback(data, "save");
            setFadeIcon();
            disableLoadingIcon();
        });
    });
    
    /********** GENERATE ACTION **********/
    form.find('button[id="generate_button"]').live("click", function(event){
        var data = $('form').serializeArray(),
            generate_action = 'Playlist/smart-block-generate',
            obj_id = $('input[id="obj_id"]').val();
        enableLoadingIcon();
        $.post(generate_action, {format: "json", data: data, obj_id: obj_id}, function(data){
            callback(data, "generate");
            disableLoadingIcon();
        });
    });
    
    /********** SHUFFLE ACTION **********/
    form.find('button[id="shuffle_button"]').live("click", function(event){
        var data = $('form').serializeArray(),
            shuffle_action = 'Playlist/smart-block-shuffle',
            obj_id = $('input[id="obj_id"]').val();
        enableLoadingIcon();
        $.post(shuffle_action, {format: "json", data: data, obj_id: obj_id}, function(data){
            callback(data, "shuffle");
            disableLoadingIcon();
        });
    });
	
    /********** CHANGE PLAYLIST TYPE **********/
    form.find('dd[id="sp_type-element"]').live("change", function(){
        setupUI();  	
    });
    
    /********** CRITERIA CHANGE **********/
    form.find('select[id^="sp_criteria"]:not([id^="sp_criteria_modifier"])').live("change", function(){
        var index_name = $(this).attr('id'),
            index_num = index_name.charAt(index_name.length-1);
        
        // disable extra field and hide the span
        disableAndHideExtraField($(this), index_num);
        populateModifierSelect(this);
    });
    
    /********** MODIFIER CHANGE **********/
    form.find('select[id^="sp_criteria_modifier"]').live("change", function(){
        var criteria_value = $(this).next(),
            index_name = criteria_value.attr('id'),
            delimiter = '_',
            start = 3,
            tokens = index_name.split(delimiter).slice(start),
            index_num = tokens.join(delimiter);
        
        if ($(this).val() == 'is in the range') {
            enableAndShowExtraField(criteria_value, index_num);
        } else {
            disableAndHideExtraField(criteria_value, index_num);
        }
    });

    setupUI();
    appendAddButton();
    appendModAddButton();
    removeButtonCheck();
}

var static_length = $('.playlist_title').children('h4[id$="_length"]').text();

/*
function setStaticLengthHolder(lenVal) {
    static_length = lenVal;
}
*/

function setFadeIcon(){
    var contents = $("#spl_sortable");
    var show = contents.is(":visible");
    if (show) {
        $("#spl_crossfade").show();
    } else {
        $("#spl_crossfade").hide();
    }
}

/* This function appends a '+' button for the last
 * modifier row of each criteria.
 * If there are no modifier rows, the '+' button
 * remains at the criteria row
 */
function appendModAddButton() {
    var divs = $('#smart-playlist-form').find('div select[name^="sp_criteria_modifier"]').parent(':visible');
    $.each(divs, function(i, div){
        if (i > 0) {
            /* If the criteria field is hidden we know it is a modifier row
             * and can hide the previous row's modifier add button
             */
            if ($(div).find('select[name^="sp_criteria_field"]').hasClass('sp-invisible')) {
                $(div).prev().find('a[id^="modifier_add"]').addClass('sp-invisible');
            } else {
                $(div).prev().find('a[id^="modifier_add"]').removeClass('sp-invisible');
            }
        }

        //always add modifier add button to the last row
        if (i+1 == divs.length) {
            $(div).find('a[id^="modifier_add"]').removeClass('sp-invisible');
        }
    });
}

/* This function re-indexes all the form elements.
 * We need to do this everytime a row gets deleted
 */
function reindexElements() {
    var divs = $('#smart-playlist-form').find('div select[name^="sp_criteria_field"]').parent(),
        index = 0,
        modIndex = 0;
    $.each(divs, function(i, div){
        if (i > 0 && index < 26) {
            
            /* If the current row's criteria field is hidden we know it is
             * a modifier row
             */
            if ($(div).find('select[name^="sp_criteria_field"]').hasClass('sp-invisible')) {
                
                $(div).find('select[name^="sp_criteria_field"]').attr('name', 'sp_criteria_field_'+index+'_'+modIndex);
                $(div).find('select[name^="sp_criteria_field"]').attr('id', 'sp_criteria_field_'+index+'_'+modIndex);
                $(div).find('select[name^="sp_criteria_modifier"]').attr('name', 'sp_criteria_modifier_'+index+'_'+modIndex);
                $(div).find('select[name^="sp_criteria_modifier"]').attr('id', 'sp_criteria_modifier_'+index+'_'+modIndex);
                $(div).find('input[name^="sp_criteria_value"]').attr('name', 'sp_criteria_value_'+index+'_'+modIndex);
                $(div).find('input[name^="sp_criteria_value"]').attr('id', 'sp_criteria_value_'+index+'_'+modIndex);
                $(div).find('input[name^="sp_criteria_extra"]').attr('name', 'sp_criteria_extra_'+index+'_'+modIndex);
                $(div).find('input[name^="sp_criteria_extra"]').attr('id', 'sp_criteria_extra_'+index+'_'+modIndex);
                $(div).find('a[name^="modifier_add"]').attr('id', 'modifier_add_'+index);
                $(div).find('a[id^="criteria_remove"]').attr('id', 'criteria_remove_'+index+'_'+modIndex);
                modIndex++;
            } else {
                index++;
                $(div).find('select[name^="sp_criteria_field"]').attr('name', 'sp_criteria_field_'+index);
                $(div).find('select[name^="sp_criteria_field"]').attr('id', 'sp_criteria_field_'+index);
                $(div).find('select[name^="sp_criteria_modifier"]').attr('name', 'sp_criteria_modifier_'+index);
                $(div).find('select[name^="sp_criteria_modifier"]').attr('id', 'sp_criteria_modifier_'+index);
                $(div).find('input[name^="sp_criteria_value"]').attr('name', 'sp_criteria_value_'+index);
                $(div).find('input[name^="sp_criteria_value"]').attr('id', 'sp_criteria_value_'+index);
                $(div).find('input[name^="sp_criteria_extra"]').attr('name', 'sp_criteria_extra_'+index);
                $(div).find('input[name^="sp_criteria_extra"]').attr('id', 'sp_criteria_extra_'+index);
                $(div).find('a[name^="modifier_add"]').attr('id', 'modifier_add_'+index);
                $(div).find('a[id^="criteria_remove"]').attr('id', 'criteria_remove_'+index);
                modIndex = 0;
            }
        } else if (i > 0) {
            $(div).remove();
        }
    });
}

function setupUI() {
    var playlist_type = $('input:radio[name=sp_type]:checked').val();
    var target_length = $('input[name="sp_limit_value"]').val();
    if (target_length == '') {
        target_length = '0.0';
    }
    
    var dynamic_length = target_length;
    if ($('#obj_type').val() == 'block') {
        if (playlist_type == "0") {
            $('button[id="generate_button"]').show();
            $('button[id="shuffle_button"]').show();
            $('#spl_sortable').show();
            //$('.playlist_title').children('h4[id$="_length"]').text(static_length);
        } else {
            $('button[id="generate_button"]').hide();
            $('button[id="shuffle_button"]').hide();
            $('#spl_sortable').hide();
            //$('.playlist_title').children('h4[id$="_length"]').text(dynamic_length);
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
    /*var criteria = $(e).val(),
        criteria_type = criteriaTypes[criteria],*/
    var criteria_type = getCriteriaOptionType(e),
        div = $(e).siblings('select[id^="sp_criteria_modifier"]');

    div.children().remove();

    if (criteria_type == 's') {
        $.each(stringCriteriaOptions, function(key, value){
            div.append($('<option></option>')
               .attr('value', key)
               .text(value));
        });
    } else {
        $.each(numericCriteriaOptions, function(key, value){
            div.append($('<option></option>')
               .attr('value', key)
               .text(value));
        });
    }
}

function getCriteriaOptionType(e) {
    var criteria = $(e).val();
    return criteriaTypes[criteria];
}

function callback(data, type) {
    var form = $('#smart-playlist-form'),
        json = $.parseJSON(data),
        dt = $('table[id="library_display"]').dataTable();

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
            } else if (type == 'generate') {
            	form.find('.success').text('Smart playlist generated and saved');
            	//redraw library table so the length gets updated
                dt.fnStandingRedraw();
            }
    	    form.find('.success').show();
    	    form.find('#smart_playlist_options').removeClass("closed");
        } else {
            form.find('.success').text('Criteria saved');
            form.find('.success').show();
            //redraw library table incase block changed from static to dynamic or vice versa
            dt.fnStandingRedraw();
            
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
            //redraw library table so the length gets updated
            var dt = $('table[id="library_display"]').dataTable();
            dt.fnStandingRedraw();
            $('div[class="playlist_title"]').find("h4").html(json.blockLength);
        }
        setTimeout(removeSuccessMsg, 5000);
    }
}

function removeSuccessMsg() {
    var $status = $('#smart-playlist-form').find('.success');
    
    $status.fadeOut("slow", function(){$status.empty()});
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

function enableLoadingIcon() {
    $("#side_playlist").block({ 
        message: "Processing...",
        theme: true,
        allowBodyStretch: true,
        applyPlatformOpacityRules: false
    });
}

function disableLoadingIcon() {
    $("#side_playlist").unblock()
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
