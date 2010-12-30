/*
function contextMenu(action, el, pos) {
	var method = action.split('/').pop(), 
		url, tr_id, id;

	tr_id = $(el).attr('id');
	id = tr_id.split("_").pop();
	url = '/'+action;
	
	if (method === 'delete') {
		url = url + '/format/json';
		url = url + '/id/' + id;
		$.post(url, function(json) {

			if(json.message) {  
				alert(json.message);	
				return;
			}

			$("#library_display tr#" +tr_id).remove();
		});
	}
	else if (method === 'add-item') {
		url = url + '/format/json';
		url = url + '/id/' + id;
		$.post(url, setSPLContent);
	}
}
*/

function contextMenu() {
	alert("callback");
}
