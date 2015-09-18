var endpoint = 'rest/podcast/';

var podcastApp = angular.module('podcast', [])
    .controller('RestController', function($scope, $http, podcast) {
        $scope.podcast = podcast;
        console.log(podcast);
        AIRTIME.tabs.setActiveTabName($scope.podcast.title);

        $scope.put = function() {
            $http.put(endpoint + $scope.podcast.id, { csrf_token: $("#csrf").val(), podcast: $scope.podcast })
                .success(function() {
                    AIRTIME.tabs.setActiveTabName($scope.podcast.title);
                    // TODO
                });
        };

        $scope.discard = function() {
            AIRTIME.tabs.closeTab();
            $scope.podcast = {};
        };
    });

var AIRTIME = (function (AIRTIME) {
    var mod;

    if (AIRTIME.podcast === undefined) {
        AIRTIME.podcast = {};
    }

    mod = AIRTIME.podcast;

    function _bulkAction(method, callback) {
        var selected = $("#podcast_table").find(".selected"),
            ids = [];
        var selectedData = AIRTIME.library.podcastTableWidget.getSelectedRows();
        selectedData.forEach(function(el) {
            ids.push(el.id);
        });

        // Bulk methods should use post because we're sending data in the request body
        $.post(endpoint + "bulk", { csrf_token: $("#csrf").val(), method: method, ids: ids }, callback);
    }

    function _bootstrapAngularApp(podcast) {
        podcastApp.value('podcast', JSON.parse(podcast));
        angular.bootstrap(document.getElementById("podcast-wrapper"), ["podcast"]);
    }

    mod.createUrlDialog = function() {
        $.get('/render/podcast-url-dialog', function(json) {
            $(document.body).append(json.html);
            $("#podcast_url_dialog").dialog({
                title: $.i18n._("Add New Podcast"),
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto'
            });
        });
    };

    mod.addPodcast = function() {
        $.post(endpoint, $("#podcast_url_dialog").find("form").serialize(), function(json) {
            AIRTIME.tabs.openTab(json, AIRTIME.podcast.init);
            _bootstrapAngularApp(json.podcast);
            $("#podcast_url_dialog").dialog("close");
        });
    };

    mod.editSelectedPodcasts = function() {
        _bulkAction("GET", function(json) {
            json.forEach(function(el) {
                AIRTIME.tabs.openTab(el, AIRTIME.podcast.init);
                _bootstrapAngularApp(el.podcast);
            });
        });
    };

    mod.deleteSelectedPodcasts = function() {
        if (confirm($.i18n._("Are you sure you want to delete the selected podcasts from your library?"))) {
            _bulkAction("DELETE", function () {
                AIRTIME.library.podcastDataTable.fnDraw();
            });
        }
    };

    /*
     * Callback when creating podcast tabs to initialize bindings
     */
    mod.init = function(newTab) {
        // FIXME: get rid of this duplication by abstracting out functionality in tabs
        newTab.tab.on("click", function() {
            if (!$(this).hasClass('active')) {
                AIRTIME.tabs.switchTab(newTab.pane, newTab.tab);
            }
        });

        $(".lib_pl_close").unbind().click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).unbind("click"); // Prevent repeated clicks in quick succession from closing multiple tabs

            var tabId = $(this).closest("li").attr("data-tab-id");

            // We need to update the text on the add button
            AIRTIME.library.checkAddButton();
            // We also need to run the draw callback to update how dragged items are drawn
            AIRTIME.library.fnDrawCallback();
            AIRTIME.tabs.closeTab(tabId);
        });
    };

    return AIRTIME;
}(AIRTIME || {}));
