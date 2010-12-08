/**
*
*	Schedule Dialog creation methods.
*
*/

//dateText mm-dd-yy

function checkDayOfWeek(date) {
	var day;

	day = date.getDay();
	$("#schedule_dialog_day_check").find('input[value="'+day+'"]').attr("checked", "true");
}

function startDpSelect(dateText, inst) {
	var time, date;

	time = dateText.split("-");
	date = new Date(time[0], time[1] - 1, time[2]);

	//checkDayOfWeek(date);

	$("#schedule_add_event_dialog")
		.find("input#schedule_dialog_end_date_input")
		.datepicker("option", "minDate", date);
}

function endDpSelect(dateText, inst) {
	var time, date;

	time = dateText.split("-");
	date = new Date(time[0], time[1] - 1, time[2]);

	$("#schedule_add_event_dialog")
		.find("input#schedule_dialog_start_date_input")
		.datepicker( "option", "maxDate", date);
}

function createDateInput(name, label) {
	var d_input, t_input, dp, div, dl, label, format, newDate;

	label = $('<label>'+label+':</label>');
	d_input = $('<input id="schedule_dialog_'+ name+ '_date_input" type="text" size="8"/>')
		.datepicker({
			minDate: new Date(),
			onSelect: window[name+"DpSelect"],
			dateFormat: 'yy-mm-dd' 
		});

	//format = $.datepicker.regional[''].dateFormat;
	newDate = $.datepicker.formatDate("yy-mm-dd", new Date());
	d_input.val(newDate);

	div = $('<div/>')
		.append(label)
		.append(d_input);
	
	return div;
}

function submitShow() {
	var name, description, hosts, all_day, repeats,
		start_time, duration, start_date, end_date, dofw;

	name = $("#schedule_dialog_name").val();
	description = $("#schedule_dialog_description").val();
	hosts = $("#schedule_dialog_hosts").val();
	all_day = $("#schedule_dialog_all_day").attr("checked");
	repeats = $("#schedule_dialog_repeats").attr("checked");
	start_time = $("#schedule_dialog_start_time").val();
	duration = $("#schedule_dialog_duration").val();
	start_date = $("#schedule_dialog_start_date_input").val();
	end_date = $("#schedule_dialog_end_date_input").val();
	dofw = $("#schedule_dialog_day_check").find(":checked").map(function(){
		return $(this).val();
	}).get();

	if(dofw.length === 0) {
		var time, date;

		time = start_date.split("-");
		date = new Date(time[0], time[1] - 1, time[2]);
		dofw.push(date.getDay());
	}

	$.post("/Schedule/add-show/format/json", 
		{ name: name, description: description, hosts: hosts, all_day: all_day, repeats: repeats, 
			start_time: start_time, duration: duration, start_date: start_date, end_date: end_date, dofw: dofw },
		function(data){
			$('#schedule_calendar').fullCalendar( 'refetchEvents' );
		});

	$(this).remove();
}

function closeDialog(event, ui) {
	$(this).remove();
}

function makeShowDialog(json) {
	
	var dialog, div, dl, time_div, host_div, 
		label, input, textarea, repeats, all_day, day_checkbox, host_select;

	//main jqueryUI dialog
	dialog = $('<div id="schedule_add_event_dialog" />');

	div_left = $('<div/>')
		.width(300);
	div_middle = $('<div/>')
		.width(250);
	div_right = $('<div/>')
		.width(350);

	dialog.append(div_left);
	dialog.append(div_middle);
	dialog.append(div_right);

	dialog.find("div")
		.css("float", "left");

	dl = $('<dl />');

	label = $('<span>Name: </span>');
	input = $('<input id="schedule_dialog_name" type="text" />');

	dl.append(label);
	dl.append(input);

	label = $('<span>Description: </span>');
	textarea = $('<textarea id="schedule_dialog_description" rows="2" cols="20"/>');

	dl.append(label);
	dl.append(textarea);

	dl.find("span").wrap('<dt/>');
	dl.find("input, textarea").wrap('<dd/>');
	div_left.append(dl);

	repeats = $('<input id="schedule_dialog_repeats" type="checkbox">repeats</input>').click(function(){
		$("#schedule_dialog_day_check").toggle();
		$("#schedule_dialog_end_date_input").parent().toggle();

	});
	all_day = $('<input id="schedule_dialog_all_day" type="checkbox">all day</input>').click(function(){

	});
	div_middle.append(all_day)
		.append(repeats);

	day_checkbox = $('<div id="schedule_dialog_day_check"/>').hide();
	
	$.datepicker.regional[''].dayNamesMin.map(function(day, i){
		day_checkbox.append($('<input value="'+i+'" type="checkbox">'+day+'</input>'));
	});

	div_right.append(day_checkbox);

	div_middle.append(createDateInput("start", "Date Start"))
		.append(createDateInput("end", "Date End").hide());

	dl = $('<dl />');

	label = $('<span>Hosts: </span>');
	host_select = $('<select id="schedule_dialog_hosts" multiple="multiple" size="4" />');
	json.hosts.map(function(host){
		host_select.append($('<option value="'+host.id+'">'+host.login+'</option>'));
	});

	dl.append(label);
	dl.append(host_select);
	dl.find("span").wrap('<dt/>');
	dl.find("select").wrap('<dd/>');
	div_left.append(dl);

	label = $('<span>Start Time: </span>');
	input = $('<input id="schedule_dialog_start_time" type="text" />');

	div_middle.append(label);
	div_middle.append(input);

	label = $('<span>Duration: </span>');
	input = $('<input id="schedule_dialog_duration" type="text" />');

	div_middle.append(label);
	div_middle.append(input);

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
	var x;
}

function eventResize( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) { 
	var x;
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

	$('#schedule_add_show').click(function() {
		var url;

		url = '/Schedule/add-show-dialog/format/json';

		$.post(url, function(json){
			var dialog = makeShowDialog(json);
			dialog.dialog('open');
		});

	});


});

