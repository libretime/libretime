function addRemove(el) {
	var id, span;
	
	id = $(el).attr("id").split("_").pop();
	
	span = $('<a href="#" id="search_remove_'+id+'" class="ui-button ui-button-icon-only  ui-widget ui-state-default"><span class="ui-icon ui-icon-closethick"></span><span class="ui-button-text">Remove</span></a>').click(function(){
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
        addRemove(newRow);
				
		$("#fieldset-group_"+group_id+" dl:first").append(newRow);
	});
}

function removeGroup() {
   $(this).parent().parent().remove();
}

function ajaxAddGroup() {

	var url = '/Search/newgroup/format/json';

	$.post(url, function(json) {

        var group = $(json.html);
        addRemove(group);
		group.find('[id$="search_add_row"]').click(ajaxAddRow);
        group.find('[id$="search_remove_group"]').click(removeGroup);
		$(".zend_form").append(group);
	});
}

function advancedSearchSubmit() {
    var data = $("#advancedSearch form").serializeArray();
   
    $.post("/Search/index", {format: "json", data: data}, function(json){
        var x;
    });
}

$(document).ready(function() {

	$("#search_add_group").click(ajaxAddGroup);
    $("#search_submit").click(advancedSearchSubmit);
	
	$('[id$="search_add_row"]').click(ajaxAddRow);
    $('[id$="search_remove_group"]').click(removeGroup);
	
	$('[id^="fieldset-row_"]').each(function(i, el){
		addRemove(el);
	});
});
