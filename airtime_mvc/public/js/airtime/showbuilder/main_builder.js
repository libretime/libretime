$(document).ready(function(){
	
	var viewport = AIRTIME.utilities.findViewportDimensions(),
		$lib = $("#library_content"),
		$libWrapper,
		$builder = $("#show_builder"),
		widgetHeight = viewport.height - 180,
		screenWidth = Math.floor(viewport.width - 120),
		oBaseDatePickerSettings,
		oBaseTimePickerSettings,
		oRange,
		dateStartId = "#sb_date_start",
		timeStartId = "#sb_time_start",
		dateEndId = "#sb_date_end",
		timeEndId = "#sb_time_end",
		$toggleLib = $('<input />', {
			"class": "ui-button ui-state-default sb-edit",
			"id": "sb_edit",
			"type": "button",
			"value": "Add Files"
		}),
		$libClose = $('<a />', {
			"class": "close-round",
			"href": "#",
			"id": "sb_lib_close"
		});
	
	//set the heights of the main widgets.
	$lib.height(widgetHeight);
	
	//builder takes all the screen on first load
	$builder
		.height(widgetHeight)
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
	
	$builder.find(dateStartId).datepicker(oBaseDatePickerSettings);
	$builder.find(timeStartId).timepicker(oBaseTimePickerSettings);
	$builder.find(dateEndId).datepicker(oBaseDatePickerSettings);
	$builder.find(timeEndId).timepicker(oBaseTimePickerSettings);
	
	oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);	
	AIRTIME.showbuilder.fnServerData.start = oRange.start;
	AIRTIME.showbuilder.fnServerData.end = oRange.end;
	
	AIRTIME.library.libraryInit();
	AIRTIME.showbuilder.builderDataTable();
	
	$libWrapper = $lib.find("#library_display_wrapper");
	$libWrapper.prepend($libClose);
	
	$builder.find('.dataTables_scrolling').css("max-height", widgetHeight - 95);
	
	$builder.on("click", "#sb_submit", function(ev){
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
	
	$builder.on("click","#sb_edit", function(ev){
		var schedTable = $("#show_builder_table").dataTable();
		
		//reset timestamp to redraw the cursors.
		AIRTIME.showbuilder.resetTimestamp();
		
		$lib.show()
			.width(Math.floor(screenWidth * 0.5));
		
		$builder.width(Math.floor(screenWidth * 0.5))
			.find("#sb_edit")
				.remove()
				.end();
		
		schedTable.fnDraw();	
	});
	
	$lib.on("click", "#sb_lib_close", function(ev) {
		var schedTable = $("#show_builder_table").dataTable();

		$lib.hide();
		$builder.width(screenWidth)
			.find(".sb-timerange")
				.append($toggleLib)
				.end();
		
		schedTable.fnDraw();
	});
	
	$builder.find('legend').click(function(ev, item){
		
		$fs = $(this).parents('fieldset');
		
		if ($fs.hasClass("closed")) {
    
        	$fs.removeClass("closed");
        	$builder.find('.dataTables_scrolling').css("max-height", widgetHeight - 150);
        }
        else {
        	$fs.addClass("closed");
        	
        	//set defaults for the options.
        	$fs.find('select').val(0);
        	$fs.find('input[type="checkbox"]').attr("checked", false);
        	$builder.find('.dataTables_scrolling').css("max-height", widgetHeight - 110);
        }
	});
	
	//set click event for all my shows checkbox.
	$builder.on("click", "#sb_my_shows", function(ev) {
		
		if ($(this).is(':checked')) {
			$(ev.delegateTarget).find('#sb_show_filter').val(0);
		}	
	});
	
	//set select event for choosing a show.
	$builder.on("change", '#sb_show_filter', function(ev) {
		
		if ($(this).val() !== 0) {
			$(ev.delegateTarget).find('#sb_my_shows').attr("checked", false);
		}
	});
	
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