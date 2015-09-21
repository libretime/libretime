var endpoint = 'rest/podcast/';

var podcastApp = angular.module('podcast', [])
    .controller('RestController', function($scope, $http, podcast) {
        $scope.podcast = podcast;
        console.log(podcast);
        AIRTIME.tabs.setActiveTabName($scope.podcast.title);

        $scope.savePodcast = function() {
            $http.put(endpoint + $scope.podcast.id, { csrf_token: jQuery("#csrf").val(), podcast: $scope.podcast })
                .success(function() {
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
        var wrapper = AIRTIME.tabs.getActiveTab().find(".editor_pane_wrapper");
        wrapper.attr("ng-controller", "RestController");
        angular.bootstrap(wrapper.get(0), ["podcast"]);
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
            mod.initPodcastEpisodeDatatable(JSON.parse(el.podcast).episodes);
        });
    };

    mod.editSelectedPodcasts = function() {
        _bulkAction("GET", function(json) {
            json.forEach(function(el) {
                AIRTIME.tabs.openTab(el, AIRTIME.podcast.init);
                _bootstrapAngularApp(el.podcast);
                mod.initPodcastEpisodeDatatable(JSON.parse(el.podcast).episodes);
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

    mod.initPodcastEpisodeDatatable = function(episodes) {
        console.log(episodes);
        var aoColumns = [
            /* Title */             { "sTitle" : $.i18n._("Title")             , "mDataProp" : "title"          , "sClass" : "podcast_episodes_title"       , "sWidth" : "170px" },
            /* Author */            { "sTitle" : $.i18n._("Author")            , "mDataProp" : "author"         , "sClass" : "podcast_episodes_author"      , "sWidth" : "170px" },
            /* Description */       { "sTitle" : $.i18n._("Description")       , "mDataProp" : "description"    , "sClass" : "podcast_episodes_description" , "sWidth" : "300px" },
            /* Link */              { "sTitle" : $.i18n._("Link")              , "mDataProp" : "link"           , "sClass" : "podcast_episodes_link"        , "sWidth" : "170px" },
            /* GUID */              { "sTitle" : $.i18n._("GUID")              , "mDataProp" : "guid"           , "sClass" : "podcast_episodes_guid"        , "sWidth" : "170px" },
            /* Publication Date */  { "sTitle" : $.i18n._("Publication Date")  , "mDataProp" : "pubDate"        , "sClass" : "podcast_episodes_pub_date"    , "sWidth" : "170px" }
        ];

        var podcastToolbarButtons = AIRTIME.widgets.Table.getStandardToolbarButtons();

        // Set up the div with id "podcast_table" as a datatable.
        mod.podcastEpisodesTableWidget = new AIRTIME.widgets.Table(
            AIRTIME.tabs.getActiveTab().find('#podcast_episodes'), // DOM node to create the table inside.
            true,                // Enable item selection
            podcastToolbarButtons, // Toolbar buttons
            {                    // Datatables overrides.
                'aoColumns' : aoColumns,
                'bServerSide': false,
                'sAjaxSource' : null,
                'aaData' : episodes
            });

        mod.podcastEpisodesDatatable = mod.podcastEpisodesTableWidget.getDatatable();
        mod.podcastEpisodesDatatable.textScroll("td");
    };

    return AIRTIME;
}(AIRTIME || {}));
