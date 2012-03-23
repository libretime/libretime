/**
*
*	Schedule Dialog creation methods.
*
*/

var serverTimezoneOffset = 0;

function closeDialog(event, ui) {
	$("#schedule_calendar").fullCalendar( 'refetchEvents' );
	$(this).remove();
}

function checkShowLength(json) {
    var percent = json.percentFilled;

    if (percent > 100){
        $("#show_time_warning")
            .text("Shows longer than their scheduled time will be cut off by a following show.")
            .show();
    }
    else {
        $("#show_time_warning")
            .empty()
            .hide();
    }
}

function confirmCancelShow(show_instance_id){
    if (confirm('Erase current show and stop playback?')){
        var url = "/Schedule/cancel-current-show/id/"+show_instance_id;
        $.ajax({
          url: url,
          success: function(data){scheduleRefetchEvents(data);}
        });
    }
}

function confirmCancelRecordedShow(show_instance_id){
    if (confirm('Erase current show and stop recording?')){
        var url = "/Schedule/cancel-current-show/id/"+show_instance_id;
        $.ajax({
          url: url,
          success: function(data){scheduleRefetchEvents(data);}
        });
    }
}

function uploadToSoundCloud(show_instance_id){
    
    var url = "/Schedule/upload-to-sound-cloud";
    var span = $(window.triggerElement).find(".recording");
    
    $.post(url,
        {id: show_instance_id, format: "json"},
        function(json){
            scheduleRefetchEvents(json);
    });
    
    if(span.length == 0){
        span = $(window.triggerElement).find(".soundcloud");
        span.removeClass("soundcloud")
        	.addClass("progress");
    }else{
        span.removeClass("recording")
        	.addClass("progress");
    }
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
		height: viewportheight
	};
}

function buildScheduleDialog (json) {
	
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
		draggable: false,
		modal: true,
		close: closeDialog,
		buttons: {"Ok": function() {
			dialog.remove();
			$("#schedule_calendar").fullCalendar( 'refetchEvents' );
		}}
	});
		
	//set the start end times so the builder datatables knows its time range.
	fnServer.start = json.start;
	fnServer.end = json.end;
	
	AIRTIME.library.libraryInit();
	AIRTIME.showbuilder.builderDataTable();
	
	//set max heights of datatables.
	dialog.find(".lib-content .dataTables_scrolling")
		.css("max-height", height - 90 - 155);
	
	dialog.find(".sb-content .dataTables_scrolling")
		.css("max-height", height - 90 - 60);
	
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
		title: 'Show Contents',
		width: width,
		height: height,
		modal: true,
		close: closeDialog,
		buttons: {"Ok": function() {
			dialog.remove();
		}}
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

    var mainHeight = document.documentElement.clientHeight - 200 - 50;
    
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
        eventResize: eventResize 
    });
}

//Alert the error and reload the page
//this function is used to resolve concurrency issue
function alertShowErrorAndReload(){
    alert("The show instance doesn't exist anymore!");
    window.location.reload();
}

$(document).ready(function() {
	$.ajax({ url: "/Api/calendar-init/format/json", dataType:"json", success:createFullCalendar
            , error:function(jqXHR, textStatus, errorThrown){}});
	
	
	$.contextMenu({
        selector: 'div.fc-event',
        trigger: "left",
        ignoreRightClick: true,
        
        build: function($el, e) {
    		var request, data, items, callback;
    		
    		data = $el.data("event");
    		
    		function processMenuItems(oItems) {
    			
    			//define a schedule callback.
    			if (oItems.schedule !== undefined) {
    				
    				callback = function() {
    					
    					$.post(oItems.schedule.url, {format: "json", id: data.id}, function(json){
    						buildScheduleDialog(json);
    					});
    				};
    				
    				oItems.schedule.callback = callback;
    			}
    			
    			//define a clear callback.
    			if (oItems.clear !== undefined) {
    				
    				callback = function() {
    					$.post(oItems.clear.url, {format: "json", id: data.id}, function(json){
    						scheduleRefetchEvents(json);
    					});
					};
    				oItems.clear.callback = callback;
    			}
    			
    			//define an edit callback.
    			if (oItems.edit !== undefined) {
    				
    				callback = function() {
    					$.get(oItems.edit.url, {format: "json", id: data.id}, function(json){
    						beginEditShow(json);
    					});
					};
    				oItems.edit.callback = callback;
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
    			
    			//define a soundcloud callback.
    			if (oItems.soundcloud !== undefined) {
    				
    				callback = function() {
    					uploadToSoundCloud(data.id);
					};
    				oItems.soundcloud.callback = callback;
    			}
    			
    			//define a cancel recorded show callback.
    			if (oItems.cancel_recorded !== undefined) {
    				
    				callback = function() {
    					confirmCancelRecordedShow(data.id);
					};
    				oItems.cancel_recorded.callback = callback;
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
    		
    		request = $.ajax({
			  url: "/schedule/make-context-menu",
			  type: "GET",
			  data: {id : data.id, format: "json"},
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