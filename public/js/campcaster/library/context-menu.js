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
