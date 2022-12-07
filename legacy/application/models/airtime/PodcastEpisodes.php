<?php

declare(strict_types=1);

/**
 * Skeleton subclass for representing a row from the 'podcast_episodes' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class PodcastEpisodes extends BasePodcastEpisodes
{
    /**
     * @override
     * We need to override this function in order to provide the rotating
     * download key for the station podcast.
     *
     * Get the [download_url] column value.
     *
     * @return string
     */
    public function getDbDownloadUrl()
    {
        $podcastId = $this->getDbPodcastId();
        // We may have more station podcasts later, so use this instead of checking the id stored in Preference
        $podcast = StationPodcastQuery::create()->findOneByDbPodcastId($podcastId);
        if ($podcast) {
            $fileId = $this->getDbFileId();
            // FIXME: this is an interim solution until we can do better...
            $file = CcFilesQuery::create()->findPk($fileId);
            $ext = FileDataHelper::getAudioMimeTypeArray()[$file->getDbMime()];
            $key = Application_Model_Preference::getStationPodcastDownloadKey();

            return Config::getPublicUrl() . "rest/media/{$fileId}/download/{$key}.{$ext}";
        }

        return parent::getDbDownloadUrl();
    }
}
