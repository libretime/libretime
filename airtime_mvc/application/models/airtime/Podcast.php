<?php

class PodcastLimitReachedException extends Exception
{
}

class InvalidPodcastException extends Exception
{
}


/**
 * Skeleton subclass for representing a row from the 'podcast' table.
 *
 * @package    propel.generator.airtime
 */
class Podcast extends BasePodcast
{
    /** Creates a Podcast object from an array containing metadata.
     *  This is used by our Podcast REST API
     * @param $podcastArray An array containing metadata for a Podcast object.
     *
     * @return Podcast Array
     * @throws Exception
     */
    public static function create($podcastArray)
    {
        if (Application_Service_PodcastService::podcastLimitReached()) {
            throw new PodcastLimitReachedException();
        }

        // TODO are we implementing this here??
        if (!Application_Service_PodcastService::validatePodcastUrl($podcastArray["url"])) {
            throw new InvalidPodcastException();
        }

        try {
            $podcast = new Podcast();
            $podcast->fromArray($podcastArray, BasePeer::TYPE_FIELDNAME);
            $podcast->setDbOwner(self::getOwnerId());
            $podcast->setDbType(IMPORTED_PODCAST);
            $podcast->save();

            return $podcast->toArray(BasePeer::TYPE_FIELDNAME);
        } catch(Exception $e) {
            $podcast->delete();
            throw $e;
        }

    }

    //TODO move this somewhere where it makes sense
    private static function getOwnerId()
    {
        try {
            if (Zend_Auth::getInstance()->hasIdentity()) {
                $service_user = new Application_Service_UserService();
                return $service_user->getCurrentUser()->getDbId();
            } else {
                $defaultOwner = CcSubjsQuery::create()
                    ->filterByDbType('A')
                    ->orderByDbId()
                    ->findOne();
                if (!$defaultOwner) {
                    // what to do if there is no admin user?
                    // should we handle this case?
                    return null;
                }
                return $defaultOwner->getDbId();
            }
        } catch(Exception $e) {
            Logging::info($e->getMessage());
        }
    }
}
