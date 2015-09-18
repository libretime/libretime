<?php

class PodcastLimitReachedException extends Exception
{
}

class InvalidPodcastException extends Exception
{
}

class PodcastNotFoundException extends \Aws\CloudFront\Exception\Exception
{
}


/**
 * Skeleton subclass for representing a row from the 'podcast' table.
 *
 * @package    propel.generator.airtime
 */
class Podcast extends BasePodcast
{
    // These fields should never be modified with POST/PUT data
    private static $privateFields = array(
        "id",
        "url",
        "type",
        "owner"
    );

    /** Creates a Podcast object from the given podcast URL.
     *  This is used by our Podcast REST API
     *
     * @param $podcastArray An array containing the URL for a Podcast object.
     *
     * @return array - Podcast Array with a full list of episodes
     * @throws Exception
     * @throws InvalidPodcastException
     * @throws PodcastLimitReachedException
     */

    public static function create($podcastArray)
    {
        if (Application_Service_PodcastService::podcastLimitReached()) {
            throw new PodcastLimitReachedException();
        }

        $rss = Application_Service_PodcastService::getPodcastFeed($podcastArray["url"]);
        if (!$rss) {
            throw new InvalidPodcastException();
        }

        try {
            $podcast = new Podcast();
            $podcast->setDbUrl($podcastArray["url"]);
            $podcast->setDbTitle($rss->title);
            $podcast->setDbCreator($rss->author);
            $podcast->setDbDescription($rss->description);
            $podcast->setDbOwner(self::getOwnerId());
            $podcast->setDbType(IMPORTED_PODCAST);
            $podcast->save();

            $podcastArray = array();
            array_push($podcastArray, $podcast->toArray(BasePeer::TYPE_FIELDNAME));

            $podcastArray["episodes"] = array();
            foreach ($rss->item as $item) {
                array_push($podcastArray["episodes"], $item);
            }
            return $podcastArray;
        } catch(Exception $e) {
            $podcast->delete();
            throw $e;
        }

    }

    /**
     * Fetches a Podcast's rss feed and returns all its episodes with
     * the Podcast object
     *
     * @param $podcastId
     *
     * @throws PodcastNotFoundException
     * @return array - Podcast Array with a full list of episodes
     */
    public static function getPodcastById($podcastId)
    {
        $podcast = PodcastQuery::create()->findPk($podcastId);
        if (!$podcast) {
            throw new PodcastNotFoundException();
        }

        $rss = Application_Service_PodcastService::getPodcastFeed($podcast->getDbUrl());

        $podcastArray = array();
        array_push($podcastArray, $podcast->toArray(BasePeer::TYPE_FIELDNAME));

        $podcastArray["episodes"] = array();
        foreach ($rss->item as $item) {
            array_push($podcastArray["episodes"], $item);
        }

        return $podcastArray;
    }

    /**
     * Updates a Podcast object with the given metadata
     *
     * @param $podcastId
     * @param $data
     * @return array
     * @throws Exception
     * @throws PodcastNotFoundException
     */
    public static function updateFromArray($podcastId, $data)
    {
        $podcast = PodcastQuery::create()->findPk($podcastId);
        if (!$podcast) {
            throw new PodcastNotFoundException();
        }

        $data = self::removePrivateFields($data);

        $podcast->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $podcast->save();

        return $podcast->toArray(BasePeer::TYPE_FIELDNAME);
    }

    /**
     * Deletes a Podcast and its podcast episodes
     *
     * @param $podcastId
     * @throws Exception
     * @throws PodcastNotFoundException
     */
    public static function deleteById($podcastId)
    {
        $podcast = PodcastQuery::create()->findPk($podcastId);
        if ($podcast) {
            $podcast->delete();
        } else {
            throw new PodcastNotFoundException();
        }
    }

    private static function removePrivateFields($data)
    {
        foreach (self::$privateFields as $key) {
            unset($data[$key]);
        }

        return $data;
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
