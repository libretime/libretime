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
    
    //takes an array of media ids
    function addToPlaylist(aIds) {
    	AIRTIME.playlist.addItems(aIds);
    };
    
    //data is the aData of the tr element.
    mod.dblClickAdd = function(data) {
    	addToPlaylist([data.Id]);
    };
    
    mod.addButtonClick = function() {
    	addToPlaylist(mod.getVisibleChosen());
    };
    
    return AIRTIME;

}(AIRTIME || {}));