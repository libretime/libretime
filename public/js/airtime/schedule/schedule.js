/**
*
*	Schedule Dialog creation methods.
*
*/

function closeDialog(event, ui) {
	$("#schedule_calendar").fullCalendar( 'refetchEvents' );
	$(this).remove();
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
}

function setScheduleDialogEvents(dialog) {

	dialog.find(".ui-icon-triangle-1-e").parent().click(function(){
		var span = $(this).find("span");

		if(span.hasClass("ui-icon-triangle-1-s")) {
			span
				.removeClass("ui-icon-triangle-1-s")
				.addClass("ui-icon ui-icon-triangle-1-e");

			$(this).parent().removeClass("ui-state-active ui-corner-top");
			$(this).parent().addClass("ui-corner-all");
			$(this).parent().parent().find(".group_list").hide();
		}
		else if(span.hasClass("ui-icon-triangle-1-e")) {
			span
				.removeClass("ui-icon-triangle-1-e")
				.addClass("ui-icon ui-icon-triangle-1-s");

			$(this).parent().addClass("ui-state-active ui-corner-top");
			$(this).parent().removeClass("ui-corner-all");
			$(this).parent().parent().find(".group_list").show();
		}
	});

	dialog.find(".ui-icon-close").parent().click(function(){
		var groupId, url;
		
		groupId = $(this).parent().parent().attr("id").split("_").pop();
		url = '/Schedule/remove-group/format/json';
	
		$.post(url, 
			{groupId: groupId},
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
	
	dialog.find("#schedule_playlist_search").keyup(function(){
		var url, string;
		
		url = "/Schedule/find-playlists/format/html";
		string = $(this).val();
		
		$.post(url, {search: string}, function(html){
			
			$("#schedule_playlist_choice")
				.empty()
				.append(html)
				.find('li')
					.draggable({ 
						helper: 'clone' 
					});
			
		});
	});

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

function openScheduleDialog(show) {
	var url, start_date, end_date;

	url = '/Schedule/schedule-show-dialog/format/json';
	
	start_date = makeTimeStamp(show.start);
	end_date = makeTimeStamp(show.end);

	$.post(url, 
		{start: start_date, end: end_date, showId: show.id},
		function(json){
			var dialog = $(json.dialog);

			makeScheduleDialog(dialog, json, show);

			dialog.dialog({
				autoOpen: false,
				title: 'Schedule Playlist',
				width: 1100,
				height: 500,
				modal: true,
				close: closeDialog,
				buttons: {"Ok": function() {
					dialog.remove();
					$("#schedule_calendar").fullCalendar( 'refetchEvents' );
				}}
			});

			dialog.dialog('open');
		});
}

function eventMenu(action, el, pos) {
	var method, event, start_timestamp, url;

	method = action.split('/').pop();
	event = $(el).data('event');
	start_timestamp = makeTimeStamp(event.start);

	if (method === 'delete-show') {
		
		url = '/Schedule/delete-show';

		$.post(action, 
			{format: "json", showId: event.id, date: start_timestamp},
			function(json){
				$("#schedule_calendar").fullCalendar( 'refetchEvents' );
			});
	}
	else if (method === 'schedule-show') {
		
		openScheduleDialog(event);
	}
	else if (method === 'clear-show') {
		
		url = '/Schedule/clear-show';

		$.post(url, 
			{format: "json", start: start_timestamp, showId: event.id},
			function(json){
				$("#schedule_calendar").fullCalendar( 'refetchEvents' );
			});	
	}
}

$(document).ready(function() {

    $('#schedule_calendar').fullCalendar({
        header: {
			left: 'prev, next, today',
			center: 'title',
			right: 'agendaDay, agendaWeek, month'
		}, 
		defaultView: 'agendaDay',
		editable: false,
		allDaySlot: false,
		
		events: getFullCalendarEvents,

		//callbacks (from full-calendar-functions.js
		dayClick: dayClick,
		eventRender: eventRender,
		eventAfterRender: eventAfterRender,
		eventClick: eventClick,
		eventMouseover: eventMouseover,
		eventMouseout: eventMouseout,
		eventDrop: eventDrop,
		eventResize: eventResize 

    });

    $(window).load(function(){

        var mainHeight = this.innerHeight - 200 - 50;
    
        $('#schedule_calendar').fullCalendar('option', 'contentHeight', mainHeight);
    });

});

