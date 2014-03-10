AIRTIME = (function(AIRTIME) {
    
    var viewport,
        $lib,
        $libWrapper,
        $builder,
        $fs,
        widgetHeight,
        screenWidth,
        resizeTimeout,
        oBaseDatePickerSettings,
        oBaseTimePickerSettings,
        oRange,
        dateStartId = "#sb_date_start",
        timeStartId = "#sb_time_start",
        dateEndId = "#sb_date_end",
        timeEndId = "#sb_time_end",
        $toggleLib = $("<a id='sb_edit' class='btn btn-small' href='#' title='"+$.i18n._("Open library to add or remove content")+"'>"+$.i18n._("Add / Remove Content")+"</a>"),
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
        //i18n_months, i18n_days_short are in common.js
        monthNames: i18n_months,
        dayNamesMin: i18n_days_short,
        onClick: function(sDate, oDatePicker) {     
            $(this).datepicker( "setDate", sDate );
        },
        onClose: validateTimeRange
    };
    
    oBaseTimePickerSettings = {
        showPeriodLabels: false,
        showCloseButton: true,
        closeButtonText: $.i18n._("Done"),
        showLeadingZero: false,
        defaultTime: '0:00',
        hourText: $.i18n._("Hour"),
        minuteText: $.i18n._("Minute"),
        onClose: validateTimeRange
    };
    
    function setWidgetSize() {
        viewport = AIRTIME.utilities.findViewportDimensions();
        widgetHeight = viewport.height - 180;
        screenWidth = Math.floor(viewport.width - 40);
        
        var libTableHeight = widgetHeight - 175,
            builderTableHeight = widgetHeight - 95,
            oTable;
        
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
            
            $lib.width(Math.floor(screenWidth * 0.48));
                
            $builder.width(Math.floor(screenWidth * 0.48))
                .find("#sb_edit")
                    .remove()
                    .end()
                .find("#sb_date_start")
                    .css("margin-left", 0)
                    .end();
            
            oTable = $('#show_builder_table').dataTable();
            //oTable.fnDraw();
        }   
    }
    
    function validateTimeRange() {
    	 var oRange,
	         inputs = $('.sb-timerange > input'),
	         start, end;
    	 
    	 oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
    	 
    	 start = oRange.start;
         end = oRange.end;
         
         if (end >= start) {
        	 inputs.removeClass('error');
         }
         else {
        	 if (!inputs.hasClass('error')) {
        		 inputs.addClass('error');  
        	 } 
         }
         
         return {
        	 start: start,
        	 end: end,
        	 isValid: end >= start
         };
    }
    
    function showSearchSubmit() {
        var fn,
            op,
            oTable = $('#show_builder_table').dataTable(),
            check;
                  
        check = validateTimeRange();
        
        if (check.isValid) {
        	
	        //reset timestamp value since input values could have changed.
	        AIRTIME.showbuilder.resetTimestamp();
	            
	        fn = oTable.fnSettings().fnServerData;
	        fn.start = check.start;
	        fn.end = check.end;
	            
	        op = $("div.sb-advanced-options");
	        if (op.is(":visible")) {
	                
	            if (fn.ops === undefined) {
	                fn.ops = {};
	            }
	            fn.ops.showFilter = op.find("#sb_show_filter").val();
	            fn.ops.myShows = op.find("#sb_my_shows").is(":checked") ? 1 : 0;
	        }
	            
	        oTable.fnDraw();
        }
    }

    mod.onReady = function() {
        // define module vars.
        $lib = $("#library_content");
        $builder = $("#show_builder");
        $fs = $builder.find('fieldset');

        /*
         * Icon hover states for search.
         */
        $builder.on("mouseenter", ".sb-timerange .ui-button", function(ev) {
            $(this).addClass("ui-state-hover");
        });
        $builder.on("mouseleave", ".sb-timerange .ui-button", function(ev) {
            $(this).removeClass("ui-state-hover");
        });

        $builder.find(dateStartId)
        	.datepicker(oBaseDatePickerSettings)
        	.blur(validateTimeRange);
        
        $builder.find(timeStartId)
        	.timepicker(oBaseTimePickerSettings)
        	.blur(validateTimeRange);
        
        $builder.find(dateEndId)
        	.datepicker(oBaseDatePickerSettings)
        	.blur(validateTimeRange);
        
        $builder.find(timeEndId)
        	.timepicker(oBaseTimePickerSettings)
        	.blur(validateTimeRange);
        

        oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId,
                dateEndId, timeEndId);
        AIRTIME.showbuilder.fnServerData.start = oRange.start;
        AIRTIME.showbuilder.fnServerData.end = oRange.end;

        //the user might not have the library on the page (guest user)
        if (AIRTIME.library !== undefined) {
        	AIRTIME.library.libraryInit();
        }
        
        AIRTIME.showbuilder.builderDataTable();
        setWidgetSize();

        $libWrapper = $lib.find("#library_display_wrapper");
        $libWrapper.prepend($libClose);

        $builder.find('.dataTables_scrolling').css("max-height",
                widgetHeight - 95);

        $builder.on("click", "#sb_submit", showSearchSubmit);

        $builder.on("click", "#sb_edit", function(ev) {
            var schedTable = $("#show_builder_table").dataTable();

            // reset timestamp to redraw the cursors.
            AIRTIME.showbuilder.resetTimestamp();

            $lib.show().width(Math.floor(screenWidth * 0.48));

            $builder.width(Math.floor(screenWidth * 0.48)).find("#sb_edit")
                    .remove().end().find("#sb_date_start")
                    .css("margin-left", 0).end();

            schedTable.fnDraw();

            $.ajax( {
                url : baseUrl+"usersettings/set-now-playing-screen-settings",
                type : "POST",
                data : {
                    settings : {
                        library : true
                    },
                    format : "json"
                },
                dataType : "json",
                success : function() {
                }
            });
        });

        $lib.on("click", "#sb_lib_close", function() {
            var schedTable = $("#show_builder_table").dataTable();

            $lib.hide();
            $builder.width(screenWidth).find(".sb-timerange").prepend(
                    $toggleLib).find("#sb_date_start").css("margin-left", 30)
                    .end().end();

            $toggleLib.removeClass("ui-state-hover");
            schedTable.fnDraw();

            $.ajax( {
                url : baseUrl+"usersettings/set-now-playing-screen-settings",
                type : "POST",
                data : {
                    settings : {
                        library : false
                    },
                    format : "json"
                },
                dataType : "json",
                success : function() {
                }
            });
        });

        $builder.find('legend').click(
                function(ev, item) {

                    if ($fs.hasClass("closed")) {

                        $fs.removeClass("closed");
                        $builder.find('.dataTables_scrolling').css(
                                "max-height", widgetHeight - 150);
                    } else {
                        $fs.addClass("closed");

                        // set defaults for the options.
                        $fs.find('select').val(0);
                        $fs.find('input[type="checkbox"]').attr("checked",
                                false);
                        $builder.find('.dataTables_scrolling').css(
                                "max-height", widgetHeight - 110);
                    }
                });

        // set click event for all my shows checkbox.
        $builder.on("click", "#sb_my_shows", function(ev) {

            if ($(this).is(':checked')) {
                $(ev.delegateTarget).find('#sb_show_filter').val(0);
            }

            showSearchSubmit();
        });

        //set select event for choosing a show.
        $builder.on("change", '#sb_show_filter', function(ev) {

            if ($(this).val() !== 0) {
                $(ev.delegateTarget).find('#sb_my_shows')
                        .attr("checked", false);
            }

            showSearchSubmit();

        });

        function checkScheduleUpdates() {
            var data = {}, oTable = $('#show_builder_table').dataTable(), fn = oTable
                    .fnSettings().fnServerData, start = fn.start, end = fn.end;

            data["format"] = "json";
            data["start"] = start;
            data["end"] = end;
            data["timestamp"] = AIRTIME.showbuilder.getTimestamp();
            data["instances"] = AIRTIME.showbuilder.getShowInstances();

            if (fn.hasOwnProperty("ops")) {
                data["myShows"] = fn.ops.myShows;
                data["showFilter"] = fn.ops.showFilter;
                data["showInstanceFilter"] = fn.ops.showInstanceFilter;
            }

            $.ajax( {
                "dataType" : "json",
                "type" : "GET",
                "url" : baseUrl+"showbuilder/check-builder-feed",
                "data" : data,
                "success" : function(json) {
                    if (json.update === true) {
                        oTable.fnDraw();
                    }
                    setTimeout(checkScheduleUpdates, 5000);
                }
            });
        }

        //check if the timeline view needs updating.
        checkScheduleUpdates();
    };

    mod.onResize = function() {

        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(setWidgetSize, 100);
    };

    return AIRTIME;

} (AIRTIME || {}));

$(document).ready(AIRTIME.builderMain.onReady);
$(window).resize(AIRTIME.builderMain.onResize);
