<?php

class Application_Service_PodcastEpisodeService extends Application_Service_ThirdPartyCeleryService
{
    /**
     * Arbitrary constant identifiers for the internal tasks array
     */

    const DOWNLOAD = 'download';

    /**
     * @var string service name to store in ThirdPartyTrackReferences database
     */
    protected static $_SERVICE_NAME = PODCAST_SERVICE_NAME;  // Service name constant from constants.php

    /**
     * @var string exchange name for Podcast tasks
     */
    protected static $_CELERY_EXCHANGE_NAME = 'podcast';

    /**
     * @var array map of constant identifiers to Celery task names
     */
    protected static $_CELERY_TASKS = [
        self::DOWNLOAD => 'podcast-download'  // TODO: rename this to ingest?
    ];

    public static function createStationRssFeed()
    {
        //TODO: get station feed podcast ID

        //hack
        $id = 1;
        try {
            $podcast = PodcastQuery::create()->findPk($id);
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

            $episodes = PodcastEpisodesQuery::create()->filterByDbPodcastId($id)->find();
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

    /**
     * Given an array of episodes, extract the download URLs and send them to Celery
     *
     * @param int $podcastId    Podcast object identifier
     * @param array $episodes   array of podcast episodes
     *
     * @return array the stored PodcastEpisodes objects
     */
    public function addPodcastEpisodePlaceholders($podcastId, $episodes) {
        $storedEpisodes = array();
        foreach ($episodes as $episode) {
            $e = new PodcastEpisodes();
            $e->setDbPodcastId($podcastId);
            $e->setDbDownloadUrl($episode["enclosure"]["link"]);
            $e->setDbEpisodeGuid($episode["guid"]);
            $e->setDbPublicationDate($episode["pub_date"]);
            $e->save();
            array_push($storedEpisodes, $e);
        }
        return $storedEpisodes;
    }

    /**
     * Given an array of episodes, extract the IDs and download URLs and send them to Celery
     *
     * @param array $episodes array of podcast episodes
     */
    public function downloadEpisodes($episodes) {
        $episodeUrls = array();
        /** @var PodcastEpisodes $episode */
        foreach($episodes as $episode) {
            array_push($episodeUrls, array("id" => $episode->getDbId(),
                                           "url" => $episode->getDbDownloadUrl()));
        }
        $this->_download($episodeUrls);
    }

    /**
     * Given an array of download URLs, download RSS feed tracks
     *
     * @param array $episodes array of episodes containing download URLs and IDs to send to Celery
     */
    private function _download($episodes) {
        $CC_CONFIG = Config::getConfig();
        $data = array(
            'episodes'      => $episodes,
            'callback_url'  => Application_Common_HTTPHelper::getStationUrl() . '/rest/media',
            'api_key'       => $apiKey = $CC_CONFIG["apiKey"][0],
        );
        $this->_executeTask(static::$_CELERY_TASKS[self::DOWNLOAD], $data);
    }

    /**
     * Update a ThirdPartyTrackReferences object for a completed upload
     *
     * @param $task         CeleryTasks the completed CeleryTasks object
     * @param $episodeId    int         PodcastEpisodes identifier
     * @param $episodes     array       array containing Podcast episode information
     * @param $status       string      Celery task status
     *
     * @return ThirdPartyTrackReferences the updated ThirdPartyTrackReferences object
     *
     * @throws Exception
     * @throws PropelException
     */
    public function updateTrackReference($task, $episodeId, $episodes, $status) {
        $ref = parent::updateTrackReference($task, $episodeId, $episodes, $status);

        if ($status == CELERY_SUCCESS_STATUS) {
            foreach($episodes as $episode) {
                // Since we process episode downloads as a batch, individual downloads can fail
                // even if the task itself succeeds
                if ($episode->status) {
                    $dbEpisode = PodcastEpisodesQuery::create()
                        ->findOneByDbId($episode->episodeid);
                    $dbEpisode->setDbFileId($episode->fileid)
                        ->save();
                }
            }
        }

        return $ref;
    }
}