<?php

declare(strict_types=1);

class PodcastFactory
{
    public static function create($feedUrl)
    {
        // check if station podcast exists and if not, create one
        $stationPodcastId = Application_Model_Preference::getStationPodcastId();
        if (empty($stationPodcastId)) {
            Application_Service_PodcastService::createStationPodcast();
        }

        return Application_Service_PodcastService::createFromFeedUrl($feedUrl);
    }
}
