$(document).ready(function(){
	
	var viewport = AIRTIME.utilities.findViewportDimensions(),
		lib = $("#library_content"),
		builder = $("#show_builder"),
		widgetHeight = viewport.height - 185,
		screenWidth = Math.floor(viewport.width - 110),
		oBaseDatePickerSettings,
		oBaseTimePickerSettings,
		oRange,
		dateStartId = "#sb_date_start",
		timeStartId = "#sb_time_start",
		dateEndId = "#sb_date_end",
		timeEndId = "#sb_time_end";
	
	//set the heights of the main widgets.
	lib.height(widgetHeight);
	
	//builder takes all the screen on first load
	builder.height(widgetHeight)
		.width(screenWidth);
	
	oBaseDatePickerSettings = {
		dateFormat: 'yy-mm-dd',
		onSelect: function(sDate, oDatePicker) {		
			$(this).datepicker( "setDate", sDate );
		}
	};
	
	oBaseTimePickerSettings = {
		showPeriodLabels: false,
		showCloseButton: true,
		showLeadingZero: false,
		defaultTime: '0:00'
	};
	
	builder.find(dateStartId).datepicker(oBaseDatePickerSettings);
	builder.find(timeStartId).timepicker(oBaseTimePickerSettings);
	builder.find(dateEndId).datepicker(oBaseDatePickerSettings);
	builder.find(timeEndId).timepicker(oBaseTimePickerSettings);
	
	$("#sb_submit").click(function(ev){
		var fn,
			oRange,
			op,
			oTable = $('#show_builder_table').dataTable();
		
		//reset timestamp value since input values could have changed.
		AIRTIME.showbuilder.resetTimestamp();
		
		oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
		
	    fn = oTable.fnSettings().fnServerData;
	    fn.start = oRange.start;
	    fn.end = oRange.end;
	    
	    op = $("div.sb-advanced-options");
	    if (op.is(":visible")) {
	    	
	    	if (fn.ops === undefined) {
	    		fn.ops = {};
	    	}
	    	fn.ops.showFilter = op.find("#sb_show_filter").val();
	    	fn.ops.myShows = op.find("#sb_my_shows").is(":checked") ? 1 : 0;
	    }
		
		oTable.fnDraw();
	});
	
	$("#sb_edit").click(function(ev){
		var $button = $(this),
			$lib = $("#library_content"),
			$builder = $("#show_builder"),
			schedTable = $("#show_builder_table").dataTable();
		
		if ($button.hasClass("sb-edit")) {
			
			//reset timestamp to redraw the cursors.
			AIRTIME.showbuilder.resetTimestamp();
			
			$lib.show();
			$lib.width(Math.floor(screenWidth * 0.5));
			$builder.width(Math.floor(screenWidth * 0.5));
			
			$button.removeClass("sb-edit");
			$button.addClass("sb-finish-edit");
			$button.val("Close Library");
		}
		else if ($button.hasClass("sb-finish-edit")) {
			
			$lib.hide();
			$builder.width(screenWidth);
			
			$button.removeClass("sb-finish-edit");
			$button.addClass("sb-edit");
			$button.val("Add Files");
		}
		
		schedTable.fnDraw();	
	});
	
	oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);	
	AIRTIME.showbuilder.fnServerData.start = oRange.start;
	AIRTIME.showbuilder.fnServerData.end = oRange.end;
	
	AIRTIME.library.libraryInit();
	AIRTIME.showbuilder.builderDataTable();
	
	//check if the timeline viewed needs updating.
	setInterval(function(){
		var data = {},
			oTable = $('#show_builder_table').dataTable(),
			fn = oTable.fnSettings().fnServerData,
	    	start = fn.start,
	    	end = fn.end;
		
		data["format"] = "json";
		data["start"] = start;
		data["end"] = end;
		data["timestamp"] = AIRTIME.showbuilder.getTimestamp();
		
		if (fn.hasOwnProperty("ops")) {
			data["myShows"] = fn.ops.myShows;
			data["showFilter"] = fn.ops.showFilter;
		}
		
		$.ajax( {
			"dataType": "json",
			"type": "GET",
			"url": "/showbuilder/check-builder-feed",
			"data": data,
			"success": function(json) {
				if (json.update === true) {
					oTable.fnDraw();
				}
			}
		} );
		
	}, 5 * 1000); //need refresh in milliseconds
});