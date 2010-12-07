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
		$.post(url, addToPlaylist);
	}
}

function deleteItem(json){	
	var j = jQuery.parseJSON(json),
		id;

	if(j.error !== undefined) {  
		alert(j.error.message);	
		return;	
	}

	id = this.url.split('/').pop();
	$("#library_display tr#" +id).remove();
}

function addToPlaylist(json){
	var j = jQuery.parseJSON(json);

	if(j.error !== undefined) {  
		alert(j.error.message);
		return;		
	}	
}

function setLibraryContents(data){
	$("#library_display tr:not(:first-child)").remove();
	$("#library_display").append(data);

	$("#library_display tr:not(:first-child)").contextMenu(
		{menu: 'myMenu'}, contextMenu
	);
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

	$("#library_display tr:not(:first-child)").contextMenu(
		{menu: 'myMenu'}, contextMenu
	);

});
