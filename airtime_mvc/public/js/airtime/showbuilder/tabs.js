var AIRTIME = (function(AIRTIME){
    var mod,
        $tabCount = 0,
        $tabMap = {},
        $openTabs = {},
        $activeTab,
        $scheduleTab;

    if (AIRTIME.tabs === undefined) {
        AIRTIME.tabs = {};
    }
    mod = AIRTIME.tabs;

    /*  #####################################################
                         Internal Functions
        ##################################################### */

    var Tab = function(json, uid) {
        var self = this;

        AIRTIME.library.selectNone();

        var existingTab = $openTabs[uid];
        if (existingTab) {
            existingTab.switchTo();
            return existingTab;
        }
        self.id = ++$tabCount;
        self.uid = uid;

        var wrapper = "<div data-tab-type='" + json.type + "' data-tab-id='" + self.id + "' id='pl-tab-content-" + self.id + "' class='side_playlist pl-content'><div class='editor_pane_wrapper'></div></div>",
            t = $("#show_builder").append(wrapper).find("#pl-tab-content-" + self.id),
            pane = $(".editor_pane_wrapper:last").append(json.html),
            name = pane.find("#track_title").length > 0 ? pane.find("#track_title").val() + $.i18n._(" - Metadata Editor")
                    : pane.find(".playlist_name_display").val(),
            tab =
                "<li data-tab-id='" + self.id + "' data-tab-type='" + json.type + "' id='pl-tab-" + self.id + "' role='presentation' class='active'>" +
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

        self.switchTo();
        return self;
    };

    Tab.prototype.switchTo = function() {
        _switchTab(this.contents, this.tab);
    };

    Tab.prototype.close = function() {
        var self = this;

        var toPane = self.contents.next().length > 0 ? self.contents.next() : self.contents.prev(),
            toTab = self.tab.next().length > 0 ? self.tab.next() : self.tab.prev();
        delete $openTabs[self.uid];  // Remove this tab from the open tab array
        delete $tabMap[self.id];  // Remove this tab from the internal tab mapping

        // Remove the relevant DOM elements (the tab and its contents)
        self.tab.remove();
        self.contents.remove();

        if (self.isActive()) {  // Closing the current tab, otherwise we don't need to switch tabs
            _switchTab(toPane, toTab);
        }

        // If we close a tab that was causing tabs to wrap to the next row
        // we need to resize to change the margin for the tab nav
        AIRTIME.playlist.onResize();

    };

    Tab.prototype.isActive = function() {
        return this.contents.get(0) == $activeTab.contents.get(0);
    };

    Tab.prototype.assignTabClickHandler = function(f) {
        this.tab.unbind("click").on("click", f);
    };
    Tab.prototype.assignTabCloseClickHandler = function(f) {
        this.tab.find(".lib_pl_close").unbind("click").click(f);
    };

    Tab.prototype._init = function() {
        var self = this;
        self.assignTabClickHandler(function() {
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
    };

    var ScheduleTab = function() {
        var self = this, uid = 0,
            tab = $("#schedule-tab"),
            pane = $("#show_builder"),
            contents = pane.find(".outer-datatable-wrapper");
        self.id = 0;

        tab.data("tab-id", self.id);
        tab.on("click", function() {
            if (!$(this).hasClass('active')) {
                _switchTab(contents, $(this));
            }
        });

        self.wrapper = pane;
        self.contents = contents;
        self.tab = tab;

        $openTabs[uid] = self;
        $tabMap[self.id] = uid;
    };
    ScheduleTab.prototype = Object.create(Tab.prototype);
    ScheduleTab.prototype.constructor = ScheduleTab;

    function _initFileMdEvents(newTab) {
        newTab.contents.find(".md-cancel").on("click", function() {
            newTab.close();
        });

        newTab.contents.find(".md-save").on("click", function() {
            var file_id = newTab.wrapper.find('#file_id').val(),
                data = newTab.wrapper.find("#edit-md-dialog form").serializeArray();
            $.post(baseUrl+'library/edit-file-md', {format: "json", id: file_id, data: data}, function() {
                // don't redraw the library table if we are on calendar page
                // we would be on calendar if viewing recorded file metadata
                if ($("#schedule_calendar").length === 0) {
                    oTable.fnStandingRedraw();
                }
            });

            newTab.close();
        });

        newTab.wrapper.find('#edit-md-dialog').on("keyup", function(event) {
            if (event.keyCode === 13) {
                newTab.wrapper.find('.md-save').click();
            }
        });

        AIRTIME.playlist.setupEventListeners();
    }

    function _initPlaylistEvents(newTab) {
        newTab.assignTabClickHandler(function() {
            if (!$(this).hasClass('active')) {
                newTab.switchTo();
                $.post(baseUrl+'playlist/edit', {
                    format: "json",
                    id: newTab.pane.find(".obj_id").val(),
                    type: newTab.pane.find(".obj_type").val()
                });
            }
        });

        AIRTIME.playlist.init();

        // functions in smart_blockbuilder.js
        setupUI();
        appendAddButton();
        appendModAddButton();
        removeButtonCheck();
        AIRTIME.playlist.setFadeIcon();
    }

    function _switchTab(tabPane, tab) {
        $activeTab.contents.hide().removeClass("active-tab");
        tabPane.addClass("active-tab").show();

        $activeTab.tab.removeClass("active");
        tab.addClass("active");

        mod.updateActiveTab();

        AIRTIME.playlist.onResize();
        AIRTIME.library.fnRedraw();
    }

    /*  #####################################################
                         External Functions
        ##################################################### */

    mod.init = function() {
        $scheduleTab = new ScheduleTab();
    };

    mod.openFileMdEditorTab = function(json, uid) {
        mod.openTab(json, uid, _initFileMdEvents);
    };

    mod.openPlaylistTab = function(json, uid) {
        mod.openTab(json, uid, _initPlaylistEvents);
    };

    mod.openTab = function(json, uid, callback) {
        var newTab = new Tab(json, uid);
        newTab._init();
        if (callback) callback(newTab);
    };

    mod.closeTab = function(id) {
        $openTabs[$tabMap[id]].close();
    };

    mod.setActiveTabName = function(name) {
        $activeTab.tab.find(".tab-name").text(name);
    };

    mod.updateActiveTab = function() {
        var t = $(".nav.nav-tabs .active");
        $activeTab = mod.get(t.data("tab-id"));
        if ($activeTab.contents.hasClass("pl-content")) {
            mod.updatePlaylist();
        }
    };

    mod.updatePlaylist = function() {
        AIRTIME.playlist.setCurrent($activeTab.contents);
        $.post(baseUrl + "playlist/change-playlist", {
            "id": AIRTIME.playlist.getId($activeTab.contents),
            "type": $activeTab.contents.find('.obj_type').val()
        });
    };

    mod.getScheduleTab = function() {
        return $scheduleTab;
    };

    mod.getActiveTab = function() {
        return $activeTab;
    };

    mod.get = function(id) {
        return $openTabs[$tabMap[id]];
    };

    return AIRTIME;

}(AIRTIME || {}));

$(document).ready(function() {
    $("#show_builder").textScroll(".tab-name");
    AIRTIME.tabs.init();
});