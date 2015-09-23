<?php

class PodcastLimitReachedException extends Exception
{
}

class InvalidPodcastException extends Exception
{
}

class PodcastNotFoundException extends Exception
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
     * @param $data An array containing the URL for a Podcast object.
     *
     * @return array - Podcast Array with a full list of episodes
     * @throws Exception
     * @throws InvalidPodcastException
     * @throws PodcastLimitReachedException
     */

    public static function create($data)
    {
        if (Application_Service_PodcastService::podcastLimitReached()) {
            throw new PodcastLimitReachedException();
        }

        $rss = Application_Service_PodcastService::getPodcastFeed($data["url"]);
        if (!$rss) {
            throw new InvalidPodcastException();
        }

        // Ensure we are only creating Podcast with the given URL, and excluding
        // any extra data fields that may have been POSTED
        $podcastArray = array();
        $podcastArray["url"] = $data["url"];

        // Kind of a pain; since the rss fields are SimpleXMLElements,
        // we need to explicitly cast them to strings
        $podcastArray["title"] = $rss->get_title();
        $podcastArray["description"] = $rss->get_description();
        $podcastArray["link"] = $rss->get_link();
        $podcastArray["language"] = $rss->get_language();
        $podcastArray["copyright"] = $rss->get_copyright();
        $podcastArray["author"] = $rss->get_author();
        $podcastArray["category"] = $rss->get_categories();

        /*$podcastArray["title"] = (string)$rss->title;
        $podcastArray["description"] = (string)$rss->description;
        $podcastArray["link"] = (string)$rss->link;
        $podcastArray["language"] = (string)$rss->language;
        $podcastArray["copyright"] = (string)$rss->copyright;
        $podcastArray["itunes_author"] = (string)$rss->{'itunes:author'};
        $podcastArray["itunes_keywords"] = (string)$rss->{'itunes:keywords'};
        $podcastArray["itunes_subtitle"] = (string)$rss->{'itunes:subtitle'};
        $podcastArray["itunes_summary"] = (string)$rss->{'itunes:summary'};
        //TODO: fix itunes_category
        $podcastArray["itunes_category"] = (string)$rss->{'itunes:category'};
        $podcastArray["itunes_explicit"] = (string)$rss->{'itunes:explicit'};*/
        self::validatePodcastMetadata($podcastArray);

        try {
            $podcast = new Podcast();
            $podcast->fromArray($podcastArray, BasePeer::TYPE_FIELDNAME);
            $podcast->setDbOwner(self::getOwnerId());
            $podcast->setDbType(IMPORTED_PODCAST);
            $podcast->save();

            $podcastArray = $podcast->toArray(BasePeer::TYPE_FIELDNAME);

            $podcastArray["episodes"] = array();
            foreach ($rss->item as $item) {
                // Same as above, we need to explicitly cast the SimpleXMLElement 'array' into an actual array
                foreach($item as $k => $v) {
                    $item[$k] = (string)$v;
                }
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
        if (!$rss) {
            throw new PodcastNotFoundException();
        }

        // FIXME: Get rid of this duplication and move into a new function (serializer/deserializer)
        $podcastArray = $podcast->toArray(BasePeer::TYPE_FIELDNAME);

        $podcastArray["episodes"] = array();
        foreach ($rss->item as $item) {
            // Same as above, we need to explicitly cast the SimpleXMLElement 'array' into an actual array
            foreach($item as $k => $v) {
                $item[$k] = (string)$v;
            }
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

        self::removePrivateFields($data);
        self::validatePodcastMetadata($data);

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

    /**
     * Trims the podcast metadata to fit the table's column max size
     *
     * @param $podcastArray
     */
    private static function validatePodcastMetadata(&$podcastArray)
    {
        $podcastTable = PodcastPeer::getTableMap();

        foreach ($podcastArray as $key => &$value) {
            try {
                // Make sure column exists in table
                $columnMaxSize = $podcastTable->getColumn($key)->getSize();
            } catch (PropelException $e) {
                continue;
            }

            if (strlen($value) > $columnMaxSize) {
                $value = substr($value, 0, $podcastTable->getColumn($key)->getSize());
            }
        }
    }

    private static function removePrivateFields(&$data)
    {
        foreach (self::$privateFields as $key) {
            unset($data[$key]);
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
