function getFullCalendarEvents(start, end, callback) {
	var url, unix_ts_start, unix_ts_end;

	unix_ts_start = Math.round(start.getTime() / 1000),
	unix_ts_end = Math.round(end.getTime() / 1000);

	url = '/Schedule/event-feed';

	var d = new Date();

	$.get(url, {format: "json", start: unix_ts_start, end: unix_ts_end, cachep: d.getTime()}, function(json){
		callback(json.events);
	});
}

$(document).ready(function() {
	$('#show_builder').fullCalendar({
		header: {
		    left:   '',
		    center: '',
		    right:  ''
		},
		defaultView: 'agendaDay',
		allDaySlot: false,
		theme: true,
		
		events: getFullCalendarEvents,
		
		axisFormat: 'H:mm',
		slotMinutes: 1,
		timeFormat: {
            agenda: 'H:mm:ss{ - H:mm:ss}'
        },
		
		minTime: '17:00',
		maxTime: '18:00',
		
		droppable: true, // this allows things to be dropped onto the calendar !!!
		drop: function(date, allDay) { // this function is called when something is dropped
		
			// retrieve the dropped element's stored Event Object
			//var originalEventObject = $(this).data('eventObject');
			
			// we need to copy it, so that multiple events don't have a reference to the same object
			//var copiedEventObject = $.extend({}, originalEventObject);
			var copiedEventObject = {};
			var data = $(this).data("show_builder");
			
			$.ajax({url: "/showbuilder/schedule", 
				data:{format: "json", sid:"", schedule_start: date},
				dataType:"json", 
				success:function(json){
					var x;
				}, 
				error:function(jqXHR, textStatus, errorThrown){
					var x;
				}	
			});
			
			// assign it the date that was reported
			copiedEventObject.title = "test title";
			copiedEventObject.start = date;
			var end = new Date(date.getTime());
			end.setMinutes(end.getMinutes() + 5);
			end.setSeconds(end.getSeconds() + 5);
			copiedEventObject.end = end;
			copiedEventObject.allDay = allDay;
			
			// render the event on the calendar
			// the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
			//$('#show_builder').fullCalendar('renderEvent', copiedEventObject, true);
			
			$("#schedule_calendar").fullCalendar( 'refetchEvents' );			
		}
	});
});
