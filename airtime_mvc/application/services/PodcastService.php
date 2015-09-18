<?php

class Application_Service_PodcastService
{
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
            return Feed::loadRss($podcastUrl);
        } catch (FeedException $e) {
            return false;
        }
    }

    public static function getPodcastEpisodeFeed($podcast)
    {

    }

}