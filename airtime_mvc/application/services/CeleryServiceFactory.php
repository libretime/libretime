<?php

class CeleryServiceFactory {

    /**
     *
     *
     * @param $serviceName string the name of the service to create
     *
     * @return Application_Service_ThirdPartyCeleryService|null
     */
    public static function getService($serviceName) {
        switch($serviceName) {
            case SOUNDCLOUD_SERVICE_NAME:
                return new Application_Service_SoundcloudService();
        }
        return null;
    }

}