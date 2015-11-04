var AIRTIME = (function (AIRTIME) {
    var mod;

    if (AIRTIME.podcast === undefined) {
        AIRTIME.podcast = {};
    }

    mod = AIRTIME.podcast;

    var endpoint = 'rest/podcast/', PodcastTable, $stationPodcastTab;

    /**
     * PodcastController constructor.
     *
     * @param {Object} $scope           angular scope service object
     * @param {Object} $http            angular http service object
     * @param {Object} podcast          podcast metadata object
     * @param {int}    podcast.id       podcast unique identifier
     * @param {string} podcast.title    podcast metadata title
     * @param {Tab}    tab              Tab object the controller is being bootstrapped in
     *
     * @constructor
     */
    function PodcastController($scope, $http, podcast, tab) {
        // We need to pass in the tab object and the episodes table object so we can reference them
        var self = this;

        //We take a podcast object in as a parameter rather fetching the podcast by ID here because
        //when you're creating a new podcast, we already have the object from the result of the POST. We're saving
        //a roundtrip by not fetching it again here.
        $scope.podcast = podcast;
        $scope.tab = tab;
        tab.setName($scope.podcast.title);
        $scope.csrf = jQuery("#csrf").val();
        tab.contents.find("table").attr("id", "podcast_episodes_" + podcast.id);

        /**
         * Override the switchTo function to reload the table when the tab is focused.
         * Should help to reduce the number of cases where the frontend doesn't match the state
         * of the backend (due to automatic ingestion).
         *
         * Note that these cases should already be very few and far between.
         *
         * TODO: make sure this doesn't noticeably slow performance
         * XXX: it's entirely possible that this (in the angular app) is not where we want this function...
         */
        tab.switchTo = function () {
            AIRTIME.tabs.Tab.prototype.switchTo.call(this);
            self.reloadEpisodeTable();
        };

        /**
         * Internal function.
         *
         * Make a PUT request to the server to update the podcast object.
         *
         * @private
         */
        self._updatePodcast = function () {
            $http.put(endpoint + $scope.podcast.id, {csrf_token: $scope.csrf, podcast: $scope.podcast})
                .success(function () {
                    self.episodeTable.getDatatable().fnDraw();
                    AIRTIME.library.podcastDataTable.fnDraw();
                    tab.setName($scope.podcast.title);
                });
        };

        /**
         * For imported podcasts.
         *
         * Save each of the selected episodes and update the podcast object.
         */
        $scope.savePodcast = function () {
            var episodes = self.episodeTable.getSelectedRows();
            // TODO: Should we implement a batch endpoint for this instead?
            jQuery.each(episodes, function () {
                $http.post(endpoint + $scope.podcast.id + '/episodes', {
                    csrf_token: $scope.csrf,
                    episode: this
                });
            });
            self._updatePodcast();
        };

        /**
         * Close the tab and discard any changes made to the podcast data.
         */
        $scope.discard = function () {
            tab.close();
            $scope.podcast = {};
        };

        self.$scope = $scope;
        self.$http = $http;
        self.initialize();

        return self;
    }

    /**
     * Initialize the podcast episode table.
     *
     * @private
     */
    PodcastController.prototype._initTable = function() {
        var self = this,
            $scope = self.$scope;
        // We want to fetch the data statically for imported podcasts because we would need to implement sorting
        // in a very convoluted way on the backend to accommodate the nonexistent rows for uningested episodes
        var params = {
            bServerSide : false,
            sAjaxSource : null,
            // Initialize the table with empty data so we can defer loading
            // If we load sequentially there's a delay before the table appears
            aaData      : {},
            aoColumns   : [
                /* GUID */              { "sTitle" : ""                            , "mDataProp" : "guid"           , "sClass" : "podcast_episodes_guid"        , "bVisible" : false },
                /* Title */             { "sTitle" : $.i18n._("Title")             , "mDataProp" : "title"          , "sClass" : "podcast_episodes_title"       , "sWidth" : "170px" },
                /* Author */            { "sTitle" : $.i18n._("Author")            , "mDataProp" : "author"         , "sClass" : "podcast_episodes_author"      , "sWidth" : "170px" },
                /* Description */       { "sTitle" : $.i18n._("Description")       , "mDataProp" : "description"    , "sClass" : "podcast_episodes_description" , "sWidth" : "300px" },
                /* Link */              { "sTitle" : $.i18n._("Link")              , "mDataProp" : "link"           , "sClass" : "podcast_episodes_link"        , "sWidth" : "170px" },
                /* Publication Date */  { "sTitle" : $.i18n._("Publication Date")  , "mDataProp" : "pub_date"       , "sClass" : "podcast_episodes_pub_date"    , "sWidth" : "170px" }
            ]
        },
            buttons = {};
        self.episodeTable = AIRTIME.podcast.initPodcastEpisodeDatatable($scope.podcast, $scope.tab, params, buttons);
        self.reloadEpisodeTable();
    };

    /**
     * Reload the podcast episode table.
     */
    PodcastController.prototype.reloadEpisodeTable = function() {
        this.episodeTable.reload(this.$scope.podcast.id);
    };

    /**
     * Initialize the controller.
     *
     * Sets up the internal datatable.
     */
    PodcastController.prototype.initialize = function() {
        // TODO: this solves a race condition, but we should look for the root cause
        AIRTIME.tabs.onResize();
        this._initTable();
    };

    /**
     * StationPodcastController constructor.
     *
     * @param {{}} $scope   angular scope service object
     * @param {{}} $http    angular http service object
     * @param {{}} podcast  podcast metadata object
     * @param {Tab} tab     Tab object the controller is being bootstrapped in
     *
     * @constructor
     */
    function StationPodcastController($scope, $http, podcast, tab) {
        // Super call to parent controller
        var self = PodcastController.call(this, $scope, $http, podcast, tab);
        // Store the station podcast tab in module scope so it can be checked if the user clicks the
        // Station Podcast button again - this way we don't have to go back to the server to get the ID.
        $stationPodcastTab = tab;

        /**
         * Override the tab close function to 'unset' the module-scope $stationPodcastTab
         *
         * @override
         */
        tab.close = function () {
            AIRTIME.tabs.Tab.prototype.close.call(this);
            $stationPodcastTab = undefined;
        };


        /**
         * For the station podcast.
         *
         * Update the station podcast object.
         */
        $scope.savePodcast = function () {
            self._updatePodcast();
        };

        self.deleteSelectedEpisodes = function () {
            var episodes = self.episodeTable.getSelectedRows();
            jQuery.each(episodes, function () {
                $http.delete(endpoint + $scope.podcast.id + '/episodes/' + this.id + '?csrf_token=' + $scope.csrf)
                    .success(function () {
                        self.reloadEpisodeTable();
                    });
            });
        };

        self.openSelectedTabEditors = function () {
            var episodes = self.episodeTable.getSelectedRows();
            $.each(episodes, function () {
                var uid = AIRTIME.library.MediaTypeStringEnum.FILE + "_" + this.file_id;
                $.get(baseUrl + "library/edit-file-md/id/" + this.file_id, {format: "json"}, function (json) {
                    AIRTIME.playlist.fileMdEdit(json, uid);
                });
            });
        };
    }

    /**
     * Subclass the PodcastController object.
     *
     * @type {PodcastController}
     */
    StationPodcastController.prototype = Object.create(PodcastController.prototype);

    /**
     * Initialize the Station podcast episode table.
     *
     * @private
     */
    StationPodcastController.prototype._initTable = function() {
        var self = this, $scope = this.$scope,
            buttons = {
                editBtn: {
                    title           : $.i18n._('Edit Metadata'),
                    iconClass       : 'icon-pencil',
                    extraBtnClass   : '',
                    elementId       : '',
                    eventHandlers   : {
                        click: function () {
                            self.openSelectedTabEditors();
                        }
                    }
                },
                deleteBtn: {
                    title           : $.i18n._('Unpublish'),
                    iconClass       : 'icon-trash',
                    extraBtnClass   : 'btn-danger',
                    elementId       : '',
                    eventHandlers   : {
                        click: function () {
                            self.deleteSelectedEpisodes();
                        }
                    }
                }

            },
            params = {
                sAjaxSource : endpoint + $scope.podcast.id + '/episodes',
                aoColumns: [
                    // TODO: it might be dangerous to use CcFiles here? We should alias this instead
                    /* Title */             { "sTitle" : $.i18n._("Title")             , "mDataProp" : "CcFiles.track_title"    , "sClass" : "podcast_episodes_title"       , "sWidth" : "170px" },
                    /* Description */       { "sTitle" : $.i18n._("Description")       , "mDataProp" : "CcFiles.description"    , "sClass" : "podcast_episodes_description" , "sWidth" : "300px" }
                ]
            };

        this.episodeTable = AIRTIME.podcast.initPodcastEpisodeDatatable($scope.podcast, $scope.tab, params, buttons);
    };

    /**
     * Reload the Station podcast episode table.
     *
     * @override
     */
    StationPodcastController.prototype.reloadEpisodeTable = function() {
        this.episodeTable.getDatatable().fnDraw();
    };

    /**
     * AngularJS app for podcast functionality.
     *
     * Bootstrapped for each podcast or Station podcast tab.
     */
    var podcastApp = angular.module('podcast', [])
        .controller('Podcast', ['$scope', '$http', 'podcast', 'tab', PodcastController])
        .controller('StationPodcast', ['$scope', '$http', 'podcast', 'tab', StationPodcastController]);

    /**
     * Implement bulk editing of podcasts in order to accommodate the existing selection
     * mechanisms on the frontend.
     *
     * Bulk methods use a POST request because we need to send data in the request body.
     *
     * @param selectedData the data to operate on
     * @param method HTTP request method type
     * @param callback function to run upon success
     * @private
     */
    function _bulkAction(selectedData, method, callback) {
        var ids = [];
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
            $.post(endpoint + "bulk", {csrf_token: $("#csrf").val(), method: method, ids: ids}, callback);
        }
    }

    /**
     * Bootstrap and initialize the Angular app for the podcast being opened.
     *
     * @param podcast podcast JSON object to pass to the angular app
     * @param tab Tab object the angular app will be initialized in
     * @private
     */
    function _bootstrapAngularApp(podcast, tab) {
        podcastApp.value('podcast', podcast);
        podcastApp.value('tab', tab);
        var wrapper = tab.contents.find(".angular_wrapper");
        angular.bootstrap(wrapper.get(0), ["podcast"]);
    }

    /**
     * Initialization function for a podcast tab.
     * Called when editing one or more podcasts.
     *
     * @param data JSON data returned from the server.
     *             Contains a JSON encoded podcast object and tab
     *             content HTML and has the following form:
     *             {
     *                 podcast: '{
     *                              ...
     *                          }'
     *                 html:    '<...>'
     *             }
     * @private
     */
    function _initAppFromResponse(data) {
        var podcast = JSON.parse(data.podcast),
            uid = AIRTIME.library.MediaTypeStringEnum.PODCAST+"_"+podcast.id,
            tab = AIRTIME.tabs.openTab(data.html, uid, null);
        _bootstrapAngularApp(podcast, tab);
    }

    /**
     * Initialize the PodcastTable subclass object (from Table).
     *
     * Do this in its own function to avoid unnecessary reinitialization of the object.
     *
     * @private
     */
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
        // Since we're sometimes using a static source, define a separate function to fetch and 'reload' the table data
        // We use this when we save the Podcast because we need to flag rows the user is ingesting
        PodcastTable.prototype.reload = function (id) {
            var dt = this._datatable;
            $.get(endpoint + id + '/episodes', function (json) {
                dt.fnClearTable();
                dt.fnAddData(JSON.parse(json));
            });
        };
    }

    /**
     * Create and show the URL dialog for podcast creation.
     */
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

    /**
     * Find the URL in the podcast creation dialog and POST it to the server
     * to store the feed as a Podcast object.
     *
     * FIXME: we should probably be passing the serialized form into this function instead
     */
    mod.addPodcast = function() {
        $.post(endpoint, $("#podcast_url_dialog").find("form").serialize(), function(json) {
            _initAppFromResponse(json);
            $("#podcast_url_dialog").dialog("close");
        });
    };

    /**
     * Open a tab to view and edit the station podcast.
     */
    mod.openStationPodcast = function() {
        if (typeof $stationPodcastTab === 'undefined') {
            $.get(endpoint + 'station', function(json) {
                _initAppFromResponse(json);
            });
        } else if ($stationPodcastTab != AIRTIME.tabs.getActiveTab()) {
            $stationPodcastTab.switchTo();
        }
    };

    /**
     * Create a bulk request to edit all currently selected podcasts.
     */
    mod.editSelectedPodcasts = function() {
        _bulkAction(AIRTIME.library.podcastTableWidget.getSelectedRows(), HTTPMethods.GET, function(json) {
            json.forEach(function(data) {
                _initAppFromResponse(data);
            });
        });
    };

    /**
     * Create a bulk request to delete all currently selected podcasts.
     */
    mod.deleteSelectedPodcasts = function() {
        if (confirm($.i18n._("Are you sure you want to delete the selected podcasts from your library?"))) {
            _bulkAction(AIRTIME.library.podcastTableWidget.getSelectedRows(), HTTPMethods.DELETE, function () {
                AIRTIME.library.podcastDataTable.fnDraw();
            });
        }
    };

    /**
     * Initialize the internal datatable for the podcast editor view to hold episode data passed back from the server.
     *
     * Selection for the internal table represents episodes marked for ingest and is disabled for ingested episodes.
     *
     * @param {Object}  podcast   the podcast data JSON object.
     * @param {Tab}     tab       Tab object the podcast will be opened in
     * @param {Object}  params    JSON object containing datatables parameters to override
     * @param {Object}  buttons   JSON object containing datatables button parameters
     *
     * @returns {*} the created Table object
     */
    mod.initPodcastEpisodeDatatable = function(podcast, tab, params, buttons) {
        buttons = $.extend({
            slideToggle: {
                title           : '',
                iconClass       : 'spl-no-r-margin icon-chevron-up',
                extraBtnClass   : 'toggle-editor-form',
                elementId       : '',
                eventHandlers   : {}
            }
        }, buttons);
        params = $.extend(params,
            {
                oColVis: {
                    sAlign      : 'right',
                    aiExclude   : [0, 1],
                    buttonText  : $.i18n._("Columns"),
                    iOverlayFade: 0,
                    oColReorder : {
                        iFixedColumns: 1  // Checkbox
                    }
                }
            }
        );

        if (typeof PodcastTable === 'undefined') {
            _initPodcastTable();
        }

        // Set up the div with id "podcast_table" as a datatable.
        var podcastEpisodesTableWidget = new PodcastTable(
            tab.contents.find('.podcast_episodes'), // DOM node to create the table inside.
            true,                                   // Enable item selection
            buttons,                                // Toolbar buttons
            params                                  // Datatables overrides.
        );

        podcastEpisodesTableWidget.getDatatable().addTitles("td");
        return podcastEpisodesTableWidget;
    };

    return AIRTIME;
}(AIRTIME || {}));
