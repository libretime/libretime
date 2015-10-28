var AIRTIME = (function (AIRTIME) {
    var mod;

    if (AIRTIME.podcast === undefined) {
        AIRTIME.podcast = {};
    }

    mod = AIRTIME.podcast;

    var endpoint = 'rest/podcast/', PodcastTable;
    
    //AngularJS app
    var podcastApp = angular.module('podcast', [])
        .controller('RestController', function($scope, $http, podcast, tab) {
            // We need to pass in the tab object and the episodes table object so we can reference them

            //We take a podcast object in as a parameter rather fetching the podcast by ID here because
            //when you're creating a new podcast, we already have the object from the result of the POST. We're saving
            //a roundtrip by not fetching it again here.
            $scope.podcast = podcast;
            tab.setName($scope.podcast.title);
            $scope.csrf = jQuery("#csrf").val();
            tab.contents.find("table").attr("id", "podcast_episodes_" + podcast.id);
            var episodeTable = AIRTIME.podcast.initPodcastEpisodeDatatable(podcast.episodes, tab);

            // Override the switchTo function to reload the table when the tab is focused.
            // Should help to reduce the number of cases where the frontend doesn't match the state
            // of the backend (due to automatic ingestion).
            // Note that these cases should already be very few and far between.
            // TODO: make sure this doesn't noticeably slow performance
            // XXX: it's entirely possible that this (in the angular app) is not where we want this function...
            tab.switchTo = function() {
                AIRTIME.tabs.Tab.prototype.switchTo.call(this);
                episodeTable.reload($scope.podcast.id);
            };

            function updatePodcast() {
                $http.put(endpoint + $scope.podcast.id, { csrf_token: $scope.csrf, podcast: $scope.podcast })
                    .success(function() {
                        episodeTable.reload($scope.podcast.id);
                        AIRTIME.library.podcastDataTable.fnDraw();
                        tab.setName($scope.podcast.title);
                    });
            }

            $scope.savePodcast = function() {
                var episodes = episodeTable.getSelectedRows();
                // TODO: Should we implement a batch endpoint for this instead?
                jQuery.each(episodes, function() {
                    $http.post(endpoint + $scope.podcast.id + '/episodes', { csrf_token: $scope.csrf, episode: this });
                });
                updatePodcast();
            };

            $scope.saveStationPodcast = function() {
                // TODO: We still need a way to delete episodes from the station podcast
                updatePodcast();
            };

            $scope.discard = function() {
                tab.close();
                $scope.podcast = {};
            };
        });

    function _bulkAction(method, callback) {
        var ids = [], selectedData = AIRTIME.library.podcastTableWidget.getSelectedRows();
        selectedData.forEach(function(el) {
            var uid = AIRTIME.library.MediaTypeStringEnum.PODCAST+"_"+el.id,
                t = AIRTIME.tabs.get(uid);
            if (t && method == HTTPMethods.DELETE) {
                t.close();
            }
            if (!(t && method == HTTPMethods.GET)) {
                ids.push(el.id);
            } else if (t != AIRTIME.tabs.getActiveTab()) {
                t.switchTo();
            }
        });

        if (ids.length > 0) {
            // Bulk methods should use post because we're sending data in the request body. There is no standard
            // RESTful way to implement bulk actions, so this is how we do it:
            $.post(endpoint + "bulk", {csrf_token: $("#csrf").val(), method: method, ids: ids}, callback);
        }
    }

    function _bootstrapAngularApp(podcast, tab) {
        podcastApp.value('podcast', podcast);
        podcastApp.value('tab', tab);
        var wrapper = tab.contents.find(".editor_pane_wrapper");
        wrapper.attr("ng-controller", "RestController");
        angular.bootstrap(wrapper.get(0), ["podcast"]);
    }

    function _initAppFromResponse(data) {
        var podcast = JSON.parse(data.podcast),
            uid = AIRTIME.library.MediaTypeStringEnum.PODCAST+"_"+podcast.id,
            tab = AIRTIME.tabs.openTab(data.html, uid, null);
        _bootstrapAngularApp(podcast, tab);
    }

    function _initPodcastTable() {
        PodcastTable = function(wrapperDOMNode, bItemSelection, toolbarButtons, dataTablesOptions) {
            // Just call the superconstructor. For clarity/extensibility
            return AIRTIME.widgets.Table.call(this, wrapperDOMNode, bItemSelection, toolbarButtons, dataTablesOptions);
        };  // Subclass AIRTIME.widgets.Table
        PodcastTable.prototype = Object.create(AIRTIME.widgets.Table.prototype);
        PodcastTable.prototype.constructor = PodcastTable;
        PodcastTable.prototype._SELECTORS = Object.freeze({
            SELECTION_CHECKBOX: ".airtime_table_checkbox:has(input)",
            SELECTION_TABLE_ROW: "tr:has(td.airtime_table_checkbox > input)"
        });
        PodcastTable.prototype._datatablesCheckboxDataDelegate = function(rowData, callType, dataToSave) {
            if (rowData.ingested) return null;  // Don't create checkboxes for ingested items
            return AIRTIME.widgets.Table.prototype._datatablesCheckboxDataDelegate.call(this, rowData, callType, dataToSave);
        };
        // Since we're using a static source, define a separate function to fetch and 'reload' the table data
        // We use this when we save the Podcast because we need to flag rows the user is ingesting
        PodcastTable.prototype.reload = function(id) {
            var dt = this._datatable;
            $.get(endpoint + id, function(json) {
                dt.fnClearTable();
                dt.fnAddData(JSON.parse(json).episodes);
            });
        };
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
            _initAppFromResponse(json);
            $("#podcast_url_dialog").dialog("close");
        });
    };

    mod.editSelectedPodcasts = function() {
        _bulkAction(HTTPMethods.GET, function(json) {
            json.forEach(function(data) {
                _initAppFromResponse(data);
            });
        });
    };

    mod.deleteSelectedPodcasts = function() {
        if (confirm($.i18n._("Are you sure you want to delete the selected podcasts from your library?"))) {
            _bulkAction(HTTPMethods.DELETE, function () {
                AIRTIME.library.podcastDataTable.fnDraw();
            });
        }
    };

    mod.initPodcastEpisodeDatatable = function(episodes, tab) {
        var aoColumns = [
            /* GUID */              { "sTitle" : ""                            , "mDataProp" : "guid"           , "sClass" : "podcast_episodes_guid"        , "bVisible" : false },
            /* Title */             { "sTitle" : $.i18n._("Title")             , "mDataProp" : "title"          , "sClass" : "podcast_episodes_title"       , "sWidth" : "170px" },
            /* Author */            { "sTitle" : $.i18n._("Author")            , "mDataProp" : "author"         , "sClass" : "podcast_episodes_author"      , "sWidth" : "170px" },
            /* Description */       { "sTitle" : $.i18n._("Description")       , "mDataProp" : "description"    , "sClass" : "podcast_episodes_description" , "sWidth" : "300px" },
            /* Link */              { "sTitle" : $.i18n._("Link")              , "mDataProp" : "link"           , "sClass" : "podcast_episodes_link"        , "sWidth" : "170px" },
            /* Publication Date */  { "sTitle" : $.i18n._("Publication Date")  , "mDataProp" : "pub_date"       , "sClass" : "podcast_episodes_pub_date"    , "sWidth" : "170px" }
        ];

        if (typeof PodcastTable === 'undefined') {
            _initPodcastTable();
        }

        var podcastToolbarButtons = AIRTIME.widgets.Table.getStandardToolbarButtons();
        podcastToolbarButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.DELETE].eventHandlers.click = function(e) {
            // TODO: add {this} reference to event handlers and implement deletion for station podcasts
        };

        // Set up the div with id "podcast_table" as a datatable.
        var podcastEpisodesTableWidget = new PodcastTable(
            tab.contents.find('.podcast_episodes'), // DOM node to create the table inside.
            true,                // Enable item selection
            podcastToolbarButtons, // Toolbar buttons
            {                    // Datatables overrides.
                'aoColumns'   : aoColumns,
                'bServerSide' : false,
                // We want to make as few round trips as possible, so we get
                // the episode data alongside the Podcast data and pass it in
                // as json. Doing this caches all the episode data on the front-end,
                // which means we also don't need to go back to the server for pagination
                'sAjaxSource' : null,
                'aaData'      : episodes,
                "oColVis": {
                    "sAlign": "right",
                    "aiExclude": [0, 1],
                    "buttonText": $.i18n._("Columns"),
                    "iOverlayFade": 0,
                    'oColReorder': {
                        'iFixedColumns': 1  // Checkbox
                    }
                }
            }
        );

        podcastEpisodesTableWidget.getDatatable().addTitles("td");
        return podcastEpisodesTableWidget;
    };

    return AIRTIME;
}(AIRTIME || {}));
