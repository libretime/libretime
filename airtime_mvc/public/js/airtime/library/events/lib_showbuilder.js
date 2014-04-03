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
    
    mod.checkAddButton = function($pane) {
    	var $selected = $pane.find("."+mod.LIB_SELECTED_CLASS),
			$button = $pane.find("." + mod.LIB_ADD_CLASS);
		
		if ($selected.length > 0) {
			AIRTIME.button.enableButton($button);
		}
		else {
			AIRTIME.button.disableButton($button);
		}
    };
    
    mod.checkToolBarIcons = function() {
    	var tabId = mod.getActiveTabId();
			$pane = $("#"+tabId);
    	
    	mod.checkAddButton($pane);
        mod.checkDeleteButton($pane);
    };
    
    function getScheduleCursors() {
    	var aSchedIds = [];
	
    	// process selected schedule rows to add media after.
	    $("#show_builder_table tr.cursor-selected-row").each(function(i, el) {
	    	var data = $(el).data("aData");
	    	
	    	aSchedIds.push( {
	            "id" : data.id,
	            "instance" : data.instance,
	            "timestamp" : data.timestamp
	        });
	    });

	    return aSchedIds;
    }
    
    function scheduleMedia(aMediaIds) {
    	var cursorInfo = getScheduleCursors();
    	
    	if (cursorInfo.length == 0) {
            alert($.i18n._("Please select a cursor position on timeline."));
            return false;
        }
        
        AIRTIME.showbuilder.fnAdd(aMediaIds, cursorInfo);
    }
    
    //data is the aData of the tr element.
    mod.dblClickAdd = function(data) {
    	scheduleMedia([data.Id]);
    };
    
    mod.addButtonClick = function() {
    	scheduleMedia(mod.getVisibleChosen());
    };
    
    return AIRTIME;

}(AIRTIME || {}));