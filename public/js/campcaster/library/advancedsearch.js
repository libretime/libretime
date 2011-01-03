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

function setUpSearch() {

	$("#search_add").click(ajaxAddField);
	
	$('[id^="fieldset-row_"]').each(function(i, el){
		addRemove(el);
	});
}
