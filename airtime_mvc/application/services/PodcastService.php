<?php

class Application_Service_PodcastService extends Application_Service_ThirdPartyCeleryService
{
    /**
     * Arbitrary constant identifiers for the internal tasks array
     */

    const DOWNLOAD = 'download';

    /**
     * @var string service name to store in ThirdPartyTrackReferences database
     */
    protected static $_SERVICE_NAME = PODCAST_SERVICE_NAME;  // Service name constant from constants.php

    /**
     * @var string exchange name for Podcast tasks
     */
    protected static $_CELERY_EXCHANGE_NAME = 'podcast';

    /**
     * @var array map of constant identifiers to Celery task names
     */
    protected static $_CELERY_TASKS = [
        self::DOWNLOAD => 'podcast-download'  // TODO: rename this to ingest?
    ];

    /**
     * There is maximum of 50 podcasts allowed in the library - to limit
     * resource consumption. This function returns true if the podcast
     * limit has been reached.
     *
     * @return bool
     */
    public static function podcastLimitReached()
    {
        if (PodcastQuery::create()->count() >= 50) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns parsed rss feed, or false if the given URL cannot be downloaded
     *
     * @param $podcastUrl String containing the podcast feed URL
     *
     * @return mixed
     */
    public static function getPodcastFeed($podcastUrl)
    {
        try {
            $feed = new SimplePie();
            $feed->set_feed_url($podcastUrl);
            $feed->enable_cache(false);
            $feed->init();
            return $feed;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getPodcastEpisodeFeed($podcast)
    {

    }
    /**
     * Given an array of episodes, extract the download URLs and send them to Celery
     *
     * @param array $episodes array of podcast episodes
     */
    public function downloadEpisodes($episodes) {
        $episodeUrls = array();
        foreach($episodes as $episode) {
            array_push($episodeUrls, $episode["enclosure"]["link"]);
        }
        $this->_download($episodeUrls);
    }

    /**
     * Given an array of download URLs, download RSS feed tracks
     *
     * @param array $downloadUrls array of download URLs to send to Celery
     * TODO: do we need other parameters here...?
     */
    private function _download($downloadUrls) {
        $CC_CONFIG = Config::getConfig();
        $data = array(
            'download_urls' => $downloadUrls,
            'callback_url'  => Application_Common_HTTPHelper::getStationUrl() . '/rest/media',
            'api_key'       => $apiKey = $CC_CONFIG["apiKey"][0],
        );
        // FIXME
        Logging::warn("FIXME: we can't create a task reference without a valid file ID");
        $this->_executeTask(static::$_CELERY_TASKS[self::DOWNLOAD], $data, null);
    }

    /**
     * Update a ThirdPartyTrackReferences object for a completed upload
     *
     * @param $task     CeleryTasks the completed CeleryTasks object
     * @param $episodeId  int       PodcastEpisodes identifier
     * @param $episode  object      object containing Podcast episode information
     * @param $status   string      Celery task status
     *
     * @return ThirdPartyTrackReferences the updated ThirdPartyTrackReferences object
     *
     * @throws Exception
     * @throws PropelException
     */
    public function updateTrackReference($task, $episodeId, $episode, $status) {
        $ref = parent::updateTrackReference($task, $episodeId, $episode, $status);

        if ($status == CELERY_SUCCESS_STATUS) {
            // TODO: handle successful download
            // $ref->setDbForeignId();
            // FIXME: we need the file ID here, but 'track' is too arbitrary...
            $ref->setDbFileId($episode->fileId);
        }

        $ref->save();
        return $ref;
    }
}