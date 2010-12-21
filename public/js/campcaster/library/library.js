function contextMenu(action, el, pos) {
	var method = action.split('/').pop(), 
		url;
	
	if (method === 'delete') {
		url = action + '/format/json';
		url = url + '/id/' + $(el).attr('id');
		$.post(url, deleteItem);
	}
	else if (method === 'add-item') {
		url = action + '/format/json';
		url = url + '/id/' + $(el).attr('id');
		$.post(url, setSPLContent);
	}
}

function deleteItem(json){	
	var id;

	if(json.message) {  
		alert(j.message);	
		return;	
	}

	id = this.url.split('/').pop();
	$("#library_display tr#" +id).remove();
}

function setLibraryContents(data){
	$("#library_display tr:not(:first-child)").remove();
	$("#library_display").append(data);

	$("#library_display tr:not(:first-child)").contextMenu(
		{menu: 'myMenu'}, contextMenu
	);
}

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


$(document).ready(function() {

	$("#library_display tr:first-child span.title").data({'ob': 'dc:title', 'order' : 'asc'});
	$("#library_display tr:first-child span.artist").data({'ob': 'dc:creator', 'order' : 'desc'});
	$("#library_display tr:first-child span.album").data({'ob': 'dc:source', 'order' : 'asc'});
	$("#library_display tr:first-child span.track").data({'ob': 'ls:track_num', 'order' : 'asc'});
	$("#library_display tr:first-child span.length").data({'ob': 'dcterms:extent', 'order' : 'asc'});

	$("#library_display tr:first-child span").click(function(){
		var url = "/Library/contents/format/html",
			ob = $(this).data('ob'),
			order = $(this).data('order');

		//append orderby category to url.
		url = url + "/ob/" + ob;
		//append asc or desc order.
		url = url + "/order/" + order;

		//toggle order for next click.
		if(order === 'asc') {
			$(this).data('order', 'desc');
		} 
		else {
			$(this).data('order', 'asc');
		}

		$.post(url, setLibraryContents);
	});

	$("#library_display tr:not(:first-child)")
		.contextMenu({menu: 'myMenu'}, contextMenu)
		.draggable({ 
				helper: 'clone' 
		});

	$("#spl_sortable").sortable();
    $("#spl_sortable" ).bind( "sortstop", moveSPLItem);
	$("#spl_remove_selected").click(deleteSPLItem);

	$("#side_playlist").droppable();
	$("#side_playlist" ).bind( "drop", addSPLItem);

	$('input[name="all"]').click(function(){
		$('form[name="SPL"]').find('input').attr("checked", $(this).attr("checked"));
	});

});
