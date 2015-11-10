<?php

class Application_Service_PublishService {

    /**
     * @var array map of arbitrary source names to descriptive labels
     */
    private static $SOURCES = array(
        "soundcloud"        => SOUNDCLOUD,
        "station_podcast"   => "Station Podcast"
    );

    /**
     * @var array map of arbitrary source names to functions that return
     *            their publication state (true = published)
     */
    private static $SOURCE_FUNCTIONS = array(
        "soundcloud"        => "getSoundCloudPublishStatus",
        "station_podcast"   => "getStationPodcastPublishStatus"
    );

    /**
     * Publish or remove the file with the given file ID from the services
     * specified in the request data (ie. SoundCloud, the station podcast)
     *
     * @param int $fileId   ID of the file to be published
     * @param array $data   request data containing what services to publish to
     */
    public static function publish($fileId, $data) {
        foreach ($data as $k => $v) {
            $service = PublishServiceFactory::getService($k);
            $service->$v($fileId);
        }
    }

    /**
     * For the file with the given ID, check external sources and generate
     * an array of source states and labels.
     *
     * Sources to which the file has been published should be passed back in a
     * "published" array, while sources to which the file has not been published
     * should be passed back in a "toPublish" array.
     *
     * @param int $fileId the ID of the file to check
     *
     * @return array array containing published and toPublish arrays. Has the form
     *               [
     *                   "toPublish" => [
     *                                      "source" => "label",
     *                                      ...
     *                                  ]
     *                   "published" => [
     *                                      "source" => "label",
     *                                      ...
     *                                  ]
     *               ]
     */
    public static function getSourceLists($fileId) {
        $sources = array();
        foreach (self::$SOURCES as $source => $label) {
            $fn = self::$SOURCE_FUNCTIONS[$source];
            // Should be in a ternary but PHP doesn't play nice
            $status = self::$fn($fileId);
            array_push($sources, array(
                "source" => $source,
                "label"  => _($label),
                "status" => $status
            ));
        }

        return $sources;
    }

    /**
     * Reflective accessor for SoundCloud publication status for the
     * file with the given ID
     *
     * @param int $fileId the ID of the file to check
     *
     * @return int 1 if the file has been published to SoundCloud,
     *             0 if the file has yet to be published, or -1 if the
     *             file is in a pending state
     */
    private static function getSoundCloudPublishStatus($fileId) {
        $soundcloudService = new Application_Service_SoundcloudService();
        return ($soundcloudService->referenceExists($fileId));
    }

    /**
     *
     * Reflective accessor for Station podcast publication status for the
     * file with the given ID
     *
     * @param int $fileId the ID of the file to check
     *
     * @return int 1 if the file has been published to SoundCloud,
     *             0 if the file has yet to be published, or -1 if the
     *             file is in a pending state
     */
    private static function getStationPodcastPublishStatus($fileId) {
        $stationPodcast = StationPodcastQuery::create()
            ->findOneByDbPodcastId(Application_Model_Preference::getStationPodcastId());
        return (int) $stationPodcast->hasEpisodeForFile($fileId);
    }

}
