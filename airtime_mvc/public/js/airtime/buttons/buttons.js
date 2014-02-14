var AIRTIME = (function(AIRTIME) {
    var mod, 
	    DEFAULT_CLASS = 'ui-button ui-state-default', 
	    DISABLED_CLASS = 'ui-state-disabled';

    if (AIRTIME.button === undefined) {
        AIRTIME.button = {};
    }
    mod = AIRTIME.button;

    //c is a unique class on the <button>
    mod.isDisabled = function($button) {
        
        //disable the <button>
        if ($button.hasClass(DISABLED_CLASS)) {
            return true;
        }

        return false;
    };

    mod.enableButton = function($button) {
       
        if ($button.hasClass(DISABLED_CLASS)) {
            $button.removeClass(DISABLED_CLASS);
            $button.removeAttr('disabled');
        }
    };

    mod.disableButton = function($button) {
       
        if (!$button.hasClass(DISABLED_CLASS)) {
            $button.addClass(DISABLED_CLASS);
            $button.attr('disabled', 'disabled');
        }
    };

    return AIRTIME;

}(AIRTIME || {}));