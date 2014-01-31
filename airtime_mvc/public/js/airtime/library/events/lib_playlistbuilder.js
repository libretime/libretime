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
    	
    	
    };
    
    return AIRTIME;

}(AIRTIME || {}));