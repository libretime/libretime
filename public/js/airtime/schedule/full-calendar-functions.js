/**
*
*	Full Calendar callback methods.
*
*/

function makeAddShowButton(){
    $('.fc-header-left tbody tr:first')
        .append('<td><span class="fc-header-space"></span></td>')
        .append('<td><a href="#" class="add-button"><span class="add-icon"></span>Show</a></td>')
        .find('td:last > a')
            .click(function(){
                $("#add-show-form").show();
                var y = $("#schedule_calendar").width();
                var z = $("#schedule-add-show").width();
                $("#schedule_calendar").width(y-z-50);
                $("#schedule_calendar").fullCalendar('render');

                var td = $(this).parent();
                $(td).prev().remove();
                $(td).remove();
            });
}

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

function dayClick(date, allDay, jsEvent, view) {
	var x;
}

function viewDisplay( view ) {

    if(view.name === 'agendaDay' || view.name === 'agendaWeek') {

        var calendarEl = this;

        var select = $('<select class="schedule_change_slots input_select"/>')
            .append('<option value="5">5m</option>')
            .append('<option value="10">10m</option>')
            .append('<option value="15">15m</option>')
            .append('<option value="30">30m</option>')
            .append('<option value="60">60m</option>')
            .change(function(){
                var x = $(this).val();
                var opt = view.calendar.options;
                opt.slotMinutes = parseInt(x);
                opt.events = getFullCalendarEvents;
                opt.defaultView = view.name;
                $(calendarEl).fullCalendar('destroy');
                $(calendarEl).fullCalendar(opt); 
            });

        var x = $(view.element).find(".fc-agenda-head th:first");
        select.width(x.width());
        x.empty();
        x.append(select);

        var slotMin = view.calendar.options.slotMinutes;
        $('.schedule_change_slots option[value="'+slotMin+'"]').attr('selected', 'selected');

    }

    if(($("#add-show-form").length == 1) && ($("#add-show-form").css('display')=='none') && ($('.fc-header-left tbody td').length == 5)) {
        makeAddShowButton();
    }
}

function eventRender(event, element, view) { 

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

		$(element).find(".fc-event-title").after(div);
	}	

	if(event.backgroundColor !== "") {
		$(element)
			.css({'border-color': '#'+event.backgroundColor})
			.find(".fc-event-time, a")
				.css({'background-color': '#'+event.backgroundColor, 'border-color': '#'+event.backgroundColor});
	}
	if(event.color !== "") {
		$(element)
			.find(".fc-event-time, a")
				.css({'color': '#'+event.color});
	}
}

function eventAfterRender( event, element, view ) {

    $(element)
		.jjmenu("rightClick", 
			[{get:"/Schedule/make-context-menu/format/json/id/#id#"}],  
			{id: event.id}, 
			{xposition: "mouse", yposition: "mouse"});
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
		{day: dayDelta, min: minuteDelta, showInstanceId: event.id},
		function(json){
			if(json.error) {
                alert(json.error);
				revertFunc();
			}
		});
}

function eventResize( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) { 
	var url;

	url = '/Schedule/resize-show/format/json';

	$.post(url, 
		{day: dayDelta, min: minuteDelta, showInstanceId: event.id},
		function(json){
			if(json.error) {
                alert(json.error);
				revertFunc();
			}
		});
}

function getFullCalendarEvents(start, end, callback) {
	var url, start_date, end_date;

	start_date = makeTimeStamp(start);
	end_date = makeTimeStamp(end);

	url = '/Schedule/event-feed';
	
	var d = new Date();

	$.post(url, {format: "json", start: start_date, end: end_date, cachep: d.getTime()}, function(json){
		callback(json.events);
	});
}
