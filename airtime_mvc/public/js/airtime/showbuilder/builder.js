function tpStartOnHourShowCallback(hour) {
    var tpEndHour = $('#show_builder_timepicker_end').timepicker('getHour');
    
    // Check if proposed hour is prior or equal to selected end time hour
    if (hour <= tpEndHour) { return true; }
    // if hour did not match, it can not be selected
    return false;
}

function tpStartOnMinuteShowCallback(hour, minute) {
    var tpEndHour = $('#show_builder_timepicker_end').timepicker('getHour'),
    	tpEndMinute = $('#show_builder_timepicker_end').timepicker('getMinute');
    
    // Check if proposed hour is prior to selected end time hour
    if (hour < tpEndHour) { return true; }
    // Check if proposed hour is equal to selected end time hour and minutes is prior
    if ( (hour == tpEndHour) && (minute < tpEndMinute) ) { return true; }
    // if minute did not match, it can not be selected
    return false;
}

function tpEndOnHourShowCallback(hour) {
    var tpStartHour = $('#show_builder_timepicker_start').timepicker('getHour');
    
    // Check if proposed hour is after or equal to selected start time hour
    if (hour >= tpStartHour) { return true; }
    // if hour did not match, it can not be selected
    return false;
}

function tpEndOnMinuteShowCallback(hour, minute) {
	var tpStartHour = $('#show_builder_timepicker_start').timepicker('getHour'),
    	tpStartMinute = $('#show_builder_timepicker_start').timepicker('getMinute');
	
    // Check if proposed hour is after selected start time hour
    if (hour > tpStartHour) { return true; }
    // Check if proposed hour is equal to selected start time hour and minutes is after
    if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
    // if minute did not match, it can not be selected
    return false;
}

/*
 * Get the schedule range start in unix timestamp form (in seconds).
 * defaults to NOW if nothing is selected.
 * 
 * @param String sDatePickerId
 * 
 * @param String sTimePickerId
 * 
 * @return Number iTime
 */
function fnGetUIPickerUnixTimestamp(sDatePickerId, sTimePickerId) {
	var oDate, 
		oTimePicker = $( sTimePickerId ),
		iTime,
		iHour,
		iMin,
		iClientOffset,
		iServerOffset;
	
	oDate = $( sDatePickerId ).datepicker( "getDate" );
	
	//nothing has been selected from this datepicker.
	if (oDate === null) {
		oDate = new Date();
	}
	else {
		iHour = oTimePicker.timepicker('getHour');
		iMin = oTimePicker.timepicker('getMinute');
		
		oDate.setHours(iHour, iMin);
	}
	
	iTime = oDate.getTime(); //value is in millisec.
	iTime = Math.round(iTime / 1000);
	iClientOffset = -(oDate.getTimezoneOffset() * 60); //offset is returned in minutes.
	iServerOffset = serverTimezoneOffset;
	
	return iTime;
}

function fnGetScheduleRange() {
	var iStart, 
		iEnd, 
		iRange;
	
	iStart = fnGetUIPickerUnixTimestamp("#show_builder_datepicker_start", "#show_builder_timepicker_start");
	iEnd = fnGetUIPickerUnixTimestamp("#show_builder_datepicker_end", "#show_builder_timepicker_end");
	
	iRange = iEnd - iStart;
}

function fnServerData( sSource, aoData, fnCallback ) {
	aoData.push( { name: "format", value: "json"} );
	
	$.ajax( {
		"dataType": "json",
		"type": "GET",
		"url": sSource,
		"data": aoData,
		"success": fnCallback
	} );
}

$(document).ready(function() {
	var dTable;
	
	dTable = $('#show_builder_table').dataTable( {
		"aoColumns": [
            /* starts */{"mDataProp": "starts", "sTitle": "starts"},
            /* ends */{"mDataProp": "ends", "sTitle": "ends"},
            /* title */{"mDataProp": "file_id", "sTitle": "file_id"}
        ],
        
        "asStripClasses": [ 'odd' ],
        
        "bJQueryUI": true,
        "bSort": false,
        "bFilter": false,
        "bProcessing": true,
		"bServerSide": true,
		"bInfo": false,
        
		"fnServerData": fnServerData,
		
        // R = ColReorder, C = ColVis, see datatables doc for others
        "sDom": 'Rr<"H"C>t<"F">',
        
        //options for infinite scrolling
        //"bScrollInfinite": true,
        //"bScrollCollapse": true,
        "sScrollY": "400px",
        
        "sAjaxDataProp": "schedule",
		"sAjaxSource": "/showbuilder/builder-feed"
		
	});
	
	$( "#show_builder_datepicker_start" ).datepicker({
		dateFormat: '@',
		onSelect: function(sDate, oDatePicker) {
			var oDate;
			
			oDate = new Date(parseInt(sDate, 10));
			$(this).val(oDate.toDateString());
		}
	});
	
	$( "#show_builder_timepicker_start" ).timepicker({
		showPeriodLabels: false,
		showCloseButton: true,
		showLeadingZero: false
	});
	
	$( "#show_builder_datepicker_end" ).datepicker({
		dateFormat: '@',
		onSelect: function(sDate, oDatePicker) {
			var oDate;
			
			oDate = new Date(parseInt(sDate, 10));
			$(this).val(oDate.toDateString());
		}
	});
	
	$( "#show_builder_timepicker_end" ).timepicker({
		showPeriodLabels: false,
		showCloseButton: true,
		showLeadingZero: false
	});
	
	$( "#show_builder_timerange_button" ).click(function(ev){
		var oTable, oSettings, iStartDate, iEndDate, iStartTime, iEndTime;
		
		fnGetScheduleRange();
		
		oTable = $('#show_builder_table').dataTable({"bRetrieve": true});
	    oSettings = oTable.fnSettings();
	    oSettings["_iDisplayStart"] = 1050;
		
		oTable.fnDraw();
	});
	
	$( "#show_builder_table" ).sortable({
		placeholder: "ui-state-highlight",
		items: 'tr',
		receive: function(event, ui) {
			var x;
		}
	});
	
});
