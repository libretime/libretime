AIRTIME = (function(AIRTIME) {
	
	var viewport,
		$lib,
		$libWrapper,
		$builder,
		$fs,
		widgetHeight,
		screenWidth,
		oBaseDatePickerSettings,
		oBaseTimePickerSettings,
		oRange,
		dateStartId = "#sb_date_start",
		timeStartId = "#sb_time_start",
		dateEndId = "#sb_date_end",
		timeEndId = "#sb_time_end",
		$toggleLib = $('<div id="sb_edit" class="ui-state-default" title="open the library to schedule files."><span class="ui-icon ui-icon-arrowthick-1-nw"></span></div>'),
		$libClose = $('<a />', {
			"class": "close-round",
			"href": "#",
			"id": "sb_lib_close"
		}),
		mod;
	
	if (AIRTIME.builderMain === undefined) {
		AIRTIME.builderMain = {};
	}
	mod = AIRTIME.builderMain;
	
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
	
	function setWidgetSize() {
		viewport = AIRTIME.utilities.findViewportDimensions();
		widgetHeight = viewport.height - 180;
		screenWidth = Math.floor(viewport.width - 90);
		
		var libTableHeight = widgetHeight - 130,
			builderTableHeight = widgetHeight - 95;
		
		if ($fs.is(':visible')) {
			builderTableHeight = builderTableHeight - 40;
		}
		
		//set the heights of the main widgets.
		$builder.height(widgetHeight)
			.find(".dataTables_scrolling")
	    			.css("max-height", builderTableHeight)
	    			.end()
			.width(screenWidth);
		
		$lib.height(widgetHeight)
			.find(".dataTables_scrolling")
				.css("max-height", libTableHeight)
				.end();
		
		if ($lib.filter(':visible').length > 0) {
	    	
	    	$lib.width(Math.floor(screenWidth * 0.5));
	    	    
	    	$builder.width(Math.floor(screenWidth * 0.5))
				.find("#sb_edit")
					.remove()
					.end();
	    }	
	}

	mod.onReady = function() {
		//define module vars.
		$lib = $("#library_content");
		$builder = $("#show_builder");
		$fs = $builder.find('fieldset');
		
		$builder.find(dateStartId).datepicker(oBaseDatePickerSettings);
		$builder.find(timeStartId).timepicker(oBaseTimePickerSettings);
		$builder.find(dateEndId).datepicker(oBaseDatePickerSettings);
		$builder.find(timeEndId).timepicker(oBaseTimePickerSettings);
		
		oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);	
		AIRTIME.showbuilder.fnServerData.start = oRange.start;
		AIRTIME.showbuilder.fnServerData.end = oRange.end;

		setWidgetSize();
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

		$builder.on("click","#sb_edit", function () {
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
			
			$.ajax({
	            url: "/usersettings/set-now-playing-screen-settings",
	            type: "POST",
	            data: {settings : {library : true}, format: "json"},
	            dataType: "json",
	            success: function(){}
	          });
		});
		
		$lib.on("click", "#sb_lib_close", function() {
			var schedTable = $("#show_builder_table").dataTable();

			$lib.hide();
			$builder.width(screenWidth)
				.find(".sb-timerange")
					.append($toggleLib)
					.end();
			
			schedTable.fnDraw();
			
			$.ajax({
	            url: "/usersettings/set-now-playing-screen-settings",
	            type: "POST",
	            data: {settings : {library : false}, format: "json"},
	            dataType: "json",
	            success: function(){}
	          });
		});
		
		$builder.find('legend').click(function(ev, item){
			
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
			data["instances"] = AIRTIME.showbuilder.getShowInstances();
			
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

	};
	
	mod.onResize = function() {
		setWidgetSize();
	};
	
	return AIRTIME;
	
} (AIRTIME || {}));

$(document).ready(AIRTIME.builderMain.onReady);
$(window).resize(AIRTIME.builderMain.onResize);