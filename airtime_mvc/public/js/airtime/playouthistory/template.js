var AIRTIME = (function(AIRTIME) {
    var mod;
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
    	
    	$templateList = $(".template_item_list");
    	$fileMDList = $(".template_file_md");
    	
    	
    	$fileMDList.find("li").draggable({
    		//helper: "clone",
    		helper: function(event, ui) {
    			var $li = $(this);
    			var name = $li.data("name");
    			var type = $li.data("type");
    			
    			return createTemplateLi(name, type, true, false);
    			
    		},
    		connectToSortable: ".template_item_list"
    	});
    	
    	$templateList.sortable(fieldSortable);
    	
    	$templateList.on("click", ".template_item_remove", function() {
    		$(this).parents("li").remove();
    	});
    	
    	$(".template_item_add").on("click", "button", function() {
    		var $div = $(this).parents("div.template_item_add");
    		
    		var name = $div.find("input").val();
    		var type = $div.find("select").val();
    		
    		addField(name, type, false, false);
    	});
    	
    	$("#template_item_save").click(function(){
    		var template_id = $(this).data("template");
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
			
			$.post(url, {name: templateName, fields: data}, function(json) {
				var x;
			});
    	});
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(AIRTIME.historyTemplate.onReady);