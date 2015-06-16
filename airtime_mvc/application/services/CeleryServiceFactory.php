<?php

class CeleryServiceFactory {

    /**
     *
     *
     * @param $serviceName string the name of the service to create
     *
     * @return ThirdPartyCeleryService|null
     */
    public static function getService($serviceName) {
        switch($serviceName) {
            case SOUNDCLOUD_SERVICE_NAME:
                return new SoundcloudService();
        }
        return null;
    }

}