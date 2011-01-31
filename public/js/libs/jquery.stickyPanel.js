/*
*   jQuery.stickyPanel
*   ----------------------
*   version: 1.0.0
*   date: 1/17/11
*
*   Copyright (c) 2011 Donny Velazquez
*   http://donnyvblog.blogspot.com/
*   http://code.google.com/p/stickyPanel
*   
*   Licensed under the Apache License 2.0
*
*/
(function ($) {

    $.fn.stickyPanel = function (options) {

        var options = $.extend({}, $.fn.stickyPanel.defaults, options);

        return this.each(function () {
            $(window).bind("scroll.stickyPanel", { selected: $(this), options: options }, Scroll);
        });

    };

    function Scroll(event) {
        var node = event.data.selected;
        var o = event.data.options;

        // when top of window reaches the top of the panel detach
        if ($(document).scrollTop() >= node.offset().top) {

            // topPadding
            var top = 0;
            if (o.topPadding != "undefined") {
                top = top + o.topPadding;
            }

            // save panels top
            node.data("PanelsTop", node.offset().top - top);

            // afterDetachCSSClass
            if (o.afterDetachCSSClass != "") {
                node.addClass(o.afterDetachCSSClass);
            }

            // savePanelSpace
            if (o.savePanelSpace == true) {
                var width = node.outerWidth(true);
                var height = node.outerHeight(true);
                var float = node.css("float");
                var randomNum = Math.ceil(Math.random() * 9999); /* Pick random number between 1 and 9999 */
                node.data("PanelSpaceID", "stickyPanelSpace" + randomNum);
                node.before("<div id='" + node.data("PanelSpaceID") + "' style='width:" + width + "px;height:" + height + "px;float:" + float + ";'></div>");
            }

            // detach panel
            node.css({
                "top": top,
                "position": "fixed"
            });

        }

        if ($(document).scrollTop() <= node.data("PanelsTop")) {

			if (o.savePanelSpace == true) {
				$("#" + node.data("PanelSpaceID")).remove();
			}
			
            // attach panel
            node.css({
                "top": "auto",
                "position": "static"
            });

            if (o.afterDetachCSSClass != "") {
                node.removeClass(o.afterDetachCSSClass);
            }
        }

    };

    $.fn.stickyPanel.defaults = {
        topPadding: 0,
        // Use this to set the top margin of the detached panel.

        afterDetachCSSClass: "",
        // This class is applied when the panel detaches.

        savePanelSpace: false
        // When set to true the space where the panel was is kept open.
    };

})(jQuery);