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

function checkShowLength() {
    var showFilled = $("#show_time_filled").text().split('.')[0];
    var showLength = $("#show_length").text();

    if (showFilled > showLength){
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


function setScheduleDialogHtml(json) {
	var dt;

	dt = $('#schedule_playlists').dataTable();
	dt.fnDraw();

	$("#schedule_playlist_chosen")
		.empty()
		.append(json.chosen);

	$("#show_time_filled").empty().append(json.timeFilled);
	$("#show_progressbar").progressbar( "value" , json.percentFilled );

    checkShowLength();
}

function setScheduleDialogEvents(dialog) {

	dialog.find(".ui-icon-triangle-1-e").click(function(){
		var span = $(this);

		if(span.hasClass("ui-icon-triangle-1-s")) {
			span
				.removeClass("ui-icon-triangle-1-s")
				.addClass("ui-icon ui-icon-triangle-1-e");

			$(this).parent().parent().find(".group_list").hide();
		}
		else if(span.hasClass("ui-icon-triangle-1-e")) {
			span
				.removeClass("ui-icon-triangle-1-e")
				.addClass("ui-icon ui-icon-triangle-1-s");

			$(this).parent().parent().find(".group_list").show();
		}
	});

	dialog.find(".ui-icon-close").click(function(){
		var groupId, url;
		
		groupId = $(this).parent().parent().attr("id").split("_").pop();
		url = '/Schedule/remove-group';
	
		$.post(url, 
			{format: "json", groupId: groupId},
			function(json){
			    if(json.show_error == true){
                    alertShowErrorAndReload();
                }
				var dialog = $("#schedule_playlist_dialog");

				setScheduleDialogHtml(json);
				setScheduleDialogEvents(dialog);
			});	
	});
}

function dtRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
	var id = "pl_" + aData['id'];

	$(nRow).attr("id", id);

	return nRow;
}

function addDtPlaylistEvents() {
	
	$('#schedule_playlists tbody tr')
		.draggable({ 
			helper: 'clone' 
		});
}

function dtDrawCallback() {
	addDtPlaylistEvents();
}

function makeScheduleDialog(dialog, json) {
    if(json.show_error == true){
        alertShowErrorAndReload();
    }
	dialog.find('#schedule_playlists').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/Schedule/find-playlists/format/json",
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			$.ajax( {
				"dataType": 'json', 
				"type": "POST", 
				"url": sSource, 
				"data": aoData, 
				"success": fnCallback
			} );
		},
		"fnRowCallback": dtRowCallback,
		"fnDrawCallback": dtDrawCallback,
		"aoColumns": [ 
			/* Id */		{"sTitle": "ID", "sName": "pl.id", "bSearchable": false, "bVisible": false, "mDataProp": "id"},
                        /* Description */	{"sTitle": "Description", "sName": "pl.description", "bSearchable": false, "bVisible": false, "mDataProp": "description"},
			/* Name */		{"sTitle": "Title", "sName": "pl.name", "mDataProp": "name"},
			/* Creator */		{"sTitle": "Creator", "sName": "pl.creator", "mDataProp": "creator"},
			/* Length */		{"sTitle": "Length", "sName": "plt.length", "mDataProp": "length"},
			/* Editing */		{"sTitle": "Editing", "sName": "sub.login", "mDataProp": "login"}
		],
		"aaSorting": [[2,'asc']],
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false
	});

    //classes added for Vladimir's styles.css
    dialog.find("#schedule_playlists_length select").addClass('input_select');
    dialog.find("#schedule_playlists_filter input").addClass('input_text auto-search');

	dialog.find("#schedule_playlist_chosen")
		.append(json.chosen)
		.droppable({
      		drop: function(event, ui) {
				var pl_id, url, search;

				search = $("#schedule_playlist_search").val();
				pl_id = $(ui.helper).attr("id").split("_").pop();
				
				url = '/Schedule/schedule-show/format/json';

				$.post(url, 
					{plId: pl_id, search: search},
					function(json){
					    if(json.show_error == true){
					        alertShowErrorAndReload();
					    }
						var dialog = $("#schedule_playlist_dialog");

						setScheduleDialogHtml(json);
						setScheduleDialogEvents(dialog);
					});	
			}
    	});

	dialog.find("#show_progressbar").progressbar({
		value: json.percentFilled
	});

	setScheduleDialogEvents(dialog);
}

function confirmCancelShow(show_instance_id){
    if(confirm('Erase current show and stop playback?')){
        var url = "/Schedule/cancel-current-show/id/"+show_instance_id;
        $.ajax({
          url: url,
          success: function(data){scheduleRefetchEvents(data);}
        });
    }
}

function confirmCancelRecordedShow(show_instance_id){
    if(confirm('Erase current show and stop recording?')){
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

function buildContentDialog(json){
    if(json.show_error == true){
        alertShowErrorAndReload();
    }
	var dialog = $(json.dialog);
        
	dialog.find("#show_progressbar").progressbar({
		value: json.percentFilled
	});
        
	var viewportwidth;
	var viewportheight;

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
	
	var height = viewportheight * 2/3;
	var width = viewportwidth * 4/5;
	
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
	
	$('#show_content_dialog tbody tr')
	.jjmenu("click", 
		[{get:"/Schedule/content-context-menu/format/json/id/#id#"}],  
		{id: getId}, 
		{xposition: "mouse", yposition: "mouse"});
}

function buildScheduleDialog(json){
	var dialog;
	if(json.show_error == true){
	    alertShowErrorAndReload();
	}
    if(json.error) {
        alert(json.error);
        return;
    }

    dialog = $(json.dialog);
	makeScheduleDialog(dialog, json);

	dialog.dialog({
		autoOpen: false,
		title: 'Schedule Media',
		width: 1100,
		height: 550,
		modal: true,
		close: closeDialog,
		buttons: {"Ok": function() {
			dialog.remove();
			$("#schedule_calendar").fullCalendar( 'refetchEvents' );
		}}
	});

	dialog.dialog('open');
    checkShowLength();
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

$(window).load(function() {
	$.ajax({ url: "/Api/calendar-init/format/json", dataType:"json", success:createFullCalendar
            , error:function(jqXHR, textStatus, errorThrown){}});
	
	
	$.contextMenu({
        selector: 'div.fc-event',
        trigger: "left",
        ignoreRightClick: true,
        
        build: function($el, e) {
    		var x, request, data, screen, items, callback, $tr;
    		
    		$tr = $el.parent();
    		data = $tr.data("aData");
    		screen = $tr.data("screen");
    		
    		function processMenuItems(oItems) {
    			
    			//define a download callback.
    			if (oItems.download !== undefined) {
    				
    				callback = function() {
    					document.location.href = oItems.download.url;
					};
    				oItems.download.callback = callback;
    			}
    		
    			items = oItems;
    		}
    		
    		request = $.ajax({
			  url: "/library/context-menu",
			  type: "GET",
			  data: {id : data.id, type: data.ftype, format: "json", "screen": screen},
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