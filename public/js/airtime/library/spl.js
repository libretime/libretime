//--------------------------------------------------------------------------------------------------------------------------------
//Side Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

function isTimeValid(time) {
	var regExpr = new RegExp("^\\d{2}[:]\\d{2}[:]\\d{2}([.]\\d{1,6})?$");

	 if (!regExpr.test(time)) {
    	displayEditorError("please put in a time '00:00:00 (.000000)'");
    	return false;
    }

	return true;
}

function displayEditorError(error) {
	$("#spl_error")
		.empty()
		.append('<span class="ui-icon ui-icon-alert"></span>')
		.append(error)
		.show();
}

function clearEditorError() {
	$("#spl_error")
		.empty()
		.hide();
}

function cueSetUp(pos, json) {

	$("#spl_"+pos).find(".spl_playlength")
		.empty()
		.append(json.response.cliplength);

	$("#spl_length")
		.empty()
		.append(json.response.length);
}

function changeCueIn() {
	var pos, url, cueIn, div;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-cue/format/json";
	cueIn = span.text().trim();

	if(!isTimeValid(cueIn)){
		return;
	}

	$.post(url, {cueIn: cueIn, pos: pos}, function(json){
		if(json.response.error) {
			displayEditorError(json.response.error);
			return;
		}

		clearEditorError();

		span.empty()
			.append(json.response.cueIn);

		cueSetUp(pos, json);
	});
}

function changeCueOut() {
	var pos, url, cueOut, div;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-cue/format/json";
	cueOut = span.text().trim();

	if(!isTimeValid(cueOut)){
		return;
	}

	$.post(url, {cueOut: cueOut, pos: pos}, function(json){
		if(json.response.error) {
			displayEditorError(json.response.error);
			return;
		}

		clearEditorError();

		span.empty()
			.append(json.response.cueOut);

		cueSetUp(pos, json);
	});
}

function changeFadeIn() {
	var pos, url, fadeIn, div;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop();
	url = "/Playlist/set-fade/format/json";
	fadeIn = span.text().trim();

	if(!isTimeValid(fadeIn)){
		return;
	}

	$.post(url, {fadeIn: fadeIn, pos: pos}, function(json){
		if(json.response.error) {
			displayEditorError(json.response.error);
			return;
		}

		clearEditorError();

		span.empty()
			.append(json.response.fadeIn);

	});
}

function changeFadeOut() {
	var pos, url, fadeOut, div;

	span = $(this);
	pos = span.parent().attr("id").split("_").pop() - 1;
	url = "/Playlist/set-fade/format/json";
	fadeOut = span.text().trim();

	if(!isTimeValid(fadeOut)){
		return;
	}

	$.post(url, {fadeOut: fadeOut, pos: pos}, function(json){
		if(json.response.error) {
			displayEditorError(json.response.error);
			return;
		}

		clearEditorError();

		span.empty()
			.append(json.response.fadeOut);

	});
}

function submitOnEnter(event) {
	//enter was pressed
	if(event.keyCode === 13) {
		$(this).blur();
	}
}

function setEditorContent(json) {
	$("#spl_editor")
		.empty()
		.append(json.html);

	clearEditorError();

	$(".spl_cue_in span:last").blur(changeCueIn);
	$(".spl_cue_out span:last").blur(changeCueOut);
	$(".spl_fade_in span:last").blur(changeFadeIn);
	$(".spl_fade_out span:last").blur(changeFadeOut);

	$(".spl_cue_in span:last, .spl_cue_out span:last, .spl_fade_in span:last, .spl_fade_out span:last").keyup(submitOnEnter);
}

function highlightActive(el) {
	$("#spl_sortable")
		.find(".ui-state-active")
		.removeClass("ui-state-active");

	$(el).addClass("ui-state-active");
}

function openFadeEditor(event) {
	event.stopPropagation();

	$('li[id ^= "cues"]').hide();
	$('li[id ^= "crossfade"]').hide();

	if($(this).hasClass("ui-state-active")) {
		$(this).removeClass("ui-state-active");
		return;
	}

	var pos, url;
	
	pos = $(this).attr("id").split("_").pop();
	url = '/Playlist/set-fade';

	highlightActive(this);

	$.get(url, {format: "json", pos: pos}, function(json){

		$("#crossfade_"+(pos-1)+"-"+pos)
			.empty()
			.append(json.html)
			.show();
	});
}

function openCueEditor(event) {
	event.stopPropagation();

	var pos, url, li;

	$('li[id ^= "cues"]').hide();
	$('li[id ^= "crossfade"]').hide();

	li = $(this).parent().parent();

	if(li.hasClass("ui-state-active")) {
		li.removeClass("ui-state-active");
		return;
	}

	pos = li.attr("id").split("_").pop();
	url = '/Playlist/set-cue';

	highlightActive(li);

	$.get(url, {format: "json", pos: pos}, function(json){
		
		$("#cues_"+pos)
			.empty()
			.append(json.html)
			.show();
	});	
}

function setSPLContent(json) {
	
	if(json.message) {  
		alert(json.message);
		return;		
	}

	$('#spl_name').empty()
		.append(json.name);
	$('#spl_length').empty()
		.append(json.length);		
	$('#spl_sortable').empty()
		.append(json.html);	
	$("#spl_editor")
		.empty();

	$("#spl_sortable .ui-icon-closethick").click(deleteSPLItem);
	$(".spl_fade_control").click(openFadeEditor);
	$(".spl_playlength").click(openCueEditor);

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
	var url, pos;

	event.stopPropagation();

	pos = $(this).parent().attr("id").split("_").pop();

	url = '/Playlist/delete-item';

	$.post(url, {format: "json", pos: pos}, setSPLContent);
}

function moveSPLItem(event, ui) {	
	var li, newPos, oldPos, url;

	li = ui.item;
	
    newPos = li.index();
    oldPos = li.attr('id').split("_").pop(); 

	url = '/Playlist/move-item'
	url = url + '/format/json';
	url = url + '/oldPos/' + oldPos;
	url = url + '/newPos/' + newPos;

	$.post(url, setSPLContent);
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

	url = '/Playlist/close/format/json';

	$.post(url, noOpenPL);
}

function newSPL() {
	var url;

	url = '/Playlist/new/format/json';

	$.post(url, function(json){
		var submit;

		submit = $('<button>Submit</button>')
			.button()
			.click(function(){
				var url, data;

				url = '/Playlist/metadata/format/json';
				data = $("#side_playlist form").serialize(); 

				$.post(url, data, function(json){
					if(json.form){

					}

					openDiffSPL(json);
				})
			});

		$("#side_playlist")
			.empty()
			.append(json.form)
			.append(submit);
	});
}

function deleteSPL() {
	var url;

	url = '/Playlist/delete-active/format/json';

	$.post(url, noOpenPL);
}

function openDiffSPL(json) {
	
	$("#side_playlist")
		.empty()
		.append(json.html);

		setUpSPL();
}

function setUpSPL() {
    
    $("#spl_sortable").sortable();
    $("#spl_sortable" ).bind( "sortstop", moveSPLItem);
	$("#spl_remove_selected").click(deleteSPLItem);
	$("#spl_new")
		.button()
		.click(newSPL);

	$("#spl_close")
		.button()
		.click(closeSPL);

	$("#spl_delete")
		.button()
		.click(deleteSPL);

	$("#spl_sortable .ui-icon-closethick").click(deleteSPLItem);
	$(".spl_fade_control").click(openFadeEditor);
	$(".spl_playlength").click(openCueEditor);

	$("#spl_sortable").droppable();
	$("#spl_sortable" ).bind( "drop", addSPLItem);
}

$(document).ready(function() {
	setUpSPL();
});
