var AIRTIME = (function(AIRTIME) {
    var mod, 
	    DEFAULT_CLASS = 'ui-button ui-state-default', 
	    DISABLED_CLASS = 'ui-state-disabled';

    if (AIRTIME.button === undefined) {
        AIRTIME.button = {};
    }
    mod = AIRTIME.button;

    //c is a unique class on the <button>
    mod.isDisabled = function(c) {
        var button = $("." + c);

        //disable the <button>
        if (button.hasClass(DISABLED_CLASS)) {
            return true;
        }

        return false;
    };

    //c is a unique class on the <button>
    mod.enableButton = function(c) {
        var button = $("." + c);
        
        if (button.hasClass(DISABLED_CLASS)) {
            button.removeClass(DISABLED_CLASS);
            button.removeAttr('disabled');
        }
    };

    //c is a unique class on the <button>
    mod.disableButton = function(c) {
        var button = $("." + c);

        if (!button.hasClass(DISABLED_CLASS)) {
            button.addClass(DISABLED_CLASS);
            button.attr('disabled', 'disabled');
        }
    };

    return AIRTIME;

}(AIRTIME || {}));