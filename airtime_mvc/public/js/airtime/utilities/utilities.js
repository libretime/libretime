var AIRTIME = (function(AIRTIME){
	var mod;
	
	if (AIRTIME.utilities === undefined) {
		AIRTIME.utilities = {};
	}
	mod = AIRTIME.utilities;
	
	mod.findViewportDimensions = function() {
		var viewportwidth,
			viewportheight;
		
		// the more standards compliant browsers (mozilla/netscape/opera/IE7) use
		// window.innerWidth and window.innerHeight
		if (typeof window.innerWidth != 'undefined') {
			viewportwidth = window.innerWidth, viewportheight = window.innerHeight;
		}
		// IE6 in standards compliant mode (i.e. with a valid doctype as the first
		// line in the document)
		else if (typeof document.documentElement != 'undefined'
				&& typeof document.documentElement.clientWidth != 'undefined'
				&& document.documentElement.clientWidth != 0) {
			viewportwidth = document.documentElement.clientWidth;
			viewportheight = document.documentElement.clientHeight;
		}
		// older versions of IE
		else {
			viewportwidth = document.getElementsByTagName('body')[0].clientWidth;
			viewportheight = document.getElementsByTagName('body')[0].clientHeight;
		}
		
		return {
			width: viewportwidth,
			height: viewportheight
		};
	};
	
	mod.fnGetSecondsEpoch = function(oDate) {
		var iTime,
			iServerOffset,
			iClientOffset;
		
		iTime = oDate.getTime(); //value is in millisec.
		iTime = Math.round(iTime / 1000);
		iServerOffset = serverTimezoneOffset;
		iClientOffset = oDate.getTimezoneOffset() * -60;//function returns minutes
		
		//adjust for the fact the the Date object is in client time.
		iTime = iTime + iClientOffset + iServerOffset;
		
		return iTime;
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
	mod.fnGetTimestamp = function(sDateId, sTimeId) {
		var date, 
			time,
			iTime,
			iServerOffset,
			iClientOffset,
			temp;
	
		temp = $(sDateId).val();
		if ( temp === "") {
			return 0;
		}
		else {
			date = temp;
		}
		
		time = $(sTimeId).val();
		
		date = date.split("-");
		time = time.split(":");
		
		//0 based month in js.
		oDate = new Date(date[0], date[1]-1, date[2], time[0], time[1]);
		
		return mod.fnGetSecondsEpoch(oDate);
	};
	
	/*
	 * Returns an object containing a unix timestamp in seconds for the start/end range
	 * 
	 * @return Object {"start", "end", "range"}
	 */
	mod.fnGetScheduleRange = function(dateStart, timeStart, dateEnd, timeEnd) {
		var iStart, 
			iEnd, 
			iRange;
		
		iStart = AIRTIME.utilities.fnGetTimestamp(dateStart, timeStart);
		iEnd = AIRTIME.utilities.fnGetTimestamp(dateEnd, timeEnd);
		
		iRange = iEnd - iStart;
		
		return {
			start: iStart,
			end: iEnd,
			range: iRange
		};
	};
	
return AIRTIME;
	
}(AIRTIME || {}));
