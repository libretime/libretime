var AIRTIME = (function(AIRTIME){
	
	if (AIRTIME.rules === undefined) {
		AIRTIME.rules = {};
    }
	
	var mod = AIRTIME.rules;
	
	var template = 
		'<div class="pl-criteria-row">' +
			'<select class="input_select sp_input_select rule_criteria <%= showCriteria %>">' +
				'<%= criteria %>' + 
			'</select>' +
			'<select class="input_select sp_input_select rule_modifier">'+
				'<%= options %>'+
			'</select>' +
			'<input class="input_text sp_input_text"></input>' +
			'<% if (range) { %>' +
				'<span class="sp_text_font" id="extra_criteria">' +	            	
					$.i18n._("to") + 
					'<input class="input_text sp_extra_input_text"></input>' +
				'</span>' +
			'<% } %>' +
			'<a class="btn btn-small btn-danger">' +
				'<i class="icon-white icon-remove"></i>' +
			'</a>' +
			'<a class="btn btn-small pl-or-criteria">' +
			    '<span>'+$.i18n._("OR")+'</span>' +
			'</a>' +
		'</div>';
	
	template = _.template(template);
	
	var criteriaOptions = {};
	
	var emptyCriteriaOptions = {
		0 : $.i18n._("Select modifier")	
	};
	
	var stringCriteriaOptions = {
	    0 : $.i18n._("Select modifier"),
	    1 : $.i18n._("contains"),
	    2 : $.i18n._("does not contain"),
	    3 : $.i18n._("is"),
	    4 : $.i18n._("is not"),
	    5 : $.i18n._("starts with"),
	    6 : $.i18n._("ends with")
	};
	    
	var numericCriteriaOptions = {
	    0 : $.i18n._("Select modifier"),
	    3 : $.i18n._("is"),
	    4 : $.i18n._("is not"),
	    7 : $.i18n._("is greater than"),
	    8 : $.i18n._("is less than"),
	    9 : $.i18n._("is greater than or equal to"),
	    10 : $.i18n._("is less than or equal to"),
	    11 : $.i18n._("is in the range")
	};
	
	// We need to know if the criteria value will be a string
	// or numeric value in order to populate the modifier
	// select list
	var criteriaTypes = {
		"": {
			type: "",
			name: $.i18n._("Select criteria")
		},
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
	
	function setupCriteriaOptions() {
		var key;
		
		for (key in criteriaTypes) {
			criteriaOptions[key] = criteriaTypes[key].name;
		}
	}
	
	function makeSelectOptions(options, selected) {
		var key;
		var modifier = "";
		
		for (key in options) {
			
			if (key === selected) {
				modifier += '<option value="'+key+'" label="'+options[key]+'" selected="selected">'+options[key]+'</option>';
			}
			else {
				modifier += '<option value="'+key+'" label="'+options[key]+'">'+options[key]+'</option>';
			}
		}
		
		return modifier;
	}
	
	function createCriteriaRow(criteria, modifierValue, options) {
    	var $el,
    		defaults,
    		settings,
    		showCriteria;
    	
    	var fullCriteria = makeSelectOptions(criteriaOptions, criteria);
    	
    	var modifier = setCorrectModifier(criteria);
    	var modifierHtml = makeSelectOptions(modifier, modifierValue);
    	
    	defaults = {
    		range: modifierValue === "11" ? true : false,
    		showCriteria: true,
    		options: modifierHtml,
    		criteria: fullCriteria
    	};
    	
    	settings = $.extend({}, defaults, options);
    	
    	showCriteria = settings.showCriteria ? "" : "sp-invisible";
    	
    	$el = $(template({
    		showCriteria: showCriteria,
    		range: settings.range,
    		options: settings.options,
    		criteria: settings.criteria
    	}));
    	
    	return $el;
    }
	
	function setCorrectModifier(criteriaValue) {
		var type = criteriaTypes[criteriaValue].type;
		
		if (type === "s") {
			return stringCriteriaOptions; 
		}
		else if (type === "n") {
			return numericCriteriaOptions;
		}
		else {
			return emptyCriteriaOptions;
		}
	}
	
	mod.onReady = function() {
		var $criteriaEl = $("#rule_criteria");
		
		setupCriteriaOptions();
		
		$criteriaEl.on("click", "#spl_AND", function(e) {
			e.preventDefault();
			
			var $el = createCriteriaRow("", "");
			
			$div = $("<div class='pl-criteria-and'></div>");
			$div.append($el);
			
			$(this).before($div);
		});
		
		$criteriaEl.on("click", ".pl-or-criteria", function(e) {
			e.preventDefault();
			
			var $el = createCriteriaRow("", "");
			
			$(this).parents("div.pl-criteria-row").after($el);
		});
		
		$criteriaEl.on("change", ".rule_criteria", function(e) {
			var $select,
				$el;
			
			e.preventDefault();
			
			$select = $(this);
			$el = createCriteriaRow($select.val(), "");
			$select.parents("div.pl-criteria-row").replaceWith($el);	
		});
		
		$criteriaEl.on("change", ".rule_modifier", function(e) {
			var $select,
				$modifier,
				$el;
			
			e.preventDefault();
			
			$modifier = $(this);
			$select = $modifier.parents("div.pl-criteria-row").find(".rule_criteria");
			
			$el = createCriteriaRow($select.val(), $modifier.val());
			$select.parents("div.pl-criteria-row").replaceWith($el);	
		});
		
		$criteriaEl.on("click", ".btn-danger", function(e) {
			var $row,
				$andBlock;
			
			e.preventDefault();
			
			$row = $(this).parents("div.pl-criteria-row");
			$andBlock = $row.parent();
			
			if ($andBlock.children().length === 1) {
				$andBlock.remove();
			}
			else {
				$row.remove();
			}
		});
	};

return AIRTIME;
	
}(AIRTIME || {}));

$(document).ready(AIRTIME.rules.onReady);