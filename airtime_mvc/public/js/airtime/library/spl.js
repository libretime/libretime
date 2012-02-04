//--------------------------------------------------------------------------------------------------------------------------------
//Side Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

var AIRTIME = (function(AIRTIME){
	AIRTIME.playlist = {};
	var mod = AIRTIME.playlist;
	
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
		
	function redrawLib() {
	    var dt;
	    dt = $("#library_display").dataTable();
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
	}
	
	mod.fnNew = function() {
		var url;

		stopAudioPreview();
		url = '/Playlist/new';

		$.post(url, {format: "json"}, function(json){
			openPlaylist(json);
			redrawLib();
		});
	}
	
	mod.fnDelete = function() {
		var url, id, lastMod;
		
		stopAudioPreview();	
		id = getId();
		lastMod = getModified(); 
		url = '/Playlist/delete';

		$.post(url, 
			{format: "json", ids: id, modified: lastMod}, 
			function(json){
				openPlaylist(json);
				redrawLib();
		});
	}
	
	mod.fnAddItems = function(aItem, iAfter) {
		
		$.post("/playlist/add-items", 
			{format: "json", "ids": aItem, "afterItem": iAfter}, 
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
	
	return AIRTIME;
	
}(AIRTIME || {}));


function isTimeValid(time) {
	var regExpr = new RegExp("^\\d{2}[:]\\d{2}[:]\\d{2}([.]\\d{1,6})?$");

	 if (!regExpr.test(time)) {
    	return false;
    }

	return true;
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

	var pos, url, cueIn, li, unqid;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-cue";
	cueIn = $.trim(span.text());
	li = span.parent().parent().parent().parent();
	unqid = li.attr("unqid");

	if(!isTimeValid(cueIn)){
        showError(span, "please put in a time '00:00:00 (.000000)'");
        return;
	}

	$.post(url, {format: "json", cueIn: cueIn, pos: pos, type: event.type}, function(json){
	   
        if(json.response !== undefined && json.response.error) {
            showError(span, json.response.error);
			return;
		}
        
        setSPLContent(json);
        
        li = $('#side_playlist li[unqid='+unqid+']');
        li.find(".cue-edit").toggle();
    	highlightActive(li);
    	highlightActive(li.find('.spl_cue'));
	});
}

function changeCueOut(event) {
    event.stopPropagation();

	var pos, url, cueOut, li, unqid;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-cue";
	cueOut = $.trim(span.text());
	li = span.parent().parent().parent().parent();
	unqid = li.attr("unqid");

	if(!isTimeValid(cueOut)){
        showError(span, "please put in a time '00:00:00 (.000000)'");
		return;
	}

	$.post(url, {format: "json", cueOut: cueOut, pos: pos}, function(json){
	   
		if(json.response !== undefined && json.response.error) {
            showError(span, json.response.error);
			return;
		}

		setSPLContent(json);
        
        li = $('#side_playlist li[unqid='+unqid+']');
        li.find(".cue-edit").toggle();
    	highlightActive(li);
    	highlightActive(li.find('.spl_cue'));
	});
}

function changeFadeIn(event) {
    event.stopPropagation();

	var pos, url, fadeIn, li, unqid;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-fade";
	fadeIn = $.trim(span.text());
	li = span.parent().parent().parent().parent();
	unqid = li.attr("unqid");

	if(!isTimeValid(fadeIn)){
        showError(span, "please put in a time '00:00:00 (.000000)'");
		return;
	}

	$.post(url, {format: "json", fadeIn: fadeIn, pos: pos}, function(json){
		
		if(json.response !== undefined && json.response.error) {
            showError(span, json.response.error);
			return;
		}

		setSPLContent(json);
        
        li = $('#side_playlist li[unqid='+unqid+']');
        li.find('.crossfade').toggle();
        highlightActive(li.find('.spl_fade_control'));
	});
}

function changeFadeOut(event) {
    event.stopPropagation();

	var pos, url, fadeOut, li, unqid;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-fade";
	fadeOut = $.trim(span.text());
	li = span.parent().parent().parent().parent();
	unqid = li.attr("unqid");

	if(!isTimeValid(fadeOut)){
        showError(span, "please put in a time '00:00:00 (.000000)'");
		return;
	}

	$.post(url, {format: "json", fadeOut: fadeOut, pos: pos}, function(json){
		if(json.response !== undefined && json.response.error) {
            showError(span, json.response.error);
			return;
		}

		setSPLContent(json);
        
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
                    redrawDataTablePage();
    	        });
        	}
        })
        .focus();
}



$(document).ready(function() {
	var playlist = $("#side_playlist"),
		sortableConf;
	
	function setUpSPL() {
		
		/*
	    $("#spl_crossfade").on("click", function(){

	        if ($(this).hasClass("ui-state-active")) {
	            $(this).removeClass("ui-state-active");
	            $("#crossfade_main").hide();
	        }
	        else {
	            $(this).addClass("ui-state-active");

	            var url = '/Playlist/set-playlist-fades';

		        $.get(url, {format: "json"}, function(json){
		            if(json.playlist_error == true){
	                    alertPlaylistErrorAndReload();
	                }
	                $("#spl_fade_in_main").find("span")
	                    .empty()
	                    .append(json.fadeIn);
	                $("#spl_fade_out_main").find("span")
	                    .empty()
	                    .append(json.fadeOut);

	                $("#crossfade_main").show();
	            });
	        }
	    });

	    $("#playlist_name_display").on("click", editName);
	    
	    $("#fieldset-metadate_change > legend").on("click", function(){
	        var descriptionElement = $(this).parent();

	        if(descriptionElement.hasClass("closed")) {
	            descriptionElement.removeClass("closed");
	        }
	        else {
	            descriptionElement.addClass("closed");
	        }
	    });

	    $("#description_save").on("click", function(){
	        var textarea = $("#fieldset-metadate_change textarea"),
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
	            
	            $("#fieldset-metadate_change").addClass("closed");
	            
	            // update the "Last Modified" time for this playlist
	            redrawDataTablePage();
	        });
	    });

	    $("#description_cancel").on("click", function(){
	        var textarea = $("#fieldset-metadate_change textarea"),
	        	url;
	        
	        url = '/Playlist/set-playlist-description';

	        $.post(url, {format: "json"}, function(json){
	            if(json.playlist_error == true){
	                alertPlaylistErrorAndReload();
	            }
	            else{
	                textarea.val(json.playlistDescription);
	            }
	            
	            $("#fieldset-metadate_change").addClass("closed");
	        });
	    });

	    $("#spl_fade_in_main span:first").on("blur", function(event){
	        event.stopPropagation();

		    var url, fadeIn, span;

		    span = $(this);
		    url = "/Playlist/set-playlist-fades";
		    fadeIn = $.trim(span.text());

		    if (!isTimeValid(fadeIn)){
	            showError(span, "please put in a time '00:00:00 (.000000)'");
			    return;
		    }

		    $.post(url, {format: "json", fadeIn: fadeIn}, function(json){
		        if(json.playlist_error == true){
	                alertPlaylistErrorAndReload();
	            }
			    if(json.response.error) {
				    return;
			    }

	             hideError(span);
		    });
	    });

	    $("#spl_fade_out_main span:first").on("blur", function(event){
	        event.stopPropagation();

		    var url, fadeIn, span;

		    span = $(this);
		    url = "/Playlist/set-playlist-fades";
		    fadeOut = $.trim(span.text());

		    if(!isTimeValid(fadeOut)){
	            showError(span, "please put in a time '00:00:00 (.000000)'");
			    return;
		    }

		    $.post(url, {format: "json", fadeOut: fadeOut}, function(json){
		        if(json.playlist_error == true){
	                alertPlaylistErrorAndReload();
	            }
			    if(json.response.error) {
				    return;
			    }

	             hideError(span);
		    });
	    });

	    $("#spl_fade_in_main span:first, #spl_fade_out_main span:first")
	        .on("keydown", submitOnEnter);

	    $("#crossfade_main > .ui-icon-closethick").on("click", function(){
	        $("#spl_crossfade").removeClass("ui-state-active");
	        $("#crossfade_main").hide();
	    });
	    */

	}

	function setPlaylistButtonEvents(el) {
		
		$(el).delegate("#spl_new", 
	    		{"click": AIRTIME.playlist.fnNew});
	
		$(el).delegate("#spl_delete", 
	    		{"click": AIRTIME.playlist.fnDelete});
	}
	
	//sets events dynamically for playlist entries (each row in the playlist)
	function setPlaylistEntryEvents(el) {
		
		$(el).delegate("#spl_sortable .ui-icon-closethick", 
				{"click": function(ev){
					var id;
					id = parseInt($(this).attr("id").split("_").pop(), 10);
					AIRTIME.playlist.fnDeleteItems([id]);
				}});
		
		/*
		$(el).delegate(".spl_fade_control", 
	    		{"click": openFadeEditor});
		
		$(el).delegate(".spl_cue", 
				{"click": openCueEditor});
		*/
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


	
	setPlaylistButtonEvents(playlist);
	setPlaylistEntryEvents(playlist);
	//setCueEvents(playlist);
	//setFadeEvents(playlist);
	
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
				iAfter;
			
			prev = ui.item.prev();
			if (prev.hasClass("spl_empty") || prev.length === 0) {
				iAfter = undefined;
			}
			else {
				iAfter = parseInt(prev.attr("id").split("_").pop(), 10);
			}
			
			//item was dragged in from library datatable
			if (origRow !== undefined) {
				aItem.push(origRow.data("aData").id);
				origRow = undefined;
				AIRTIME.playlist.fnAddItems(aItem, iAfter);
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
					.height(56)
					.append('<div style="clear:both;"/>');
			},
			receive: fnReceive,
			update: fnUpdate
		};
	}());

    playlist.sortable(sortableConf);
});
