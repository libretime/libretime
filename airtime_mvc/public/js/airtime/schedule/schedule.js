var AIRTIME = (function(AIRTIME){
    var mod;
    
    if (AIRTIME.schedule === undefined) {
        AIRTIME.schedule = {};
    }
    mod = AIRTIME.schedule;
    
    return AIRTIME;
    
}(AIRTIME || {}));

var serverTimezoneOffset = 0;

function closeDialogCalendar(event, ui) {
    
    $el = $(this);
    $el.dialog('destroy');
    $el.remove();
    
    //need to refetch the events to update scheduled status.
    $("#schedule_calendar").fullCalendar( 'refetchEvents' );
}

function checkShowLength(json) {
    var percent = json.percentFilled;

    if (percent > 100){
        $("#show_time_warning")
            .text($.i18n._("Shows longer than their scheduled time will be cut off by a following show."))
            .show();
    }
    else {
        $("#show_time_warning")
            .empty()
            .hide();
    }
}

function confirmCancelShow(show_instance_id){
    if (confirm($.i18n._('Cancel Current Show?'))) {
        var url = baseUrl+"Schedule/cancel-current-show";
        $.ajax({
            url: url,
            data: {format: "json", id: show_instance_id},
            success: function(data){
                scheduleRefetchEvents(data);
            }
        });
    }
}

function confirmCancelRecordedShow(show_instance_id){
    if (confirm($.i18n._('Stop recording current show?'))) {
        var url = baseUrl+"Schedule/cancel-current-show";
        $.ajax({
            url: url,
            data: {format: "json", id: show_instance_id},
            success: function(data){
                scheduleRefetchEvents(data);
            }
        });
    }
}

function uploadToSoundCloud(show_instance_id, el){
    
    var url = baseUrl+"Schedule/upload-to-sound-cloud",
    	$el = $(el),
    	$span = $el.find(".soundcloud");
    
    $.post(url, {id: show_instance_id, format: "json"});
    
    //first upload to soundcloud.
    if ($span.length === 0){
        $span = $("<span/>", {"class": "progress"});
        
        $el.find(".fc-event-title").after($span);
    }
    else {
        $span.removeClass("soundcloud").addClass("progress");
    }
}

function checkCalendarSCUploadStatus(){
    var url = baseUrl+'Library/get-upload-to-soundcloud-status',
        span,
        id;
    
    function checkSCUploadStatusCallback(json) {
        
        if (json.sc_id > 0) {
            span.removeClass("progress").addClass("soundcloud");
            
        }
        else if (json.sc_id == "-3") {
            span.removeClass("progress").addClass("sc-error");
        }
    }
    
    function checkSCUploadStatusRequest() {
        
        span = $(this);
        id = span.parents("div.fc-event").data("event").id;
       
        $.post(url, {format: "json", id: id, type:"show"}, checkSCUploadStatusCallback);
    }
    
    $("#schedule_calendar span.progress").each(checkSCUploadStatusRequest);
    setTimeout(checkCalendarSCUploadStatus, 5000);
}

function findViewportDimensions() {
    var viewportwidth,
        viewportheight;
    
    // the more standards compliant browsers (mozilla/netscape/opera/IE7) use
    // window.innerWidth and window.innerHeight
    if (typeof window.innerWidth != 'undefined') {
        viewportwidth = window.innerWidth, viewportheight = window.innerHeight;
    }
    // IE6 in standards compliant mode (i.e. with a valid doctype as the first
    // line in the document)
    else if (typeof document.documentElement != 'undefined'
            && typeof document.documentElement.clientWidth != 'undefined'
            && document.documentElement.clientWidth != 0) {
        viewportwidth = document.documentElement.clientWidth;
        viewportheight = document.documentElement.clientHeight;
    }
    // older versions of IE
    else {
        viewportwidth = document.getElementsByTagName('body')[0].clientWidth;
        viewportheight = document.getElementsByTagName('body')[0].clientHeight;
    }
    
    return {
        width: viewportwidth,
        height: viewportheight-45
    };
}

function buildScheduleDialog (json, instance_id) {
    var dialog = $(json.dialog),
        viewport = findViewportDimensions(),
        height = Math.floor(viewport.height * 0.96),
        width = Math.floor(viewport.width * 0.96),
        fnServer = AIRTIME.showbuilder.fnServerData,
        //subtract padding in pixels
        widgetWidth = width - 60,
        libWidth = Math.floor(widgetWidth * 0.5),
        builderWidth = Math.floor(widgetWidth * 0.5);
    
    dialog.find("#library_content")
        .height(height - 115)
        .width(libWidth);
    
    dialog.find("#show_builder")
        .height(height - 115)
        .width(builderWidth);
    
    dialog.dialog({
        autoOpen: false,
        title: json.title,
        width: width,
        height: height,
        resizable: false,
        draggable: true,
        modal: true,
        close: closeDialogCalendar,
        buttons: [
            {
                text: $.i18n._("Ok"),
                "class": "btn",
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });
        
    //set the start end times so the builder datatables knows its time range.
    fnServer.start = json.start;
    fnServer.end = json.end;
    fnServer.ops = {};
    fnServer.ops.showFilter = 0;
    fnServer.ops.showInstanceFilter = instance_id;
    fnServer.ops.myShows = 0;
    
    AIRTIME.library.libraryInit();
    AIRTIME.showbuilder.builderDataTable();
    
    //set max heights of datatables.
    dialog.find(".lib-content .dataTables_scrolling")
        .css("max-height", height - 90 - 200);
    
    dialog.find(".sb-content .dataTables_scrolling")
        .css("max-height", height - 90 - 65);
    
    dialog.dialog('open');
}

function buildContentDialog (json){
    var dialog = $(json.dialog),
        viewport = findViewportDimensions(),
        height = viewport.height * 2/3,
        width = viewport.width * 4/5;
    
    if (json.show_error == true){
        alertShowErrorAndReload();
    }
          
    dialog.find("#show_progressbar").progressbar({
        value: json.percentFilled
    });
     
    dialog.dialog({
        autoOpen: false,
        title: $.i18n._("Contents of Show") +" '" + json.showTitle + "'",
        width: width,
        height: height,
        modal: true,
        close: closeDialogCalendar,
        buttons: [
            {
                text: $.i18n._("Ok"),
                "class": "btn",
                click: function() {
                    dialog.remove();
                }
            }
        ]
    });

    dialog.dialog('open');
}

/**
 * Use user preference for time scale; defaults to month if preference was never set
 */
function getTimeScalePreference(data) {
    return data.calendarInit.timeScale;
}

/**
 * Use user preference for time interval; defaults to 30m if preference was never set
 */
function getTimeIntervalPreference(data) {
    return parseInt(data.calendarInit.timeInterval);
}

function createFullCalendar(data){

    serverTimezoneOffset = data.calendarInit.timezoneOffset;

    var mainHeight = $(window).height() - 200 - 35;

    $('#schedule_calendar').fullCalendar({
        header: {
            left: 'prev, next, today',
            center: 'title',
            right: 'agendaDay, agendaWeek, month'
        }, 
        defaultView: getTimeScalePreference(data),
        slotMinutes: getTimeIntervalPreference(data),
        firstDay: data.calendarInit.weekStartDay,
        editable: false,
        allDaySlot: false,
        axisFormat: 'H:mm',
        timeFormat: {
            agenda: 'H:mm{ - H:mm}',
            month: 'H:mm{ - H:mm}'
        },
        //i18n_months is in common.js
        monthNames: i18n_months,
        monthNamesShort: [
            $.i18n._('Jan'),
            $.i18n._('Feb'),
            $.i18n._('Mar'),
            $.i18n._('Apr'),
            $.i18n._('May'),
            $.i18n._('Jun'),
            $.i18n._('Jul'),
            $.i18n._('Aug'),
            $.i18n._('Sep'),
            $.i18n._('Oct'),
            $.i18n._('Nov'),
            $.i18n._('Dec')
        ],
        buttonText: {
            today: $.i18n._('today'),
            month: $.i18n._('month'),
            week: $.i18n._('week'),
            day: $.i18n._('day')
        },
        dayNames: [
            $.i18n._('Sunday'),
            $.i18n._('Monday'),
            $.i18n._('Tuesday'),
            $.i18n._('Wednesday'),
            $.i18n._('Thursday'),
            $.i18n._('Friday'),
            $.i18n._('Saturday')
        ],
        dayNamesShort: [
            $.i18n._('Sun'),
            $.i18n._('Mon'),
            $.i18n._('Tue'),
            $.i18n._('Wed'),
            $.i18n._('Thu'),
            $.i18n._('Fri'),
            $.i18n._('Sat')
        ],
        contentHeight: mainHeight,
        theme: true,
        lazyFetching: false,
        serverTimestamp: parseInt(data.calendarInit.timestamp, 10),
        serverTimezoneOffset: parseInt(data.calendarInit.timezoneOffset, 10),
       
        events: getFullCalendarEvents,

        //callbacks (in full-calendar-functions.js)
        viewDisplay: viewDisplay,
        dayClick: dayClick,
        eventRender: eventRender,
        eventAfterRender: eventAfterRender,
        eventDrop: eventDrop,
        eventResize: eventResize,
        windowResize: windowResize
    });
}

//Alert the error and reload the page
//this function is used to resolve concurrency issue
function alertShowErrorAndReload(){
    alert($.i18n._("The show instance doesn't exist anymore!"));
    window.location.reload();
}

$(document).ready(function() {
    checkCalendarSCUploadStatus();
    
    $.contextMenu({
        selector: 'div.fc-event',
        trigger: "left",
        ignoreRightClick: true,
        
        build: function($el, e) {
            var data, 
                items, 
                callback;
            
            data = $el.data("event");
            
            function processMenuItems(oItems) {
                
                //define a schedule callback.
                if (oItems.schedule !== undefined) {
                    
                    callback = function() {
                        
                        $.post(oItems.schedule.url, {format: "json", id: data.id}, function(json){
                            buildScheduleDialog(json, data.id);
                        });
                    };
                    
                    oItems.schedule.callback = callback;
                }
                
                //define a clear callback.
                if (oItems.clear !== undefined) {
                    
                    callback = function() {
                        if (confirm($.i18n._("Remove all content?"))) {
                            $.post(oItems.clear.url, {format: "json", id: data.id}, function(json){
                                scheduleRefetchEvents(json);
                            });
                        }
                    };
                    oItems.clear.callback = callback;
                }
                
                //define an edit callback.
                if (oItems.edit !== undefined) {
                    if(oItems.edit.items !== undefined){
                        var edit = oItems.edit.items;
                        
                        //edit a single instance
                        callback = function() {
                            $.get(edit.instance.url, {format: "json", showId: data.showId, instanceId: data.id, type: "instance"}, function(json){
                                beginEditShow(json);
                            });
                        };
                        edit.instance.callback = callback;
                        
                        //edit this instance and all
                        callback = function() {
                            $.get(edit.all.url, {format: "json", showId: data.showId, instanceId: data.id, type: "all"}, function(json){
                                beginEditShow(json);
                            });
                        };
                        edit.all.callback = callback;
                    }else{
                        callback = function() {
                            $.get(oItems.edit.url, {format: "json", showId: data.showId, instanceId: data.id, type: oItems.edit._type}, function(json){
                                beginEditShow(json);
                            });
                        };
                        oItems.edit.callback = callback;
                    }
                }

                //define a content callback.
                if (oItems.content !== undefined) {
                    
                    callback = function() {
                        $.get(oItems.content.url, {format: "json", id: data.id}, function(json){
                            buildContentDialog(json);
                        });
                    };
                    oItems.content.callback = callback;
                }
                
                //define a soundcloud upload callback.
                if (oItems.soundcloud_upload !== undefined) {
                    
                    callback = function() {
                        uploadToSoundCloud(data.id, this.context);
                    };
                    oItems.soundcloud_upload.callback = callback;
                }
                
                //define a view on soundcloud callback.
                if (oItems.soundcloud_view !== undefined) {
                    
                    callback = function() {
                        window.open(oItems.soundcloud_view.url);
                    };
                    oItems.soundcloud_view.callback = callback;
                }
                
                //define a cancel recorded show callback.
                if (oItems.cancel_recorded !== undefined) {
                    
                    callback = function() {
                        confirmCancelRecordedShow(data.id);
                    };
                    oItems.cancel_recorded.callback = callback;
                }
                
                //define a view recorded callback.
                if (oItems.view_recorded !== undefined) {
                    callback = function() {
                        $.get(oItems.view_recorded.url, {format: "json"}, function(json){
                            //in library.js
                            buildEditMetadataDialog(json);
                        });
                    };
                    oItems.view_recorded.callback = callback;
                }
                
                //define a cancel callback.
                if (oItems.cancel !== undefined) {
                    
                    callback = function() {
                        confirmCancelShow(data.id);
                    };
                    oItems.cancel.callback = callback;
                }
                
                //define a delete callback.
                if (oItems.del !== undefined) {
                    
                    //repeating show multiple delete options
                    if (oItems.del.items !== undefined) {
                        var del = oItems.del.items;
                        
                        //delete a single instance
                        callback = function() {
                            $.post(del.single.url, {format: "json", id: data.id}, function(json){
                                scheduleRefetchEvents(json);
                            });
                        };
                        del.single.callback = callback;
                        
                        //delete this instance and all following instances.
                        callback = function() {
                            $.post(del.following.url, {format: "json", id: data.id}, function(json){
                                scheduleRefetchEvents(json);
                            });
                        };
                        del.following.callback = callback;  
                        
                    }
                    //single show
                    else {
                        callback = function() {
                            $.post(oItems.del.url, {format: "json", id: data.id}, function(json){
                                scheduleRefetchEvents(json);
                            });
                        };
                        oItems.del.callback = callback; 
                    }
                }
            
                items = oItems;
            }

            $.ajax({
              url: baseUrl+"schedule/make-context-menu",
              type: "GET",
              data: {instanceId : data.id, showId: data.showId, format: "json"},
              dataType: "json",
              async: false,
              success: function(json){
                  processMenuItems(json.items);
              }
            });

            return {
                items: items,
                determinePosition : function($menu, x, y) {
                    $menu.css('display', 'block')
                        .position({ my: "left top", at: "right top", of: this, offset: "-20 10", collision: "fit"})
                        .css('display', 'none');
                }
            };
        }
    });
});
