var AIRTIME = (function(AIRTIME){
    var mod,
        $tabCount = 0,
        $openTabs = {},
        $activeTab,
        $activeTabPane;

    if (AIRTIME.tabs === undefined) {
        AIRTIME.tabs = {};
    }
    mod = AIRTIME.tabs;

    /*  #####################################################
                         Internal Functions
        ##################################################### */

    function buildNewTab(json) {
        AIRTIME.library.selectNone();

        var tabId = $openTabs[json.type + json.id];
        if (tabId !== undefined) {
            mod.switchTab($("#pl-tab-content-" + tabId), $("#pl-tab-" + tabId));
            return undefined;
        }
        $tabCount++;

        var wrapper = "<div data-tab-type='" + json.type + "' data-tab-id='" + $tabCount + "' id='pl-tab-content-" + $tabCount + "' class='side_playlist pl-content'><div class='editor_pane_wrapper'></div></div>",
            t = $("#show_builder").append(wrapper).find("#pl-tab-content-" + $tabCount),
            pane = $(".editor_pane_wrapper:last"),
            name = json.type == "md" ?  // file
            pane.append(json.html).find("#track_title").val() + $.i18n._(" - Metadata Editor")
                : pane.append(json.html).find(".playlist_name_display").val(),
            tab =
                "<li data-tab-id='" + $tabCount + "' data-tab-type='" + json.type + "' id='pl-tab-" + $tabCount + "' role='presentation' class='active'>" +
                "<a href='javascript:void(0)'><span class='tab-name'></span>" +
                "<span href='#' class='lib_pl_close icon-remove'></span>" +
                "</a>" +
                "</li>",
            tabs = $(".nav.nav-tabs");

        if (json.id) {
            $openTabs[json.type + json.id] = $tabCount;
        }

        $(".nav.nav-tabs li").removeClass("active");
        tabs.append(tab);
        tabs.find("#pl-tab-" + $tabCount + " span.tab-name").text(name);

        var newTab = $("#pl-tab-" + $tabCount);
        mod.switchTab(t, newTab);

        return {wrapper: pane, tab: newTab, pane: t};
    }

    function initFileMdEvents(newTab) {
        newTab.tab.on("click", function() {
            if (!$(this).hasClass('active')) {
                mod.switchTab(newTab.pane, newTab.tab);
            }
        });

        newTab.wrapper.find(".md-cancel").on("click", function() {
            mod.closeTab();
        });

        newTab.wrapper.find(".md-save").on("click", function() {
            var file_id = newTab.wrapper.find('#file_id').val(),
                data = newTab.wrapper.find("#edit-md-dialog form").serializeArray();
            $.post(baseUrl+'library/edit-file-md', {format: "json", id: file_id, data: data}, function() {
                // don't redraw the library table if we are on calendar page
                // we would be on calendar if viewing recorded file metadata
                if ($("#schedule_calendar").length === 0) {
                    oTable.fnStandingRedraw();
                }
            });

            mod.closeTab();
        });

        newTab.wrapper.find('#edit-md-dialog').on("keyup", function(event) {
            if (event.keyCode === 13) {
                newTab.wrapper.find('.md-save').click();
            }
        });
    }

    /*  #####################################################
                         External Functions
        ##################################################### */

    mod.openFileMdEditorTab = function(json) {
        var newTab = buildNewTab(json);
        if (newTab === undefined) {
            return;
        }

        initFileMdEvents(newTab);
        AIRTIME.playlist.setupEventListeners();
    };

    mod.openPlaylistTab = function(json) {
        var newTab = buildNewTab(json);
        if (newTab === undefined) {
            return;
        }
        newTab.tab.on("click", function() {
            if (!$(this).hasClass('active')) {
                mod.switchTab(newTab.pane, newTab.tab);
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
    };

    mod.closeTab = function(id) {
        var pane = id ? mod.get(id, true) : $activeTabPane,
            tab = id ? mod.get(id) : $activeTab,
            toPane = pane.next().length > 0 ? pane.next() : pane.prev(),
            toTab = tab.next().length > 0 ? tab.next() : tab.prev(),
            objId = pane.find(".obj_id").val(),
            contents = id ? pane : $activeTabPane;
        delete $openTabs[tab.data("tab-type") + objId]; // Remove the closed tab from our open tabs array

        // Remove the relevant DOM elements (the tab and the tab content)
        tab.remove();
        contents.remove();

        if (pane.get(0) == $activeTabPane.get(0)) { // Closing the current tab, otherwise we don't need to switch tabs
            mod.switchTab(toPane, toTab);
        }

        // If we close a tab that was causing tabs to wrap to the next row, we need to resize to change the
        // margin for the tab nav
        AIRTIME.playlist.onResize();
    };

    mod.switchTab = function(tabPane, tab) {
        $activeTabPane.hide().removeClass("active-tab");
        tabPane.addClass("active-tab").show();

        $activeTab.removeClass("active");
        tab.addClass("active");

        mod.updateActiveTab();

        AIRTIME.playlist.onResize();
        AIRTIME.library.fnRedraw();
    };

    mod.setActiveTabName = function(name) {
        $activeTab.find(".tab-name").text(name);
    };

    mod.updateActiveTab = function() {
        $activeTabPane = $(".active-tab");
        $activeTab = $(".nav.nav-tabs .active");
        if ($activeTabPane.hasClass("pl-content")) {
            mod.updatePlaylist();
        }
    };

    mod.updatePlaylist = function() {
        AIRTIME.playlist.setCurrent($activeTabPane);
        $.post(baseUrl + "playlist/change-playlist", {
            "id": AIRTIME.playlist.getId($activeTabPane),
            "type": $activeTabPane.find('.obj_type').val()
        });
    };

    mod.getActiveTab = function() {
        return $activeTabPane;
    };

    mod.get = function(id, getContents) {
        var allTabs = getContents ? $(".pl-content") : $(".nav.nav-tabs li");
        if (id) {
            var t = null;
            allTabs.each(function() {
                if ($(this).data("tab-id") == id) {
                    t = $(this);
                }
            });
            // An id was passed in, but no tab with that id exists
            return t;
        }
        return allTabs;
    };

    return AIRTIME;

}(AIRTIME || {}));

$(document).ready(function() {
    setupTextScrolling($("#show_builder"), ".tab-name");
});