/**
*
*	Schedule Dialog creation methods.
*
*/

function makeTimeStamp(date){
	var sy, sm, sd, h, m, s, timestamp;
	sy = date.getFullYear();
	sm = date.getMonth() + 1;
	sd = date.getDate();
	h = date.getHours();
	m = date.getMinutes();
	s = date.getSeconds();

	timestamp = sy+"-"+ sm +"-"+ sd +" "+ h +":"+ m +":"+ s;
	return timestamp;
}

//dateText mm-dd-yy
function startDpSelect(dateText, inst) {
	var time, date;

	time = dateText.split("-");
	date = new Date(time[0], time[1] - 1, time[2]);

	$("#end_date").datepicker("option", "minDate", date);
}

function endDpSelect(dateText, inst) {
	var time, date;

	time = dateText.split("-");
	date = new Date(time[0], time[1] - 1, time[2]);

	$("#start_date").datepicker( "option", "maxDate", date);
}

function createDateInput(el, onSelect) {
	var date;

	el.datepicker({
			minDate: new Date(),
			onSelect: onSelect,
			dateFormat: 'yy-mm-dd' 
		});

	date = $.datepicker.formatDate("yy-mm-dd", new Date());
	el.val(date);
}

function submitShow() {

	var formData, dialog;

	formData = $("#schedule_add_event_dialog").find("form").serializeArray();
	dialog = $(this);
	
	$.post("/Schedule/add-show-dialog/format/json", 
		formData,
		function(data){
			if(data.form) {
				dialog.find("form").remove();
				dialog.append(data.form);

				var start  = dialog.find("#start_date");
				var end  = dialog.find("#end_date");

				createDateInput(start, startDpSelect);
				createDateInput(end, endDpSelect);

				if(data.overlap) {
					var table, tr, days;
					table = $("<table/>");
					days = $.datepicker.regional[''].dayNamesShort;

					$.each(data.overlap, function(i, val){
						tr = $("<tr/>");
						tr
							.append("<td>"+val.name+"</td>")
							.append("<td>"+days[val.day]+"</td>")
							.append("<td>"+val.start_time+"</td>")
							.append("<td>"+val.end_time+"</td>");

						table.append(tr);
					});
				
					dialog.append("<span>Cannot add show. New show overlaps the following shows:</span>");
					dialog.append(table);
				}
	
			}
			else {
				$("#schedule_calendar").fullCalendar( 'refetchEvents' );
				dialog.remove();
			}
		});
}

function closeDialog(event, ui) {
	$(this).remove();
}

function schedulePlaylist() {
	var li, pl_id, url, event, start, dialog;

	dialog = $(this);
	li = $("#schedule_playlist_dialog").find(".ui-state-active");

	if(li.length === 0) {
		dialog.remove();
		return;
	}

	pl_id = li.data('pl_id');
	event = li.parent().data('event');	

	start_date = makeTimeStamp(event.start);

	url = '/Schedule/schedule-show/format/json';

	$.post(url, 
		{plId: pl_id, start: start_date, showId: event.id},
		function(json){
			dialog.remove();
			$("#schedule_calendar").fullCalendar( 'refetchEvents' );
		});	
	
}

function makeShowDialog(html) {
	
	var dialog;

	//main jqueryUI dialog
	dialog = $('<div id="schedule_add_event_dialog" />');

	dialog.append(html);

	var start  = dialog.find("#start_date");
	var end  = dialog.find("#end_date");

	createDateInput(start, startDpSelect);
	createDateInput(end, endDpSelect);

	dialog.dialog({
		autoOpen: false,
		title: 'Add Show',
		width: 950,
		height: 400,
		close: closeDialog,
		buttons: { "Cancel": closeDialog, "Ok": submitShow}
	});

	return dialog;
}

function makeScheduleDialog(playlists, event) {
	
	var dialog;

	//main jqueryUI dialog
	dialog = $('<div id="schedule_playlist_dialog" />');

	var ol, li;
	ol = $('<ul/>');
	$.each(playlists, function(i, val){
		li = $('<li />')
			.addClass('ui-widget-content')
			.append('<div>'+val.name+'</div>')
			.append('<div>'+val.description+'</div>')
			.append('<div>'+val.length+'</div>')
			.click(function(){
				$(this).parent().find("li").removeClass("ui-state-active")
				$(this).addClass("ui-state-active");
			});

		li.data({'pl_id': val.id});
		ol.append(li);
	});
	
	ol.data({'event': event});
	dialog.append(ol);

	dialog.dialog({
		autoOpen: false,
		title: 'Schedule Playlist',
		width: 950,
		height: 400,
		close: closeDialog,
		buttons: { "Cancel": closeDialog, "Ok": schedulePlaylist}
	});

	return dialog;
}

function openShowDialog() {
	var url;

	url = '/Schedule/add-show-dialog/format/json';

	$.get(url, function(json){
		var dialog = makeShowDialog(json.form);
		dialog.dialog('open');
	});
}

function openScheduleDialog(event, time) {
	var url;

	url = '/Schedule/schedule-show/format/json';

	$.get(url, 
		{length: time},
		function(json){
			var dialog = makeScheduleDialog(json.playlists, event);
			dialog.dialog('open');
		});
}

function eventMenu(action, el, pos) {
	var method = action.split('/').pop(),
		event;

	event = $(el).data('event');

	if (method === 'delete-show') {
		$.post(action, 
			{format: "json", showId: event.id},
			function(json){
				$("#schedule_calendar").fullCalendar( 'refetchEvents' );
			});
	}
	else if (method === 'schedule-show') {
		var length, h, m, s, time;

		length = event.end.getTime() - event.start.getTime();

		h = length / (1000*60*60);
		m = (length % (1000*60*60)) / (1000*60);
		s = ((length % (1000*60*60)) % (1000*60)) / 1000;

		time = h+":"+m+":"+s;

		openScheduleDialog(event, time);
	}
	else if (method === 'clear-show') {
		start_date = makeTimeStamp(event.start);

		url = '/Schedule/clear-show/format/json';

		$.post(url, 
			{start: start_date, showId: event.id},
			function(json){
				$("#schedule_calendar").fullCalendar( 'refetchEvents' );
			});	
	}
}

/**
*
*	Full Calendar callback methods.
*
*/

function dayClick(date, allDay, jsEvent, view) {
	var x;
}

function eventRender(event, element, view) { 
	//element.qtip({
     //       content: event.description
     //   });
	
}

function eventAfterRender( event, element, view ) {
	var today = new Date();	

	if(event.isHost === true && event.start > today) {
		$(element).contextMenu(
			{menu: 'schedule_event_host_menu'}, eventMenu
		);
	}
	else{
		$(element).contextMenu(
			{menu: 'schedule_event_default_menu'}, eventMenu
		);
	}

	$(element).data({'event': event});
}

function eventClick(event, jsEvent, view) { 
	var x;
}

function eventMouseover(event, jsEvent, view) { 
}

function eventMouseout(event, jsEvent, view) { 
}

function eventDrop(event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view) {
	var url;

	if (event.repeats && dayDelta !== 0) {
		revertFunc();
		return;
	}

	url = '/Schedule/move-show/format/json';

	$.post(url, 
		{day: dayDelta, min: minuteDelta, showId: event.id},
		function(json){
			if(json.overlap) {
				revertFunc();
			}
		});
}

function eventResize( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) { 
	var url;

	url = '/Schedule/resize-show/format/json';

	$.post(url, 
		{day: dayDelta, min: minuteDelta, showId: event.id},
		function(json){
			if(json.overlap) {
				revertFunc();
			}
		});
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

		events: function(start, end, callback) {
			var url, start_date, end_date;
	
			var sy, sm, sd, ey, em, ed;
			sy = start.getFullYear();
			sm = start.getMonth() + 1;
			sd = start.getDate();

			start_date = sy +"-"+ sm +"-"+ sd;

			ey = end.getFullYear();
			em = end.getMonth() + 1;
			ed = end.getDate();
			end_date = ey +"-"+ em +"-"+ ed;

			url = '/Schedule/event-feed/format/json';
			url = url + '/start/' + start_date;
			url = url + '/end/' + end_date;

			if ((ed - sd) === 1) {
				url = url + '/weekday/' + start.getDay();
			}

			$.post(url, function(json){
				callback(json.events);
			});
		},

		//callbacks
		dayClick: dayClick,
		eventRender: eventRender,
		eventAfterRender: eventAfterRender,
		eventClick: eventClick,
		eventMouseover: eventMouseover,
		eventMouseout: eventMouseout,
		eventDrop: eventDrop,
		eventResize: eventResize 

    })

	$('#schedule_add_show').click(openShowDialog);


});

