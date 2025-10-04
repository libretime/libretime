var AIRTIME = (function (AIRTIME) {
  var mod;

  if (AIRTIME.podcast === undefined) {
    AIRTIME.podcast = {};
  }

  mod = AIRTIME.podcast;
  mod.episodeTables = {};

  var endpoint = "/rest/podcast/",
    PodcastEpisodeTable;

  /**
   * PodcastController constructor.
   *
   * @param {angular.scope}   $scope           angular scope service object
   * @param {angular.http}    $http            angular http service object
   * @param {Object}          podcast          podcast metadata object
   * @param {int}             podcast.id       podcast unique identifier
   * @param {string}          podcast.title    podcast metadata title
   * @param {Tab}             tab              Tab object the controller is being bootstrapped in
   *
   * @constructor
   */
  function PodcastController($scope, $http, podcast, tab) {
    // We need to pass in the tab object and the episodes table object so we can reference them
    var self = this,
      view = tab ? tab.contents : $(document);

    //We take a podcast object in as a parameter rather fetching the podcast by ID here because
    //when you're creating a new podcast, we already have the object from the result of the POST. We're saving
    //a roundtrip by not fetching it again here.
    $scope.podcast = podcast;
    $scope.tab = tab;
    $scope.csrf = jQuery("#csrf").val();
    view.find("table").attr("id", "podcast_episodes_" + podcast.id);

    self.onSaveCallback = function () {
      var successMsg = $(".active-tab .btn-toolbar .success");
      successMsg.text($.i18n._("Podcast settings saved")).show("fast");
      setTimeout(function () {
        successMsg.hide("fast");
      }, 5000);
      AIRTIME.library.podcastDataTable.fnDraw();
      self.$scope.tab.setName(self.$scope.podcast.title);
    };

    /**
     * Save and update the podcast object.
     */
    $scope.savePodcast = function () {
      $http
        .put(endpoint + $scope.podcast.id, {
          csrf_token: $scope.csrf,
          podcast: $scope.podcast,
        })
        .success(function () {
          self.onSaveCallback();
        });
    };

    /**
     * Generate a smartblock and playlist for this smartblock.
     */
    $scope.createSmartblock = function () {
      // send smarblock creation instruction to API
      $.post(
        endpoint + "smartblock",
        {
          csrf_token: $("#csrf").val(),
          id: $scope.podcast.id,
          title: $scope.podcast.title,
        },
        function () {
          // show success message
          var successMsg = $(".active-tab .pc-sb-success");
          successMsg.show("fast");
          setTimeout(function () {
            successMsg.hide("fast");
          }, 5000);

          // save podcast but do not display notification beside save button below
          $http
            .put(endpoint + $scope.podcast.id, {
              csrf_token: $scope.csrf,
              podcast: $scope.podcast,
            })
            .success(function () {
              AIRTIME.library.podcastDataTable.fnDraw();
              self.$scope.tab.setName(self.$scope.podcast.title);
            });

          // redraw list of smartblocks just in case they have it visible on the left
          dt = $('table[id="library_display"]').dataTable();
          dt.fnStandingRedraw();
        },
      );
    };
    /**
     * Close the tab and discard any changes made to the podcast data.
     */
    $scope.discard = function () {
      !tab || tab.close();
      $scope.podcast = {};
    };

    self.$scope = $scope;
    self.$http = $http;
    self.initialize();

    return self;
  }

  /**
   * Initialize the controller.
   *
   * Sets up the internal datatable.
   */
  PodcastController.prototype.initialize = function () {
    var self = this;
    // TODO: this solves a race condition, but we should look for the root cause
    AIRTIME.tabs.onResize();
    self.$scope.tab.setName(self.$scope.podcast.title);
    // Add an onclose hook to the tab to remove the table object and the
    // import listener so we don't cause memory leaks.
    var podcastId = self.$scope.podcast.id.toString();
    self.$scope.tab.assignOnCloseHandler(function () {
      if (AIRTIME.podcast.episodeTables.hasOwnProperty(podcastId)) {
        AIRTIME.podcast.episodeTables[podcastId].destroy();
        AIRTIME.podcast.episodeTables[podcastId] = null;
        self.$scope.$destroy();
      }
    });
  };

  /**
   * StationPodcastController constructor.
   *
   * @param {angular.scope}   $scope          angular scope service object
   * @param {angular.http}    $http           angular http service object
   * @param {Object}          podcast         podcast metadata object
   * @param {int}             podcast.id      podcast unique identifier
   * @param {string}          podcast.title   podcast metadata title
   * @param {Tab}             tab             Tab object the controller is being bootstrapped in
   *
   * @constructor
   */
  function StationPodcastController($scope, $http, podcast, tab) {
    // Super call to parent controller
    PodcastController.call(this, $scope, $http, podcast, tab);
    this.onSaveCallback = function () {
      $http({
        method: "POST",
        url: "/preference/station-podcast-settings",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        data: {
          stationPodcastPrivacy: $("#podcast-settings")
            .find("input:checked")
            .val(),
        },
      }).success(function (data) {
        jQuery.extend($scope.podcast, data);
        $(".success")
          .text($.i18n._("Podcast settings saved"))
          .slideDown("fast");
        setTimeout(function () {
          $(".success").slideUp("fast");
        }, 2000);
      });
    };
    return this;
  }

  /**
   * Subclass the PodcastController object.
   *
   * @type {PodcastController}
   */
  StationPodcastController.prototype = Object.create(
    PodcastController.prototype,
  );

  /**
   * Remove the selected episodes from the station podcast feed.
   */
  StationPodcastController.prototype.unpublishSelectedEpisodes = function () {
    var self = this,
      $scope = self.$scope,
      episodes = self.episodeTable.getSelectedRows();
    jQuery.each(episodes, function () {
      self.$http
        .delete(
          endpoint +
            $scope.podcast.id +
            "/episodes/" +
            this.id +
            "?csrf_token=" +
            $scope.csrf,
        )
        .success(function () {
          self.reloadEpisodeTable();
        });
    });
  };

  /**
   * Initialize the Station podcast episode table.
   *
   * @private
   */
  StationPodcastController.prototype._initTable = function () {
    var self = this,
      $scope = this.$scope,
      buttons = {
        deleteBtn: {
          title: $.i18n._("Unpublish"),
          iconClass: "icon-trash",
          extraBtnClass: "btn-danger",
          elementId: "",
          eventHandlers: {
            click: self.unpublishSelectedEpisodes.bind(self),
          },
          validateConstraints: function () {
            return this.getSelectedRows().length >= 1;
          },
        },
      },
      params = {
        sAjaxSource: endpoint + $scope.podcast.id + "/episodes",
        aoColumns: [
          // TODO: it might be wrong to use CcFiles here? We should alias this instead
          /* Title */ {
            sTitle: $.i18n._("Title"),
            mDataProp: "CcFiles.track_title",
            sClass: "podcast_episodes_title",
            sWidth: "170px",
          },
          /* Description */ {
            sTitle: $.i18n._("Description"),
            mDataProp: "CcFiles.description",
            sClass: "podcast_episodes_description",
            sWidth: "300px",
          },
        ],
      };

    this.episodeTable = AIRTIME.podcast.initPodcastEpisodeDatatable(
      $(".podcast_episodes"),
      params,
      buttons,
      {
        hideIngestCheckboxes: true,
        podcastId: $scope.podcast.id,
        emptyPlaceholder: {
          iconClass: "icon-white icon-th-list",
          html:
            $.i18n._("You haven't published any episodes!") +
            "<br/>" +
            $.i18n._(
              "You can publish your uploaded content from the 'Tracks' view.",
            ) +
            "<br/><a target='_parent' href='/showbuilder#tracks'>" +
            $.i18n._("Try it now") +
            "</a>",
        },
      },
    );

    mod.stationPodcastTable = this.episodeTable.getDatatable();
  };

  /**
   * Initialize the Station podcast.
   */
  StationPodcastController.prototype.initialize = function () {
    // We want to override the default tab name behaviour and use "My Podcast" for clarity
    this._initTable();
  };

  /**
   * @override
   *
   * Reload the Station podcast episode table.
   */
  StationPodcastController.prototype.reloadEpisodeTable = function () {
    this.episodeTable.getDatatable().fnDraw();
  };

  /**
   * AngularJS app for podcast functionality.
   *
   * Bootstrapped for each podcast or Station podcast tab.
   */
  mod.podcastApp = angular
    .module("podcast", [])
    .controller("Podcast", [
      "$scope",
      "$http",
      "podcast",
      "tab",
      PodcastController,
    ])
    .controller("StationPodcast", [
      "$scope",
      "$http",
      "podcast",
      "tab",
      StationPodcastController,
    ]);

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
    selectedData.forEach(function (el) {
      var uid = AIRTIME.library.MediaTypeStringEnum.PODCAST + "_" + el.id,
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
      $.post(
        endpoint + "bulk",
        { csrf_token: $("#csrf").val(), method: method, ids: ids },
        callback,
      );
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
    mod.podcastApp.value("podcast", podcast);
    mod.podcastApp.value("tab", tab);
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
      uid = AIRTIME.library.MediaTypeStringEnum.PODCAST + "_" + podcast.id,
      tab = AIRTIME.tabs.openTab(data.html, uid, null);
    _bootstrapAngularApp(podcast, tab);

    $(".album_names.help_icon").qtip({
      content: {
        text: $.i18n._(
          'Overwrite downloaded podcast episodes\' "Album" and "Creator" metadata tag with the Podcast Name specified above and set the track title to the title of the Podcast Episode. This album name can then be used as a search criteria by a smartblock. ',
        ),
      },
      hide: {
        delay: 500,
        fixed: true,
      },
      style: {
        border: {
          width: 0,
          radius: 4,
        },
        classes: "ui-tooltip-dark ui-tooltip-rounded",
      },
      position: {
        my: "left bottom",
        at: "right center",
      },
    });

    // Add podcast episode table in right-side panel below podcast edit form
    var episodeTable =
      AIRTIME.podcast.initPodcastEpisodeDatatableWithButtonEvents(
        $("#podcast_episodes_" + podcast.id),
        podcast.id.toString(),
      );
    episodeTable.reload(podcast.id);
    episodeTable.clearSelection();
  }

  /**
   * Initialize the PodcastTable subclass object (from Table).
   *
   * Do this in its own function to avoid unnecessary reinitialization of the object.
   *
   * @private
   */
  function _initPodcastEpisodeTable() {
    PodcastEpisodeTable = function (
      wrapperDOMNode,
      bItemSelection,
      toolbarButtons,
      dataTablesOptions,
      config,
    ) {
      this.config = config; // Internal configuration object
      this._setupImportListener();
      // Call the superconstructor
      return AIRTIME.widgets.Table.call(
        this,
        wrapperDOMNode,
        bItemSelection,
        toolbarButtons,
        dataTablesOptions,
        config.emptyPlaceholder,
      );
    }; // Subclass AIRTIME.widgets.Table
    PodcastEpisodeTable.prototype = Object.create(
      AIRTIME.widgets.Table.prototype,
    );
    PodcastEpisodeTable.prototype.constructor = PodcastEpisodeTable;
    PodcastEpisodeTable.prototype._SELECTORS = Object.freeze({
      SELECTION_CHECKBOX: ".airtime_table_checkbox:has(input)",
      SELECTION_TABLE_ROW: "tr:has(td.airtime_table_checkbox > input)",
    });

    /**
     * @override
     *
     * Override the checkbox delegate function in the Table object to change
     * the row's checkbox and import status columns depending on the status
     * of the episode (unimported: 0, imported: 1, pending import: -1).
     *
     * @param rowData
     * @param callType
     * @param dataToSave
     *
     * @returns {string}
     * @private
     */
    PodcastEpisodeTable.prototype._datatablesCheckboxDataDelegate = function (
      rowData,
      callType,
      dataToSave,
    ) {
      var defaultIcon = "<span class='icon icon-circle-arrow-down'></span>",
        importIcon =
          "<span class='sp-checked-icon checked-icon imported-flag'></span>",
        pendingIcon = "<span class='loading-icon'></span>";
      if (
        this.config.hideIngestCheckboxes &&
        rowData.ingested &&
        rowData.ingested != 0
      ) {
        return rowData.ingested > 0 ? importIcon : pendingIcon;
      }
      rowData.importIcon =
        rowData.ingested != 0
          ? rowData.ingested > 0
            ? importIcon
            : pendingIcon
          : defaultIcon;
      return AIRTIME.widgets.Table.prototype._datatablesCheckboxDataDelegate.call(
        this,
        rowData,
        callType,
        dataToSave,
      );
    };

    /**
     * Reload the episode table.
     * Since we're sometimes using a static source, define a separate function to fetch and reload the table data.
     * We use this when we save the Podcast because we need to flag rows the user is ingesting.
     *
     * @param [id] optional podcast identifier
     */
    PodcastEpisodeTable.prototype.reload = function (id) {
      // When using static source data, we instantiate an empty table
      // and pass this function the ID of the podcast we want to display.
      if (id) this.config.podcastId = id;
      var self = this,
        dt = self._datatable;
      dt.block({
        message: "",
        theme: true,
        applyPlatformOpacityRules: false,
      });
      $.get(endpoint + self.config.podcastId + "/episodes", function (json) {
        dt.fnClearTable(false);
        dt.fnAddData(JSON.parse(json));
      }).done(function () {
        dt.unblock();
      });
    };

    /**
     * Setup an interval that checks for any pending imports and reloads
     * the table once imports are finished.
     *
     * TODO: remember selection; make this more elegant?
     *
     * @private
     */
    PodcastEpisodeTable.prototype._setupImportListener = function () {
      var self = this;
      self.importListener = setInterval(function () {
        var podcastId = self.config.podcastId,
          pendingRows = [];
        if (!podcastId) return false;
        var dt = self.getDatatable(),
          data = dt.fnGetData();
        // Iterate over the table data to check for any rows pending import
        $.each(data, function () {
          if (this.ingested == -1) {
            pendingRows.push(this.guid);
          }
        });
        if (pendingRows.length > 0) {
          // Fetch the table data if there are pending rows,
          // then check if any of the pending rows have
          // succeeded or failed before reloading the table.
          $.get(endpoint + podcastId + "/episodes", function (json) {
            data = JSON.parse(json);
            var delta = false;
            $.each(data, function () {
              var idx = pendingRows.indexOf(this.guid);
              if (idx > -1 && this.ingested != -1) {
                delta = true;
                pendingRows.slice(idx, 0);
              }
            });
            if (delta) {
              // Has there been a change?
              // We already have the data, so there's no reason to call
              // reload() here; this also provides a smoother transition
              dt.fnClearTable(false);
              dt.fnAddData(data);
            }
          });
        }
      }, 5000); // Run every 5 seconds
    };

    /**
     * Explicit destructor
     */
    PodcastEpisodeTable.prototype.destroy = function () {
      clearInterval(this.importListener);
    };
  }

  /**
   * Create and show the URL dialog for podcast creation.
   */
  mod.createUrlDialog = function () {
    $.get("/render/podcast-url-dialog", function (json) {
      $(document.body).append(json.html);
      $("#podcast_url_dialog").dialog({
        title: $.i18n._("Add New Podcast"),
        resizable: false,
        modal: true,
        width: "450px",
        height: 129,
        close: function () {
          $(this).remove();
        },
      });
    });
  };

  /**
   * Find the URL in the podcast creation dialog and POST it to the server
   * to store the feed as a Podcast object.
   *
   * FIXME: we should probably be passing the serialized form into this function instead
   */
  mod.addPodcast = function () {
    $.post(
      endpoint,
      $("#podcast_url_dialog").find("form").serialize(),
      function (json) {
        // Refresh left-side library pane to show newly created podcast
        AIRTIME.library.podcastDataTable.fnDraw();

        // close modal
        $("#podcast_url_dialog").dialog("close");

        // open newly created podcast in right-side edit pane
        _initAppFromResponse(json);
      },
    ).fail(function (e) {
      var errors = $("#podcast_url_dialog").find(".errors");
      errors.show(200).text(e.responseText);
      setTimeout(function () {
        errors.hide(200);
      }, 3000);
    });
  };

  /**
   * Create a bulk request to edit all currently selected podcasts.
   */
  mod.editSelectedPodcasts = function () {
    _bulkAction(
      AIRTIME.library.podcastTableWidget.getSelectedRows(),
      HTTPMethods.GET,
      function (json) {
        json.forEach(function (data) {
          _initAppFromResponse(data);
        });
      },
    );
  };

  /**
   * Create a bulk request to delete all currently selected podcasts.
   */
  mod.deleteSelectedPodcasts = function () {
    if (
      confirm(
        $.i18n._(
          "Are you sure you want to delete the selected podcasts from your library?",
        ),
      )
    ) {
      _bulkAction(
        AIRTIME.library.podcastTableWidget.getSelectedRows(),
        HTTPMethods.DELETE,
        function () {
          AIRTIME.library.podcastDataTable.fnDraw();
        },
      );
    }
  };

  /**
   * Open metadata editor tabs for each of the selected episodes.
   *
   * @param {Array} episodes the array of selected episodes
   */
  mod.editSelectedEpisodes = function (episodes) {
    $.each(episodes, function () {
      if (this.file && !Object.keys(this.file).length > 0) return false;
      var fileId = this.file_id || this.file.id,
        uid = AIRTIME.library.MediaTypeStringEnum.FILE + "_" + fileId;
      $.get(
        baseUrl + "library/edit-file-md/id/" + fileId,
        { format: "json" },
        function (json) {
          AIRTIME.playlist.fileMdEdit(json, uid);
        },
      );
    });
  };

  /**
   * Import one or more podcast episodes.
   *
   * @param {Array} episodes          array of episode data to be imported
   * @param {PodcastEpisodeTable} dt  PodcastEpisode table containing the data
   */
  mod.importSelectedEpisodes = function (episodes, dt) {
    $.each(episodes, function () {
      // remainingDiskSpace is defined in layout.phtml
      if (this.enclosure.length > remainingDiskSpace) {
        alert("You don't have enough disk space to import " + this.title);
        return false;
      }
      if (this.file && Object.keys(this.file).length > 0) return false;
      $.post(
        endpoint + this.podcast_id + "/episodes",
        JSON.stringify({
          csrf_token: $("#csrf").val(),
          episode: this,
        }),
        function () {
          dt.reload(dt.config.podcastId);
        },
      );

      remainingDiskSpace -= this.enclosure.length;
    });

    dt.clearSelection();
  };

  /**
   * Delete one or more podcast episodes.
   *
   * @param {id:string, type:string}[] data  Array of data objects to be deleted
   * @param podcastId:string
   */
  mod.deleteSelectedEpisodes = function (data, podcastId) {
    $.each(data, function () {
      AIRTIME.library.fnDeleteItems(data, podcastId);
    });
  };

  /**
   * Initialize the podcast episode table with working buttons
   */
  mod.initPodcastEpisodeDatatableWithButtonEvents = function (
    domNode,
    podcastId,
  ) {
    /**
     * Check the import statuses of each selected episode to see which
     * buttons should be enabled or disabled.
     *
     * @param shouldBeImported whether or not the selected item(s)
     *        should be imported to obtain a valid result.
     *
     * @returns {boolean} true if all selected episodes are valid and
     *                    the button should be enabled, otherwise false.
     */
    var checkSelectedEpisodeImportStatus = function (shouldBeImported) {
      var selected = this.getSelectedRows(),
        isValid = true;
      if (selected.length == 0) return false;
      $.each(selected, function () {
        if (this.ingested < 0) isValid = false;
        var isImported = !$.isEmptyObject(this.file);
        if (shouldBeImported ? !isImported : isImported) {
          isValid = false;
        }
      });
      return isValid;
    };

    // Setup the default buttons (new, edit, delete)
    podcastEpisodeButtons = AIRTIME.widgets.Table.getStandardToolbarButtons();
    $.extend(
      true,
      podcastEpisodeButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.NEW],
      {
        title: $.i18n._("Import"),
        eventHandlers: {
          click: function () {
            var episodes = mod.episodeTables[podcastId].getSelectedRows();
            AIRTIME.podcast.importSelectedEpisodes(
              episodes,
              mod.episodeTables[podcastId],
            );
          },
        },
        validateConstraints: function () {
          return checkSelectedEpisodeImportStatus.call(this, false);
        },
      },
    );
    $.extend(
      true,
      podcastEpisodeButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.EDIT],
      {
        eventHandlers: {
          click: function () {
            var episodes = mod.episodeTables[podcastId].getSelectedRows();
            AIRTIME.podcast.editSelectedEpisodes(episodes);
          },
        },
        validateConstraints: function () {
          return checkSelectedEpisodeImportStatus.call(this, true);
        },
      },
    );
    $.extend(
      true,
      podcastEpisodeButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.DELETE],
      {
        eventHandlers: {
          click: function () {
            var data = [],
              episodes = mod.episodeTables[podcastId].getSelectedRows();
            $.each(episodes, function () {
              data.push({ id: this.file.id, type: this.file.ftype });
            });
            AIRTIME.podcast.deleteSelectedEpisodes(data, podcastId);
          },
        },
        validateConstraints: function () {
          return checkSelectedEpisodeImportStatus.call(this, true);
        },
      },
    );

    // Reassign these because integer keys take precedence in iteration order - we want to order based on insertion
    // FIXME: this is a pretty flimsy way to try to set up iteration order (possibly not xbrowser compatible?)
    podcastEpisodeButtons = {
      newBtn:
        podcastEpisodeButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.NEW],
      editBtn:
        podcastEpisodeButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.EDIT],
      delBtn:
        podcastEpisodeButtons[
          AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.DELETE
        ],
    };

    $.extend(true, podcastEpisodeButtons, {
      addToScheduleBtn: {
        title: $.i18n._("Add to Schedule"),
        iconClass: "icon-plus",
        extraBtnClass: "btn-small",
        elementId: "",
        eventHandlers: {
          click: function () {
            var data = [],
              selected =
                AIRTIME.podcast.episodeTables[podcastId].getSelectedRows();
            $.each(selected, function () {
              data.push(this.file);
            });
            AIRTIME.library.addToSchedule(data);
          },
        },
        validateConstraints: function () {
          // TODO: change text + behaviour for playlists, smart blocks, etc.
          return checkSelectedEpisodeImportStatus.call(this, true);
        },
      },
      viewDescBtn: {
        title: $.i18n._("View"),
        iconClass: "icon-globe",
        extraBtnClass: "btn-small",
        elementId: "",
        eventHandlers: {
          click: function () {
            AIRTIME.library.openPodcastEpisodeDialog(podcastId);
          },
        },
        validateConstraints: function () {
          return this.getSelectedRows().length == 1;
        },
      },
    });

    mod.episodeTables[podcastId] = AIRTIME.podcast.initPodcastEpisodeDatatable(
      domNode,
      {},
      podcastEpisodeButtons,
      {
        hideIngestCheckboxes: false,
        emptyPlaceholder: {
          iconClass: "icon-white icon-th-list",
          html:
            $.i18n._("This podcast doesn't have any episodes!") +
            "<br/>" +
            $.i18n._(
              "Make sure the RSS feed contains audio items (with enclosure tags).",
            ) +
            "<br/><a target='_blank' href='http://www.apple.com/ca/itunes/podcasts/specs.html'>" +
            $.i18n._("Learn about podcasts") +
            "</a>",
        },
      },
    );

    mod.podcastEpisodeDataTable = mod.episodeTables[podcastId].getDatatable();
    mod.episodeTables[podcastId].assignDblClickHandler(function () {
      var data = mod.podcastEpisodeDataTable.fnGetData(this);
      if (!$.isEmptyObject(data.file)) {
        mod.dblClickAdd(data.file, data.file.ftype);
      } else {
        if (data.ingested >= 0) {
          // Only import if the file isn't pending
          AIRTIME.podcast.importSelectedEpisodes(
            [data],
            mod.episodeTables[podcastId],
          );
        }
      }
    });

    return mod.episodeTables[podcastId];
  };

  /**
   * Initialize the internal datatable for the podcast editor view to hold episode data passed back from the server.
   *
   * Selection for the internal table represents episodes marked for ingest and is disabled for ingested episodes.
   *
   * @param {jQuery}  domNode   the jQuery DOM node to create the table inside.
   * @param {Object}  buttons   JSON object containing datatables button parameters
   * @param {Object}  config    JSON object containing internal PodcastEpisodeTable parameters
   * @param {boolean} config.hideIngestCheckboxes flag denoting whether or not to hide checkboxes for ingested items
   *
   * @returns {Table} the created Table object
   */
  mod.initPodcastEpisodeDatatable = function (
    domNode,
    params,
    buttons,
    config,
  ) {
    params = $.extend(true, params, {
      aoColumns: [
        /* GUID */ {
          sTitle: "",
          mDataProp: "guid",
          sClass: "podcast_episodes_guid",
          bVisible: false,
        },
        /* Ingested */ {
          sTitle: $.i18n._("Imported?"),
          mDataProp: "importIcon",
          sClass: "podcast_episodes_imported",
          sWidth: "120px",
        },
        /* Title */ {
          sTitle: $.i18n._("Title"),
          mDataProp: "title",
          sClass: "podcast_episodes_title",
          sWidth: "170px",
        },
        /* Author */ {
          sTitle: $.i18n._("Author"),
          mDataProp: "author",
          sClass: "podcast_episodes_author",
          sWidth: "170px",
        },
        /* Description */ {
          sTitle: $.i18n._("Description"),
          mDataProp: "description",
          sClass: "podcast_episodes_description",
          sWidth: "300px",
        },
        /* Link */ {
          sTitle: $.i18n._("Link"),
          mDataProp: "link",
          sClass: "podcast_episodes_link",
          sWidth: "170px",
        },
        /* Publication Date */ {
          sTitle: $.i18n._("Publication Date"),
          mDataProp: "pub_date",
          sClass: "podcast_episodes_pub_date",
          sWidth: "170px",
        },
      ],
      bServerSide: false,
      sAjaxSource: null,
      // Initialize the table with empty data so we can defer loading
      // If we load sequentially there's a delay before the table appears
      aaData: {},
      oColVis: {
        buttonText: $.i18n._("Columns"),
        iOverlayFade: 0,
        aiExclude: [0, 1, 2],
      },
      bDeferRender: true,
      oColReorder: {
        iFixedColumns: 3, // Checkbox + imported
      },
      fnCreatedRow: function (nRow, aData, iDataIndex) {
        var self = this;
        if (aData.file && Object.keys(aData.file).length > 0) {
          $(nRow).draggable({
            helper: function () {
              var $row = $(this),
                data = self._datatable.fnGetData(nRow);
              $row.data("aData", data.file);
              self.selectRow(
                this,
                data,
                self.SELECTION_MODE.SINGLE,
                $row.index(),
              );
              var selected = self.getSelectedRows().length,
                container,
                width = self._$wrapperDOMNode
                  .closest(".dataTables_wrapper")
                  .outerWidth(),
                message;

              message = sprintf(
                $.i18n._(selected > 1 ? "Adding %s Items" : "Adding %s Item"),
                selected,
              );
              container = $("<div/>")
                .attr("id", "draggingContainer")
                .append("<tr/>")
                .find("tr")
                .append("<td/>")
                .find("td")
                .attr("colspan", 100)
                .width(width)
                .css("max-width", "none")
                .addClass("ui-state-highlight")
                .append(message)
                .end()
                .end();

              return container;
            },
            tolerance: "pointer",
            cursor: "move",
            cursorAt: {
              top: 20,
              left: Math.floor(self._datatable.outerWidth() / 2),
            },
            distance: 25, // min-distance for dragging
            connectToSortable: $(
              "#show_builder_table, .active-tab .spl_sortable",
            ),
          });
        }
      },
      fnDrawCallback: function () {
        AIRTIME.library.drawEmptyPlaceholder(this);
        // Hide the processing div
        var dt = this.getDatatable();
        !dt ||
          dt
            .closest(".dataTables_wrapper")
            .find(".dataTables_processing")
            .css("visibility", "hidden");
      },
    });

    if (typeof PodcastEpisodeTable === "undefined") {
      _initPodcastEpisodeTable();
    }

    var podcastEpisodeTableObj = new PodcastEpisodeTable(
      domNode, // DOM node to create the table inside.
      true, // Enable item selection
      buttons, // Toolbar buttons
      params, // Datatables overrides.
      config, // Internal config
    );

    podcastEpisodeTableObj.getDatatable().addTitles("td");
    return podcastEpisodeTableObj;
  };

  return AIRTIME;
})(AIRTIME || {});

$(document).ready(function () {
  $(document).on("submit", "#podcast_url_form", function (e) {
    e.preventDefault();
    AIRTIME.podcast.addPodcast();
  });
});
