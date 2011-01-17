//--------------------------------------------------------------------------------------------------------------------------------
//Side Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

function setEditorContent(json) {
	$("#spl_editor")
		.empty()
		.append(json.html);
}

function highlightActive(el) {
	$("#spl_sortable")
		.find(".ui-state-active")
		.removeClass("ui-state-active");

	$(el).addClass("ui-state-active");
}

function openFadeEditor(event) {
	event.stopPropagation();

	var pos, url;
	
	pos = $(this).attr("id").split("_").pop();
	url = '/Playlist/set-fade/format/json';
	url = url + '/pos/' + pos;

	highlightActive(this);

	$.get(url, setEditorContent);
}

function openCueEditor(event) {
	event.stopPropagation();

	var pos, url, li;
	
	li = $(this).parent().parent();
	pos = li.attr("id").split("_").pop();
	url = '/Playlist/set-cue/format/json';
	url = url + '/pos/' + pos;

	highlightActive(li);

	$.get(url, setEditorContent);	
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

	$(".ui-icon-close").click(deleteSPLItem);
	$(".spl_fade_control").click(openFadeEditor);
	$(".spl_playlength").click(openCueEditor);

	return false;
}

function addSPLItem(event, ui){
	
	var url, tr, id;

	tr = ui.helper; 	
    	
	if(tr.get(0).tagName === 'LI')
		return;
	
	id = tr.attr('id').split("_").pop();

	url = '/Playlist/add-item/format/json';
	url = url + '/id/'+id;

	$.post(url, setSPLContent);
}

function deleteSPLItem(event){
	var url, pos;

	event.stopPropagation();

	pos = $(this).parent().parent().attr("id").split("_").pop();

	url = '/Playlist/delete-item/format/json';
	url = url + '/pos/' + pos;

	$.post(url, setSPLContent);
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

	$(".ui-icon-close").click(deleteSPLItem);
	$(".spl_fade_control").click(openFadeEditor);
	$(".spl_playlength").click(openCueEditor);

	$("#spl_sortable").droppable();
	$("#spl_sortable" ).bind( "drop", addSPLItem);

}

