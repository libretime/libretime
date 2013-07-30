var AIRTIME = (function(AIRTIME) {
    var mod;
    var $templateDiv;
    var $templateList;
    var $fileMDList;
    
    if (AIRTIME.historyTemplate === undefined) {
        AIRTIME.historyTemplate = {};
    }
    mod = AIRTIME.historyTemplate;
    
    function createTemplateLi(name, type, filemd, required) {
    	
    	var templateRequired = 
    		"<li id='<%= id %>' data-name='<%= name %>' data-type='<%= type %>' data-filemd='<%= filemd %>'>" +
    			"<span><%= name %></span>" +
    			"<span><%= type %></span>" +
    		"</li>";
    	
    	var templateOptional = 
    		"<li id='<%= id %>' data-name='<%= name %>' data-type='<%= type %>' data-filemd='<%= filemd %>'>" +
    			"<span><%= name %></span>" +
    			"<span><%= type %></span>" +
    			"<span class='template_item_remove'>Remove</span>" +
    		"</li>";
    	
    	var template = (required) === true ? templateRequired : templateOptional;
    	
    	var template = _.template(template);
    	var count = $templateList.find("li").length;
    	var id = "field_"+count;
    	var $li = $(template({id: id, name: name, type: type, filemd: filemd}));
    	
    	return $li;
    }
    
    function addField(name, type, filemd, required) {
    	
    	$templateList.append(createTemplateLi(name, type, filemd, required));
    }
    
    function getFieldData($el) {
    	
    	return {
    		name: $el.data("name"),
    		type: $el.data("type"),
    		filemd: $el.data("filemd"),
    		id: $el.data("id")
    	};
    	
    }
    
    var fieldSortable = (function() {
    	
    	var $newLi;
    	
    	return {
			receive: function( event, ui ) {
				var name = $newLi.data("name");
				var type = $newLi.data("type");
				var $prev = $newLi.prev();
				
				$newLi.remove();
				
				var $li = createTemplateLi(name, type, true, false);
				
				if ($prev.length) {
					$prev.after($li);
				}
				else {
					$templateList.prepend($li);
				}
			},
			beforeStop: function( event, ui ) {
				$newLi = ui.item;
			}
    	};
	})();
    
    mod.onReady = function() {
    	
    	$templateDiv = $("#configure_item_template");
    	$templateList = $(".template_item_list");
    	$fileMDList = $(".template_file_md");
    	
    	
    	$fileMDList.find("li").draggable({
    		helper: function(event, ui) {
    			var $li = $(this);
    			var name = $li.data("name");
    			var type = $li.data("type");
    			
    			return createTemplateLi(name, type, true, false);
    			
    		},
    		connectToSortable: ".template_item_list"
    	});
    	
    	$templateList.sortable(fieldSortable);
    	
    	$templateDiv.on("click", ".template_item_remove", function() {
    		$(this).parents("li").remove();
    	});
    	
    	$templateDiv.on("click", ".template_item_add button", function() {
    		var $div = $(this).parents("div.template_item_add");
    		
    		var name = $div.find("input").val();
    		var type = $div.find("select").val();
    		
    		addField(name, type, false, false);
    	});
    	
    	function createUpdateTemplate(template_id, isDefault) {
    		var createUrl = baseUrl+"Playouthistory/create-template/format/json";
			var updateUrl = baseUrl+"Playouthistory/update-template/format/json";
			var url;
			var data = {};
			var $lis, $li;
			var i, len;
			var templateName;
			
			url = (isNaN(parseInt(template_id, 10))) ? createUrl : updateUrl;
			
			templateName = $("#template_name").val();
			$lis = $templateList.children();
			
			for (i = 0, len = $lis.length; i < len; i++) {
				$li = $($lis[i]);
				
				data[i] = getFieldData($li);
			}
			
			$.post(url, {'name': templateName, 'fields': data, 'setDefault': isDefault}, function(json) {
				var x;
			});
    	}
    	
    	$templateDiv.on("click", "#template_item_save", function(){
    		var template_id = $(this).data("template");
    		
    		createUpdateTemplate(template_id, false);
    	});
    	
    	$templateDiv.on("click", "#template_set_default", function(){
    		var template_id = $(this).data("template");
    		
    		if (isNaN(parseInt(template_id, 10))) {
    			
    			createUpdateTemplate(template_id, true);
    		}
    		else {
    			
    			var url = baseUrl+"Playouthistory/set-item-template-default/format/json";
    				
    			$.post(url, {id: template_id}, function(json) {
    				var x;
    			});
    		}
    	});
    	
    	$("#template_list").change(function(){
    		var template_id = $(this).find(":selected").val(),
    			url;
    		
    		if (!isNaN(parseInt(template_id, 10))) {
    			url = baseUrl+"Playouthistory/configure-item-template/id/"+template_id;
    		}
    		else {
    			url = baseUrl+"Playouthistory/configure-item-template";
    		}
    		
    		window.location.href = url;
    	});
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(AIRTIME.historyTemplate.onReady);