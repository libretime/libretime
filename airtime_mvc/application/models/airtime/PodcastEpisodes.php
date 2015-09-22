<?php

class PodcastEpisodeNotFoundException extends Exception
{
}

/**
 * Skeleton subclass for representing a row from the 'podcast_episodes' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class PodcastEpisodes extends BasePodcastEpisodes
{

    private static $privateFields = array(
        "id"
    );

    /**
     * @param $episodeId
     * @return array
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

    public static function getPodcastEpisodes($podcastId)
    {
        $podcast = PodcastQuery::create()->findPk($podcastId);
        if (!$podcast) {
            throw new PodcastNotFoundException();
        }

        $episodes = PodcastEpisodesQuery::create()->findByDbPodcastId($podcastId);
        $episodesArray = array();
        foreach ($episodes as $episode) {
            array_push($episodesArray, $episode->toArray(BasePeer::TYPE_FIELDNAME));
        }

        return $episodesArray;
    }

    public static function create($podcastId, $data)
    {
        self::removePrivateFields($data);
        self::validateEpisodeData($data);

        try {
            $episode = new PodcastEpisodes();
            $episode->setDbPodcastId($podcastId);
            $episode->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $episode->save();

            return $episode->toArray(BasePeer::TYPE_FIELDNAME);
        } catch (Exception $e) {
            $episode->delete();
            throw $e;
        }

    }

    public static function deleteById($episodeId)
    {
        $episode = PodcastEpisodesQuery::create()->findByDbId($episodeId);

        if ($episode) {
            $episode->delete();
        } else {
            throw new PodcastEpisodeNotFoundException();
        }
    }

    private static function removePrivateFields(&$data)
    {
        foreach (self::$privateFields as $key) {
            unset($data[$key]);
        }
    }

    /**
     * Trims the episode data to fit the table's column max size
     *
     * @param $episodeArray
     */
    private static function validateEpisodeData(&$episodeArray)
    {
        $podcastEpisodeTable = PodcastEpisodesPeer::getTableMap();

        foreach ($episodeArray as $key => &$value) {
            try {
                // Make sure column exists in table
                $columnMaxSize = $podcastEpisodeTable->getColumn($key)->getSize();

                if (is_null($columnMaxSize)) {
                    continue;
                }
            } catch (PropelException $e) {
                continue;
            }

            if (strlen($value) > $columnMaxSize) {
                $value = substr($value, 0, $podcastEpisodeTable->getColumn($key)->getSize());
            }
        }
    }
}
