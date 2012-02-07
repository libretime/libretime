//--------------------------------------------------------------------------------------------------------------------------------
// Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

var AIRTIME = (function(AIRTIME){
	
	AIRTIME.playlist = {};
	var mod = AIRTIME.playlist;
	
	function isTimeValid(time) {
		var regExpr = new RegExp("^\\d{2}[:]\\d{2}[:]\\d{2}([.]\\d{1,6})?$");
		
		return regExpr.test(time);
	}
	
	function isFadeValid(fade) {
        var regExpr = new RegExp("^\\d{1}(\\d{1})?([.]\\d{1,6})?$");

        return regExpr.test(fade);
	}
	
	
	function stopAudioPreview() {
		// stop any preview playing
		$('#jquery_jplayer_1').jPlayer('stop');
	}
	
	function highlightActive(el) {

		$(el).addClass("ui-state-active");
	}

	function unHighlightActive(el) {

		$(el).removeClass("ui-state-active");
	}

	function showError(el, error) {
	    $(el).parent().next()
	        .empty()
	        .append(error)
	        .show();
	}

	function hideError(el) {
	     $(el).parent().next()
	        .empty()
	        .hide();
	}

	function changeCueIn(event) {
	    event.stopPropagation();

		var id, url, cueIn, li, unqid;

		span = $(this);
		id = span.parent().attr("id").split("_").pop();
		url = "/Playlist/set-cue";
		cueIn = $.trim(span.text());
		li = span.parent().parent().parent().parent();
		unqid = li.attr("unqid");

		if(!isTimeValid(cueIn)){
	        showError(span, "please put in a time '00:00:00 (.000000)'");
	        return;
		}

		$.post(url, {format: "json", cueIn: cueIn, id: id, type: event.type}, function(json){
		   
	        if(json.response !== undefined && json.response.error) {
	            showError(span, json.response.error);
				return;
			}
	        
	        setPlaylistContent(json);
	        
	        li = $('#side_playlist li[unqid='+unqid+']');
	        li.find(".cue-edit").toggle();
	    	highlightActive(li);
	    	highlightActive(li.find('.spl_cue'));
		});
	}

	function changeCueOut(event) {
	    event.stopPropagation();

		var id, url, cueOut, li, unqid;

		span = $(this);
		id = span.parent().attr("id").split("_").pop();
		url = "/Playlist/set-cue";
		cueOut = $.trim(span.text());
		li = span.parent().parent().parent().parent();
		unqid = li.attr("unqid");

		if(!isTimeValid(cueOut)){
	        showError(span, "please put in a time '00:00:00 (.000000)'");
			return;
		}

		$.post(url, {format: "json", cueOut: cueOut, id: id}, function(json){
		   
			if(json.response !== undefined && json.response.error) {
	            showError(span, json.response.error);
				return;
			}

			setPlaylistContent(json);
	        
	        li = $('#side_playlist li[unqid='+unqid+']');
	        li.find(".cue-edit").toggle();
	    	highlightActive(li);
	    	highlightActive(li.find('.spl_cue'));
		});
	}

	function changeFadeIn(event) {
	    event.stopPropagation();

		var id, url, fadeIn, li, unqid;

		span = $(this);
		id = span.parent().attr("id").split("_").pop();
		url = "/Playlist/set-fade";
		fadeIn = $.trim(span.text());
		li = span.parent().parent().parent().parent();
		unqid = li.attr("unqid");

		if(!isFadeValid(fadeIn)){
	        showError(span, "please put in a time in seconds '00 (.000000)'");
			return;
		}

		$.post(url, {format: "json", fadeIn: fadeIn, id: id}, function(json){
			
			if(json.response !== undefined && json.response.error) {
	            showError(span, json.response.error);
				return;
			}

			setPlaylistContent(json);
	        
	        li = $('#side_playlist li[unqid='+unqid+']');
	        li.find('.crossfade').toggle();
	        highlightActive(li.find('.spl_fade_control'));
		});
	}

	function changeFadeOut(event) {
	    event.stopPropagation();

		var id, url, fadeOut, li, unqid;

		span = $(this);
		id = span.parent().attr("id").split("_").pop();
		url = "/Playlist/set-fade";
		fadeOut = $.trim(span.text());
		li = span.parent().parent().parent().parent();
		unqid = li.attr("unqid");

		if(!isFadeValid(fadeOut)){
	        showError(span, "please put in a time in seconds '00 (.000000)'");
			return;
		}

		$.post(url, {format: "json", fadeOut: fadeOut, id: id}, function(json){
			if(json.response !== undefined && json.response.error) {
	            showError(span, json.response.error);
				return;
			}

			setPlaylistContent(json);
	        
	        li = $('#side_playlist li[unqid='+unqid+']');
	        li.find('.crossfade').toggle();
	        highlightActive(li.find('.spl_fade_control'));
		});
	}

	function submitOnEnter(event) {
		//enter was pressed
		if(event.keyCode === 13) {
	        event.preventDefault();
			$(this).blur();
		}
	}

	function openFadeEditor(event) {
		var pos, url, li;
		
		event.stopPropagation();  

	    li = $(this).parent().parent();
	    li.find(".crossfade").toggle();

		if($(this).hasClass("ui-state-active")) {
			unHighlightActive(this);
		}
		else {
			highlightActive(this);
		}
	}

	function openCueEditor(event) {
		var pos, url, li, icon;
		
		event.stopPropagation();

		icon = $(this);
		li = $(this).parent().parent().parent(); 
	    li.find(".cue-edit").toggle();

		if (li.hasClass("ui-state-active")) {
			unHighlightActive(li);
			unHighlightActive(icon);
		}
		else {
			highlightActive(li);
			highlightActive(icon);
		}
	}

	function editName() {
	    var nameElement = $(this);
	    var playlistName = nameElement.text();

	    $("#playlist_name_input")
	        .removeClass('element_hidden')
	        .val(playlistName)
	        .keydown(function(event){
	        	if(event.keyCode === 13) {
	                event.preventDefault();
	                var input = $(this);
	                var url;
	    	        url = '/Playlist/set-playlist-name';

	    	        $.post(url, {format: "json", name: input.val()}, function(json){
	    	            if(json.playlist_error == true){
	    	                alertPlaylistErrorAndReload();
	    	            }
	                    input.addClass('element_hidden');
	                    nameElement.text(json.playlistName);
	                    redrawLib();
	    	        });
	        	}
	        })
	        .focus();
	}
		
	function redrawLib() {
	    var dt = $("#library_display").dataTable(),
	    	oLibTT = TableTools.fnGetInstance('library_display');
	    
	    oLibTT.fnSelectNone();
	    dt.fnStandingRedraw();
	}
	
	function setPlaylistContent(json) {

		$('#spl_name > a')
			.empty()
			.append(json.name);
		$('#spl_length')
			.empty()
			.append(json.length);
	    $('#fieldset-metadate_change textarea')
	        .empty()
	        .val(json.description);
		$('#spl_sortable')
			.empty()
			.append(json.html);

		redrawLib();
	}
	
	function getId() {
		return parseInt($("#pl_id").val(), 10);
	}
	
	function getModified() {
		return parseInt($("#pl_lastMod").val(), 10);
	}
	
	function openPlaylist(json) {
		
		$("#side_playlist")
			.empty()
			.append(json.html);
		
		setUpPlaylist();
	}
	
	//sets events dynamically for playlist entries (each row in the playlist)
	function setPlaylistEntryEvents(el) {
		
		$(el).delegate("#spl_sortable .ui-icon-closethick", 
				{"click": function(ev){
					var id;
					id = parseInt($(this).attr("id").split("_").pop(), 10);
					AIRTIME.playlist.fnDeleteItems([id]);
				}});

		$(el).delegate(".spl_fade_control", 
	    		{"click": openFadeEditor});
		
		$(el).delegate(".spl_cue", 
				{"click": openCueEditor});

	}
	
	//sets events dynamically for the cue editor.
	function setCueEvents(el) {
	
	    $(el).delegate(".spl_cue_in span", 
	    		{"focusout": changeCueIn, 
	    		"keydown": submitOnEnter});
	    
	    $(el).delegate(".spl_cue_out span", 
	    		{"focusout": changeCueOut, 
	    		"keydown": submitOnEnter});
	}
	
	//sets events dynamically for the fade editor.
	function setFadeEvents(el) {
	
	    $(el).delegate(".spl_fade_in span", 
	    		{"focusout": changeFadeIn, 
	    		"keydown": submitOnEnter});
	    
	    $(el).delegate(".spl_fade_out span", 
	    		{"focusout": changeFadeOut, 
	    		"keydown": submitOnEnter});
	}
	
	function setUpPlaylist(playlist) {
		
		var playlist = $("#side_playlist"),
			sortableConf;

	    playlist.find("#spl_crossfade").on("click", function(){

	        if ($(this).hasClass("ui-state-active")) {
	            $(this).removeClass("ui-state-active");
	            playlist.find("#crossfade_main").hide();
	        }
	        else {
	            $(this).addClass("ui-state-active");

	            var url = '/Playlist/get-playlist-fades';

		        $.get(url, {format: "json"}, function(json){
		            if(json.playlist_error == true){
	                    alertPlaylistErrorAndReload();
	                }
		            playlist.find("#spl_fade_in_main").find("span")
	                    .empty()
	                    .append(json.fadeIn);
		            playlist.find("#spl_fade_out_main").find("span")
	                    .empty()
	                    .append(json.fadeOut);

		            playlist.find("#crossfade_main").show();
	            });
	        }
	    });

		playlist.find("#playlist_name_display").on("click", editName);
	    
		playlist.find("#fieldset-metadate_change > legend").on("click", function(){
	        var descriptionElement = $(this).parent();

	        if(descriptionElement.hasClass("closed")) {
	            descriptionElement.removeClass("closed");
	        }
	        else {
	            descriptionElement.addClass("closed");
	        }
	    });

		playlist.find("#description_save").on("click", function(){
	        var textarea = playlist.find("#fieldset-metadate_change textarea"),
	        	description = textarea.val(),
	        	url;
	        
	        url = '/Playlist/set-playlist-description';

	        $.post(url, {format: "json", description: description}, function(json){
	            if(json.playlist_error == true){
	                alertPlaylistErrorAndReload();
	            }
	            else{
	                textarea.val(json.playlistDescription);
	            }
	            
	            playlist.find("#fieldset-metadate_change").addClass("closed");
	            redrawLib();
	        });
	    });

		playlist.find("#description_cancel").on("click", function(){
	        var textarea = playlist.find("#fieldset-metadate_change textarea"),
	        	url;
	        
	        url = '/Playlist/set-playlist-description';

	        $.post(url, {format: "json"}, function(json){
	            if(json.playlist_error == true){
	                alertPlaylistErrorAndReload();
	            }
	            else{
	                textarea.val(json.playlistDescription);
	            }
	            
	            playlist.find("#fieldset-metadate_change").addClass("closed");
	        });
	    });

		playlist.find("#spl_fade_in_main span:first").on("blur", function(event){
	        event.stopPropagation();

		    var url, fadeIn, span;
		    span = $(this);
		    url = "/Playlist/set-playlist-fades";
		    fadeIn = $.trim(span.text());

		    if(!isFadeValid(fadeIn)){
	            showError(span, "please put in a time in seconds '00 (.000000)'");
			    return;
		    }

		    $.post(url, {format: "json", fadeIn: fadeIn}, function(json){
		        
	            hideError(span);
		    });
	    });

		playlist.find("#spl_fade_out_main span:last").on("blur", function(event){
	        event.stopPropagation();

		    var url, fadeOut, span;

		    span = $(this);
		    url = "/Playlist/set-playlist-fades";
		    fadeOut = $.trim(span.text());

		    if(!isFadeValid(fadeOut)){
	            showError(span, "please put in a time in seconds '00 (.000000)'");
			    return;
		    }

		    $.post(url, {format: "json", fadeOut: fadeOut}, function(json){
	            hideError(span);
		    });
	    });

		playlist.find("#spl_fade_in_main span:first, #spl_fade_out_main span:first")
	        .on("keydown", submitOnEnter);

		playlist.find("#crossfade_main > .ui-icon-closethick").on("click", function(){
			playlist.find("#spl_crossfade").removeClass("ui-state-active");
			playlist.find("#crossfade_main").hide();
	    });
		
		sortableConf = (function(){
			var origRow,
				fnReceive,
				fnUpdate;		
			
			fnReceive = function(event, ui) {
				origRow = ui.item;
			};
			
			fnUpdate = function(event, ui) {
				var prev,
					aItem = [],
					iAfter,
					sAddType;
				
				prev = ui.item.prev();
				if (prev.hasClass("spl_empty") || prev.length === 0) {
					iAfter = undefined;
					sAddType = 'before';
				}
				else {
					iAfter = parseInt(prev.attr("id").split("_").pop(), 10);
					sAddType = 'after';
				}
				
				//item was dragged in from library datatable
				if (origRow !== undefined) {
					aItem.push(origRow.data("aData").id);
					origRow = undefined;
					AIRTIME.playlist.fnAddItems(aItem, iAfter, sAddType);
				}
				//item was reordered.
				else {
					aItem.push(parseInt(ui.item.attr("id").split("_").pop(), 10));
					AIRTIME.playlist.fnMoveItems(aItem, iAfter);
				}
			};
			
			return {
				items: 'li',
				placeholder: "placeholder lib-placeholder ui-state-highlight",
				forcePlaceholderSize: true,
				handle: 'div.list-item-container',
				start: function(event, ui) {
					ui.placeholder.html("PLACE HOLDER")
						.width("99.5%")
						.height(56);
				},
				receive: fnReceive,
				update: fnUpdate
			};
		}());

	    playlist.find("#spl_sortable").sortable(sortableConf);
	}
	
	mod.fnNew = function() {
		var url;

		stopAudioPreview();
		url = '/Playlist/new';

		$.post(url, {format: "json"}, function(json){
			openPlaylist(json);
			redrawLib();
		});
	};
	
	mod.fnEdit = function(id) {
		var url;
		
		stopAudioPreview();	
		
		url = '/Playlist/edit';

		$.post(url, 
			{format: "json", id: id}, 
			function(json){
				openPlaylist(json);
				//redrawLib();
		});
	};
	
	mod.fnDelete = function(plid) {
		var url, id, lastMod;
		
		stopAudioPreview();	
		id = (plid === undefined) ? getId() : plid;
		lastMod = getModified(); 
		url = '/Playlist/delete';

		$.post(url, 
			{format: "json", ids: id, modified: lastMod}, 
			function(json){
				openPlaylist(json);
				redrawLib();
		});
	};
	
	mod.fnAddItems = function(aItems, iAfter, sAddType) {
		
		$.post("/playlist/add-items", 
			{format: "json", "ids": aItems, "afterItem": iAfter, "type": sAddType}, 
			function(json){
				setPlaylistContent(json);
			});
	};
	
	mod.fnMoveItems = function(aIds, iAfter) {
		
		$.post("/playlist/move-items", 
			{format: "json", "ids": aIds, "afterItem": iAfter}, 
			function(json){
				setPlaylistContent(json);
			});
	};
	
	mod.fnDeleteItems = function(aItems) {
		
		$.post("/playlist/delete-items", 
			{format: "json", "ids": aItems}, 
			function(json){
				setPlaylistContent(json);
			});
	};
	
	mod.init = function() {
		var playlist = $("#side_playlist");
		
		$(playlist).delegate("#spl_new", 
	    		{"click": AIRTIME.playlist.fnNew});

		$(playlist).delegate("#spl_delete", 
	    		{"click": AIRTIME.playlist.fnDelete});
		
		setPlaylistEntryEvents(playlist);
		setCueEvents(playlist);
		setFadeEvents(playlist);
		
		setUpPlaylist(playlist);
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));


$(document).ready(function() {
	AIRTIME.playlist.init();	
});
