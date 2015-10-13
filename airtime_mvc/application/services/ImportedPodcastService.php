<?php

class PodcastLimitReachedException extends Exception {}

class InvalidPodcastException extends Exception {}

class PodcastNotFoundException extends Exception {}


class Application_Service_ImportedPodcastService
{

    // These fields should never be modified with POST/PUT data
    private static $privateFields = array(
        "id",
        "url",
        "type",
        "owner"
    );

    /**
     * There is maximum of 50 podcasts allowed in the library - to limit
     * resource consumption. This function returns true if the podcast
     * limit has been reached.
     *
     * @return bool
     */
    public static function importedPodcastLimitReached()
    {
        if (ImportedPodcastQuery::create()->count() >= 50) {
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
    public static function getPodcastFeed($feedUrl)
    {
        try {
            $feed = new SimplePie();
            $feed->set_feed_url($feedUrl);
            $feed->enable_cache(false);
            $feed->init();
            return $feed;
        } catch (Exception $e) {
            return false;
        }
    }

    /** Creates a Podcast object from the given podcast URL.
     *  This is used by our Podcast REST API
     *
     * @param $feedUrl Podcast RSS Feed Url
     *
     * @return array - Podcast Array with a full list of episodes
     * @throws Exception
     * @throws InvalidPodcastException
     * @throws PodcastLimitReachedException
     */
    public static function createFromFeedUrl($feedUrl)
    {
        if (self::importedPodcastLimitReached()) {
            throw new PodcastLimitReachedException();
        }

        //TODO: why is this so slow?
        $rss = self::getPodcastFeed($feedUrl);
        if (!$rss) {
            throw new InvalidPodcastException();
        }

        // Ensure we are only creating Podcast with the given URL, and excluding
        // any extra data fields that may have been POSTED
        $podcastArray = array();
        $podcastArray["url"] = $feedUrl;

        $podcastArray["title"] = $rss->get_title();
        $podcastArray["description"] = $rss->get_description();
        $podcastArray["link"] = $rss->get_link();
        $podcastArray["language"] = $rss->get_language();
        $podcastArray["copyright"] = $rss->get_copyright();
        $podcastArray["creator"] = $rss->get_author()->get_name();
        $podcastArray["category"] = $rss->get_categories();

        //TODO: put in constants
        $itunesChannel = "http://www.itunes.com/dtds/podcast-1.0.dtd";

        $itunesSubtitle = $rss->get_channel_tags($itunesChannel, 'subtitle');
        $podcastArray["itunes_subtitle"] = isset($itunesSubtitle[0]["data"]) ? $itunesSubtitle[0]["data"] : "";

        $itunesCategory = $rss->get_channel_tags($itunesChannel, 'category');
        $categoryArray = array();
        foreach ($itunesCategory as $c => $data) {
            foreach ($data["attribs"] as $attrib) {
                array_push($categoryArray, $attrib["text"]);
            }
        }
        $podcastArray["itunes_category"] = implode(",", $categoryArray);

        $itunesAuthor = $rss->get_channel_tags($itunesChannel, 'author');
        $podcastArray["itunes_author"] = isset($itunesAuthor[0]["data"]) ? $itunesAuthor[0]["data"] : "";

        $itunesSummary = $rss->get_channel_tags($itunesChannel, 'summary');
        $podcastArray["itunes_summary"] = isset($itunesSummary[0]["data"]) ? $itunesSummary[0]["data"] : "";

        $itunesKeywords = $rss->get_channel_tags($itunesChannel, 'keywords');
        $podcastArray["itunes_keywords"] = isset($itunesKeywords[0]["data"]) ? $itunesKeywords[0]["data"] : "";

        $itunesExplicit = $rss->get_channel_tags($itunesChannel, 'explicit');
        $podcastArray["itunes_explicit"] = isset($itunesExplicit[0]["data"]) ? $itunesExplicit[0]["data"] : "";

        self::validatePodcastMetadata($podcastArray);

        try {
            // Base class
            $podcast = new Podcast();
            $podcast->fromArray($podcastArray, BasePeer::TYPE_FIELDNAME);
            $podcast->setDbOwner(self::getOwnerId());
            $podcast->save();

            $importedPodcast = new ImportedPodcast();
            $importedPodcast->fromArray($podcastArray, BasePeer::TYPE_FIELDNAME);
            $importedPodcast->setPodcast($podcast);
            $importedPodcast->save();

            return self::_generatePodcastArray($importedPodcast, $rss);
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

    /**
     * Given a podcast object and a SimplePie feed object,
     * generate a data array to pass back to the front-end
     *
     * @param $importedPodcast  ImportedPodcast model object
     * @param SimplePie $rss    SimplePie feed object
     *
     * @return array
     */
    private static function _generatePodcastArray($importedPodcast, $rss) {
        $podcastArray = $importedPodcast->toArray(BasePeer::TYPE_FIELDNAME);

        $podcastArray["episodes"] = array();
        foreach ($rss->get_items() as $item) {
            /** @var SimplePie_Item $item */
            array_push($podcastArray["episodes"], array(
                "guid" => $item->get_id(),
                "title" => $item->get_title(),
                "author" => $item->get_author()->get_name(),
                "description" => $item->get_description(),
                "pub_date" => $item->get_date("Y-m-d H:i:s"),
                "link" => $item->get_link(),
                "enclosure" => $item->get_enclosure()
            ));
        }

        return $podcastArray;
    }
}