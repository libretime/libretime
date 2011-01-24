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

function autoSelect(event, ui) {

	$("#hosts-"+ui.item.value).attr("checked", "checked");
	event.preventDefault();
}

function setAddShowEvents() {
	var start, end;

	$("#schedule-add-show-tabs").tabs();

	start  = $("#add_show_start_date");
	end  = $("#add_show_end_date");

	createDateInput(start, startDpSelect);
	createDateInput(end, endDpSelect);

	//var auto = json.hosts.map(function(el) {
	//	return {value: el.id, label: el.login};
	//});

	//dialog.find("#add_show_hosts_autocomplete").autocomplete({
	//	source: auto,
	//	select: autoSelect
	//});

	$("#schedule-show-style input").ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	});

	$("#add-show-submit")
		.button()
		.click(function(){
			$("form").submit();
		});
}

$(document).ready(function() {

	setAddShowEvents();

	$("#fullcalendar_show_display").fullCalendar({
		header: {
			left: 'prev, next, today',
			center: '',
			right: ''
		}, 
		defaultView: 'agendaDay',
		editable: false,
		allDaySlot: false,
		lazyFetching: false,

		events: getFullCalendarEvents,

		//callbacks
		eventRender: eventRender
	});

});
