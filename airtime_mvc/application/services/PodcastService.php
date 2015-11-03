<?php

class PodcastLimitReachedException extends Exception {}

class InvalidPodcastException extends Exception {}

class PodcastNotFoundException extends Exception {}


class Application_Service_PodcastService
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
    public static function PodcastLimitReached()
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
     * @param string $feedUrl String containing the podcast feed URL
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
     * @param string $feedUrl Podcast RSS Feed Url
     *
     * @return array Podcast Array with a full list of episodes
     * @throws Exception
     * @throws InvalidPodcastException
     * @throws PodcastLimitReachedException
     */
    public static function createFromFeedUrl($feedUrl)
    {
        if (self::PodcastLimitReached()) {
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

        $podcastArray["title"] = htmlspecialchars($rss->get_title());
        $podcastArray["description"] = htmlspecialchars($rss->get_description());
        $podcastArray["link"] = htmlspecialchars($rss->get_link());
        $podcastArray["language"] = htmlspecialchars($rss->get_language());
        $podcastArray["copyright"] = htmlspecialchars($rss->get_copyright());
        $podcastArray["creator"] = htmlspecialchars($rss->get_author()->get_name());
        $podcastArray["category"] = htmlspecialchars($rss->get_categories());

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

            return $podcast->toArray(BasePeer::TYPE_FIELDNAME);
        } catch(Exception $e) {
            $podcast->delete();
            throw $e;
        }
    }

    public static function createStationPodcast()
    {
        $podcast = new Podcast();
        $podcast->setDbUrl(Application_Common_HTTPHelper::getStationUrl() . "feeds/station-rss");

        $title = Application_Model_Preference::GetStationName();
        $title = empty($title) ? "My Station's Podcast" : $title;
        $podcast->setDbTitle($title);

        $podcast->setDbDescription(Application_Model_Preference::GetStationDescription());
        $podcast->setDbLink(Application_Common_HTTPHelper::getStationUrl());
        $podcast->setDbLanguage(Application_Model_Preference::GetLocale());
        $podcast->setDbCreator(Application_Model_Preference::GetStationName());
        $podcast->setDbOwner(self::getOwnerId());
        $podcast->save();

        $stationPodcast = new StationPodcast();
        $stationPodcast->setPodcast($podcast);
        $stationPodcast->save();

        Application_Model_Preference::setStationPodcastId($podcast->getDbId());
        // Set the download key when we create the station podcast
        // The value is randomly generated in the setter
        Application_Model_Preference::setStationPodcastDownloadKey();
        return $podcast->getDbId();
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
     * Fetches a Podcast's rss feed and returns all its episodes with
     * the Podcast object
     *
     * @param $podcastId
     *
     * @throws PodcastNotFoundException
     * @throws InvalidPodcastException
     * @return array - Podcast Array with a full list of episodes
     */
    public static function getPodcastById($podcastId)
    {
        $podcast = PodcastQuery::create()->findPk($podcastId);
        if (!$podcast) {
            throw new PodcastNotFoundException();
        }

        return $podcast->toArray(BasePeer::TYPE_FIELDNAME);
    }

    /**
     * Deletes a Podcast and its podcast episodes
     *
     * @param $podcastId
     * @throws Exception
     * @throws PodcastNotFoundException
     */
    public static function deletePodcastById($podcastId)
    {
        $podcast = PodcastQuery::create()->findPk($podcastId);
        if ($podcast) {
            $podcast->delete();

            // FIXME: I don't think we should be able to delete the station podcast...
            if ($podcastId == Application_Model_Preference::getStationPodcastId()) {
                Application_Model_Preference::setStationPodcastId(null);
            }
        } else {
            throw new PodcastNotFoundException();
        }
    }

    /**
     * Build a response with podcast data and embedded HTML to load on the frontend
     *
     * @param int $podcastId            ID of the podcast to build a response for
     * @param Zend_View_Interface $view Zend view object to render the response HTML
     *
     * @return array the response array containing the podcast data and editor HTML
     *
     * @throws PodcastNotFoundException
     */
    public static function buildPodcastEditorResponse($podcastId, $view) {
        // Check the StationPodcast table rather than checking
        // the station podcast ID key in preferences for extensibility
        $podcast = StationPodcastQuery::create()->findOneByDbPodcastId($podcastId);
        $path = $podcast ? 'podcast/station_podcast.phtml' : 'podcast/podcast.phtml';
        $podcast = Application_Service_PodcastService::getPodcastById($podcastId);
        return array(
            "podcast" => json_encode($podcast),
            "html" => $view->render($path),
        );
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
    public static function updatePodcastFromArray($podcastId, $data)
    {
        $podcast = PodcastQuery::create()->findPk($podcastId);
        if (!$podcast) {
            throw new PodcastNotFoundException();
        }

        self::removePrivateFields($data["podcast"]);
        self::validatePodcastMetadata($data["podcast"]);
        if (array_key_exists("auto_ingest", $data["podcast"])) {
            self::_updateAutoIngestTimestamp($podcast, $data);
        }

        $podcast->fromArray($data["podcast"], BasePeer::TYPE_FIELDNAME);
        $podcast->save();

        return $podcast->toArray(BasePeer::TYPE_FIELDNAME);
    }

    /**
     * Update the automatic ingestion timestamp for the given Podcast
     *
     * @param Podcast $podcast  Podcast object to update
     * @param array $data       Podcast update data array
     */
    private static function _updateAutoIngestTimestamp($podcast, $data) {
        // Get podcast data with lazy loaded columns since we can't directly call getDbAutoIngest()
        $currData = $podcast->toArray(BasePeer::TYPE_FIELDNAME, true);
        // Add an auto-ingest timestamp when turning auto-ingest on
        if ($data["podcast"]["auto_ingest"] == 1 && $currData["auto_ingest"] != 1) {
            $data["podcast"]["auto_ingest_timestamp"] = gmdate('r');
        }
    }

    private static function removePrivateFields(&$data)
    {
        foreach (self::$privateFields as $key) {
            unset($data[$key]);
        }
    }

    public static function createStationRssFeed()
    {
        $stationPodcastId = Application_Model_Preference::getStationPodcastId();
        
        try {
            $podcast = PodcastQuery::create()->findPk($stationPodcastId);
            if (!$podcast) {
                throw new PodcastNotFoundException();
            }

            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"/>');

            $channel = $xml->addChild("channel");
            $channel->addChild("title", $podcast->getDbTitle());
            $channel->addChild("link", $podcast->getDbLink());
            $channel->addChild("description", $podcast->getDbDescription());
            $channel->addChild("language", $podcast->getDbLanguage());
            $channel->addChild("copyright", $podcast->getDbCopyright());

            $imageUrl = Application_Common_HTTPHelper::getStationUrl()."images/airtime_logo.png";
            $image = $channel->addChild("image");
            $image->addChild("title", "image title");
            $image->addChild("url", $imageUrl);
            $image->addChild("link", Application_Common_HTTPHelper::getStationUrl());

            $xml->addAttribute('xmlns:xmlns:itunes', ITUNES_XML_NAMESPACE_URL);
            $channel->addChild("xmlns:itunes:author", $podcast->getDbItunesAuthor());
            $channel->addChild("xmlns:itunes:keywords", $podcast->getDbItunesKeywords());
            $channel->addChild("xmlns:itunes:summary", $podcast->getDbItunesSummary());
            $channel->addChild("xmlns:itunes:subtitle", $podcast->getDbItunesSubtitle());
            $channel->addChild("xmlns:itunes:explicit", $podcast->getDbItunesExplicit());

            $itunesImage = $channel->addChild("xmlns:itunes:image");
            $itunesImage->addAttribute("href", $imageUrl);

            // Need to split categories into separate tags
            $itunesCategories = explode(",", $podcast->getDbItunesCategory());
            foreach ($itunesCategories as $c) {
                $category = $channel->addChild("xmlns:itunes:category");
                $category->addAttribute("text", $c);
            }

            $episodes = PodcastEpisodesQuery::create()->filterByDbPodcastId($stationPodcastId)->find();
            foreach ($episodes as $episode) {
                $item = $channel->addChild("item");
                $publishedFile = CcFilesQuery::create()->findPk($episode->getDbFileId());

                //title
                $item->addChild("title", $publishedFile->getDbTrackTitle());

                //link - do we need this?

                //pubDate
                $item->addChild("pubDate", $episode->getDbPublicationDate());

                //category
                foreach($itunesCategories as $c) {
                    $item->addChild("category", $c);
                }

                //guid
                $guid = $item->addChild("guid", $episode->getDbEpisodeGuid());
                $guid->addAttribute("isPermaLink", "false");

                //description
                $item->addChild("description", $publishedFile->getDbDescription());

                //encolsure - url, length, type attribs
                $enclosure = $item->addChild("enclosure");
                $enclosure->addAttribute("url", $episode->getDbDownloadUrl());
                $enclosure->addAttribute("length", Application_Common_DateHelper::calculateLengthInSeconds($publishedFile->getDbLength()));
                $enclosure->addAttribute("type", $publishedFile->getDbMime());

                //itunes:subtitle
                $item->addChild("xmlns:itunes:subtitle", $publishedFile->getDbTrackTitle());

                //itunes:summary
                $item->addChild("xmlns:itunes:summary", $publishedFile->getDbDescription());

                //itunes:author
                $item->addChild("xmlns:itunes:author", $publishedFile->getDbArtistName());

                //itunes:explicit - skip this?

                //itunes:duration
                $item->addChild("xmlns:itunes:duration", $publishedFile->getDbLength());
            }

            return $xml->asXML();

        } catch (FeedException $e) {
            return false;
        }
    }
}