/**
*
*	Schedule Dialog creation methods.
*
*/

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
				var dialog = $("#schedule_playlist_dialog");

				setScheduleDialogHtml(json);
				setScheduleDialogEvents(dialog);
			});	
	});
}

function dtRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
	var id = "pl_" + aData[0];

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
			/* Id */			{ "sName": "pl.id", "bSearchable": false, "bVisible": false },
			/* Description */	{ "sName": "pl.description", "bVisible": false },
			/* Name */			{ "sName": "pl.name" },
			/* Creator */		{ "sName": "pl.creator" },
			/* Length */		{ "sName": "plt.length" },
			/* Editing */		{ "sName": "sub.login" }
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
          success: function(data){scheduleRefetchEvents();}
        });
    }
}

function uploadToSoundCloud(show_instance_id){
    
    var url = "/Schedule/upload-to-sound-cloud";
    var span = $(window.triggerElement).find(".recording");

    span.removeClass("recording")
        .addClass("progress");

    $.post(url,
        {id: show_instance_id, format: "json"},
        function(data){
            if(data.error) {
                span.removeClass("progress")
                    .addClass("recording");

                alert(data.error);
                return;
            }
            scheduleRefetchEvents();
    });

}

//used by jjmenu
function getId() { 
	var tr_id =  $(this.triggerElement).attr("id");
	tr_id = tr_id.split("_");

	return tr_id[1];
}
//end functions used by jjmenu

function buildContentDialog(json){
	var dialog = $(json.dialog);
	
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

function buildEditDialog(json){

}

$(window).load(function() {
    var mainHeight = document.documentElement.clientHeight - 200 - 50;

    $('#schedule_calendar').fullCalendar({
        header: {
			left: 'prev, next, today',
			center: 'title',
			right: 'agendaDay, agendaWeek, month'
		}, 
		defaultView: 'month',
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
       
		events: getFullCalendarEvents,

		//callbacks (in full-calendar-functions.js)
        viewDisplay: viewDisplay,
		dayClick: dayClick,
		eventRender: eventRender,
		eventAfterRender: eventAfterRender,
		eventDrop: eventDrop,
		eventResize: eventResize 
    });
    
});

