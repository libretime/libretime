//--------------------------------------------------------------------------------------------------------------------------------
//Side Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

function stopAudioPreview() {
	// stop any preview playing
	$('#jquery_jplayer_1').jPlayer('stop');
}

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
	cueIn = span.text().trim();
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
	cueOut = span.text().trim();
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
	fadeIn = span.text().trim();
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
	fadeOut = span.text().trim();
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

function highlightActive(el) {

	$(el).addClass("ui-state-active");
}

function unHighlightActive(el) {

	$(el).removeClass("ui-state-active");
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

function redrawDataTablePage() {
    var dt;
    dt = $("#library_display").dataTable();
    dt.fnStandingRedraw();
}

function setSPLContent(json) {
    if(json.playlist_error == true){
        alertPlaylistErrorAndReload();
    }
    
	if(json.message) {
		alert(json.message);
		return;
	}

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

	//redraw the library list
	redrawDataTablePage();

	return false;
}

function addSPLItem(event, ui){
	var url, tr, id, items, draggableOffset, elOffset, pos;

	tr = ui.helper;

	if(tr.get(0).tagName === 'LI')
		return;

	items = $(event.currentTarget).children();

	draggableOffset = ui.offset;

	$.each(items, function(i, val){
		elOffset = $(this).offset();

		if(elOffset.top > draggableOffset.top) {
			pos = $(this).attr('id').split("_").pop();
			return false;
		}
	});

	id = tr.attr('id').split("_").pop();

	url = '/Playlist/add-item';

	$.post(url, {format: "json", id: id, pos: pos}, setSPLContent);
}

function deleteSPLItem(event){
    event.stopPropagation(); 
    stopAudioPreview();

	var url, pos;

	pos = $(this).parent().parent().attr("id").split("_").pop();
	url = '/Playlist/delete-item';

	$.post(url, {format: "json", pos: pos}, setSPLContent);
}

function moveSPLItem(event, ui) {
	var li, newPos, oldPos, url;

	li = ui.item;

    newPos = li.index();
    oldPos = li.attr('id').split("_").pop();

	url = '/Playlist/move-item';

	$.post(url, {format: "json", oldPos: oldPos, newPos: newPos}, setSPLContent);
}

function noOpenPL(json) {
    if(json.playlist_error == true){
        alertPlaylistErrorAndReload();
    }
	$("#side_playlist")
		.empty()
		.append(json.html);

	$("#spl_new")
		.button()
		.click(newSPL);
}

function newSPL() {
	var url;

	stopAudioPreview();
	url = '/Playlist/new/format/json';

	$.post(url, function(json){
		openDiffSPL(json);
		
		//redraw the library list
		redrawDataTablePage();
	});
}

function deleteSPL() {
	var url;
	
	stopAudioPreview();
	
	url = '/Playlist/delete-active/format/json';

	$.post(url, function(json){
	    if(json.playlist_error == true){
            alertPlaylistErrorAndReload();
        }
		noOpenPL(json);
		//redraw the library list
		redrawDataTablePage();
	});
}

function openDiffSPL(json) {
	
    if(json.playlist_error == true){
        alertPlaylistErrorAndReload();
    }
	$("#side_playlist")
		.empty()
		.append(json.html);

	currentlyOpenedSplId = json.pl_id;
	setUpSPL();
}

function editName() {
    var nameElement = $(this);
    var playlistName = nameElement.text();

    $("#playlist_name_input")
        .removeClass('element_hidden')
        .val(playlistName)
        .blur(function(){
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
        })
        .keydown(submitOnEnter)
        .focus();
}

function setUpSPL() {

    $("#spl_sortable").sortable({
        handle: 'div.list-item-container'
    });
    $("#spl_sortable" ).bind( "sortstop", moveSPLItem);
	$("#spl_remove_selected").click(deleteSPLItem);
	$("#spl_new")
		.button()
		.click(newSPL);

    $("#spl_crossfade").click(function(){

        if($(this).hasClass("ui-state-active")) {
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

    $("#playlist_name_display").click(editName);
    $("#fieldset-metadate_change > legend").click(function(){
        var descriptionElement = $(this).parent();

        if(descriptionElement.hasClass("closed")) {
            descriptionElement.removeClass("closed");
        }
        else {
            descriptionElement.addClass("closed");
        }
    });

    $("#description_save").click(function(){
        var textarea = $("#fieldset-metadate_change textarea");
        var description = textarea.val();
        var url;
        url = '/Playlist/set-playlist-description';

        $.post(url, {format: "json", description: description}, function(json){
            if(json.playlist_error == true){
                alertPlaylistErrorAndReload();
            }else{
                textarea.val(json.playlistDescription);
            }
        });
    });

    $("#description_cancel").click(function(){
        var textarea = $("#fieldset-metadate_change textarea");
        var url;
        url = '/Playlist/set-playlist-description';

        $.post(url, {format: "json"}, function(json){
            if(json.playlist_error == true){
                alertPlaylistErrorAndReload();
            }else{
                textarea.val(json.playlistDescription);
            }
        });
    });

    $("#spl_fade_in_main span:first").blur(function(event){
        event.stopPropagation();

	    var url, fadeIn, span;

	    span = $(this);
	    url = "/Playlist/set-playlist-fades";
	    fadeIn = span.text().trim();

	    if(!isTimeValid(fadeIn)){
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

    $("#spl_fade_out_main span:first").blur(function(event){
        event.stopPropagation();

	    var url, fadeIn, span;

	    span = $(this);
	    url = "/Playlist/set-playlist-fades";
	    fadeOut = span.text().trim();

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
        .keydown(submitOnEnter);

    $("#crossfade_main > .ui-icon-closethick").click(function(){
        $("#spl_crossfade").removeClass("ui-state-active");
        $("#crossfade_main").hide();
    });

	$("#spl_delete")
		.button()
		.click(deleteSPL);
		

	$("#spl_sortable").droppable();
	$("#spl_sortable" ).bind( "drop", addSPLItem);
}

//sets events dynamically for playlist entries (each row in the playlist)
function setPlaylistEntryEvents(el) {
	
	$(el).delegate("#spl_sortable .ui-icon-closethick", 
    		{"click": deleteSPLItem});
	
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

// Alert the error and reload the page
// this function is used to resolve concurrency issue
function alertPlaylistErrorAndReload(){
    alert("The playlist doesn't exist anymore!");
    window.location.reload();
}

$(document).ready(function() {
	var currentlyOpenedSplId;
	var playlist = $("#side_playlist");
	
	setUpSPL(playlist);
	
	setPlaylistEntryEvents(playlist);
	setCueEvents(playlist);
	setFadeEvents(playlist);
});
