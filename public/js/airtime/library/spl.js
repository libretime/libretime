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

function setCueEvents() {
    
    $(".spl_cue_in span:last").blur(changeCueIn);
	$(".spl_cue_out span:last").blur(changeCueOut);

    $(".spl_cue_in span:first, .spl_cue_out span:first")
        .keydown(submitOnEnter);
}

function setFadeEvents() {

    $(".spl_fade_in span:first").blur(changeFadeIn);
	$(".spl_fade_out span:first").blur(changeFadeOut);

    $(".spl_fade_in span:first, .spl_fade_out span:first")
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

        setFadeEvents();
	});
}

function openCueEditor(event) {
	event.stopPropagation();

	var pos, url, li;

	li = $(this).parent().parent().parent();
    pos = li.attr("id").split("_").pop();

	if(li.hasClass("ui-state-active")) {
		li.removeClass("ui-state-active");

        $("#cues_"+pos)
			.empty()
			.hide();

		return;
	}

	url = '/Playlist/set-cue';

	highlightActive(li);

	$.get(url, {format: "json", pos: pos}, function(json){
		
		$("#cues_"+pos)
			.empty()
			.append(json.html)
			.show();

        setCueEvents();
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

    $("#spl_main_crossfade")
		.button({
            icons: {
                primary: "crossfade-main-icon"
            },
            text: false
        });

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
