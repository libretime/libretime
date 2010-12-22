//--------------------------------------------------------------------------------------------------------------------------------
//Side Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

function setSPLContent(json) {
	
	if(json.message) {  
		alert(json.message);
		return;		
	}

	$('input[name="all"]').attr("checked", false);

	$('#spl_name').empty()
		.append(json.name);
	$('#spl_length').empty()
		.append(json.length);		
	$('#spl_sortable').empty()
		.append(json.html);	
}

function addSPLItem(event, ui){
	
	var url, tr, id;

	tr = ui.helper; 	
    	
	if(tr.get(0).tagName === 'LI')
		return;
	
	id = tr.attr('id');

	url = '/Playlist/add-item/format/json';
	url = url + '/id/'+id;

	$.post(url, setSPLContent);
}

function deleteSPLItem(){

	var url, pos;

	url = '/Playlist/delete-item/format/json/view/spl';

	pos = $('form[name="SPL"]').find(':checked').not('input[name="all"]').map(function() {
		return "/pos/" + $(this).attr('name');
	}).get().join("");

	url = url + pos;

	$.post(url, setSPLContent);
}

function moveSPLItem(event, ui) {	
	var li, newPos, oldPos, url;
	
	li = ui.item;
	
    newPos = li.index();
    oldPos = li.attr('id').split("_").pop(); 

	url = '/Playlist/move-item'
	url = url + '/format/json';
	url = url + '/view/spl';
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

	url = '/Playlist/close/format/json/view/spl';

	$.post(url, noOpenPL);
}

function deleteSPL() {
	var url;

	url = '/Playlist/delete-active/format/json/view/spl';

	$.post(url, noOpenPL);
}

function setUpSPL() {

	$("#spl_sortable").sortable();
    $("#spl_sortable" ).bind( "sortstop", moveSPLItem);
	$("#spl_remove_selected").click(deleteSPLItem);
	$("#spl_close").click(closeSPL);
	$("#spl_delete").click(deleteSPL);

	$("#spl_sortable").droppable();
	$("#spl_sortable" ).bind( "drop", addSPLItem);

	$('input[name="all"]').click(function(){
		$('form[name="SPL"]').find('input').attr("checked", $(this).attr("checked"));
	});
}

