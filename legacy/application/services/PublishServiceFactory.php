<?php

declare(strict_types=1);

class PublishServiceFactory
{
    /**
     * Given an identifying string, get a PublishService object of that type.
     *
     * @param $serviceName string the name of the service to create
     *
     * @return null|Publish
     */
    public static function getService($serviceName)
    {
        switch ($serviceName) {
            case STATION_PODCAST_SERVICE_NAME:
                return new Application_Service_PodcastEpisodeService();

            default:
                return null;
        }
    }
}
