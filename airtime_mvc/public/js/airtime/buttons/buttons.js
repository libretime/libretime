var AIRTIME = (function(AIRTIME){
	var mod,
		DEFAULT_CLASS = 'ui-button ui-state-default',
		DISABLED_CLASS = 'ui-state-disabled';
	
	if (AIRTIME.button === undefined) {
		AIRTIME.button = {};
	}
	mod = AIRTIME.button;
	
	mod.enableButton = function(c) {
		var button = $("."+c).find("button");
		
	    if (button.hasClass(DISABLED_CLASS)) {
	        button.removeClass(DISABLED_CLASS);
	    }
	};

	mod.disableButton = function(c) {
		var button = $("."+c).find("button");
		
	    if (!button.hasClass(DISABLED_CLASS)) {
	        button.addClass(DISABLED_CLASS);
	    }
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));