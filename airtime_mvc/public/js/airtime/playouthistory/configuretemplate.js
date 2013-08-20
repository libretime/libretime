var AIRTIME = (function(AIRTIME) {
    var mod;
    var $templateDiv;
    var $templateList;
    var $fileMDList;
    
    if (AIRTIME.itemTemplate === undefined) {
        AIRTIME.itemTemplate = {};
    }
    mod = AIRTIME.itemTemplate;
    
    //config: name, type, filemd, required
    function createTemplateLi(config) {
    	
    	var templateRequired = 
    		"<li " +
    		  "data-name='<%= name %>' " +
    		  "data-type='<%= type %>' " +
    		  "data-filemd='<%= filemd %>'" +
    		  "data-label='<%= label %>'" +
    		 ">" +
    			"<span><%= label %></span>" +
    			"<span><%= type %></span>" +
    		"</li>";
    	
    	var templateOptional = 
    		"<li " +
    		  "data-name='<%= name %>' " +
    		  "data-type='<%= type %>' " +
    		  "data-filemd='<%= filemd %>'" +
    		  "data-label='<%= label %>'" +
    		">" +
    			"<span><%= label %></span>" +
    			"<span><%= type %></span>" +
    			"<span class='template_item_remove'><i class='icon icon-trash'></i></span>" +
    		"</li>";
    	
    	var template = (config.required) === true ? templateRequired : templateOptional;
    
    	template = _.template(template);
    	var $li = $(template(config));
    	
    	return $li;
    }
    
    function addField(config) {
    	
    	$templateList.append(createTemplateLi(config));
    }
    
    function getFieldData($el) {
    	
    	return {
    		name: $el.data("name"),
    		type: $el.data("type"),
    		label: $el.data("label"),
    		isFileMd: $el.data("filemd")
    	};
    	
    }
    
    mod.onReady = function() {
    	
    	$templateDiv = $("#configure_item_template");
    	$templateList = $(".template_item_list");
    	$fileMDList = $(".template_file_md");
    	
    	$fileMDList.on("click", "i.icon-plus", function(){
    		
    		var $li = $(this).parents("li");
			var config = {
				name: $li.data("name"), 
				type: $li.data("type"),
				label: $li.data("label"),
				filemd: true, 
				required: false
    		};
			
			 addField(config);
    	});
    	
    	$templateList.sortable();
    	
    	$templateDiv.on("click", ".template_item_remove", function() {
    		$(this).parents("li").remove();
    	});
    	
    	$templateDiv.on("click", ".template_item_add button", function() {
    		var $div = $(this).parents("div.template_item_add"),
    			label = $div.find("input").val(),
    			name;
    		
    		name = label.toLowerCase().replace(/[^a-z0-9]+/g, "");
    		
    		var config = {
				name: name,
				label: label,
				type: $div.find("select").val(), 
				filemd: false, 
				required: false
    		};
    		
    		addField(config);
    	});
    	
    	function updateTemplate(template_id, isDefault) {
			var url = baseUrl+"Playouthistory/update-template/format/json";
			var data = {};
			var $lis, $li;
			var i, len;
			var templateName;
			
			templateName = $("#template_name").val();
			$lis = $templateList.children();
			
			for (i = 0, len = $lis.length; i < len; i++) {
				$li = $($lis[i]);
				
				data[i] = getFieldData($li);
			}
			
			$.post(url, {'id': template_id, 'name': templateName, 'fields': data, 'setDefault': isDefault}, function(json) {
				var x;
			});
    	}
    	
    	$templateDiv.on("click", "#template_item_save", function(){
    		var template_id = $(this).data("template");
    		
    		updateTemplate(template_id, false);
    	});
    	
    	$templateDiv.on("click", "#template_set_default", function(){
    		var template_id = $(this).data("template");	
			var url = baseUrl+"Playouthistory/set-template-default/format/json";
				
			$.post(url, {id: template_id}, function(json) {
				var x;
			});
    	});
    	
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(AIRTIME.itemTemplate.onReady);