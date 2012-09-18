var AIRTIME = (function(AIRTIME) {
    var mod, DEFAULT_CLASS = 'ui-button ui-state-default', DISABLED_CLASS = 'ui-state-disabled';

    if (AIRTIME.button === undefined) {
        AIRTIME.button = {};
    }
    mod = AIRTIME.button;

    mod.isDisabled = function(c, useParent) {
        var button = $("." + c);
        if (useParent) {
            button = button.parent();
        }

        if (button.hasClass(DISABLED_CLASS)) {
            return true;
        }

        return false;
    };

    mod.enableButton = function(c, useParent) {
        if (useParent) {
            var button = $("." + c).parent();
        } else {
            var button = $("." + c);
        }

        if (button.hasClass(DISABLED_CLASS)) {
            button.removeClass(DISABLED_CLASS);
            button.removeAttr('disabled');
        }
    };

    mod.disableButton = function(c, useParent) {
        if (useParent) {
            var button = $("." + c).parent();
        } else {
            var button = $("." + c);
        }

        if (!button.hasClass(DISABLED_CLASS)) {
            button.addClass(DISABLED_CLASS);
            button.attr('disabled', 'disabled');
        }
    };

    return AIRTIME;

}(AIRTIME || {}));