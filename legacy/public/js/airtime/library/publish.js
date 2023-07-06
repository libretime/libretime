var AIRTIME = (function (AIRTIME) {
  var mod;

  if (AIRTIME.publish === undefined) {
    AIRTIME.publish = {};
  }

  mod = AIRTIME.publish;

  var endpoint = "rest/media/";
  var dialogUrl = "library/publish-dialog";
  var PUBLISH_APP_NAME = "publish";

  //AngularJS app
  var publishApp = angular
    .module(PUBLISH_APP_NAME, [])
    .controller("Publish", function ($sce, $scope, $http, mediaId, tab) {
      $scope.publishData = {};
      var sourceInterval;

      tab.contents.on("click", "input[type='checkbox']", function () {
        var noSourcesChecked = true;
        $.each(tab.contents.find("input[type='checkbox']"), function () {
          if ($(this).is(":checked")) {
            noSourcesChecked = false;
          }
        });
        tab.contents.find(".publish-btn").prop("disabled", noSourcesChecked);
      });

      function fetchSourceData() {
        var csrfToken = jQuery("#csrf").val();
        $http
          .get(endpoint + mediaId, { csrf_token: csrfToken })
          .success(function (json) {
            $scope.media = json;
            tab.setName($scope.media.track_title);
          });

        // Get an object containing all sources, their translated labels,
        // and their publication state for the file with the given ID
        $http
          .get(endpoint + mediaId + "/publish-sources", {
            csrf_token: csrfToken,
          })
          .success(function (json) {
            $scope.sources = { toPublish: [], published: [] };
            $.each(json, function () {
              if (Math.abs(this.status) == 1) {
                $scope.sources.published.push(this);
              } else {
                $scope.sources.toPublish.push(this);
              }
            });
          });
      }

      function init() {
        fetchSourceData();
        sourceInterval = setInterval(function () {
          fetchSourceData();
        }, 5000);

        tab.assignOnCloseHandler(function () {
          clearInterval(sourceInterval);
          $scope.$destroy();
        });
      }

      $scope.openEditDialog = function () {
        var uid = AIRTIME.library.MediaTypeStringEnum.FILE + "_" + mediaId;
        $.get(
          baseUrl + "library/edit-file-md/id/" + mediaId,
          { format: "json" },
          function (json) {
            AIRTIME.playlist.fileMdEdit(json, uid);
          },
        );
      };

      $scope.publish = function () {
        var data = {};
        jQuery.each($scope.publishData, function (k, v) {
          if (v) {
            data[k] = "publish"; // FIXME: should be more robust
          }
        });

        if (data && Object.keys(data).length > 0) {
          $http
            .put(endpoint + mediaId + "/publish", {
              csrf_token: jQuery("#csrf").val(),
              sources: data,
            })
            .success(function () {
              tab.contents.find(".publish-btn").prop("disabled", true);
              fetchSourceData();
              $scope.publishData = {}; // Reset the publishData in case the user publishes
              // and unpublishes without closing the tab
            });
        }
      };

      $scope.remove = function (source) {
        var data = {};
        data[source] = "unpublish"; // FIXME: should be more robust
        $http
          .put(endpoint + mediaId + "/publish", {
            csrf_token: jQuery("#csrf").val(),
            sources: data,
          })
          .success(function () {
            fetchSourceData();
          });
      };

      $scope.discard = function () {
        tab.close();
        $scope.media = {};
      };

      init();
    });

  /*
    var selected = $("#podcast_table").find(".selected"),
        ids = [];
    var selectedData = AIRTIME.library.podcastTableWidget.getSelectedRows();
    selectedData.forEach(function(el) {
        ids.push(el.id);
    });*/

  function _bootstrapAngularApp(mediaId, tab) {
    publishApp.value("mediaId", mediaId);
    publishApp.value("tab", tab);
    var wrapper = AIRTIME.tabs.getActiveTab().contents.find(".angular_wrapper");
    angular.bootstrap(wrapper.get(0), [PUBLISH_APP_NAME]);
  }

  mod.publishSelectedTracks = function () {
    /*
        _bulkAction("GET", function(json) {
            json.forEach(function(el) {
                var uid = AIRTIME.library.MediaTypeStringEnum.FILE+"_"+el.id;
                var mediaId = el.id;

                $http.get(dialogUrl, { csrf_token: jQuery("#csrf").val() })
                    .success(function(json) {

                        AIRTIME.tabs.openTab(json, uid, null);
                        _bootstrapAngularApp(mediaId);
                    });
            });
        });*/
  };

  mod.openPublishDialog = function (mediaId) {
    jQuery
      .get(dialogUrl, { csrf_token: jQuery("#csrf").val() })
      .success(function (html) {
        var tab = AIRTIME.tabs.openTab(
          html,
          PUBLISH_APP_NAME + "_" + mediaId,
          null,
        );
        _bootstrapAngularApp(mediaId, tab);
      });

    /*
        _bulkAction("GET", function(json) {
            json.forEach(function(el) {
                var uid = AIRTIME.library.MediaTypeStringEnum.FILE+"_"+el.id;

                $http.get(dialogUrl, { csrf_token: jQuery("#csrf").val() })
                    .success(function(json) {

                        AIRTIME.tabs.openTab(json, uid, null);
                        _bootstrapAngularApp(el.media);
                    });
            });
        });*/
  };

  return AIRTIME;
})(AIRTIME || {});
