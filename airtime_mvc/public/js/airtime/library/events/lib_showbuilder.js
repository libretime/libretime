var AIRTIME = (function(AIRTIME) {
    var mod;

    if (AIRTIME.library === undefined) {
        AIRTIME.library = {};
    }

    mod = AIRTIME.library;
    
    mod.setupToolbar = function(tabId) {
        var $toolbar = $("#"+tabId+" .fg-toolbar:first"),
        	$menu = mod.createToolbarButtons();

        $toolbar.append($menu);
        
    };
    
    mod.checkAddButton = function(tabId) {
    	
    };
    
    mod.checkDeleteButton = function(tabId) {
    	
    };
    
    mod.checkToolBarIcons = function(tabId) {
    	
    	mod.checkAddButton();
        mod.checkDeleteButton();
    };
    
    mod.dblClickAdd = function(data) {
        var i, 
	        length, 
	        temp, 
	        aMediaIds = [], 
	        aSchedIds = [], 
	        aData = [];

        // process selected media.
        aMediaIds.push(data.Id);

        $("#show_builder_table tr.cursor-selected-row").each(function(i, el) {
            aData.push($(el).prev().data("aData"));
        });

        // process selected schedule rows to add media after.
        for (i = 0, length = aData.length; i < length; i++) {
            temp = aData[i];
            aSchedIds.push( {
                "id" : temp.id,
                "instance" : temp.instance,
                "timestamp" : temp.timestamp
            });
        }

        if (aSchedIds.length == 0) {
            alert($.i18n._("Please select a cursor position on timeline."));
            return false;
        }
        AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
    };
    
    return AIRTIME;

}(AIRTIME || {}));