<?php

class PodcastEpisodeNotFoundException extends Exception
{
}

class DuplicatePodcastEpisodeException extends Exception
{
}

class Application_Service_PodcastEpisodeService extends Application_Service_ThirdPartyCeleryService implements Publish
{
    /**
     * Arbitrary constant identifiers for the internal tasks array.
     */
    public const DOWNLOAD = 'download';

    public const PENDING_EPISODE_TIMEOUT_SECONDS = 900;

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
        self::DOWNLOAD => 'podcast-download',
    ];

    private static $privateFields = [
        'id',
    ];

    /**
     * Utility function to import and download a single episode.
     *
     * @param int   $podcastId ID of the podcast the episode should belong to
     * @param array $episode   array of episode data to store
     *
     * @return PodcastEpisodes the stored PodcastEpisodes object
     */
    public function importEpisode($podcastId, $episode)
    {
        $e = $this->addPlaceholder($podcastId, $episode);
        $p = $e->getPodcast();
        $this->_download($e->getDbId(), $e->getDbDownloadUrl(), $p->getDbTitle(), $this->_getAlbumOverride($p), $episode['title']);

        return $e;
    }

    /**
     * Given an array of episodes, store them in the database as placeholder objects until
     * they can be processed by Celery.
     *
     * @param int   $podcastId Podcast object identifier
     * @param array $episodes  array of podcast episodes
     *
     * @return array the stored PodcastEpisodes objects
     */
    public function addPodcastEpisodePlaceholders($podcastId, $episodes)
    {
        $storedEpisodes = [];
        foreach ($episodes as $episode) {
            try {
                $e = $this->addPlaceholder($podcastId, $episode);
            } catch (DuplicatePodcastEpisodeException $ex) {
                Logging::warn($ex->getMessage());

                continue;
            }
            array_push($storedEpisodes, $e);
        }

        return $storedEpisodes;
    }

    /**
     * Given an episode, store it in the database as a placeholder object until
     * it can be processed by Celery.
     *
     * @param int   $podcastId Podcast object identifier
     * @param array $episode   array of podcast episode data
     *
     * @return PodcastEpisodes the stored PodcastEpisodes object
     *
     * @throws DuplicatePodcastEpisodeException
     */
    public function addPlaceholder($podcastId, $episode)
    {
        $existingEpisode = PodcastEpisodesQuery::create()->findOneByDbEpisodeGuid($episode['guid']);
        if (!empty($existingEpisode)) {
            throw new DuplicatePodcastEpisodeException(sprintf("Episode already exists for podcast: %s, guid: %s\n", $episode['podcast_id'], $episode['guid']));
        }
        // We need to check whether the array is parsed directly from the SimplePie
        // feed object, or whether it's passed in as json
        $enclosure = $episode['enclosure'];
        $url = $enclosure instanceof SimplePie_Enclosure ? $enclosure->get_link() : $enclosure['link'];

        return $this->_buildEpisode($podcastId, $url, $episode['guid'], $episode['pub_date'], $episode['title'], $episode['description']);
    }

    /**
     * Given episode parameters, construct and store a basic PodcastEpisodes object.
     *
     * @param int    $podcastId       the podcast the episode belongs to
     * @param string $url             the download URL for the episode
     * @param string $guid            the unique id for the episode. Often the same as the download URL
     * @param string $publicationDate the publication date of the episode
     * @param string $title           the title of the episode
     * @param string $description     the description of the epsiode
     *
     * @return PodcastEpisodes the newly created PodcastEpisodes object
     *
     * @throws Exception
     * @throws PropelException
     */
    private function _buildEpisode($podcastId, $url, $guid, $publicationDate, $title = null, $description = null)
    {
        $e = new PodcastEpisodes();
        $e->setDbPodcastId($podcastId);
        $e->setDbDownloadUrl($url);
        $e->setDbEpisodeGuid($guid);
        $e->setDbPublicationDate($publicationDate);
        $e->setDbEpisodeTitle($title);
        $e->setDbEpisodeDescription($description);
        $e->save();

        return $e;
    }

    /**
     * Given an array of episodes, extract the IDs and download URLs and send them to Celery.
     *
     * @param array $episodes array of podcast episodes
     */
    public function downloadEpisodes($episodes)
    {
        /** @var PodcastEpisodes $episode */
        foreach ($episodes as $episode) {
            $podcast = $episode->getPodcast();
            $this->_download($episode->getDbId(), $episode->getDbDownloadUrl(), $podcast->getDbTitle(), $this->_getAlbumOverride($podcast), $episode->getDbEpisodeTitle());
        }
    }

    /**
     * check if there is a podcast specific album override.
     *
     * @param object $podcast podcast object
     *
     * @return bool
     */
    private function _getAlbumOverride($podcast)
    {
        $override = Application_Model_Preference::GetPodcastAlbumOverride();
        $podcast_override = $podcast->toArray();
        $podcast_override = $podcast_override['DbAlbumOverride'];
        if ($podcast_override) {
            $override = $podcast_override;
        }

        return $override;
    }

    /**
     * Given an episode ID and a download URL, send a Celery task
     * to download an RSS feed track.
     *
     * @param int        $id             episode unique ID
     * @param string     $url            download url for the episode
     * @param string     $title          title of podcast to be downloaded - added as album to track metadata
     * @param bool       $album_override should we override the album name when downloading
     * @param null|mixed $track_title
     */
    private function _download($id, $url, $title, $album_override, $track_title = null)
    {
        $data = [
            'episode_id' => $id,
            'episode_url' => $url,
            'episode_title' => $track_title,
            'podcast_name' => $title,
            'override_album' => $album_override,
        ];
        $task = $this->_executeTask(static::$_CELERY_TASKS[self::DOWNLOAD], $data);
        // Get the created ThirdPartyTaskReference and set the episode ID so
        // we can remove the placeholder if the import ends up stuck in a pending state
        $ref = ThirdPartyTrackReferencesQuery::create()->findPk($task->getDbTrackReference());
        $ref->setDbForeignId($id)->save();
    }

    /**
     * Update a ThirdPartyTrackReferences object for a completed upload.
     *
     * @param $task         CeleryTasks the completed CeleryTasks object
     * @param $episodeId    int         PodcastEpisodes identifier
     * @param $episode      stdClass    simple object containing Podcast episode information
     * @param $status       string      Celery task status
     *
     * @return ThirdPartyTrackReferences the updated ThirdPartyTrackReferences object
     *
     * @throws Exception
     * @throws PropelException
     */
    public function updateTrackReference($task, $episodeId, $episode, $status)
    {
        $ref = parent::updateTrackReference($task, $episodeId, $episode, $status);
        $ref->setDbForeignId($episode->episodeid)->save();
        $dbEpisode = PodcastEpisodesQuery::create()->findOneByDbId($episode->episodeid);

        try {
            // If the placeholder for the episode is somehow removed, return with a warning
            if (!$dbEpisode) {
                Logging::warn("Celery task {$task} episode {$episode->episodeid} unsuccessful: episode placeholder removed");

                return $ref;
            }

            // Even if the task itself succeeds, the download could have failed, so check the status
            if ($status == CELERY_SUCCESS_STATUS && $episode->status == 1) {
                $dbEpisode->setDbFileId($episode->fileid)->save();
            } else {
                Logging::warn("Celery task {$task} episode {$episode->episodeid} unsuccessful with message {$episode->error}");
                $dbEpisode->delete();
            }
        } catch (Exception $e) {
            $dbEpisode->delete();
            Logging::warn("Catastrophic failure updating from task {$task}, recovering by deleting episode row.\n
                           This can occur if the episode's corresponding CcFile is deleted before being processed.");
        }

        return $ref;
    }

    /**
     * Publish the file with the given file ID to the station podcast.
     *
     * @param int $fileId ID of the file to be published
     */
    public function publish($fileId)
    {
        $id = Application_Model_Preference::getStationPodcastId();
        $url = $guid = Config::getPublicUrl() . "rest/media/{$fileId}/download";
        if (!PodcastEpisodesQuery::create()
            ->filterByDbPodcastId($id)
            ->findOneByDbFileId($fileId)) {  // Don't allow duplicate episodes
            $e = $this->_buildEpisode($id, $url, $guid, date('r'));
            $e->setDbFileId($fileId)->save();
        }
    }

    /**
     * Unpublish the file with the given file ID from the station podcast.
     *
     * @param int $fileId ID of the file to be unpublished
     */
    public function unpublish($fileId)
    {
        $id = Application_Model_Preference::getStationPodcastId();
        PodcastEpisodesQuery::create()
            ->filterByDbPodcastId($id)
            ->findOneByDbFileId($fileId)
            ->delete();
    }

    /**
     * Fetch the publication status for the file with the given ID.
     *
     * @param int $fileId the ID of the file to check
     *
     * @return int 1 if the file has been published,
     *             0 if the file has yet to be published,
     *             -1 if the file is in a pending state,
     *             2 if the source is unreachable (disconnected)
     */
    public function getPublishStatus($fileId)
    {
        $stationPodcast = StationPodcastQuery::create()
            ->findOneByDbPodcastId(Application_Model_Preference::getStationPodcastId());

        return (int) $stationPodcast->hasEpisodeForFile($fileId);
    }

    /**
     * Find any episode placeholders that have been stuck pending (empty file ID) for over
     * PENDING_EPISODE_TIMEOUT_SECONDS.
     *
     * @see Application_Service_PodcastEpisodeService::PENDING_EPISODE_TIMEOUT_SECONDS
     *
     * @return array the episode imports stuck in pending
     */
    public static function getStuckPendingImports()
    {
        $timeout = gmdate(DEFAULT_TIMESTAMP_FORMAT, microtime(true) - self::PENDING_EPISODE_TIMEOUT_SECONDS);
        $episodes = PodcastEpisodesQuery::create()
            ->filterByDbFileId()
            ->find();
        $stuckImports = [];
        foreach ($episodes as $episode) {
            $ref = ThirdPartyTrackReferencesQuery::create()
                ->findOneByDbForeignId(strval($episode->getDbId()));
            if (!empty($ref)) {
                $task = CeleryTasksQuery::create()
                    ->filterByDbDispatchTime($timeout, Criteria::LESS_EQUAL)
                    ->findOneByDbTrackReference($ref->getDbId());
                if (!empty($task)) {
                    array_push($stuckImports, $episode);
                }
            }
        }

        return $stuckImports;
    }

    /**
     * @param mixed $episodeId
     *
     * @return array
     *
     * @throws PodcastEpisodeNotFoundException
     */
    public static function getPodcastEpisodeById($episodeId)
    {
        $episode = PodcastEpisodesQuery::create()->findPk($episodeId);
        if (!$episode) {
            throw new PodcastEpisodeNotFoundException();
        }

        return $episode->toArray(BasePeer::TYPE_FIELDNAME);
    }

    /**
     * Returns an array of Podcast episodes, with the option to paginate the results.
     *
     * @param int    $offset
     * @param int    $limit
     * @param string $sortColumn
     * @param string $sortDir    "ASC" || "DESC"
     * @param mixed  $podcastId
     *
     * @return array
     *
     * @throws PodcastNotFoundException
     */
    public function getPodcastEpisodes(
        $podcastId,
        $offset = 0,
        $limit = 10,
        $sortColumn = PodcastEpisodesPeer::PUBLICATION_DATE,
        $sortDir = 'ASC'
    ) {
        $podcast = PodcastQuery::create()->findPk($podcastId);
        if (!$podcast) {
            throw new PodcastNotFoundException();
        }

        $sortDir = ($sortDir === 'DESC') ? $sortDir = Criteria::DESC : Criteria::ASC;
        $isStationPodcast = $podcastId == Application_Model_Preference::getStationPodcastId();

        $episodes = PodcastEpisodesQuery::create()
            ->filterByDbPodcastId($podcastId);
        if ($isStationPodcast && $limit != 0) {
            $episodes = $episodes->setLimit($limit);
        }
        // XXX: We should maybe try to alias this so we don't pass CcFiles as an array key to the frontend.
        //      It would require us to iterate over all the episodes and change the key for the response though...
        $episodes = $episodes->joinWith('PodcastEpisodes.CcFiles', Criteria::LEFT_JOIN)
            ->setOffset($offset)
            ->orderBy($sortColumn, $sortDir)
            ->find();

        return $isStationPodcast ? $this->_getStationPodcastEpisodeArray($episodes)
            : $this->_getImportedPodcastEpisodeArray($podcast, $episodes);
    }

    /**
     * Given an array of PodcastEpisodes objects from the Station Podcast,
     * convert the episode data into array form.
     *
     * @param array $episodes array of PodcastEpisodes to convert
     *
     * @return array
     */
    private function _getStationPodcastEpisodeArray($episodes)
    {
        $episodesArray = [];
        foreach ($episodes as $episode) {
            /** @var PodcastEpisodes $episode */
            $episodeArr = $episode->toArray(BasePeer::TYPE_FIELDNAME, true, [], true);
            array_push($episodesArray, $episodeArr);
        }

        return $episodesArray;
    }

    /**
     * Given an ImportedPodcast object and an array of stored PodcastEpisodes objects,
     * fetch all episodes from the podcast RSS feed, and serialize them in a readable form.
     *
     * TODO: there's definitely a better approach than this... we should be trying to create
     *       PodcastEpisdoes objects instead of our own arrays
     *
     * @param ImportedPodcast $podcast  Podcast object to fetch the episodes for
     * @param array           $episodes array of PodcastEpisodes objects to
     *
     * @return array array of episode data
     *
     * @throws CcFiles/LibreTimeFileNotFoundException
     */
    public function _getImportedPodcastEpisodeArray($podcast, $episodes)
    {
        $rss = Application_Service_PodcastService::getPodcastFeed($podcast->getDbUrl());
        $episodeIds = [];
        $episodeFiles = [];
        foreach ($episodes as $e) {
            // @var PodcastEpisodes $e
            array_push($episodeIds, $e->getDbEpisodeGuid());
            $episodeFiles[$e->getDbEpisodeGuid()] = $e->getDbFileId();
        }

        $episodesArray = [];
        foreach ($rss->get_items() as $item) {
            /** @var SimplePie_Item $item */
            // If the enclosure is empty or has not URL, this isn't a podcast episode (there's no audio data)
            // technically podcasts shouldn't have multiple enclosures but often CMS add non-audio files
            $enclosure = $item->get_enclosure();
            $url = $enclosure instanceof SimplePie_Enclosure ? $enclosure->get_link() : $enclosure['link'];
            if (empty($url)) {
                continue;
            }
            // next we check and see if the enclosure is not an audio file - this can happen from improperly
            // formatted podcasts and we instead will search through the enclosures and see if there is an audio item
            // then we pass that on, otherwise we just pass the first item since it is probably an audio file
            if (!(substr($enclosure->get_type(), 0, 5) === 'audio')) {
                // this is a rather hackish way of accessing the enclosures but get_enclosures() didnt detect multiple
                // enclosures at certain points so we search through them and then manually create an enclosure object
                // if we find an audio file in an enclosure and send it off
                Logging::info('found a non audio');
                $testenclosures = $enclosures = $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'enclosure');
                Logging::info($testenclosures);
                // we need to check if this is an array otherwise sizeof will fail and stop this whole script
                if (is_array($testenclosures)) {
                    $numenclosures = count($testenclosures);
                    // now we loop through and look for a audio file and then stop the loop at the first one we find
                    for ($i = 0; $i < $numenclosures + 1; ++$i) {
                        $enclosure_attribs = array_values($testenclosures[$i]['attribs'])[0];
                        if (stripos($enclosure_attribs['type'], 'audio') !== false) {
                            $url = $enclosure_attribs['url'];
                            $enclosure = new SimplePie_Enclosure($enclosure_attribs['url'], $enclosure_attribs['type'], $length = $enclosure_attribs['length']);

                            break;
                        }
                        // if we didn't find an audio file we need to continue because there were no audio item enclosures
                        // so this should keep it from showing items without audio items on the episodes list
                        if ($i = $numenclosures) {
                            continue;
                        }
                    }
                } else {
                    continue;
                }
            } else {
                $enclosure = $item->get_enclosure();
            }
            // Logging::info($enclosure);
            $itemId = $item->get_id();
            $ingested = in_array($itemId, $episodeIds) ? (empty($episodeFiles[$itemId]) ? -1 : 1) : 0;
            $file = $ingested > 0 && !empty($episodeFiles[$itemId]) ?
                CcFiles::getSanitizedFileById($episodeFiles[$itemId]) : [];
            // If the analyzer hasn't finished with the file, leave it as pending
            if (!empty($file) && $file['import_status'] == CcFiles::IMPORT_STATUS_PENDING) {
                $ingested = -1;
            }

            array_push($episodesArray, [
                'podcast_id' => $podcast->getDbId(),
                'guid' => $itemId,
                'ingested' => $ingested,
                'title' => $item->get_title(),
                // From the RSS spec best practices:
                // 'An item's author element provides the e-mail address of the person who wrote the item'
                'author' => $this->_buildAuthorString($item),
                'description' => htmlspecialchars($item->get_description()),
                'pub_date' => $item->get_gmdate(),
                'link' => $url,
                'enclosure' => $enclosure,
                'file' => $file,
            ]);
        }

        return $episodesArray;
    }

    /**
     * Construct a string representation of the author fields of a SimplePie_Item object.
     *
     * @param SimplePie_Item $item the SimplePie_Item to extract the author data from
     *
     * @return string the string representation of the author data
     */
    private function _buildAuthorString(SimplePie_Item $item)
    {
        $authorString = $author = $item->get_author();
        if (!empty($author)) {
            $authorString = $author->get_email();
            $authorString = empty($authorString) ? $author->get_name() : $authorString;
        }

        return $authorString;
    }

    public function deletePodcastEpisodeById($episodeId)
    {
        $episode = PodcastEpisodesQuery::create()->findByDbId($episodeId);

        if ($episode) {
            $episode->delete();
        } else {
            throw new PodcastEpisodeNotFoundException();
        }
    }

    private function removePrivateFields(&$data)
    {
        foreach (self::$privateFields as $key) {
            unset($data[$key]);
        }
    }
}
