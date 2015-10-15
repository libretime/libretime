<?php

class PodcastManager {

    /**
     * @var int how often, in seconds, to check for and ingest new podcast episodes
     */
    private static $_PODCAST_POLL_INTERVAL_SECONDS = 3600;  // 1 hour

    /**
     * Check whether $_PODCAST_POLL_INTERVAL_SECONDS have passed since the last call to
     * downloadNewestEpisodes
     *
     * @return bool true if $_PODCAST_POLL_INTERVAL_SECONDS has passed since the last check
     */
    public static function hasPodcastPollIntervalPassed() {
        $lastPolled = Application_Model_Preference::getPodcastPollLock();
        return empty($lastPolled) || (microtime(true) > $lastPolled + self::$_PODCAST_POLL_INTERVAL_SECONDS);
    }

    /**
     * Find all podcasts flagged for automatic ingest whose most recent episode has
     * yet to be downloaded and download it with Celery
     *
     * @throws InvalidPodcastException
     * @throws PodcastNotFoundException
     */
    public static function downloadNewestEpisodes() {
        $autoIngestPodcasts = static::_getAutoIngestPodcasts();
        $service = new Application_Service_PodcastEpisodeService();
        $episodes = array();
        foreach ($autoIngestPodcasts as $podcast) {
            /** @var ImportedPodcast $podcast */
            $podcastArray = Application_Service_PodcastService::getPodcastById($podcast->getDbId());
            // A bit hacky... sort the episodes by publication date to get the most recent
            usort($podcastArray["episodes"], array(static::class, "_sortByEpisodePubDate"));
            $episodeData = $podcastArray["episodes"][0];
            $episode = PodcastEpisodesQuery::create()->findOneByDbEpisodeGuid($episodeData["guid"]);
            // Make sure there's no existing episode placeholder or import, and that the data is non-empty
            if (empty($episode) && !empty($episodeData)) {
                $placeholder = $service->addPodcastEpisodePlaceholder($podcast->getDbId(), $episodeData);
                array_push($episodes, $placeholder);
            }
        }

        $service->downloadEpisodes($episodes);
        Application_Model_Preference::setPodcastPollLock(microtime(true));
    }

    /**
     * Find all podcasts flagged for automatic ingest
     *
     * @return PropelObjectCollection collection of ImportedPodcast objects
     *                                flagged for automatic ingest
     */
    protected static function _getAutoIngestPodcasts() {
        return ImportedPodcastQuery::create()
            ->filterByDbAutoIngest(true)
            ->find();
    }

    /**
     * Custom sort function for podcast episodes
     *
     * @param array $a first episode array to compare
     * @param array $b second episode array to compare
     * @return bool boolean for ordering
     */
    protected static function _sortByEpisodePubDate($a, $b) {
        if ($a["pub_date"] == $b["pub_date"]) return 0;
        return ($a["pub_date"] < $b["pub_date"]) ? 1 : -1;  // Descending order
    }

}