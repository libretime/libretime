//--------------------------------------------------------------------------------------------------------------------------------
//Side Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

function isTimeValid(time) {
	var regExpr = new RegExp("^\\d{2}[:]\\d{2}[:]\\d{2}([.]\\d{1,6})?$");

	 if (!regExpr.test(time)) {
    	return false;
    }

	return true;
}

function changeClipLength(pos, json) {

	$("#spl_"+pos).find(".spl_playlength")
		.empty()
		.append(json.response.cliplength);

	$("#spl_length")
		.empty()
		.append(json.response.length);
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

	var pos, url, cueIn, div;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-cue";
	cueIn = span.text().trim();

	if(!isTimeValid(cueIn)){
        showError(span, "please put in a time '00:00:00 (.000000)'");
        return;
	}

	$.post(url, {format: "json", cueIn: cueIn, pos: pos}, function(json){

        if(json.response.error) {
            showError(span, json.response.error);
			return;
		}

        changeClipLength(pos, json);
        hideError(span);
	});
}

function changeCueOut(event) {
    event.stopPropagation();

	var pos, url, cueOut, div;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-cue";
	cueOut = span.text().trim();

	if(!isTimeValid(cueOut)){
        showError(span, "please put in a time '00:00:00 (.000000)'");
		return;
	}

	$.post(url, {format: "json", cueOut: cueOut, pos: pos}, function(json){

		if(json.response.error) {
            showError(span, json.response.error);
			return;
		}

        changeClipLength(pos, json);
        hideError(span);
	});
}

function changeFadeIn(event) {
    event.stopPropagation();

	var pos, url, fadeIn, div;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-fade";
	fadeIn = span.text().trim();

	if(!isTimeValid(fadeIn)){
        showError(span, "please put in a time '00:00:00 (.000000)'");
		return;
	}

	$.post(url, {format: "json", fadeIn: fadeIn, pos: pos}, function(json){
		if(json.response.error) {
			return;
		}

         hideError(span);
	});
}

function changeFadeOut(event) {
    event.stopPropagation();

	var pos, url, fadeOut, div;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-fade";
	fadeOut = span.text().trim();

	if(!isTimeValid(fadeOut)){
        showError(span, "please put in a time '00:00:00 (.000000)'");
		return;
	}

	$.post(url, {format: "json", fadeOut: fadeOut, pos: pos}, function(json){
		if(json.response.error) {
			return;
		}

         hideError(span);
	});
}

function submitOnEnter(event) {
	//enter was pressed
	if(event.keyCode === 13) {
        event.preventDefault();
		$(this).blur();
	}
}

function setCueEvents(el) {

    $(el).find(".spl_cue_in span:last").blur(changeCueIn);
	$(el).find(".spl_cue_out span:last").blur(changeCueOut);

    $(el).find(".spl_cue_in span:first, .spl_cue_out span:first")
        .keydown(submitOnEnter);
}

function setFadeEvents(el) {

    $(el).find(".spl_fade_in span:first").blur(changeFadeIn);
	$(el).find(".spl_fade_out span:first").blur(changeFadeOut);

    $(el).find(".spl_fade_in span:first, .spl_fade_out span:first")
        .keydown(submitOnEnter);
}

function highlightActive(el) {

	$(el).addClass("ui-state-active");
}

function openFadeEditor(event) {
	event.stopPropagation();

    var pos, url, li;

    li = $(this).parent().parent();
	pos = parseInt(li.attr("id").split("_").pop());

	if($(this).hasClass("ui-state-active")) {
		$(this).removeClass("ui-state-active");

        $("#crossfade_"+pos+"-"+(pos+1))
			.empty()
			.hide();

		return;
	}

	url = '/Playlist/set-fade';

	highlightActive(this);

	$.get(url, {format: "json", pos: pos}, function(json){

		$("#crossfade_"+(pos)+"-"+(pos+1))
			.empty()
			.append(json.html)
			.show();

        setFadeEvents(li);
	});
}

function openCueEditor(event) {
	event.stopPropagation();

	var pos, url, li, icon;

	li = $(this).parent().parent().parent();
    icon = $(this);
    pos = li.attr("id").split("_").pop();

	if(li.hasClass("ui-state-active")) {
		li.removeClass("ui-state-active");
        icon.attr("class", "spl_cue ui-state-default");

        $("#cues_"+pos)
			.empty()
			.hide();

		return;
	}

    icon.attr("class", "spl_cue ui-state-default ui-state-active");
	url = '/Playlist/set-cue';

	highlightActive(li);

	$.get(url, {format: "json", pos: pos}, function(json){

		$("#cues_"+pos)
			.empty()
			.append(json.html)
			.show();

        setCueEvents(li);
	});
}

function redrawDataTablePage() {
    var dt;
    dt = $("#library_display").dataTable();
    dt.fnStandingRedraw();
}

function setSPLContent(json) {

	if(json.message) {
		alert(json.message);
		return;
	}

	$('#spl_name > a').empty()
		.append(json.name);
	$('#spl_length').empty()
		.append(json.length);
    $('#fieldset-metadate_change textarea')
        .empty()
        .val(json.description);
	$('#spl_sortable').empty()
		.append(json.html);
	$("#spl_editor")
		.empty();

	$("#spl_sortable .ui-icon-closethick").click(deleteSPLItem);
	$(".spl_fade_control").click(openFadeEditor);
	$(".spl_cue").click(openCueEditor);

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
	$("#side_playlist")
		.empty()
		.append(json.html);

	$("#spl_new")
		.button()
		.click(newSPL);
}

function closeSPL() {
	var url;
	
	// stop playing any preview
	$('#jquery_jplayer_1').jPlayer('stop');

	url = '/Playlist/close/format/json';

	$.post(url, noOpenPL);
}

function createPlaylistMetaForm(json) {
    var submit, form;

    form = $(json.form);
    form.find("fieldset").addClass("simple-formblock metadata");

    form.find("input, textarea")
        .keydown(function(event){
            //enter was pressed
            if(event.keyCode === 13) {
                event.preventDefault();
	            $("#new_playlist_submit").click();
            }
        })

    form.find("#new_playlist_submit")
		.button()
		.click(function(event){
            event.preventDefault();

			var url, data;

			url = '/Playlist/metadata/format/json';
			data = $("#side_playlist form").serialize();

			$.post(url, data, function(json){
				openDiffSPL(json);
				//redraw the library list
				redrawDataTablePage();
			})

		});

	$("#side_playlist")
		.empty()
		.append(form);

	currentlyOpenedSplId = json.pl_id;
}

function newSPL() {
	var url;

	// stop any preview playing
	$('#jquery_jplayer_1').jPlayer('stop');
	
	url = '/Playlist/new/format/json';

	$.post(url, createPlaylistMetaForm);
}

function deleteSPL() {
	var url;

	// stop any preview playing
	$('#jquery_jplayer_1').jPlayer('stop');
	
	url = '/Playlist/delete-active/format/json';

	$.post(url, function(){
		noOpenPL;
		//redraw the library list
		redrawDataTablePage();
	});
}

function openDiffSPL(json) {

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

	$("#spl_close")
		.button()
		.click(closeSPL);

    $("#spl_crossfade").click(function(){

        if($(this).hasClass("ui-state-active")) {
            $(this).removeClass("ui-state-active");
            $("#crossfade_main").hide();
        }
        else {
            $(this).addClass("ui-state-active");

            var url = '/Playlist/set-playlist-fades';

	        $.get(url, {format: "json"}, function(json){
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
            textarea.val(json.playlistDescription);
        });
    });

    $("#description_cancel").click(function(){
        var textarea = $("#fieldset-metadate_change textarea");
        var url;
        url = '/Playlist/set-playlist-description';

        $.post(url, {format: "json"}, function(json){
            textarea.val(json.playlistDescription);
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

	$("#spl_sortable .ui-icon-closethick").click(deleteSPLItem);
	$(".spl_fade_control").click(openFadeEditor);
	$(".spl_cue").click(openCueEditor);

	$("#spl_sortable").droppable();
	$("#spl_sortable" ).bind( "drop", addSPLItem);
}

$(document).ready(function() {
	var currentlyOpenedSplId;
	setUpSPL();
});
