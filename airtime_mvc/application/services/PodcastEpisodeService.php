<?php

class Application_Service_PodcastEpisodeService extends Application_Service_ThirdPartyCeleryService
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
     * Given an array of episodes, store them in the database as placeholder objects until
     * they can be processed by Celery
     *
     * @param int $podcastId    Podcast object identifier
     * @param array $episodes   array of podcast episodes
     *
     * @return array the stored PodcastEpisodes objects
     */
    public function addPodcastEpisodePlaceholders($podcastId, $episodes) {
        $storedEpisodes = array();
        foreach ($episodes as $episode) {
            $e = $this->addPodcastEpisodePlaceholder($podcastId, $episode);
            array_push($storedEpisodes, $e);
        }
        return $storedEpisodes;
    }

    /**
     * Given an episode, store it in the database as a placeholder object until
     * it can be processed by Celery
     *
     * @param int $podcastId  Podcast object identifier
     * @param array $episode  array of podcast episode data
     *
     * @return PodcastEpisodes the stored PodcastEpisodes object
     */
    public function addPodcastEpisodePlaceholder($podcastId, $episode) {
        // We need to check whether the array is parsed directly from the SimplePie
        // feed object, or whether it's passed in as json
        if ($episode["enclosure"] instanceof SimplePie_Enclosure) {
            $url = $episode["enclosure"]->get_link();
        } else {
            $url = $episode["enclosure"]["link"];
        }
        $e = new PodcastEpisodes();
        $e->setDbPodcastId($podcastId);
        $e->setDbDownloadUrl($url);
        $e->setDbEpisodeGuid($episode["guid"]);
        $e->setDbPublicationDate($episode["pub_date"]);
        $e->save();
        return $e;
    }

    /**
     * Given an array of episodes, extract the IDs and download URLs and send them to Celery
     *
     * @param array $episodes array of podcast episodes
     */
    public function downloadEpisodes($episodes) {
        $episodeUrls = array();
        /** @var PodcastEpisodes $episode */
        foreach($episodes as $episode) {
            array_push($episodeUrls, array("id" => $episode->getDbId(),
                                           "url" => $episode->getDbDownloadUrl()));
        }
        if (empty($episodeUrls)) return;
        $this->_download($episodeUrls);
    }

    /**
     * Given an array of download URLs, download RSS feed tracks
     *
     * @param array $episodes array of episodes containing download URLs and IDs to send to Celery
     */
    private function _download($episodes) {
        $CC_CONFIG = Config::getConfig();
        $data = array(
            'episodes'      => $episodes,
            'callback_url'  => Application_Common_HTTPHelper::getStationUrl() . '/rest/media',
            'api_key'       => $apiKey = $CC_CONFIG["apiKey"][0],
        );
        $this->_executeTask(static::$_CELERY_TASKS[self::DOWNLOAD], $data);
    }

    /**
     * Update a ThirdPartyTrackReferences object for a completed upload
     *
     * @param $task         CeleryTasks the completed CeleryTasks object
     * @param $episodeId    int         PodcastEpisodes identifier
     * @param $episodes     array       array containing Podcast episode information
     * @param $status       string      Celery task status
     *
     * @return ThirdPartyTrackReferences the updated ThirdPartyTrackReferences object
     *
     * @throws Exception
     * @throws PropelException
     */
    public function updateTrackReference($task, $episodeId, $episodes, $status) {
        $ref = parent::updateTrackReference($task, $episodeId, $episodes, $status);

        if ($status == CELERY_SUCCESS_STATUS) {
            foreach ($episodes as $episode) {
                // Since we process episode downloads as a batch, individual downloads can fail
                // even if the task itself succeeds
                $dbEpisode = PodcastEpisodesQuery::create()
                    ->findOneByDbId($episode->episodeid);
                if ($episode->status) {
                    $dbEpisode->setDbFileId($episode->fileid)
                        ->save();
                } else {
                    Logging::warn("Celery task $task episode $episode->episodeid unsuccessful with status $episode->status");
                    $dbEpisode->delete();
                }
            }
        }
        // TODO: do we need a broader fail condition here?

        return $ref;
    }
}