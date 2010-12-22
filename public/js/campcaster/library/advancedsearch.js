function addRemove(el) {
	var id, span;
	
	id = $(el).attr("id").split("_").pop();
	
	span = $('<span id="search_remove_'+id+'">Remove</span>').click(function(){
		$(this).parent().parent().remove();
	});

	$(el).find("dl input").after(span);
}

function ajaxAddField() {

	var id = $("#search_next_id").val();

	var url = '/Search/newfield';
	url = url + '/format/html';
	url = url + '/id/' + id;

	$.post(url, function(newElement) {
       
		var el = $(newElement);
		addRemove(el);
		
		$(".zend_form").append(el);
		$("#search_next_id").val(++id);
	});
}

function searchLibrary() {
	var url, data;

	url = '/Search/display/format/json';
	data = $("form").serializeArray();

	$.post(url, data, function(json){
		
		if(json.form) {
			$("#search")
				.empty()
				.append(json.form);
		}
		

		if(json.results) {
			$("#library_display tr:not(:first-child)").remove();
			$("#library_display tbody").append(json.results);

			$("#library_display tr:not(:first-child)")
				.contextMenu({menu: 'myMenu'}, contextMenu)
				.draggable({ 
						helper: 'clone' 
				});
		}

	});
}

function setUpSearch() {

	$("#search_add").click(ajaxAddField);
	$("#search_submit").click(searchLibrary);
	
	$('[id^="fieldset-row_"]').each(function(i, el){
		addRemove(el);
	});
}
