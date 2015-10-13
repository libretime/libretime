<?php

class PodcastFactory
{
    public static function create($feedUrl)
    {
        // check if station podcast exists and if not, create one

        return Application_Service_ImportedPodcastService::createFromFeedUrl($feedUrl);
    }
}