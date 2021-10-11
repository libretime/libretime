var AIRTIME = (function(AIRTIME){
        /**
         * AIRTIME module namespace object
         */
    var mod,
        /**
         * Tab counter to use as unique tab IDs that can be
         * retrieved from the DOM
         *
         * @type {number}
         */
        $tabCount = 0,
        /**
         * Map of Tab IDs (by tabCount) to object UIDs so
         * Tabs can be referenced either by ID (from the DOM)
         * or by UID (from object data)
         *
         * @type {{}}
         */
        $tabMap = {},
        /**
         * Map of object UIDs to currently open Tab objects
         *
         * @type {{}}
         */
        $openTabs = {},
        /**
         * The currently active (open) Tab object
         *
         * @type {Tab}
         */
        $activeTab,
        /**
         * Singleton object used to reference the schedule tab
         *
         * @type {ScheduleTab}
         */
        $scheduleTab;

    if (AIRTIME.tabs === undefined) {
        AIRTIME.tabs = {};
    }
    mod = AIRTIME.tabs;

    /*  #####################################################
                  Object Initialization and Functions
        ##################################################### */

    /**
     * Tab object constructor
     *
     * @param {string} html the HTML to render as the tab contents
     * @param {string} uid  the unique ID for the tab. Uses the values in
     *                      AIRTIME.library.MediaTypeStringEnum and the object ID
     *                      to create a string of the form TYPE_ID.
     * @returns {Tab}       the created Tab object
     * @constructor
     */
    var Tab = function(html, uid) {
        var self = this;

        AIRTIME.library.selectNone();

        var existingTab = $openTabs[uid];
        if (existingTab) {
            existingTab.switchTo();
            return existingTab;
        }
        self.id = ++$tabCount;
        self.uid = uid;

        // TODO: clean this up a bit and use js instead of strings to create elements
        var wrapper = "<div data-tab-id='" + self.id + "' id='pl-tab-content-" + self.id + "' class='side_playlist pl-content'><div class='editor_pane_wrapper'></div></div>",
            t = $("#show_builder").append(wrapper).find("#pl-tab-content-" + self.id),
            pane = $(".editor_pane_wrapper:last").append(html),
            name = pane.find("#track_title").length > 0 ? pane.find("#track_title").val() + $.i18n._(" - Metadata Editor")
                : pane.find(".playlist_name_display").val(),
            tab =
                "<li data-tab-id='" + self.id + "' id='pl-tab-" + self.id + "' role='presentation' class='active'>" +
                    "<a href='javascript:void(0)'>" +
                        "<span class='tab-name'>" + name + "</span>" +
                        "<span href='#' class='lib_pl_close icon-remove'></span>" +
                    "</a>" +
                "</li>",
            tabs = $(".nav.nav-tabs");

        $(".nav.nav-tabs li").removeClass("active");
        tabs.append(tab);

        var newTab = $("#pl-tab-" + self.id);

        self.wrapper = pane;
        self.contents = t;
        self.tab = newTab;

        $openTabs[uid] = self;
        $tabMap[self.id] = uid;

        self._init();
        self.switchTo();
        return self;
    };

    /**
     * Private initialization function for Tab objects
     *
     * Assigns default action handlers to the tab DOM element
     *
     * @private
     */
    Tab.prototype._init = function() {
        var self = this;
        self.assignTabClickHandler(function(e) {
            if (!$(this).hasClass('active')) {
                self.switchTo();
            }
        });

        self.assignTabCloseClickHandler(function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).unbind("click"); // Prevent repeated clicks in quick succession from closing multiple tabs

            // We need to update the text on the add button
            AIRTIME.library.checkAddButton();
            // We also need to run the draw callback to update how dragged items are drawn
            AIRTIME.library.fnDrawCallback();
            self.close();
        });

        self.contents.on("click", ".toggle-editor-form", function(event) {
            self.contents.find(".inner_editor_wrapper").slideToggle(200);
            var buttonIcon = $(this).find('.icon-white');
            buttonIcon.toggleClass('icon-chevron-up');
            buttonIcon.toggleClass('icon-chevron-down');
        });
    };

    /**
     * Internal destructor. Can be assigned via assignOnCloseHandler
     *
     * @private
     */
    Tab.prototype._destroy = function () {};

    /**
     * Assign the given function f as the click handler for the tab
     *
     * @param {function} f the function to call when the tab is clicked
     */
    Tab.prototype.assignTabClickHandler = function(f) {
        var self = this;
        self.tab.unbind("click").on("click", function (e) {
            // Always close on middle mouse press
            if (e.which == 2) {
                // Simulate a click on the close tab button so any
                // additional on-close behaviour is executed
                self.tab.find(".lib_pl_close").click();
                return;
            }
            f();
        });
    };

    /**
     * Assign the given function f as the click handler for the tab close button
     *
     * @param {function} f the function to call when the tab's close button is clicked
     */
    Tab.prototype.assignTabCloseClickHandler = function(f) {
        this.tab.find(".lib_pl_close").unbind("click").click(f);
    };

    /**
     * Assign an implicit destructor
     *
     * @param {function} fn function to run when this Tab is destroyed
     */
    Tab.prototype.assignOnCloseHandler = function (fn) {
        this._destroy = fn;
    };

    /**
     * Open this tab in the right-hand pane and set it as the currently active tab
     */
    Tab.prototype.switchTo = function() {
        var self = this;
        $activeTab.contents.hide().removeClass("active-tab");
        self.contents.addClass("active-tab").show();

        $activeTab.tab.removeClass("active");
        self.tab.addClass("active");

        mod.updateActiveTab();

        // In case we're adding a tab that wraps to the next row
        // It's better to call this here so we don't have to call it in multiple places
        mod.onResize();
        return this;  // For chaining
    };

    /**
     * Close the tab. Switches to the nearest open tab, prioritizing the
     * more recent (rightmost) tabs
     */
    Tab.prototype.close = function() {
        var self = this;

        var ascTabs = Object.keys($openTabs).sort(function(a, b){return a-b}),
            pos = ascTabs.indexOf(self.uid),
            toTab = pos < ascTabs.length-1 ? $openTabs[ascTabs[++pos]] : $openTabs[ascTabs[--pos]];
        delete $openTabs[self.uid];  // Remove this tab from the open tab array
        delete $tabMap[self.id];  // Remove this tab from the internal tab mapping

        // Remove the relevant DOM elements (the tab and its contents)
        if (self.uid !== 0) {
            self.tab.remove();
            self.contents.remove();
        } else {
            // only hide scheduled shows tab so we can still interact with it.
            self.tab.hide();
            self.contents.hide();
        }


        if (self.isActive() && toTab) {  // Closing the current tab, otherwise we don't need to switch tabs
            toTab.switchTo();
        } else {
            mod.onResize();
        }

        if (Object.keys($openTabs).length < 1) {
            $('#show_builder').hide();
        }

        self._destroy();
    };

    /**
     * Set the visible Tab name to the given string
     *
     * @param {string} name the name to set
     */
    Tab.prototype.setName = function(name) {
        this.tab.find(".tab-name").text(name);
        return this;  // For chaining
    };

    /**
     * Check if the Tab object is the currently active (open) Tab
     *
     * @returns {boolean} true if the Tab is the currently active Tab
     */
    Tab.prototype.isActive = function() {
        return this.contents.get(0) == $activeTab.contents.get(0);
    };

    /**
     * ScheduledTab object constructor
     *
     * The schedule tab is present in the DOM already on load, and we
     * need to be able to reference it in the same way as other tabs
     * (to avoid duplication and confusion) so we define it statically
     *
     * @constructor
     */
    var ScheduleTab = function() {
        var self = this, uid = 0,
            tab = $("#schedule-tab"),
            pane = $("#show_builder"),
            contents = pane.find(".outer-datatable-wrapper");
        self.id = 0;
        self.uid = uid;

        tab.data("tab-id", self.id);

        self.wrapper = pane;
        self.contents = contents;
        self.tab = tab;

        self.assignTabClickHandler(function(e) {
            if (!self.isActive()) {
                self.switchTo();
            }
        });

        self.assignTabCloseClickHandler(function(e) {
            self.close();
        });

        $openTabs[uid] = self;
        $tabMap[self.id] = uid;
    };
    /**
     * Subclass the Tab object
     * @type {Tab}
     */
    ScheduleTab.prototype = Object.create(Tab.prototype);
    ScheduleTab.prototype.constructor = ScheduleTab;

    /*  #####################################################
                           Module Functions
        ##################################################### */

    /**
     * Initialize the singleton ScheduleTab object on startup
     */
    mod.initScheduleTab = function() {
        $scheduleTab = new ScheduleTab();
        $activeTab = $scheduleTab;
    };

    /**
     * Create a new Tab object and open it in the ShowBuilder pane
     *
     * @param {string} html         the HTML to render as the tab contents
     * @param {string} uid          the unique ID for the tab. Uses the values in
     *                              AIRTIME.library.MediaTypeStringEnum and the object ID
     * @param {function} callback   an optional callback function to call once the
     *                              Tab object is initialized
     * @returns {Tab}               the created Tab object
     */
    mod.openTab = function(html, uid, callback) {
        $('#show_builder').show();
        var newTab = new Tab(html, uid);
        if (callback) callback(newTab);
        return newTab;
    };

    /**
     * open the schedule tab if if was closed
     *
     * @returns {Tab}
     */
    mod.openScheduleTab = function() {
        var $scheduleTab = this.getScheduleTab();
        $('#show_builder').show();
        $openTabs[0] = $scheduleTab;
        $scheduleTab.tab.show();
        $scheduleTab.contents.show();
        $scheduleTab.switchTo();
        $scheduleTab.assignTabCloseClickHandler(function(e) {
            $scheduleTab.close();
        });
    };

    /**
     * Updates the currently active tab
     *
     * Called when the user switches tabs for any reason
     *
     * NOTE: this function updates the currently active playlist
     *       as a side-effect, which is necessary for playlist tabs
     *       but not for other types of tabs... would be good to
     *       get rid of this dependency at some point
     */
    mod.updateActiveTab = function() {
        var t = $(".nav.nav-tabs .active");
        $activeTab = mod.get(t.data("tab-id"));
        if (!$activeTab) $activeTab = $scheduleTab;
        if ($activeTab.contents.hasClass("pl-content")) {
            AIRTIME.playlist.setCurrent($activeTab.contents);
        }
    };

    /**
     * Get the ScheduleTab object
     *
     * @returns {ScheduleTab}
     */
    mod.getScheduleTab = function() {
        return $scheduleTab;
    };

    /**
     * Get the currently active (open) Tab object
     *
     * @returns {Tab} the currently active tab
     */
    mod.getActiveTab = function() {
        return $activeTab;
    };

    /**
     * Given a tab id, get the corresponding Tab object
     *
     * @param {int|string}      id the tab or object ID of the Tab to retrieve
     * @returns {Tab|undefined} the Tab object with the given ID, or undefined
     *                          if no Tab with the given ID exists
     */
    mod.get = function(id) {
        return $.isNumeric(id) ? $openTabs[$tabMap[id]] : $openTabs[id];
    };

    /**
     * Adjust the margins on the right-hand pane when we have multiple rows of tabs
     */
    mod.onResize = function() {
        var h = $(".panel-header .nav").height();
        $(".pl-content").css("margin-top", h + 5); // 8px extra for padding
        $("#show_builder_table_wrapper").css("top", h + 5);
    };

    /**
     * Expose the Tab object so it can be subclassed
     *
     * @type {Function}
     */
    mod.Tab = Tab;

    return AIRTIME;

}(AIRTIME || {}));

$(document).ready(function() {
    var sb = $("#show_builder");
    // Add text scrolling to tab names
    sb.addTitles(".tab-name");
    sb.find(".nav.nav-tabs").sortable({
        containment: "parent",
        distance: 25
    });
    // Initialize the ScheduleTab
    AIRTIME.tabs.initScheduleTab();
});
$(window).resize(AIRTIME.tabs.onResize);
