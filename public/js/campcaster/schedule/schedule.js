/**
*
*	Schedule Dialog creation methods.
*
*/

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
				$("#schedule_add_event_dialog").find("form").remove();
				$("#schedule_add_event_dialog").append(data.form);
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
		buttons: { "Ok": submitShow }
	});

	return dialog;
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
	var x;
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

function openShowDialog() {
	var url;

	url = '/Schedule/add-show-dialog/format/json';

	$.get(url, function(json){
		var dialog = makeShowDialog(json.form);
		dialog.dialog('open');
	});
}

$(document).ready(function() {

    $('#schedule_calendar').fullCalendar({
        header: {
			left: 'next, today',
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
		eventClick: eventClick,
		eventMouseover: eventMouseover,
		eventMouseout: eventMouseout,
		eventDrop: eventDrop,
		eventResize: eventResize 

    })

	$('#schedule_add_show').click(openShowDialog);


});

