$(document).ready(function() {
    setSmartBlockEvents();
});

function setSmartBlockEvents() {
    var form = $('#smart-block-form');
    
    /********** ADD CRITERIA ROW **********/
    form.find('#criteria_add').live('click', function(){
        
        var div = $('dd[id="sp_criteria-element"]').children('div:visible:last');

        div.find('.db-logic-label').text('and').show();
        div = div.next().show();

        div.children().removeAttr('disabled');
        div = div.next();
        if (div.length === 0) {
            $(this).hide();
        }
        
        appendAddButton();
        appendModAddButton();
        removeButtonCheck();
    });
    
    /********** ADD MODIFIER ROW **********/
    form.find('a[id^="modifier_add"]').live('click', function(){
        var criteria_value = $(this).siblings('select[name^="sp_criteria_field"]').val();

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
        
        //keep criteria value the same
        newRowCrit.val(criteria_value);
        
        //reset all other values
        newRowMod.val('0');
        newRowVal.val('');
        newRowExtra.val('');
        disableAndHideExtraField(newRowVal);
        sizeTextBoxes(newRowVal, 'sp_extra_input_text', 'sp_input_text');
        
        //remove the 'criteria add' button from new modifier row
        newRow.find('#criteria_add').remove();
        
        $(this).parent().after(newRow);
        reindexElements();
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
        var prev;
        var index;
        
        //remove error message from current row, if any
        var error_element = curr.find('span[class="errors sp-errors"]');
        if (error_element.is(':visible')) {
            error_element.remove();
        }

        /* assign next row to current row for all rows below and including
         * the row getting removed
         */
        for (var i=0; i<count; i++) {
            
            index = getRowIndex(curr);
            
            var criteria = next.find('[name^="sp_criteria_field"]').val();
            curr.find('[name^="sp_criteria_field"]').val(criteria);
            
            var modifier = next.find('[name^="sp_criteria_modifier"]').val();
            populateModifierSelect(curr.find('[name^="sp_criteria_field"]'), false);
            curr.find('[name^="sp_criteria_modifier"]').val(modifier);
            
            var criteria_value = next.find('[name^="sp_criteria_value"]').val();
            curr.find('[name^="sp_criteria_value"]').val(criteria_value);
             
            /* if current and next row have the extra criteria value
             * (for 'is in the range' modifier), then assign the next
             * extra value to current and remove that element from
             * next row
             */
            if (curr.find('[name^="sp_criteria_extra"]').attr("disabled") != "disabled"
                && next.find('#extra_criteria').is(':visible')) {
            	
                var criteria_extra = next.find('[name^="sp_criteria_extra"]').val();
                curr.find('[name^="sp_criteria_extra"]').val(criteria_extra);
                disableAndHideExtraField(next.find(':first-child'), getRowIndex(next));
            
            /* if only the current row has the extra criteria value,
             * then just remove the current row's extra criteria element
             */
            } else if (curr.find('[name^="sp_criteria_extra"]').attr("disabled") != "disabled"
                       && next.find('#extra_criteria').not(':visible')) {
                disableAndHideExtraField(curr.find(':first-child'), index);
                
            /* if only the next row has the extra criteria value,
             * then add the extra criteria element to current row
             * and assign next row's value to it
             */
            } else if (next.find('#extra_criteria').is(':visible')) {
                criteria_extra = next.find('[name^="sp_criteria_extra"]').val();
                enableAndShowExtraField(curr.find(':first-child'), index);
                curr.find('[name^="sp_criteria_extra"]').val(criteria_extra);
            }

            /* determine if current row is a modifier row
             * if it is, make the criteria select invisible
             */
            prev = curr.prev();
            if (curr.find('[name^="sp_criteria_field"]').val() == prev.find('[name^="sp_criteria_field"]').val()) {
                if (!curr.find('select[name^="sp_criteria_field"]').hasClass('sp-invisible')) {
                    curr.find('select[name^="sp_criteria_field"]').addClass('sp-invisible');
                }
            } else {
                if (curr.find('select[name^="sp_criteria_field"]').hasClass('sp-invisible')) {
                    curr.find('select[name^="sp_criteria_field"]').removeClass('sp-invisible');
                }
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
        if (item_to_hide.find('select[name^="sp_criteria_field"]').hasClass('sp-invisible')) {
            item_to_hide.find('select[name^="sp_criteria_field"]').removeClass('sp-invisible');
        }
        item_to_hide.find('[name^="sp_criteria_field"]').val(0).end()
                    .find('[name^="sp_criteria_modifier"]').val(0).end()
                    .find('[name^="sp_criteria_value"]').val('').end()
                    .find('[name^="sp_criteria_extra"]').val('');
        
        sizeTextBoxes(item_to_hide.find('[name^="sp_criteria_value"]'), 'sp_extra_input_text', 'sp_input_text');
        item_to_hide.hide();

        list.next().show();
        
        //check if last row is a modifier row
        var last_row = list.find('div:visible:last');
        if (last_row.find('[name^="sp_criteria_field"]').val() == last_row.prev().find('[name^="sp_criteria_field"]').val()) {
            if (!last_row.find('select[name^="sp_criteria_field"]').hasClass('sp-invisible')) {
                last_row.find('select[name^="sp_criteria_field"]').addClass('sp-invisible');
            }
        }
        
        // always put '+' button on the last enabled row
        appendAddButton();
        
        reindexElements();
        
        // always put '+' button on the last modifier row
        appendModAddButton();
        
        // remove the 'x' button if only one row is enabled
        removeButtonCheck();
    });
	
    /********** SAVE ACTION **********/
    // moved to spl.js
    
    /********** GENERATE ACTION **********/
    $('button[id="generate_button"]').live("click", function(){
        buttonClickAction('generate', 'Playlist/smart-block-generate');
    });
    
    /********** SHUFFLE ACTION **********/
    $('button[id="shuffle_button"]').live("click", function(){
        buttonClickAction('shuffle', 'Playlist/smart-block-shuffle');
    });
	
    /********** CHANGE PLAYLIST TYPE **********/
    form.find('dd[id="sp_type-element"]').live("change", function(){
        setupUI();
        AIRTIME.library.checkAddButton();
    });
    
    /********** CRITERIA CHANGE **********/
    form.find('select[id^="sp_criteria"]:not([id^="sp_criteria_modifier"])').live("change", function(){
        var index = getRowIndex($(this).parent());
        //need to change the criteria value for any modifier rows
        var critVal = $(this).val();
        var divs = $(this).parent().nextAll(':visible');
        $.each(divs, function(i, div){
            var critSelect = $(div).children('select[id^="sp_criteria_field"]');
            if (critSelect.hasClass('sp-invisible')) {
                critSelect.val(critVal);
            /* If the select box is visible we know the modifier rows
             * have ended
             */
            } else {
                return false;
            }
        });
        
        // disable extra field and hide the span
        disableAndHideExtraField($(this), index);
        populateModifierSelect(this, true);
    });
    
    /********** MODIFIER CHANGE **********/
    form.find('select[id^="sp_criteria_modifier"]').live("change", function(){
        var criteria_value = $(this).next(),
            index_num = getRowIndex($(this).parent());
        
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

function getRowIndex(ele) {
    var id = ele.find('[name^="sp_criteria_field"]').attr('id'),
        delimiter = '_',
        start = 3,
        tokens = id.split(delimiter).slice(start),
        index = tokens.join(delimiter);
    
    return index;
}

/* This function appends a '+' button for the last
 * modifier row of each criteria.
 * If there are no modifier rows, the '+' button
 * remains at the criteria row
 */
function appendModAddButton() {
    var divs = $('#smart-block-form').find('div select[name^="sp_criteria_modifier"]').parent(':visible');
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
    var divs = $('#smart-block-form').find('div select[name^="sp_criteria_field"]').parent(),
        index = 0,
        modIndex = 0;
    /* Hide all logic labels
     * We will re-add them as each row gets indexed
     */
    $('.db-logic-label').text('').hide();

    $.each(divs, function(i, div){
        if (i > 0 && index < 26) {
            
            /* If the current row's criteria field is hidden we know it is
             * a modifier row
             */
            if ($(div).find('select[name^="sp_criteria_field"]').hasClass('sp-invisible')) {
                if ($(div).is(':visible')) {
                    $(div).prev().find('.db-logic-label').text('or').show();
                }
                modIndex++;
            } else {
                if ($(div).is(':visible')) {
                    $(div).prev().find('.db-logic-label').text('and').show();
                }
                index++;
                modIndex = 0;
            }
            
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
        } else if (i > 0) {
            $(div).remove();
        }
    });
}

function buttonClickAction(clickType, url){
    var data = $('#smart-block-form').serializeArray(),
        obj_id = $('input[id="obj_id"]').val();
    
    enableLoadingIcon();
    $.post(url, {format: "json", data: data, obj_id: obj_id}, function(data){
        callback(data, clickType);
        disableLoadingIcon();
    });
}

function setupUI() {
    var playlist_type = $('input:radio[name=sp_type]:checked').val();
    var target_length = $('input[name="sp_limit_value"]').val();
    if (target_length == '') {
        target_length = '0.0';
    }
    
    /* Activate or Deactivate shuffle button
     * It is only active if playlist is not empty
     */
    var plContents = $('#spl_sortable').children();
    var shuffleButton = $('button[id="shuffle_button"], button[id="playlist_shuffle_button"], button[id="pl-bl-clear-content"]');

    if (!plContents.hasClass('spl_empty')) {
        if (shuffleButton.hasClass('ui-state-disabled')) {
            shuffleButton.removeClass('ui-state-disabled');
            shuffleButton.removeAttr('disabled');
        }
    } else if (!shuffleButton.hasClass('ui-state-disabled')) {
        shuffleButton.addClass('ui-state-disabled');
        shuffleButton.attr('disabled', 'disabled');
    }
    
    var dynamic_length = target_length;
    if ($('#obj_type').val() == 'block') {
        if (playlist_type == "0") {
            $('button[id="generate_button"]').show();
            $('button[id="shuffle_button"]').show();
            $('#spl_sortable').show();
        } else {
            $('button[id="generate_button"]').hide();
            $('button[id="shuffle_button"]').hide();
            $('#spl_sortable').hide();
        }
    }
    
    $(".playlist_type_help_icon").qtip({
        content: {
            text: $.i18n._("A static smart block will save the criteria and generate the block content immediately. This allows you to edit and view it in the Library before adding it to a show.")+"<br /><br />" +
                  $.i18n._("A dynamic smart block will only save the criteria. The block content will get generated upon adding it to a show. You will not be able to view and edit the content in the Library.")
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
    
    $(".repeat_tracks_help_icon").qtip({
        content: {
            text: sprintf($.i18n._("The desired block length will not be reached if %s cannot find enough unique tracks to match your criteria. Enable this option if you wish to allow tracks to be added multiple times to the smart block."), PRODUCT_NAME)
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
    if (ele.hasClass(classToRemove)) {
        ele.removeClass(classToRemove).addClass(classToAdd);
    }
}

function populateModifierSelect(e, popAllMods) {
    var criteria_type = getCriteriaOptionType(e),
        index = getRowIndex($(e).parent()),
        divs;
 
    if (popAllMods) {
        index = index.substring(0, 1);
    }
    divs = $(e).parents().find('select[id^="sp_criteria_modifier_'+index+'"]');
    
    $.each(divs, function(i, div){
        $(div).children().remove();
    
        if (criteria_type == 's') {
            $.each(stringCriteriaOptions, function(key, value){
                $(div).append($('<option></option>')
                   .attr('value', key)
                   .text(value));
            });
        } else {
            $.each(numericCriteriaOptions, function(key, value){
                $(div).append($('<option></option>')
                   .attr('value', key)
                   .text(value));
            });
        }
    });
}

function getCriteriaOptionType(e) {
    var criteria = $(e).val();
    return criteriaTypes[criteria];
}

function callback(json, type) {
    var dt = $('table[id="library_display"]').dataTable();

    if (type == 'shuffle' || type == 'generate') {
        if (json.error !== undefined) {
            alert(json.error);
        }
        AIRTIME.playlist.fnOpenPlaylist(json);
        var form = $('#smart-block-form');
        if (json.result == "0") {
            if (type == 'shuffle') {
                form.find('.success').text($.i18n._('Smart block shuffled'));
            } else if (type == 'generate') {
            	form.find('.success').text($.i18n._('Smart block generated and criteria saved'));
            	//redraw library table so the length gets updated
                dt.fnStandingRedraw();
            }
            form.find('.success').show();
        }
	    form.find('#smart_block_options').removeClass("closed");
    } else {
        AIRTIME.playlist.fnOpenPlaylist(json);
        var form = $('#smart-block-form');
        if (json.result == "0") {
            $('#sp-success-saved').text($.i18n._('Smart block saved'));
            $('#sp-success-saved').show();
        
            //redraw library table so the length gets updated
            var dt = $('table[id="library_display"]').dataTable();
            dt.fnStandingRedraw();
        }
        form.find('#smart_block_options').removeClass("closed");
    }
    setTimeout(removeSuccessMsg, 5000);
}

function appendAddButton() {
    var add_button = "<a class='btn btn-small' id='criteria_add'>" +
                     "<i class='icon-white icon-plus'></i></a>";
    var rows = $('#smart_block_options'),
        enabled = rows.find('select[name^="sp_criteria_field"]:enabled');

    rows.find('#criteria_add').remove();
    
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
        message: $.i18n._("Processing..."),
        theme: true,
        allowBodyStretch: true,
        applyPlatformOpacityRules: false
    });
}

function disableLoadingIcon() {
    $("#side_playlist").unblock()
}
// We need to know if the criteria value will be a string
// or numeric value in order to populate the modifier
// select list
var criteriaTypes = {
    0              : "",
    "album_title"  : "s",
    "bit_rate"     : "n",
    "bpm"          : "n",
    "composer"     : "s",
    "conductor"    : "s",
    "copyright"    : "s",
    "cuein"        : "n",
    "cueout"       : "n",
    "artist_name"  : "s",
    "encoded_by"   : "s",
    "utime"        : "n",
    "mtime"        : "n",
    "lptime"       : "n",
    "genre"        : "s",
    "isrc_number"  : "s",
    "label"        : "s",
    "language"     : "s",
    "length"       : "n",
    "mime"         : "s",
    "mood"         : "s",
    "owner_id"     : "s",
    "replay_gain"  : "n",
    "sample_rate"  : "n",
    "track_title"  : "s",
    "track_number" : "n",
    "info_url"     : "s",
    "year"         : "n"
};

var stringCriteriaOptions = {
    "0" : $.i18n._("Select modifier"),
    "contains" : $.i18n._("contains"),
    "does not contain" : $.i18n._("does not contain"),
    "is" : $.i18n._("is"),
    "is not" : $.i18n._("is not"),
    "starts with" : $.i18n._("starts with"),
    "ends with" : $.i18n._("ends with")
};
    
var numericCriteriaOptions = {
    "0" : $.i18n._("Select modifier"),
    "is" : $.i18n._("is"),
    "is not" : $.i18n._("is not"),
    "is greater than" : $.i18n._("is greater than"),
    "is less than" : $.i18n._("is less than"),
    "is in the range" : $.i18n._("is in the range")
};
