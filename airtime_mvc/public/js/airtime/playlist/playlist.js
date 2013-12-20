var AIRTIME = (function(AIRTIME){
	
	if (AIRTIME.playlist === undefined) {
		AIRTIME.playlist = {};
    }
	
	var mod = AIRTIME.playlist;
	
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
	
	function serializePlaylist(order) {
		var i, len,
			info = {},
			entries = [];
		
		for (i = 0, len = order.length; i < len; i++) {
			entries.push(getEntryDetails(order[i]));
		}
		
		info["name"] = cleanString($("#playlist_name").text());
		info["description"] = cleanString($("#playlist_description").val());
		info["contents"] = entries;
		
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
	
	mod.addItems = function(mediaIds) {
		
		var url = baseUrl+"playlist/add-items",
			data = {
				format: "json",
				media: mediaIds
			};
		
		$.post(url, data, function(json) {
			
		});
	};
	
	mod.onReady = function() {
		
		var $playlist = $("#side_playlist");
		
		makeSortable();
		
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
