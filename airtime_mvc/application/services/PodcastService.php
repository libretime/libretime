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
     * Returns true if the given podcast URL is valid.
     *
     * @param $podcastUrl String containing the podcast feed URL
     *
     * @return bool
     */
    public static function validatePodcastUrl($podcastUrl)
    {
        //TODO
        return true;
    }

}