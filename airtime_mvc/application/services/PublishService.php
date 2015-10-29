<?php

class Application_Service_PublishService {

    /**
     * @var array map of arbitrary source names to descriptive labels
     */
    private static $SOURCES = array(
        "soundcloud"        => SOUNDCLOUD,
        "station_podcast"   => "My Station Podcast"
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
        $publishSources = $publishedSources = array();
        foreach (self::$SOURCES as $source => $label) {
            $fn = self::$SOURCE_FUNCTIONS[$source];
            // Should be in a ternary but PHP doesn't play nice
            if (self::$fn($fileId)) {
                $publishedSources[$source] = _($label);
            } else {
                $publishSources[$source] = _($label);
            }
        }

        return array(
            "toPublish" => $publishSources,
            "published" => $publishedSources
        );
    }

    /** @noinspection PhpUnusedPrivateMethodInspection
     * Reflective accessor for SoundCloud publication status for the
     * file with the given ID
     *
     * @param int $fileId the ID of the file to check
     *
     * @return bool true if the file has been published to SoundCloud,
     *              otherwise false
     */
    private static function getSoundCloudPublishStatus($fileId) {
        $soundcloudService = new Application_Service_SoundcloudService();
        return ($soundcloudService->getServiceId($fileId) != 0);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection
     * Reflective accessor for Station podcast publication status for the
     * file with the given ID
     *
     * @param int $fileId the ID of the file to check
     *
     * @return bool true if the file has been published to the Station podcast,
     *              otherwise false
     */
    private static function getStationPodcastPublishStatus($fileId) {
        $stationPodcast = StationPodcastQuery::create()
            ->findOneByDbPodcastId(Application_Model_Preference::getStationPodcastId());
        return $stationPodcast->hasEpisodeForFile($fileId);
    }

}
