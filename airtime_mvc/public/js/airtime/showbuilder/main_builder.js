$(document).ready(function(){
	
	var oBaseDatePickerSettings,
		oBaseTimePickerSettings,
		oRange;
	
	oBaseDatePickerSettings = {
		dateFormat: 'yy-mm-dd',
		onSelect: function(sDate, oDatePicker) {
			var oDate,
				dInput;
			
			dInput = $(this);			
			oDate = dInput.datepicker( "setDate", sDate );
		}
	};
	
	oBaseTimePickerSettings = {
		showPeriodLabels: false,
		showCloseButton: true,
		showLeadingZero: false,
		defaultTime: '0:00'
	};
	
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
	function fnGetTimestamp(sDatePickerId, sTimePickerId) {
		var date, 
			time,
			iTime,
			iServerOffset,
			iClientOffset;
	
		if ($(sDatePickerId).val() === "") {
			return 0;
		}
		
		date = $(sDatePickerId).val();
		time = $(sTimePickerId).val();
		
		date = date.split("-");
		time = time.split(":");
		
		//0 based month in js.
		oDate = new Date(date[0], date[1]-1, date[2], time[0], time[1]);
		
		iTime = oDate.getTime(); //value is in millisec.
		iTime = Math.round(iTime / 1000);
		iServerOffset = serverTimezoneOffset;
		iClientOffset = oDate.getTimezoneOffset() * -60;//function returns minutes
		
		//adjust for the fact the the Date object is in client time.
		iTime = iTime + iClientOffset + iServerOffset;
		
		return iTime;
	}
	/*
	 * Returns an object containing a unix timestamp in seconds for the start/end range
	 * 
	 * @return Object {"start", "end", "range"}
	 */
	function fnGetScheduleRange() {
		var iStart, 
			iEnd, 
			iRange,
			DEFAULT_RANGE = 60*60*24;
		
		iStart = fnGetTimestamp("#sb_date_start", "#sb_time_start");
		iEnd = fnGetTimestamp("#sb_date_end", "#sb_time_end");
		
		iRange = iEnd - iStart;
		
		if (iRange === 0 || iEnd < iStart) {
			iEnd = iStart + DEFAULT_RANGE;
			iRange = DEFAULT_RANGE;
		}
		
		return {
			start: iStart,
			end: iEnd,
			range: iRange
		};
	}
	
	$("#sb_date_start").datepicker(oBaseDatePickerSettings);
	$("#sb_time_start").timepicker(oBaseTimePickerSettings);
	$("#sb_date_end").datepicker(oBaseDatePickerSettings);
	$("#sb_time_end").timepicker(oBaseTimePickerSettings);
	
	$("#sb_submit").click(function(ev){
		var fn,
			oRange,
			op,
			oTable = $('#show_builder_table').dataTable();
		
		oRange = fnGetScheduleRange();
		
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
	
	oRange = fnGetScheduleRange();	
	AIRTIME.showbuilder.fnServerData.start = oRange.start;
	AIRTIME.showbuilder.fnServerData.end = oRange.end;
		
	AIRTIME.showbuilder.builderDataTable();
});