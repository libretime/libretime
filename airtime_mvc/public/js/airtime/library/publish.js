
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
        .controller('RestController', function($scope, $http, mediaId) {

            $http.get(endpoint + mediaId, { csrf_token: jQuery("#csrf").val() })
                .success(function(json) {
                    console.log(json);
                    $scope.media = json;
                    AIRTIME.tabs.setActiveTabName($scope.media.track_title);
                });

            $scope.save = function() {
                $http.put(endpoint + $scope.media.id, { csrf_token: jQuery("#csrf").val(), media: $scope.media })
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

    function _bootstrapAngularApp(mediaId) {
        publishApp.value('mediaId', mediaId);
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

                var jsonWrapper = {'html' : html}; //Silly wrapper to make the openTab function happy
                AIRTIME.tabs.openTab(jsonWrapper, mediaId);
                _bootstrapAngularApp(mediaId);
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
