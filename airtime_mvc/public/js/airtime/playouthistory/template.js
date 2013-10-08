var AIRTIME = (function(AIRTIME) {
    var mod;
    var $historyTemplate;
    
    if (AIRTIME.template === undefined) {
        AIRTIME.template = {};
    }
    mod = AIRTIME.template;
    
    function createItemLi(id, name, configured) {
    	
    	var editUrl = baseUrl+"Playouthistorytemplate/configure-template/id/"+id;
    	var defaultUrl = baseUrl+"Playouthistorytemplate/set-template-default/format/json/id/"+id;
    	var removeUrl = baseUrl+"Playouthistorytemplate/delete-template/format/json/id/"+id;
    	
    	var itemConfigured = 
    		"<li class='template_configured' data-template='<%= id %>' data-name='<%= name %>'>" +
    		    "<a href='<%= editUrl %>' class='template_name'><%= name %></a>" +
    		    "<i class='icon icon-ok'></i>" +
    		"</li>";
    	
    	var item = 
    		"<li data-template='<%= id %>' data-name='<%= name %>'>" +
    			"<a href='<%= editUrl %>' class='template_name'><%= name %></a>" +
    			"<a href='<%= removeUrl %>' class='template_remove'><i class='icon icon-trash'></i></a>" +
    			"<a href='<%= defaultUrl %>' class='template_default'>" + $.i18n._('Set Default') + "</a>" +	
    		"</li>";
    	
    	var template = (configured) === true ? itemConfigured : item;
    	
    	var template = _.template(template);
    	
    	var $li = $(template({id: id, name: name, editUrl: editUrl, defaultUrl: defaultUrl, removeUrl: removeUrl}));
    	
    	return $li;
    }
    
    mod.onReady = function() {
    	
    	$historyTemplate = $("#history_template");
    	
    	$historyTemplate.on("click", ".template_remove", function(ev) {
    		
    		ev.preventDefault();
    		
    		var $a = $(this);
    		var url = $a.attr("href");
    		$a.parents("li").remove();
    		
    		$.post(url, function(){
    			var x;
    		});
    	});
    	
    	$historyTemplate.on("click", ".template_default", function(ev) {
    		
    		ev.preventDefault();
    		
    		var $a = $(this);
    		var url = $a.attr("href");
    		var $oldLi, $newLi;
    		
    		$oldLi = $a.parents("ul").find("li.template_configured");
    		$newLi = $a.parents("li");
    		
    		$oldLi.replaceWith(createItemLi($oldLi.data('template'), $oldLi.data('name'), false));
    		$newLi.replaceWith(createItemLi($newLi.data('template'), $newLi.data('name'), true));
    		
    		$.post(url, function(){
    			var x;
    		});
    	});
    	
    	function createTemplate(type) {
    		
    		var createUrl = baseUrl+"Playouthistorytemplate/create-template";
    		
    		$.post(createUrl, {format: "json", type: type}, function(json) {
    			
    			if (json.error !== undefined) {
    				alert(json.error);
    				return;
    			}
    			
    			window.location.href = json.url;
    		});
    	}
    	
    	$historyTemplate.on("click", "#new_item_template", function() {
    		createTemplate("item");
    	});
    	
    	$historyTemplate.on("click", "#new_file_template", function() {
    		createTemplate("file");
    	});
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(AIRTIME.template.onReady);