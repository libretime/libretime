var AIRTIME = (function(AIRTIME){
	
	if (AIRTIME.rules === undefined) {
		AIRTIME.rules = {};
    }
	
	var mod = AIRTIME.rules;
	
	var template = 
		'<div class="pl-criteria-row">' +
			'<select><%= criteria %></select>' +
			'<select><%= options %></select>' +
			'<input class="<%= fieldOneClass %>"></input>' +
			'<% if (range) { %>' +
			'<input class="<%= fieldTwoClass %>"></input>' +
			'<% } %>' +
			'<a class="btn btn-small">' +
			    '<i class="icon-white icon-minus"></i>' +
			'</a>' +
			'<a class="btn btn-small">' +
			    '<span class="pl-or">OR</span>' +
			'</a>' +
			'<a class="btn btn-small">' +
			    '<span class="pl-and">AND</span>' +
			'</a>' +
		'</div>';
	
	var stringCriteriaOptions = {
	    "" : $.i18n._("Select modifier"),
	    "contains" : $.i18n._("contains"),
	    "does not contain" : $.i18n._("does not contain"),
	    "is" : $.i18n._("is"),
	    "is not" : $.i18n._("is not"),
	    "starts with" : $.i18n._("starts with"),
	    "ends with" : $.i18n._("ends with")
	};
	    
	var numericCriteriaOptions = {
	    "" : $.i18n._("Select modifier"),
	    "is" : $.i18n._("is"),
	    "is not" : $.i18n._("is not"),
	    "is greater than" : $.i18n._("is greater than"),
	    "is less than" : $.i18n._("is less than"),
	    "is in the range" : $.i18n._("is in the range")
	};
	
	// We need to know if the criteria value will be a string
	// or numeric value in order to populate the modifier
	// select list
	var criteriaTypes = {
	    "AlbumTitle": {
	    	type: "s",
	    	name: $.i18n._("Album")
	    },
	    "BitRate": {
	    	type: "n",
	    	name: $.i18n._("Bit Rate (Kbps)")
	    },
	    "Bpm": {
	    	type: "n",
	    	name: $.i18n._("BPM")
	    },
	    "Composer": {
	    	type: "s",
	    	name: $.i18n._("Composer")
	    },
	    "Conductor": {
	    	type: "s",
	    	name: $.i18n._("Conductor")
	    },
	    "Copyright": {
	    	type: "s",
	    	name: $.i18n._("Copyright")
	    },
	    "Cuein": {
	    	type: "n",
	    	name: $.i18n._("Cue In")
	    },
	    "Cueout": {
	    	type: "n",
	    	name: $.i18n._("Cue Out")
	    },
	    "ArtistName": {
	    	type: "s",
	    	name: $.i18n._("Creator")
	    },
	    "EncodedBy": {
	    	type: "s",
	    	name: $.i18n._("Encoded By")
	    },
	    "CreatedAt": {
	    	type: "n",
	    	name: $.i18n._("Uploaded")
	    },
	    "UpdatedAt": {
	    	type: "n",
	    	name: $.i18n._("Last Modified")
	    },
	    "LastPlayedTime": {
	    	type: "n",
	    	name: $.i18n._("Last Played")
	    },
	    "Genre": {
	    	type: "s",
	    	name: $.i18n._("Genre")
	    },
	    "IsrcNumber": {
	    	type: "s",
	    	name: $.i18n._("ISRC")
	    },
	    "Label": {
	    	type: "s",
	    	name: $.i18n._("Label")
	    },
	    "Language": {
	    	type: "s",
	    	name: $.i18n._("Language")
	    },
	    "Length": {
	    	type: "n",
	    	name: $.i18n._("Length")
	    },
	    "Mime": {
	    	type: "s",
	    	name: $.i18n._("Mime")
	    },
	    "Mood": {
	    	type: "s",
	    	name: $.i18n._("Mood")
	    },
	    "ReplayGain": {
	    	type: "n",
	    	name: $.i18n._("Replay Gain")
	    },
	    "SampleRate": {
	    	type: "n",
	    	name: $.i18n._("Sample Rate (kHz)")
	    },
	    "TrackTitle": {
	    	type: "s",
	    	name: $.i18n._("Title")
	    },
	    "TrackNumber": {
	    	type: "n",
	    	name: $.i18n._("Track Number")
	    },
	    "InfoUrl": {
	    	type: "s",
	    	name: $.i18n._("Website")
	    },
	    "Year": {
	    	type: "n",
	    	name: $.i18n._("Year")
	    }
	};
	
return AIRTIME;
	
}(AIRTIME || {}));