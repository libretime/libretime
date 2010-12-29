function getId() { 
	var tr_id =  $(this.triggerElement).attr("id");
	tr_id = tr_id.split("_");

	return tr_id[1];
}

function getType() { 
	var tr_id =  $(this.triggerElement).attr("id");
	tr_id = tr_id.split("_");

	return tr_id[0];
}

function setLibraryContents(data){
	$("#library_display tr:not(:first-child)").remove();
	$("#library_display").append(data);

	/*
	$('#library_display tr[id ^= "au"]')
		.contextMenu({menu: 'audioMenu'}, contextMenu)
		.draggable({ 
				helper: 'clone' 
		});

	$('#library_display tr[id ^= "pl"]')
		.contextMenu({menu: 'plMenu'}, contextMenu)
		.draggable({ 
				helper: 'clone' 
		});
	*/
}

function setUpLibrary() {

	$("#library_display tr:first-child span.title").data({'ob': 'dc:title', 'order' : 'asc'});
	$("#library_display tr:first-child span.artist").data({'ob': 'dc:creator', 'order' : 'desc'});
	$("#library_display tr:first-child span.album").data({'ob': 'dc:source', 'order' : 'asc'});
	$("#library_display tr:first-child span.track").data({'ob': 'ls:track_num', 'order' : 'asc'});
	$("#library_display tr:first-child span.length").data({'ob': 'dcterms:extent', 'order' : 'asc'});

	$("#library_display tr:first-child span").click(function(){
		var url = "/Library/contents/format/html",
			ob = $(this).data('ob'),
			order = $(this).data('order');

		//toggle order for next click.
		if(order === 'asc') {
			$(this).data('order', 'desc');
		} 
		else {
			$(this).data('order', 'asc');
		}

		$.post(url, {ob: ob, order: order}, setLibraryContents);
	});

	/*
	$('#library_display tr[id ^= "au"]')
		.contextMenu({menu: 'audioMenu'}, contextMenu)
		.draggable({ 
				helper: 'clone' 
		});

	$('#library_display tr[id ^= "pl"]')
		.contextMenu({menu: 'plMenu'}, contextMenu)
		
	*/

	$('#library_display tr:not(:first-child)')
		.draggable({ 
				helper: 'clone' 
		});

	$('#library_display tr:not(:first-child)')
		.jjmenu("rightClick", 
			[{get:"/Library/context-menu/format/json/id/#id#/type/#type#"}],  
			{id: getId, type: getType}, 
			{xposition: "mouse", yposition: "mouse"});

}
