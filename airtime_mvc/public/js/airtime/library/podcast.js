var AIRTIME = (function (AIRTIME) {
    var mod;

    if (AIRTIME.podcast === undefined) {
        AIRTIME.podcast = {};
    }

    mod = AIRTIME.podcast;

    var endpoint = 'rest/podcast/';
    
    //AngularJS app
    var podcastApp = angular.module('podcast', [])
        .controller('RestController', function($scope, $http, podcast, tab, episodeTable) {
            // We need to pass in the tab object and the episodes table object so we can reference them

            //We take a podcast object in as a parameter rather fetching the podcast by ID here because
            //when you're creating a new podcast, we already have the object from the result of the POST. We're saving
            //a roundtrip by not fetching it again here.
            $scope.podcast = podcast;
            tab.setName($scope.podcast.title);

            $scope.savePodcast = function() {
                var podcastData = $scope.podcast;  // Copy the podcast in scope so we can modify it
                podcastData.episodes = episodeTable.getSelectedRows();
                $http.put(endpoint + $scope.podcast.id, { csrf_token: jQuery("#csrf").val(), podcast: podcastData })
                    .success(function() {
                        // TODO
                    });
            };

            $scope.discard = function() {
                tab.close();
                $scope.podcast = {};
            };
        });

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

    function _bootstrapAngularApp(podcast, tab, table) {
        podcastApp.value('podcast', podcast);
        podcastApp.value('tab', tab);
        podcastApp.value('episodeTable', table);
        var wrapper = tab.contents.find(".editor_pane_wrapper");
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
            var podcast = JSON.parse(json.podcast);
            var uid = AIRTIME.library.MediaTypeStringEnum.PODCAST+"_"+podcast.id,
                tab = AIRTIME.tabs.openTab(json, uid, null);
            var table = mod.initPodcastEpisodeDatatable(podcast.episodes);
            _bootstrapAngularApp(podcast, tab, table);
            $("#podcast_url_dialog").dialog("close");
        });
    };

    mod.editSelectedPodcasts = function() {
        _bulkAction("GET", function(json) {
            json.forEach(function(el) {
                var podcast = JSON.parse(el.podcast);
                var uid = AIRTIME.library.MediaTypeStringEnum.PODCAST+"_"+podcast.id,
                    tab = AIRTIME.tabs.openTab(el, uid, null);
                var table = mod.initPodcastEpisodeDatatable(podcast.episodes);
                _bootstrapAngularApp(podcast, tab, table);
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

    mod.initPodcastEpisodeDatatable = function(episodes) {
        var aoColumns = [
            /* Title */             { "sTitle" : $.i18n._("Title")             , "mDataProp" : "title"          , "sClass" : "podcast_episodes_title"       , "sWidth" : "170px" },
            /* Author */            { "sTitle" : $.i18n._("Author")            , "mDataProp" : "author"         , "sClass" : "podcast_episodes_author"      , "sWidth" : "170px" },
            /* Description */       { "sTitle" : $.i18n._("Description")       , "mDataProp" : "description"    , "sClass" : "podcast_episodes_description" , "sWidth" : "300px" },
            /* Link */              { "sTitle" : $.i18n._("Link")              , "mDataProp" : "link"           , "sClass" : "podcast_episodes_link"        , "sWidth" : "170px" },
            /* Publication Date */  { "sTitle" : $.i18n._("Publication Date")  , "mDataProp" : "pub_date"       , "sClass" : "podcast_episodes_pub_date"    , "sWidth" : "170px" }
        ];

        var podcastToolbarButtons = AIRTIME.widgets.Table.getStandardToolbarButtons();

        // Set up the div with id "podcast_table" as a datatable.
        var podcastEpisodesTableWidget = new AIRTIME.widgets.Table(
            AIRTIME.tabs.getActiveTab().contents.find('#podcast_episodes'), // DOM node to create the table inside.
            true,                // Enable item selection
            podcastToolbarButtons, // Toolbar buttons
            {                    // Datatables overrides.
                'aoColumns' : aoColumns,
                'bServerSide': false,
                'sAjaxSource' : null,
                'aaData' : episodes,
                "oColVis": {
                    "sAlign": "right",
                    "aiExclude": [0, 1],
                    "buttonText": $.i18n._("Columns"),
                    "iOverlayFade": 0
                }
            });

        podcastEpisodesTableWidget.getDatatable().textScroll("td");
        return podcastEpisodesTableWidget;
    };

    return AIRTIME;
}(AIRTIME || {}));
