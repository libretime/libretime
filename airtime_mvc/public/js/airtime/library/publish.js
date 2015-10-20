
var AIRTIME = (function (AIRTIME) {
    var mod;

    if (AIRTIME.publish === undefined) {
        AIRTIME.publish = {};
    }

    mod = AIRTIME.publish;

    var endpoint = 'rest/media/';
    var dialogUrl = 'library/publish-dialog';
    var PUBLISH_APP_NAME = 'publish';


    //AngularJS app
    var publishApp = angular.module(PUBLISH_APP_NAME, [])
        .controller('RestController', function($scope, $http, mediaId, tab) {

            $scope.publishSources = {};

            $http.get(endpoint + mediaId, { csrf_token: jQuery("#csrf").val() })
                .success(function(json) {
                    console.log(json);
                    $scope.media = json;
                    tab.setName($scope.media.track_title);
                });

            $scope.publish = function() {
                var sources = {};
                $.each($scope.publishSources, function(k, v) {
                    if (v) sources[k] = 'publish';  // Tentative TODO: decide on a robust implementation
                });
                $http.put(endpoint + $scope.media.id + '/publish', { csrf_token: jQuery("#csrf").val(), sources: sources })
                    .success(function() {
                        // TODO
                    });
            };

            $scope.discard = function() {
                AIRTIME.tabs.getActiveTab().close();
                $scope.media = {};
            };
        });


    /*
    var selected = $("#podcast_table").find(".selected"),
        ids = [];
    var selectedData = AIRTIME.library.podcastTableWidget.getSelectedRows();
    selectedData.forEach(function(el) {
        ids.push(el.id);
    });*/

    function _bootstrapAngularApp(mediaId, tab) {
        publishApp.value('mediaId', mediaId);
        publishApp.value('tab', tab);
        var wrapper = AIRTIME.tabs.getActiveTab().contents.find(".editor_pane_wrapper");
        wrapper.attr("ng-controller", "RestController");
        angular.bootstrap(wrapper.get(0), [PUBLISH_APP_NAME]);
    }

    mod.publishSelectedTracks = function() {
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

    mod.publishTrack = function(mediaId) {

        jQuery.get(dialogUrl, { csrf_token: jQuery("#csrf").val() })
            .success(function(html) {
                var tab = AIRTIME.tabs.openTab(html, mediaId, null);
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
}(AIRTIME || {}));
