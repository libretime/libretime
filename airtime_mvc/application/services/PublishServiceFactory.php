<?php

class PublishServiceFactory {

    /**
     * Given an identifying string, get a PublishService object of that type
     *
     * @param $serviceName string the name of the service to create
     *
     * @return Publish|null
     */
    public static function getService($serviceName) {
        switch($serviceName) {
            case SOUNDCLOUD_SERVICE_NAME:
                return new Application_Service_SoundcloudService();
            case STATION_PODCAST_SERVICE_NAME:
                return new Application_Service_PodcastEpisodeService();
            default:
                return null;
        }
    }

}