<?php

declare(strict_types=1);

/**
 * Skeleton subclass for representing a row from the 'station_podcast' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class StationPodcast extends BaseStationPodcast
{
    /**
     * Utility function to check whether an episode for the file with the given ID
     * is contained within the station podcast.
     *
     * @param int $fileId the file ID to check for
     *
     * @return bool true if the station podcast contains an episode with
     *              the given file ID, otherwise false
     */
    public function hasEpisodeForFile($fileId)
    {
        $episodes = PodcastEpisodesQuery::create()
            ->filterByDbPodcastId($this->getDbPodcastId())
            ->find();
        foreach ($episodes as $e) {
            if ($e->getDbFileId() == $fileId) {
                return true;
            }
        }

        return false;
    }
}
