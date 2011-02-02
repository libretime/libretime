function addRemove(el) {
	var id, span;
	
	id = $(el).attr("id").split("_").pop();
	
	span = $('<span id="search_remove_'+id+'">Remove</span>').click(function(){
		$(this).parent().parent().remove();
	});

	$(el).find("dl input").after(span);
}

function ajaxAddRow() {
	var group_id;

	group_id = $(this).parent().parent().attr("id").split("_").pop();

	var url = '/Search/newfield/format/json';

	$.post(url, {group: group_id}, function(json) {

		var newRow = $(json.html).find("#fieldset-row_"+json.row);
				
		$("#fieldset-group_"+group_id+" dl:first").append(newRow);
	});
}

function ajaxAddGroup() {

	var url = '/Search/newgroup/format/json';

	$.post(url, function(json) {
		
		$(".zend_form").append(json.html);
		$('[id$="search_add_row"]').click(ajaxAddRow);
	});
}

$(document).ready(function() {

	$("#search_add_group").click(ajaxAddGroup);
	
	$('[id$="search_add_row"]').click(ajaxAddRow);
	
	$('[id^="fieldset-row_"]').each(function(i, el){
		addRemove(el);
	});
});
