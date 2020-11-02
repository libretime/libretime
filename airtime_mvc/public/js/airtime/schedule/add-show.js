/**
*
*    Schedule Dialog creation methods.
*
*/

function openAddShowForm(nowOrFuture) {
     if($("#add-show-form").length == 1) {
        if( ($("#add-show-form").css('display')=='none')) {

            if (nowOrFuture === true) //true means "now"
            {
                $('#add_show_start_now-now').attr('checked', 'checked');
                setupStartTimeWidgets();
            }
            $("#add-show-form").show();

            windowResize();

            // collapse advanced configuration sections
            $('#schedule-show-auto').hide();
            $('#live-stream-override').hide();
            $('#schedule-record-rebroadcast').hide();
            $('#schedule-show-who').hide();
            $('#schedule-show-style').hide();
            
        }
        $("#schedule-show-what").show(0, function(){
            $add_show_name = $("#add_show_name");
            $add_show_name.focus();
            $add_show_name.select();
        });
    }
}

function makeAddShowButton() {
    if($('.add-button').length === 0) {
        $('.fc-header-left')
            .prepend('<span class="fc-header-space"></span>')
            .prepend('<span class="btn-group">' +
                        '<button onclick="showForm()" class="add-button btn btn-small btn-new">' +
                            '<i class="icon-white icon-plus"></i>' +
                            '<span>' + $.i18n._("New Show") + '</span>' +
                        '</button>' +
                    '</span>');
    }
}

function showForm() {
    openAddShowForm(true);
    toggleAddShowButton();
}

function toggleAddShowButton(){
    var aTag = $('.add-button');
    aTag.prop('disabled', function(i, v) { return !v; });
}

function setupStartTimeWidgets() {
    if ($('input[name=add_show_start_now]:checked').val() == 'now') {
        $('#add_show_start_date').prop('disabled', 'true');
        $('#add_show_start_time').prop('disabled', 'true');
        var currentTimezone = $("#add_show_timezone").val();

        //Set the show start time to now (in the show timezone)
        var now = moment(new Date()).tz(currentTimezone);
        $('#add_show_start_date').val(now.format('YYYY-MM-DD'));
        $('#add_show_start_time').val(now.format('HH:mm'));

        //Set the show end time to be now + 1 hour.
        var nowShowEnd = now.add(1, 'h');
        $('#add_show_end_date').val(nowShowEnd.format('YYYY-MM-DD'));
        $('#add_show_end_date_no_repeat').val(nowShowEnd.format('YYYY-MM-DD'));
        $('#add_show_end_time').val(nowShowEnd.format('HH:mm'));

        //Disabled linked show option since user won't be able to schedule
        //content
        $('#add_show_linked').prop('disabled', 'true');

    } else {
        //Do not enable start date and time option when a show has already started
        if (!$('#add_show_start_now-now').prop('disabled')) {
            $('#add_show_start_date').removeProp('disabled');
            $('#add_show_start_time').removeProp('disabled');
        }
    }
}

function calculateShowColor() {
    var bgColorEle = $("#add_show_background_color");
    var textColorEle = $("#add_show_color");
    var colorCode = stringToColor($('#add_show_start_time').val());//$(this).val());
    //bgColorEle.val(colorCode);
    //textColorEle.val(getContrastYIQ(colorCode));
}

//$el is DOM element #add-show-form
//form is the new form contents to append to $el
function redrawAddShowForm($el, form) {
    
    //need to clean up the color picker.
    $el.find("#schedule-show-style input").each(function(i, el){
        var $input = $(this), 
            colId = $input.data("colorpickerId");
        
        $("#"+colId).remove();
        $input.removeData();
    });
    
    $el.empty().append(form);
    
    setAddShowEvents($el);
}

function closeAddShowForm(event) {
    event.stopPropagation();
    event.preventDefault();

    var $el = $("#add-show-form");
    
    $el.hide();
    windowResize();

    $.get(baseUrl+"Schedule/get-form", {format:"json"}, function(json) {

        redrawAddShowForm($el, json.form);
    });

    toggleAddShowButton();
}

//dateText mm-dd-yy
function startDpSelect(dateText, inst) {
    var time, date;

    time = dateText.split("-");
    date = new Date(time[0], time[1] - 1, time[2]);

    if (inst.input)
        inst.input.trigger('input');
}

function endDpSelect(dateText, inst) {
    var time, date;
    
    time = dateText.split("-");
    date = new Date(time[0], time[1] - 1, time[2]);

    if (inst.input)
        inst.input.trigger('input');
}

function createDateInput(el, onSelect) {
    var date;

    el.datepicker({
        minDate: adjustDateToServerDate(new Date(), timezoneOffset),
        onSelect: onSelect,
        dateFormat: 'yy-mm-dd',
        //i18n_months, i18n_days_short are in common.js
        monthNames: i18n_months,
        dayNamesMin: i18n_days_short,
        closeText: $.i18n._('Close'),
        //showButtonPanel: true,
        firstDay: calendarPref.weekStart
        });
}

function autoSelect(event, ui) {

    $("#add_show_hosts-"+ui.item.index).attr("checked", "checked");
    event.preventDefault();
}

function findHosts(request, callback) {
    var search, url;

    url = baseUrl+"User/get-hosts";
    search = request.term;

    var noResult = new Array();
    noResult[0] = new Array();
    noResult[0]['value'] = $("#add_show_hosts_autocomplete").val();
    noResult[0]['label'] = $.i18n._("No result found");
    noResult[0]['index'] = null;
    
    $.post(url,
        {format: "json", term: search},

        function(json) {
            if(json.hosts.length<1){
                callback(noResult);
            }else{
                callback(json.hosts);
            }
        });

}

function beginEditShow(data){
    
    if (data.show_error == true){
        alertShowErrorAndReload();
        return false;
    }
    
    redrawAddShowForm($("#add-show-form"), data.newForm);
    toggleAddShowButton();
    openAddShowForm(false);
}

function onStartTimeSelect(){
    $("#add_show_start_time").trigger('input');
}

function onEndTimeSelect(){
    $("#add_show_end_time").trigger('input');
}

function padZeroes(number, length)
{
    var str = '' + number;
    while (str.length < length) {str = '0' + str;}
    return str;
}

function hashCode(str) { // java String#hashCode
    var hash = 0;
    for (var i = 0; i < str.length; i++) {
       hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    return hash;
} 

function intToRGB(i){
    return (padZeroes(((i>>16)&0xFF).toString(16), 2) + 
           padZeroes(((i>>8)&0xFF).toString(16), 2)+ 
           padZeroes((i&0xFF).toString(16), 2)
           );
}

function stringToColor(s)
{
    var palette = ['42d5a1', '56bd99', '65ab93', '7b938b',
        '42a4d5', '569bbd', '6594ab', '7b8b93',
    '4264d5', '566fbd', '6576ab', '7b8193'];
    //var palette = ['d56f42', 'aad542', '7242d5', '42d563', 'd542be'];
    return palette[Math.abs(hashCode(s)) % palette.length];
    //return intToRGB(hashCode(s));
}

function getContrastYIQ(hexcolor){
    var r = parseInt(hexcolor.substr(0,2),16);
    var g = parseInt(hexcolor.substr(2,2),16);
    var b = parseInt(hexcolor.substr(4,2),16);
    var yiq = ((r*299)+(g*587)+(b*114))/1000;
    return (yiq >= 128) ? '000000' : 'ffffff';
}


function setAddShowEvents(form) {

    //var form = $("#add-show-form");

    form.find("h3").click(function(){
        $(this).next().toggle();
    });


    form.find('input:radio[name=add_show_start_now]').click(function() {
        setupStartTimeWidgets();

        if ($(this).val() == "future") {
            $('#add_show_linked').removeProp('disabled');
        }
    });

    if(!form.find("#add_show_has_autoplaylist").attr('checked')) {
        form.find("#add_show_playlist_dropdown").hide();
        form.find("#add_show_autoplaylist_repeat").hide();
    }
    else {
        $("#add_show_playlist_dropdown").show();
    }

    if(!form.find("#add_show_repeats").attr('checked')) {
        form.find("#schedule-show-when > fieldset:last").hide();
        $("#add_show_rebroadcast_relative").hide();
    }
    else {
        $("#add_show_rebroadcast_absolute").hide();
    }

    if(!form.find("#add_show_record").attr('checked')) {
        form.find("#add_show_rebroadcast").hide();
    }

    if(!form.find("#add_show_rebroadcast").attr('checked')) {
        form.find("#schedule-record-rebroadcast > fieldset:not(:first-child)").hide();
    }
    
    // If we're adding a new show or the show has no logo, hide the "Current Logo" element tree
    $("[id^=add_show_logo_current]").toggle(($("#add_show_logo_current").attr("src") !== "")
            && ($(".button-bar.bottom").find(".ui-button-text").text() === "Update show"));
    
    var submitButton = $(".button-bar.bottom").find(".add-show-submit");
    $("[id^=add_show_instance_description]").toggle(submitButton.attr("data-action") === "edit-repeating-show-instance");

    form.find("#add_show_has_autoplaylist").click(function(){
        $(this).blur();
        form.find("#add_show_playlist_dropdown").toggle();
        form.find("#add_show_autoplaylist_repeat").toggle();
    });

    form.find("#add_show_repeats").click(function(){
        $(this).blur();
        form.find("#schedule-show-when > fieldset:last").toggle();
        
        var checkBoxSelected = false;
        var days = form.find("#add_show_day_check-element input").each( function() {
                var currentCheckBox = $(this).attr("checked");
                if (currentCheckBox && currentCheckBox == "checked"){
                    checkBoxSelected = true;
                }
            });
        

        if (!checkBoxSelected){
            var d = getDateFromString(form.find("#add_show_start_date").attr("value"));
            if ( d != null)
                form.find("#add_show_day_check-"+d.getDay()).attr('checked', true);
        }
        
        //must switch rebroadcast displays
        if(form.find("#add_show_rebroadcast").attr('checked')) {

            if($(this).attr('checked')){
                form.find("#add_show_rebroadcast_absolute").hide();
                form.find("#add_show_rebroadcast_relative").show();
            }
            else {
                form.find("#add_show_rebroadcast_absolute").show();
                form.find("#add_show_rebroadcast_relative").hide();
            }
        }
    });

    form.find("#add_show_linked").click(function(){
        if ($(this).attr("readonly")) {
            if ($("#show-link-readonly-warning").length === 0) {
                $(this).parent().after("<ul id='show-link-readonly-warning' class='errors'><li>"+$.i18n._("Warning: You cannot change this field while the show is currently playing")+"</li></ul>");
            }
            return false;
        }
        
        //only display the warning message if a show is being edited
        if ($(".button-bar.bottom").find(".ui-button-text").text() === "Update show") {
            if ($(this).attr("checked") && $("#show-link-warning").length === 0) {
                $(this).parent().after("<ul id='show-link-warning' class='errors'><li>"+$.i18n._("Warning: All other repetitions of this show will have their contents replaced to match the show you selected 'Edit Show' with.")+"</li></ul>");
            }
            
            if (!$(this).attr("checked") && $("#show-link-warning").length !== 0) {
                $("#show-link-warning").remove();
            }
        }
    });

    form.find("#add_show_linked-label").before("<span class='show_linking_help_icon'></span>");

    form.find("#add_show_record").click(function(){
        $(this).blur();
        form.find("#add_show_rebroadcast").toggle();

        if (form.find("#add_show_record").attr("checked")) {
            form.find("#add_show_linked").attr("checked", false).attr("disabled", true);
        } else {
            form.find("#add_show_linked").attr("disabled", false);
        }

        //uncheck rebroadcast checkbox
        form.find("#add_show_rebroadcast").attr('checked', false);

        //hide rebroadcast options
        form.find("#schedule-record-rebroadcast > fieldset:not(:first-child)").hide();
    });

    form.find("#add_show_rebroadcast").click(function(){
        $(this).blur();
        if(form.find("#add_show_record").attr('checked')){
            if($(this).attr('checked') && !form.find("#add_show_repeats").attr('checked')) {
                form.find("#add_show_rebroadcast_absolute").show();
            }
            else if($(this).attr('checked') && form.find("#add_show_repeats").attr('checked')) {
                form.find("#add_show_rebroadcast_relative").show();
            }
            else {
                form.find("#schedule-record-rebroadcast > fieldset:not(:first-child)").hide();
            }
        }
    });

    // in case user is creating a new show, there will be
    // no show_id so we have to store the default timezone
    // to be able to do the conversion when the timezone
    // setting changes
    var currentTimezone = form.find("#add_show_timezone").val();

    form.find("#add_show_timezone").change(function(){
        var startDateField = form.find("#add_show_start_date"),
            startTimeField = form.find("#add_show_start_time"),
            endDateField = form.find("#add_show_end_date_no_repeat"),
            endTimeField = form.find("#add_show_end_time"),
            newTimezone = form.find("#add_show_timezone").val();

        $.post(baseUrl+"Schedule/localize-start-end-time",
               {format: "json",
                startDate: startDateField.val(),
                startTime: startTimeField.val(),
                endDate: endDateField.val(),
                endTime: endTimeField.val(),
                newTimezone: newTimezone,
                oldTimezone: currentTimezone}, function(json){

            startDateField.val(json.start.date);
            startTimeField.val(json.start.time);
            endDateField.val(json.end.date);
            endTimeField.val(json.end.time);
            // Change the timezone now that we've updated the times
            currentTimezone = newTimezone;
        });
    });

    form.find("#add_show_repeat_type").change(function(){
        toggleRepeatDays();
        toggleMonthlyRepeatType();
    });
    toggleMonthlyRepeatType();
    toggleRepeatDays();
    function toggleRepeatDays() {
        if(form.find("#add_show_repeat_type").val() == 2 || form.find("#add_show_repeat_type").val() == 3) {
            form.find("#add_show_day_check-label, #add_show_day_check-element").hide();
            //form.find("#add_show_monthly_repeat_type-label, #add_show_monthly_repeat_type-element").show();
        }
        else {
            form.find("#add_show_day_check-label, #add_show_day_check-element").show();
            //form.find("#add_show_monthly_repeat_type-label, #add_show_monthly_repeat_type-element").hide();
        }
    }
    function toggleMonthlyRepeatType() {
        if (form.find("#add_show_repeat_type").val() == 2) {
            form.find("#add_show_monthly_repeat_type-label, #add_show_monthly_repeat_type-element").show();
        } else {
            form.find("#add_show_monthly_repeat_type-label, #add_show_monthly_repeat_type-element").hide();
        }
    }

    form.find("#add_show_day_check-label").addClass("block-display");
    form.find("#add_show_day_check-element").addClass("block-display clearfix");
    form.find("#add_show_day_check-element label").addClass("wrapp-label");
    form.find("#add_show_day_check-element br").remove();

    form.find(".show_timezone_help_icon").qtip({
        content: {
            text: $.i18n._("Timezone is set to the station timezone by default. Shows in the calendar will be displayed in your local time defined by the " +
                "Interface Timezone in your user settings.")
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
        }
    });
    
    form.find(".show_autoplaylist_help_icon").qtip({
        content: {
            text: $.i18n._("Autoloading playlists' contents are added to shows one hour before the show airs. <a target='_blank' href='http://libretime.org/docs/playlists'>More information</a>")
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
        }
    });

    form.find(".airtime_auth_help_icon").qtip({
        content: {
            text: $.i18n._("This follows the same security pattern for the shows: only users assigned to the show can connect.")
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
        }
    });
    form.find(".custom_auth_help_icon").qtip({
        content: {
            text: $.i18n._("Specify custom authentication which will work only for this show.")
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
        }
    });
    form.find(".stream_username_help_icon").qtip({
        content: {
            text: $.i18n._("If your live streaming client does not ask for a username, this field should be 'source'.")
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
        }
    });
    form.find(".show_linking_help_icon").qtip({
        content: {
            text: $.i18n._("By linking your repeating shows any media items scheduled in any repeat show will also get scheduled in the other repeat shows")
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
        }
    });
    function endDateVisibility(){
        if(form.find("#add_show_no_end").is(':checked')){
            form.find("#add_show_end_date").hide();
        } else {
            form.find("#add_show_end_date").show();
        }
    }
    endDateVisibility();
    form.find("#add_show_no_end").click(endDateVisibility);

    createDateInput(form.find("#add_show_start_date"), startDpSelect);
    createDateInput(form.find("#add_show_end_date_no_repeat"), endDpSelect);
    createDateInput(form.find("#add_show_end_date"), endDpSelect);

    $("#add_show_start_time").timepicker({
        amPmText: ['', ''],
        defaultTime: '00:00',
        onSelect: onStartTimeSelect,
        hourText: $.i18n._("Hour"),
        minuteText: $.i18n._("Minute")
    });
    $("#add_show_end_time").timepicker({
        amPmText: ['', ''],
        onSelect: onEndTimeSelect,
        hourText: $.i18n._("Hour"),
        minuteText: $.i18n._("Minute")
    });

    form.find('input[name^="add_show_rebroadcast_date_absolute"]').datepicker({
        minDate: adjustDateToServerDate(new Date(), timezoneOffset),
        dateFormat: 'yy-mm-dd',
        //i18n_months, i18n_days_short are in common.js
        monthNames: i18n_months,
        dayNamesMin: i18n_days_short,
        closeText: 'Close',
        showButtonPanel: true,
        firstDay: calendarPref.weekStart
    });

    form.find('input[name^="add_show_rebroadcast_time"]').timepicker({
        amPmText: ['', ''],
        defaultTime: '',
        closeButtonText: $.i18n._("Done"),
        hourText: $.i18n._("Hour"),
        minuteText: $.i18n._("Minute")
    });

    form.find(".add_absolute_rebroadcast_day").click(function(){
        var li = $(this).parent().find("ul.formrow-repeat > li:visible:last").next();

        li.show();
        li = li.next();
        if(li.length === 0) {
            $(this).hide();
        }
    });

    form.find('a[id^="remove_rebroadcast"]').click(function(){
        var list = $(this).parent().parent();
        var li_num = $(this).parent().index();
        var num = list.find("li").length;
        var count = num - li_num;

        var curr = $(this).parent();
        var next = curr.next();

        for(var i=0; i<=count; i++) {
            var date = next.find('[name^="add_show_rebroadcast_date"]').val();
            curr.find('[name^="add_show_rebroadcast_date"]').val(date);
            var time = next.find('[name^="add_show_rebroadcast_time"]').val();
            curr.find('[name^="add_show_rebroadcast_time"]').val(time);

            curr = next;
            next = curr.next();
        }

        list.find("li:visible:last")
                .find('[name^="add_show_rebroadcast_date"]')
                    .val('')
                .end()
                .find('[name^="add_show_rebroadcast_time"]')
                    .val('')
                .end()
            .hide();

        list.next().show();
    });

    form.find("#add_show_hosts_autocomplete").autocomplete({
        source: findHosts,
        select: autoSelect,
        delay: 200
    });
    
    form.find("#add_show_hosts_autocomplete").keypress(function(e){
        if( e.which == 13 ){
            return false;
        }
    })

    form.find("#schedule-show-style .input_text").ColorPicker({
        onChange: function (hsb, hex, rgb, el) {
            $(el).val(hex);
        },
        onSubmit: function(hsb, hex, rgb, el) {
            $(el).val(hex);
            $(el).ColorPickerHide();
        },
        onBeforeShow: function () {
            $(this).ColorPickerSetColor(this.value);
        }
    });
    
    // when an image is uploaded, we want to show it to the user
    form.find("#add_show_logo").change(function(event) {
        if (this.files && this.files[0]) {
            $("#add_show_logo_preview").show();
            var reader = new FileReader(); // browser compatibility?
            
            reader.onload = function (e) {
                $("#add_show_logo_preview")
                    .attr('src', e.target.result);
            };

            // check image size so we don't crash the page trying to render
            if (validateImage(this.files[0], $("#add_show_logo"))) {
                // read the image data as though it were a data URI
                reader.readAsDataURL(this.files[0]);
            } else {
                // remove the file element data
                $(this).val('').replaceWith($(this).clone(true));
                $("#add_show_logo_preview").hide();
            }
        } else {
            $("#add_show_logo_preview").hide();
        }
    });
    
    form.find("#add_show_logo_current_remove").click(function() {
        if (confirm($.i18n._('Are you sure you want to delete the current logo?'))) {
            var showId = $("#add_show_id").attr("value");
            
            if (showId && $("#add_show_logo_current").attr("src") !== "") {
                var action = '/rest/show-image?csrf_token=' + $('#csrf').val() + '&id=' + showId;
                
                $.ajax({
                    url: action,
                    data: '',
                    type: 'DELETE',
                    success: function() {
                        $("#add_show_logo_current").prop("src", "");
                        $("[id^=add_show_logo_current]").hide();
                    }
                });
            }
        }
    });
    
    form.find("#add-show-close").click(closeAddShowForm);

    form.find(".add-show-submit").click(function(event) {
        event.preventDefault();
        
        var addShowButton = $(this);
        
        $('#schedule-add-show').block({ 
            message: null,
            applyPlatformOpacityRules: false
        });

        //when editing a show, the record option is disabled
        //we have to enable it to get the correct value when
        //we call serializeArray()
        if (form.find("#add_show_record").attr("disabled", true)) {
            form.find("#add_show_record").attr("disabled", false);
        }
        
        var startDateDisabled = false,
            startTimeDisabled = false;
        
        // Similarly, we need to re-enable start date and time if they're disabled
        if (form.find("#add_show_start_date").prop("disabled") === true) {
            form.find("#add_show_start_date").attr("disabled", false);
            startDateDisabled = true;
        }
        if (form.find("#add_show_start_time").prop("disabled") === true) {
            form.find("#add_show_start_time").attr("disabled", false);
            startTimeDisabled = true;
        }

        var data = $("form").serializeArray();
        // We need to notify the application if date and time were disabled
        data.push({name: 'start_date_disabled', value: startDateDisabled});
        data.push({name: 'start_time_disabled', value: startTimeDisabled});
        
        var hosts = $('#add_show_hosts-element input').map(function() {
            if($(this).attr("checked")) {
                return $(this).val();
            }
        }).get();

        var days = $('#add_show_day_check-element input').map(function() {
            if($(this).attr("checked")) {
                return $(this).val();
            }
        }).get();

        var start_date = $("#add_show_start_date").val(),
            end_date = $("#add_show_end_date").val(),
            action = baseUrl+"Schedule/"+String(addShowButton.attr("data-action"));
        
        var image;
        if ($('#add_show_logo')[0] && $('#add_show_logo')[0].files
                && $('#add_show_logo')[0].files[0]) {
            image = new FormData();
            image.append('file', $('#add_show_logo')[0].files[0]);
        }

        $.ajax({
            url: action, 
            data: {format: "json", data: data, hosts: hosts, days: days},
            success: function(json) {
                if (json.showId && image) { // Successfully added the show, and it contains an image to upload
                    var imageAction = '/rest/show-image?csrf_token=' + $('#csrf').val() + '&id=' + json.showId;
                    
                    // perform a second xhttprequest in order to send the show image
                    $.ajax({
                        url: imageAction,
                        data: image,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST'
                    });
                }

                $('#schedule-add-show').unblock();
                
                var $addShowForm = $("#add-show-form");
                
                if (json.form) {
                    
                    redrawAddShowForm($addShowForm, json.form);
    
                    $("#add_show_end_date").val(end_date);
                    $("#add_show_start_date").val(start_date);
                    showErrorSections();
                } else if (json.edit) {
                    $("#schedule_calendar").removeAttr("style")
                        .fullCalendar('render');
    
                    $addShowForm.hide();
                    toggleAddShowButton();
                    $.get(baseUrl+"Schedule/get-form", {format:"json"}, function(json){
                        redrawAddShowForm($addShowForm, json.form);
                    });
                } else {
                    redrawAddShowForm($addShowForm, json.newForm);
                    scheduleRefetchEvents(json);
                    $addShowForm.hide();
                    toggleAddShowButton();
                }

                /* CC-6062: Resize the window to avoid stretching the last column */
                windowResize();
                makeAddShowButton();
            }
        });
    });

    var regDate = new RegExp(/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/);
    var regTime = new RegExp(/^[0-2][0-9]:[0-5][0-9]$/);
    
    // when start date/time changes, set end date/time to start date/time+1 hr
    $('#add_show_start_date, #add_show_start_time').bind('input', 'change', function(){
        var startDateString = $('#add_show_start_date').val();
        var startTimeString = $('#add_show_start_time').val();
        
        if(regDate.test(startDateString) && regTime.test(startTimeString)){
            var startDate = startDateString.split('-');
            var startTime = startTimeString.split(':');
            var startDateTime = new Date(startDate[0], parseInt(startDate[1], 10)-1, startDate[2], startTime[0], startTime[1], 0, 0);
    
            var endDateString = $('#add_show_end_date_no_repeat').val();
            var endTimeString = $('#add_show_end_time').val()
            var endDate = endDateString.split('-');
            var endTime = endTimeString.split(':');
            var endDateTime = new Date(endDate[0], parseInt(endDate[1], 10)-1, endDate[2], endTime[0], endTime[1], 0, 0);
    
            if(startDateTime.getTime() >= endDateTime.getTime()){
                var duration = $('#add_show_duration').val();
                // parse duration
                var time = 0;
                var info = duration.split(' ');
                var h = parseInt(info[0], 10);
                time += h * 60 * 60* 1000;
                if(info.length >1 && $.trim(info[1]) !== ''){
                    var m = parseInt(info[1], 10);
                    time += m * 60 * 1000;
                }
                endDateTime = new Date(startDateTime.getTime() + time);
            }
    
            var endDateFormat = endDateTime.getFullYear() + '-' + pad(endDateTime.getMonth()+1,2) + '-' + pad(endDateTime.getDate(),2);
            var endTimeFormat = pad(endDateTime.getHours(),2) + ':' + pad(endDateTime.getMinutes(),2);
    
            $('#add_show_end_date_no_repeat').val(endDateFormat);
            $('#add_show_end_time').val(endTimeFormat);
    
            // calculate duration
            var startDateTimeString = startDateString + " " + startTimeString;
            var endDateTimeString = $('#add_show_end_date_no_repeat').val() + " " + $('#add_show_end_time').val();
            var timezone = $("#add_show_timezone").val();
            calculateDuration(startDateTimeString, endDateTimeString, timezone);
        }
    });

    // when end date/time changes, check if the changed date is in past of start date/time
    $('#add_show_end_date_no_repeat, #add_show_end_time').bind('input', 'change', function(){
        var endDateString = $('#add_show_end_date_no_repeat').val();
        var endTimeString = $('#add_show_end_time').val()
        
        if(regDate.test(endDateString) && regTime.test(endTimeString)){
            var startDateString = $('#add_show_start_date').val();
            var startTimeString = $('#add_show_start_time').val();
            var startDate = startDateString.split('-');
            var startTime = startTimeString.split(':');
            var startDateTime = new Date(startDate[0], parseInt(startDate[1], 10)-1, startDate[2], startTime[0], startTime[1], 0, 0);

            var endDate = endDateString.split('-');
            var endTime = endTimeString.split(':');
            var endDateTime = new Date(endDate[0], parseInt(endDate[1], 10)-1, endDate[2], endTime[0], endTime[1], 0, 0);
    
            if(startDateTime.getTime() > endDateTime.getTime()){
                $('#add_show_end_date_no_repeat').css('background-color', '#F49C9C');
                $('#add_show_end_time').css('background-color', '#F49C9C');
            }else{
                $('#add_show_end_date_no_repeat').css('background-color', '');
                $('#add_show_end_time').css('background-color', '');
            }
    
            // calculate duration
            var startDateTimeString = startDateString + " " + startTimeString;
            var endDateTimeString = endDateString + " " + endTimeString;
            var timezone = $("#add_show_timezone").val();
            calculateDuration(startDateTimeString, endDateTimeString, timezone);
        }
    });

    if($('#cb_custom_auth').attr('checked')){
        $('#custom_auth_div').show()
    }else{
        $('#custom_auth_div').hide()
    }
    
    $('#cb_custom_auth').change(function(){
        if($(this).attr('checked')){
            $('#custom_auth_div').show()
        }else{
            $('#custom_auth_div').hide()
        }
    })

    function calculateDuration(startDateTime, endDateTime, timezone){
        var loadingIcon = $('#icon-loader-small');
        
        loadingIcon.show();
        $.post(
            baseUrl+"Schedule/calculate-duration", 
            {startTime: startDateTime, endTime: endDateTime, timezone: timezone}, 
            function(data) {
                $('#add_show_duration').val(JSON.parse(data));
                loadingIcon.hide();
        });
    }
    
    // Since Zend's setAttrib won't apply through the wrapper, set accept=image/* here
    $("#add_show_logo").prop("accept", "image/*");

    //$('#add_show_name').bind('input', 'change', function(){
    $('#add_show_start_time').bind('input', 'change', function(){
        calculateShowColor();
    });

}

function showErrorSections() {

    if($("#schedule-show-what .errors").length > 0) {
        $("#schedule-show-what").show();
    }
    if($("#schedule-show-when .errors").length > 0) {
        $("#schedule-show-when").show();
    }
    if($("#schedule-show-who .errors").length > 0) {
        $("#schedule-show-who").show();
    }
    if($("#schedule-show-style .errors").length > 0) {
        $("#schedule-show-style").show();
    }
    if($("#add_show_rebroadcast_absolute .errors").length > 0) {
        $("#schedule-record-rebroadcast").show();
        $("#add_show_rebroadcast_absolute").show();
    }
    if($("#live-stream-override .errors").length > 0) {
        $("#live-stream-override").show();
    }
    if($("#add_show_rebroadcast_relative .errors").length > 0) {
        $("#schedule-record-rebroadcast").show();
        $("#add_show_rebroadcast_relative").show();
    }
}

$(document).ready(function() {
    setAddShowEvents($("#add-show-form"));
});

//Alert the error and reload the page
//this function is used to resolve concurrency issue
function alertShowErrorAndReload(){
    alert($.i18n._("The show instance doesn't exist anymore!"));
    window.location.reload();
}
