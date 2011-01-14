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
				dialog.find("#show_overlap_error").remove();
				dialog.append(data.form);

				var start  = dialog.find("#start_date");
				var end  = dialog.find("#end_date");

				createDateInput(start, startDpSelect);
				createDateInput(end, endDpSelect);

				if(data.overlap) {
					var div, table, tr, days;
					div = $('<div id="show_overlap_error"/>');
					table = $('<table/>');
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
				
					div.append("<span>Cannot add show. New show overlaps the following shows:</span>");
					div.append(table);
					dialog.append(div);
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

function autoSelect(event, ui) {

	$("#hosts-"+ui.item.value).attr("checked", "checked");
	event.preventDefault();
}

function makeShowDialog(json) {
	
	var dialog;

	//main jqueryUI dialog
	dialog = $('<div id="schedule_add_event_dialog" />');

	dialog.append(json.form);

	var start  = dialog.find("#start_date");
	var end  = dialog.find("#end_date");

	createDateInput(start, startDpSelect);
	createDateInput(end, endDpSelect);

	var auto = json.hosts.map(function(el) {
		return {value: el.id, label: el.login};
	});

	dialog.find("#hosts_autocomplete").autocomplete({
		source: auto,
		select: autoSelect
	});


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

function openShowDialog() {
	var url;

	url = '/Schedule/add-show-dialog/format/json';

	$.get(url, function(json){
		var dialog = makeShowDialog(json);

		dialog.dialog('open');
	});
}

function makeScheduleDialog(dialog, json, show) {
	
	dialog.find("#schedule_playlist_search").keyup(function(){
		var url, string, day;
		
		url = "/Schedule/find-playlists/format/html";
		string = $(this).val();
		day = show.start.getDay();

		$.post(url, {search: string, id: show.id, day: day}, function(html){
			
			$("#schedule_playlist_choice")
				.empty()
				.append(html)
				.find('li')
					.draggable({ 
						helper: 'clone' 
					});
			
		});
	});

	dialog.find('#schedule_playlist_choice')
		.append(json.choice)
		.find('li')
			.draggable({ 
				helper: 'clone' 
			});

	dialog.find("#schedule_playlist_chosen")
		.append(json.chosen)
		.droppable({
      		drop: function(event, ui) {
				var li, pl_id, url, start_date, end_date, day, search;

				search = $("#schedule_playlist_search").val();

				pl_id = $(ui.helper).attr("id").split("_").pop();
				day = show.start.getDay();
				
				start_date = makeTimeStamp(show.start);
				end_date = makeTimeStamp(show.end);

				url = '/Schedule/schedule-show/format/json';

				$.post(url, 
					{plId: pl_id, start: start_date, end: end_date, showId: show.id, day: day, search: search},
					function(json){
						var x;

						$("#schedule_playlist_choice")
							.empty()
							.append(json.choice)
							.find('li')
								.draggable({ 
									helper: 'clone' 
								});

						$("#schedule_playlist_chosen")
							.empty()
							.append(json.chosen)
							.find("li")
								.click(function(){
									$(this).find(".group_list").toggle();
								});
					});
				
			}
    	});

	dialog.find("#schedule_playlist_chosen li")
		.click(function(){
			$(this).find(".group_list").toggle();
		});
}

function openScheduleDialog(show) {
	var url, start_date, end_date, day;

	url = '/Schedule/schedule-show/format/json';
	day = show.start.getDay();

	start_date = makeTimeStamp(show.start);
	end_date = makeTimeStamp(show.end);

	$.get(url, 
		{day: day, start: start_date, end: end_date, showId: show.id},
		function(json){
			var dialog = $(json.dialog);

			makeScheduleDialog(dialog, json, show);

			dialog.dialog({
				autoOpen: false,
				title: 'Schedule Playlist',
				width: 950,
				height: 400,
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
		
		openScheduleDialog(event);
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

	if(view.name === 'agendaDay' || view.name === 'agendaWeek') {
		var div = $('<div/>');
		div
			.height('5px')
			.width('100px')
			.css('margin-top', '5px')
			.progressbar({
				value: event.percent
			});

		if(event.percent === 0) {
			// even at 0, the bar still seems to display a little bit of progress...
			div.find("div").hide();
		}
		else {
			div.find("div")
				.removeClass("ui-widget-header")
				.addClass("ui-state-active");
		}

		$(element).find(".fc-event-title").after(div);
	}	
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

