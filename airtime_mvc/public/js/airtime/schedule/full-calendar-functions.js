/**
*
*	Full Calendar callback methods.
*
*/

function scheduleRefetchEvents() {
    $("#schedule_calendar").fullCalendar( 'refetchEvents' );
}

function openAddShowForm() {

     if($("#add-show-form").length == 1) {
        if( ($("#add-show-form").css('display')=='none')) {
            $("#add-show-form").show();
            var windowWidth = $(window).width();
            // margin on showform are 16 px on each side
            var calendarWidth = 100-(($("#schedule-add-show").width() + (16 * 6))/windowWidth*100);
            var widthPercent = parseInt(calendarWidth)+"%";
            $("#schedule_calendar").css("width", widthPercent);
            $("#schedule_calendar").fullCalendar('render');
        }
        $("#schedule-show-what").show(0, function(){
        	$add_show_name = $("#add_show_name");
        	$add_show_name.focus();
        	$add_show_name.select();
        });
    }
}

function makeAddShowButton(){
    $('.fc-header-left')
        .append('<span class="fc-header-space"></span>')
        .append('<span class="fc-button"><a href="#" class="add-button"><span class="add-icon"></span>Show</a></span>')
        .find('span.fc-button:last > a')
            .click(function(){
                openAddShowForm();
                removeAddShowButton();
            });
}

function removeAddShowButton(){
    var aTag = $('.fc-header-left')
        .find("span.fc-button:last > a");

    var span = aTag.parent();
    span.prev().remove();
    span.remove();
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
    var now, today, selected, chosenDate, chosenTime;

    now = new Date();

    if(view.name === "month") {
        today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        selected = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }
    else {
        today = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(), now.getMinutes());
        selected = new Date(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes());
    }

    if(selected >= today) {
        var addShow = $('.add-button');

        //remove the +show button if it exists.
        if(addShow.length == 1){
             var span = $(addShow).parent();
            $(span).prev().remove();
            $(span).remove();
        }

        chosenDate = selected.getFullYear();

        var month = selected.getMonth() + 1;
        if(month < 10) {
            chosenDate = chosenDate+'-0'+month;
        }
        else {
            chosenDate = chosenDate+'-'+month;
        }

        var day = selected.getDate();
        if(day < 10) {
            chosenDate = chosenDate+'-0'+day;
        }
        else {
            chosenDate = chosenDate+'-'+day;
        }

        var min = selected.getMinutes();
        var hours = selected.getHours();
        if(min < 10){
            chosenTime = hours+":0"+min;
        }
        else {
            chosenTime = hours+":"+min;
        }
        
        if(hours < 10){
        	chosenTime = "0"+chosenTime;
        }
        
        var endHour = hours + 1;
        var chosenEndTime;
        
        if(min < 10){
        	chosenEndTime = endHour+":0"+min;
        }
        else {
        	chosenEndTime = endHour+":"+min;
        }
        
        if(endHour < 10){
        	chosenEndTime = "0"+chosenEndTime;
        }

        $("#add_show_start_date").val(chosenDate);
        $("#add_show_end_date_no_repeat").val(chosenDate);
        $("#add_show_end_date").datepicker("option", "minDate", chosenDate);
        $("#add_show_end_date").val(chosenDate);
        $("#add_show_start_time").val(chosenTime);
        $("#add_show_end_time").val(chosenEndTime);
        $("#add_show_duration").val('1h');
        $("#schedule-show-when").show();

	    openAddShowForm();
    }
}

function viewDisplay( view ) {

    if(view.name === 'agendaDay' || view.name === 'agendaWeek') {

        var calendarEl = this;

        var select = $('<select class="schedule_change_slots input_select"/>')
            .append('<option value="1">1m</option>')
            .append('<option value="5">5m</option>')
            .append('<option value="10">10m</option>')
            .append('<option value="15">15m</option>')
            .append('<option value="30">30m</option>')
            .append('<option value="60">60m</option>')
            .change(function(){
                var slotMin = $(this).val();
                var opt = view.calendar.options;
                var date = $(calendarEl).fullCalendar('getDate');

                opt.slotMinutes = parseInt(slotMin);
                opt.events = getFullCalendarEvents;
                opt.defaultView = view.name;

                //re-initialize calendar with new slotmin options
                $(calendarEl)
                    .fullCalendar('destroy')
                    .fullCalendar(opt)
                    .fullCalendar( 'gotoDate', date );
            });

        var topLeft = $(view.element).find("table.fc-agenda-days > thead th:first");

        select.width(topLeft.width())
            .height(topLeft.height());

        topLeft.empty()
            .append(select);

        var slotMin = view.calendar.options.slotMinutes;
        $('.schedule_change_slots option[value="'+slotMin+'"]').attr('selected', 'selected');
    }

    if(($("#add-show-form").length == 1) && ($("#add-show-form").css('display')=='none') && ($('.fc-header-left > span').length == 5)) {
        makeAddShowButton();
    }
}

function eventRender(event, element, view) {

    //only put progress bar on shows that aren't being recorded.
	if((view.name === 'agendaDay' || view.name === 'agendaWeek') && event.record === 0) {
		var div = $('<div/>');
		div
			.height('5px')
			.width('95%')
			.css('margin-top', '1px')
            .css('margin-left', 'auto')
            .css('margin-right', 'auto')
			.progressbar({
				value: event.percent
			});

		$(element).find(".fc-event-content").append(div);
	}

    //add the record/rebroadcast icons if needed.

    //record icon (only if not on soundcloud, will always be true for future events)
    if((view.name === 'agendaDay' || view.name === 'agendaWeek') && event.record === 1 && event.soundcloud_id === -1) {

		$(element).find(".fc-event-time").before('<span class="small-icon recording"></span>');
	}
    if(view.name === 'month' && event.record === 1 && event.soundcloud_id === -1) {

		$(element).find(".fc-event-title").after('<span class="small-icon recording"></span>');
	}
    //rebroadcast icon
    if((view.name === 'agendaDay' || view.name === 'agendaWeek') && event.rebroadcast === 1) {

		$(element).find(".fc-event-time").before('<span class="small-icon rebroadcast"></span>');
	}
    if(view.name === 'month' && event.rebroadcast === 1) {

		$(element).find(".fc-event-title").after('<span class="small-icon rebroadcast"></span>');
	}
    //soundcloud icon
    if((view.name === 'agendaDay' || view.name === 'agendaWeek') && event.soundcloud_id !== -1 && event.record === 1) {

		$(element).find(".fc-event-time").before('<span class="small-icon soundcloud"></span>');
	}
    if(view.name === 'month' && event.soundcloud_id !== -1 && event.record === 1) {

		$(element).find(".fc-event-title").after('<span class="small-icon soundcloud"></span>');
	}
}

function eventAfterRender( event, element, view ) {

    $(element)
		.jjmenu("click",
			[{get:"/Schedule/make-context-menu/format/json/id/#id#"}],
			{id: event.id},
			{xposition: "mouse", yposition: "mouse"});
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

            scheduleRefetchEvents();
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
