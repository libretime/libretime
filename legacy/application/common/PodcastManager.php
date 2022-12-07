<?php

declare(strict_types=1);

class PodcastManager
{
    /**
     * @var int how often, in seconds, to check for and ingest new podcast episodes
     */
    private static $_PODCAST_POLL_INTERVAL_SECONDS = 3600;  // 1 hour

    /**
     * Check whether $_PODCAST_POLL_INTERVAL_SECONDS have passed since the last call to
     * downloadNewestEpisodes.
     *
     * @return bool true if $_PODCAST_POLL_INTERVAL_SECONDS has passed since the last check
     */
    public static function hasPodcastPollIntervalPassed()
    {
        $lastPolled = Application_Model_Preference::getPodcastPollLock();

        return empty($lastPolled) || (microtime(true) > $lastPolled + self::$_PODCAST_POLL_INTERVAL_SECONDS);
    }

    /**
     * Find all podcasts flagged for automatic ingest whose most recent episode has
     * yet to be downloaded and download it with Celery.
     *
     * @throws InvalidPodcastException
     * @throws PodcastNotFoundException
     */
    public static function downloadNewestEpisodes()
    {
        $autoIngestPodcasts = static::_getAutoIngestPodcasts();
        $service = new Application_Service_PodcastEpisodeService();
        foreach ($autoIngestPodcasts as $podcast) {
            $episodes = static::_findUningestedEpisodes($podcast, $service);
            // Since episodes don't have to be uploaded with a time (H:i:s) component,
            // store the timestamp of the most recent (first pushed to the array) episode
            // that we're ingesting.
            // Note that this folds to the failure case (Celery task timeout/download failure)
            //  but will at least continue to ingest new episodes.
            if (!empty($episodes)) {
                $podcast->setDbAutoIngestTimestamp(gmdate('r', strtotime($episodes[0]->getDbPublicationDate())))->save();
                $service->downloadEpisodes($episodes);
            }
        }

        Application_Model_Preference::setPodcastPollLock(microtime(true));
    }

    /**
     * Given an ImportedPodcast, find all uningested episodes since the last automatic ingest,
     * and add them to a given episodes array.
     *
     * @param ImportedPodcast                           $podcast the podcast to search
     * @param Application_Service_PodcastEpisodeService $service podcast episode service object
     *
     * @return array array of episodes to append be downloaded
     */
    protected static function _findUningestedEpisodes($podcast, $service)
    {
        $episodeList = $service->getPodcastEpisodes($podcast->getDbPodcastId());
        $episodes = [];
        usort($episodeList, [__CLASS__, '_sortByEpisodePubDate']);
        for ($i = 0; $i < count($episodeList); ++$i) {
            $episodeData = $episodeList[$i];
            $ts = $podcast->getDbAutoIngestTimestamp();
            // If the timestamp for this podcast is empty (no previous episodes have been ingested) and there are no
            //  episodes in the list of episodes to ingest, don't skip this episode - we should try to ingest the
            //  most recent episode when the user first sets the podcast to automatic ingest.
            // If the publication date of this episode is before the ingest timestamp, we don't need to ingest it
            if ((empty($ts) && ($i > 0)) || strtotime($episodeData['pub_date']) < strtotime($ts)) {
                continue;
            }
            $episode = PodcastEpisodesQuery::create()->findOneByDbEpisodeGuid($episodeData['guid']);
            // Make sure there's no existing episode placeholder or import, and that the data is non-empty
            if (empty($episode) && !empty($episodeData)) {
                $placeholder = $service->addPlaceholder($podcast->getDbPodcastId(), $episodeData);
                array_push($episodes, $placeholder);
            }
        }

        return $episodes;
    }

    /**
     * Find all podcasts flagged for automatic ingest.
     *
     * @return PropelObjectCollection collection of ImportedPodcast objects
     *                                flagged for automatic ingest
     */
    protected static function _getAutoIngestPodcasts()
    {
        return ImportedPodcastQuery::create()
            ->filterByDbAutoIngest(true)
            ->find();
    }

    /**
     * Custom sort function for podcast episodes.
     *
     * @param array $a first episode array to compare
     * @param array $b second episode array to compare
     *
     * @return bool boolean for ordering
     */
    protected static function _sortByEpisodePubDate($a, $b)
    {
        if ($a['pub_date'] == $b['pub_date']) {
            return 0;
        }

        return (strtotime($a['pub_date']) < strtotime($b['pub_date'])) ? 1 : -1;  // Descending order
    }
}
