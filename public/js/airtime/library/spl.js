//--------------------------------------------------------------------------------------------------------------------------------
//Side Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

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

	$(".ui-icon-close").click(deleteSPLItem);
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

function deleteSPLItem(){
	var url, pos;

	pos = $(this).parent().attr("id").split("_").pop();

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
}

function closeSPL() {
	var url;

	url = '/Playlist/close/format/json';

	$.post(url, noOpenPL);
}

function newSPL() {

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
	$("#spl_close").click(closeSPL);
	$("#spl_delete").click(deleteSPL);
	$(".ui-icon-close").click(deleteSPLItem);

	$("#spl_sortable").droppable();
	$("#spl_sortable" ).bind( "drop", addSPLItem);

}

