AIRTIME = (function(AIRTIME) {
    
    var $lib,
        $libWrapper,
        $builder,
        $fs,
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
        console.log("calling builder datatable");
        AIRTIME.showbuilder.builderDataTable();

        $libWrapper = $lib.find(".ui-tabs-nav");
        $libWrapper.append($libClose);

        $builder.on("click", "#sb_submit", showSearchSubmit);

        $builder.on("click", "#sb_edit", function(ev) {
            var schedTable = $("#show_builder_table").dataTable();

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

            //$lib.hide();
            
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

        $builder.find('legend').click(function(ev, item) {

            if ($fs.hasClass("closed")) {

                $fs.removeClass("closed");
            } 
            else {
                $fs.addClass("closed");

                // set defaults for the options.
                $fs.find('select').val(0);
                $fs.find('input[type="checkbox"]').attr("checked", false);
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
                $(ev.delegateTarget)
                	.find('#sb_my_shows')
                    .attr("checked", false);
            }

            showSearchSubmit();
        });
    };

    return AIRTIME;

} (AIRTIME || {}));

$(document).ready(AIRTIME.builderMain.onReady);
