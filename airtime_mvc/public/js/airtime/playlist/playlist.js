var AIRTIME = (function(AIRTIME){
	
	if (AIRTIME.playlist === undefined) {
		AIRTIME.playlist = {};
    }
	
	var mod = AIRTIME.playlist;
	
	var template = 
		'<div class="pl-criteria-row">' +
			'<select class="input_select sp_input_select rule_criteria <%= showCriteria %>">' +
				'<%= criteria %>' + 
			'</select>' +
			'<select class="input_select sp_input_select rule_modifier">'+
				'<%= options %>'+
			'</select>' +
			'<% if (input) { %>' +
				'<input class="input_text sp_input_text"></input>' +
			'<% } %>' +
			'<% if (range) { %>' +
				'<span class="sp_text_font" id="extra_criteria">' +	            	
					$.i18n._("to") + 
					'<input class="input_text sp_extra_input_text"></input>' +
				'</span>' +
			'<% } %>' +
			'<% if (relDateOptions) { %>' +
				'<select class="input_select sp_input_select sp_rule_unit">'+
					'<%= relDateOptions %>'+
				'</select>' +
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
	
	var relativeDateCriteriaOptions = {
	    12 : $.i18n._("today"),
	    13 : $.i18n._("yesterday"),
	    14 : $.i18n._("this week"),
		15 : $.i18n._("last week"),
		16 : $.i18n._("this month"),
		17 : $.i18n._("last month"),
		18 : $.i18n._("this year"),
		19 : $.i18n._("last year"),
		20 : $.i18n._("in the last"),
		21 : $.i18n._("not in the last")
	};
	
	var relativeDateUnitOptions = {
		0 : $.i18n._("-----"),
		1 : $.i18n._("seconds"),
	    2 : $.i18n._("minutes"),
	    3 : $.i18n._("hours"),
	    4 : $.i18n._("days"),
		5 : $.i18n._("weeks"),
		6 : $.i18n._("months"),
		7 : $.i18n._("years"),
		8 : $.i18n._("hh:mm(:ss)")
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
	    "ArtistName": {
	    	type: "s",
	    	name: $.i18n._("Creator")
	    },
	    "EncodedBy": {
	    	type: "s",
	    	name: $.i18n._("Encoded By")
	    },
	    "CreatedAt": {
	    	type: "d",
	    	name: $.i18n._("Uploaded")
	    },
	    "UpdatedAt": {
	    	type: "d",
	    	name: $.i18n._("Last Modified")
	    },
	    "LastPlayedTime": {
	    	type: "d",
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
    		showCriteria,
    		noInput = ["12", "13", "14", "15", "16", "17", "18", "19"],
    		hasRange = ["11"],
    		hasRelDateOptions = ["20", "21"],
    		type = criteriaTypes[criteria].type;
    	
    	var fullCriteria = makeSelectOptions(criteriaOptions, criteria);
    	var relDateSelect = makeSelectOptions(relativeDateUnitOptions);
    	
    	var modifier = setCorrectModifier(criteria);
    	var modifierHtml = makeSelectOptions(modifier, modifierValue);
    	
    	defaults = {
    		input: noInput.indexOf(modifierValue) === -1 ? true : false,
    		range: hasRange.indexOf(modifierValue) !== -1 ? true : false,
    		showCriteria: true,
    		options: modifierHtml,
    		criteria: fullCriteria,
    		relDate: type === "d" ? true : false,
    		relDateOptions: hasRelDateOptions.indexOf(modifierValue) === -1 ? null : relDateSelect
    	};
    	
    	settings = $.extend({}, defaults, options);
    	
    	showCriteria = settings.showCriteria ? "" : "sp-invisible";
    	
    	$el = $(template({
    		showCriteria: showCriteria,
    		input: settings.input,
    		range: settings.range,
    		options: settings.options,
    		criteria: settings.criteria,
    		relDate: settings.relDate,
    		relDateOptions: settings.relDateOptions
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
		else if (type === "d") {
			return $.extend({}, numericCriteriaOptions, relativeDateCriteriaOptions);
		}
		else {
			return emptyCriteriaOptions;
		}
	}
	
	function cleanString(string) {
		return string.trim();
	}
	
	function isTimeValid(time) {
	    var regExpr = new RegExp("^\\d{2}[:]([0-5]){1}([0-9]){1}[:]([0-5]){1}([0-9]){1}([.]\\d{1,6})?$");
		
		return regExpr.test(time);
	}
	
	function isFadeValid(fade) {
        var regExpr = new RegExp("^\\d{1}(\\d{1})?([.]\\d{1})?$");

        return regExpr.test(fade);
	}
	
	function createErrorSpan(text) {
		var $error = $("<span/>", {
			class: "edit-error"
		});
		
		$error.text(text);
		return $error;
	}
	
	function changeFade(event) {
		var $span = $(this), 
			fade = cleanString($span.text()),
			$error,
			$dd = $span.parent();
		
		$dd.find(".edit-error").remove();
		
		if (!isFadeValid(fade)) {
			$error = createErrorSpan($.i18n._("please put in a time in seconds '00 (.0)'"));
			$dd.append($error);
		}
		else {
			$dd.data("fade", fade);
		}
	}
	
	function changeCue(event) {
		var $span = $(this), 
			cue = cleanString($span.text()),
			$error,
			$dd = $span.parent();
		
		$dd.find(".edit-error").remove();
		
		if (!isTimeValid(cue)) {
			$error = createErrorSpan($.i18n._("please put in a time '00:00:00 (.000)'"));
			$dd.append($error);
		}
		else {
			setCue($dd, cue);
		}
	}
	
	function submitOnEnter(event) {
		//enter was pressed
		if (event.keyCode === 13) {
	        event.preventDefault();
			$(this).blur();
		}
	}
	
	//format of cue will be in hh:mm:ss.uuu
	function cueToSec(cue) {
		var c = cue.split(":"),
			time;
		
		time = parseFloat(c.pop());
		time = time + parseInt(c.pop(), 10)*60;
		time = time + parseInt(c.pop(), 10)*3600;
		
		return time;
	}
	
	function setCue($dd, cue) {
		var cueSec = cueToSec(cue);
		
		$dd.data("cue", cue)
			.data("cueSec", cueSec);
	}
	
	function getEntryDetails(entryId) {
		var id = entryId.split("_").pop(),
			$entry = $("#"+entryId),
			data = {};
		
		data["id"] = $entry.data("media-id");
		data["cuein"] = $entry.find("#spl_cue_in_"+id).data("cue");
		data["cueout"] = $entry.find("#spl_cue_out_"+id).data("cue");
		data["fadein"] = $entry.find("#spl_fade_in_"+id).data("fade");
		data["fadeout"] = $entry.find("#spl_fade_out_"+id).data("fade");
		
		return data;
	}
	
	function getCriteriaDetails() {
		
		var $andBlocks = $("#rule_criteria").find(".pl-criteria-and"),
			criteria = [],
			i, lenAnd,
			j, lenOr,
			$nodes,
			$row,
			$input,
			$extra,
			$unit;
		
		for (i = 0, lenAnd = $andBlocks.length; i < lenAnd; i++) {
			criteria[i] = [];
			$nodes = $($andBlocks[i]).children();
			
			for (j = 0, lenOr = $nodes.length; j < lenOr; j++) {
				$row = $($nodes[j]);
				$input = $row.find("input.sp_input_text");
				$extra = $row.find("input.sp_extra_input_text");
				$unit = $row.find("select.sp_rule_unit");
				
				criteria[i].push({
					"criteria": $row.find("select.rule_criteria").val(),
					"modifier": $row.find("select.rule_modifier").val(),
					"input1": $input ? $input.val() : null,
					"input2": $extra ? $extra.val() : null,
					"unit": $unit ? $unit.val() : null,
				});
			}
		}
		
		return criteria;
	}
	
	function serializePlaylist(order) {
		var i, len,
			info = {},
			entries = [];
		
		for (i = 0, len = order.length; i < len; i++) {
			entries.push(getEntryDetails(order[i]));
		}
		
		info["name"] = cleanString($("#playlist_name").text());
		info["description"] = cleanString($("#playlist_description").val());
		info["content"] = entries;
		
		info["rules"] = {
			"repeat-tracks": $("#pl_repeat_tracks").is(":checked"),
			"my-tracks": $("#pl_my_tracks").is(":checked"),
			"limit": {
				"value": $("#pl_limit_value").val(),
				"unit":  $("#pl_limit_options").val()
			},
			"criteria": getCriteriaDetails(),
			"order": {
				"column": $("#pl_order_column").val(),
				"direction": $("#pl_order_direction").val()
			}
		};
		
		return info;
	}
	
	function makeSortable() {
		var $contents = $("#spl_sortable");
		
		$contents.sortable({
			items: 'li',
			handle: 'div.list-item-container'
		});
	}
	
	mod.redrawPlaylist = function redrawPlaylist(data) {
		var $wrapper = $("div.wrapper"),
			$playlist = $("#side_playlist");
		
		$playlist.detach();
		
		$playlist.find("#playlist_lastmod").val(data.modified);
		$playlist.find("#playlist_length").text(data.length);
		$playlist.find("#spl_sortable").html(data.html).sortable("refresh");
		
		$wrapper.append($playlist);
	};
	
	mod.drawPlaylist = function drawPlaylist(data) {
		var $playlist = $("#side_playlist");
		
		$playlist
			.empty()
			.append(data.html);
		
		makeSortable();
	};
	
	function showCuesWaveform(e) {
		var $el = $(e.target),
			$li = $el.parents("li"), 
			$parent = $el.parent(),
			uri = $parent.data("uri"),
			$html = $($("#tmpl-pl-cues").html()),
			cueIn = $li.find('.spl_cue_in').data("cue"),
			cueOut = $li.find('.spl_cue_out').data("cue"),
			cueInSec = $li.find('.spl_cue_in').data("cueSec"),
			cueOutSec = $li.find('.spl_cue_out').data("cueSec"),
			tracks = [{
				src: uri,
				selected: {
					start: cueInSec,
					end: cueOutSec
				}
			}],
			dim = AIRTIME.utilities.findViewportDimensions(),
			playlistEditor;
		
		function removeDialog() {
			playlistEditor.stop();
			
        	$html.dialog("destroy");
        	$html.remove();
        }
		
		function saveDialog() {
        	var cueIn = $html.find('.editor-cue-in').html(),
        		cueOut = $html.find('.editor-cue-out').html(),
        		$ddIn = $li.find('.spl_cue_in'),
        		$ddOut = $li.find('.spl_cue_out');
        	
        	setCue($ddIn, cueIn);
        	$ddIn.find(".spl_text_input").text(cueIn);
        	setCue($ddOut, cueOut);
        	$ddOut.find(".spl_text_input").text(cueOut);
        	removeDialog();
        }
		
		$html.find('.editor-cue-in').html(cueIn);
		$html.find('.editor-cue-out').html(cueOut);
		
		$html.on("click", ".set-cue-in", function(e) {
			var cueIn = $html.find('.audio_start').val();
			
			$html.find('.editor-cue-in').html(cueIn);
		});
		
		$html.on("click", ".set-cue-out", function(e) {
			var cueOut = $html.find('.audio_end').val();
			
			$html.find('.editor-cue-out').html(cueOut);
		});
		
		$html.dialog({
            modal: true,
            title: $.i18n._("Cue Editor"),
            show: 'clip',
            hide: 'clip',
            width: dim.width - 100,
            height: 325,
            buttons: [
                {text: $.i18n._("Cancel"), class: "btn btn-small", click: removeDialog},
                {text: $.i18n._("Save"),  class: "btn btn-small btn-inverse", click: saveDialog}
            ],
            open: function (event, ui) {
            	
            	var config = new Config({
        			resolution: 15000,
        	        mono: true,
        	        timescale: true,
        	        waveHeight: 80,
        	        container: $html[0],
        	        UITheme: "jQueryUI",
        	        timeFormat: 'hh:mm:ss.uuu'
        	    });
        		
        		playlistEditor = new PlaylistEditor();
        	    playlistEditor.setConfig(config);
        	    playlistEditor.init(tracks);	
            },
            close: removeDialog,
            resizeStop: function(event, ui) {
            	playlistEditor.resize();
            }
        });	
	};
	
	mod.edit = function(id) {
		var url = baseUrl+"playlist/edit",
			data;
		
		data = {format: "json", id: id};
		
		$.post(url, data, function(json) {
			mod.drawPlaylist(json);
		});
	};
	
	mod.addItems = function(mediaIds) {
		
		var content = $.map(mediaIds, function(value, index) {
			return {"id": value};
		});
		
		var url = baseUrl+"playlist/add-items",
			data = {
				format: "json",
				content: content
			};
		
		$.post(url, data, function(json) {
			mod.redrawPlaylist(json);
		});
	};
	
	mod.onReady = function() {
		
		var $playlist = $("#side_playlist");
		
		setupCriteriaOptions();
		
		$playlist.on("click", "#spl_AND", function(e) {
			e.preventDefault();
			
			var $el = createCriteriaRow("", "");
			
			$div = $("<div class='pl-criteria-and'></div>");
			$div.append($el);
			
			$(this).before($div);
		});
		
		$playlist.on("click", ".pl-or-criteria", function(e) {
			e.preventDefault();
			
			var $el = createCriteriaRow("", "");
			
			$(this).parents("div.pl-criteria-row").after($el);
		});
		
		$playlist.on("change", ".rule_criteria", function(e) {
			var $select,
				$el;
			
			e.preventDefault();
			
			$select = $(this);
			$el = createCriteriaRow($select.val(), "");
			$select.parents("div.pl-criteria-row").replaceWith($el);	
		});
		
		$playlist.on("change", ".rule_modifier", function(e) {
			var $select,
				$modifier,
				$el;
			
			e.preventDefault();
			
			$modifier = $(this);
			$select = $modifier.parents("div.pl-criteria-row").find(".rule_criteria");
			
			$el = createCriteriaRow($select.val(), $modifier.val());
			$select.parents("div.pl-criteria-row").replaceWith($el);	
		});
		
		$playlist.on("click", ".btn-danger", function(e) {
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
		
		$playlist.on("change", "#pl_order_column", function(e) {
			var $orderCol,
				$orderDir;
			
			e.preventDefault();
			
			$orderCol = $(this);
			$orderDir = $("#pl_order_direction");
			
			if ($orderCol.val() === "") {
				$orderDir.hide();
			}
			else {
				$orderDir.show();
			}	
		});
		
		makeSortable();
		
		$playlist.on("click", "#lib-new-pl-static", function(e) {
    		var url = baseUrl+"playlist/new",
    			data = {format: "json", type: "0"};
    		
    		$.post(url, data, function(json) {
    			AIRTIME.playlist.drawPlaylist(json);
    		});
    	});
		
		$playlist.on("click", "#lib-new-pl-dynamic", function(e) {
    		var url = baseUrl+"playlist/new",
    			data = {format: "json", type: "1"};
    		
    		$.post(url, data, function(json) {
    			AIRTIME.playlist.drawPlaylist(json);
    		});
    	});
		
		$playlist.on("click", ".ui-icon-closethick", function(e) {
			var $li = $(this).parents("li");
				$li.remove();
		});
		
		$playlist.on("click", ".spl_cue", function(e) {
			var $li = $(this).parents("li");
				$li.find(".cue-edit").toggle();
		});
		
		$playlist.on("click", ".spl_fade", function(e) {
			var $li = $(this).parents("li");
				$li.find(".crossfade").toggle();
		});
		
		$playlist.on("click", "legend", function(e) {
			$(this).parents("fieldset").toggleClass("closed");
		});
		
		$playlist.on("click", ".pl-waveform-cues-btn", showCuesWaveform);
		
		$playlist.on("keydown", ".spl_soe", submitOnEnter);
		
		$playlist.on("blur", ".spl_fade_in span, .spl_fade_out span", changeFade);
		$playlist.on("blur", ".spl_cue_in span, .spl_cue_out span", changeCue);
		
		$playlist.on("click", "#spl_generate", function(e) {
			
			var url = baseUrl+"playlist/generate",
				data;
			
			data = {format: "json"};
			
			$.post(url, data, function(json) {
				mod.redrawPlaylist(json);
			});
		});
		
		$playlist.on("click", "#spl_shuffle", function(e) {
			
			var url = baseUrl+"playlist/shuffle",
				data;
			
			data = {format: "json"};
			
			$.post(url, data, function(json) {
				mod.redrawPlaylist(json);
			});
		});

		$playlist.on("click", "#spl_clear", function(e) {
			
			var url = baseUrl+"playlist/clear",
				data;
			
			data = {format: "json"};
			
			$.post(url, data, function(json) {
				mod.redrawPlaylist(json);
			});
		});
		
		$playlist.on("click", "#spl_save", function(e) {
			var $contents = $("#spl_sortable"),
				order = $contents.sortable("toArray"),
				url = baseUrl+"playlist/save",
				data,
				$errors = $playlist.find(".edit-error");
			
			//can't save playlist while errors are still existing.
			if ($errors.length > 0) {
				$errors.parents("div").show();
				return;
			}
			
			data = {format: "json", serialized: serializePlaylist(order)};
			
			$.post(url, data, function(json) {
				mod.redrawPlaylist(json);
			});
		});
		
		$playlist.on("click", "#spl_delete", function(e) {
			
			var url = baseUrl+"playlist/delete",
				data;
			
			data = {format: "json"};
			
			$.post(url, data, function(json) {
				mod.drawPlaylist(json);
			});
		});
	};
	
return AIRTIME;
	
}(AIRTIME || {}));

$(document).ready(AIRTIME.playlist.onReady);
