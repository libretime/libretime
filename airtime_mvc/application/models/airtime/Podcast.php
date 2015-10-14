<?php



/**
 * Skeleton subclass for representing a row from the 'podcast' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class Podcast extends BasePodcast
{
    /**
     * Override this function so it returns its child class fields as well.
     * Child class will either be ImportedPodcast or StationPodcast
     *
     * @param string $keyType
     * @param bool $includeLazyLoadColumns
     * @param array $alreadyDumpedObjects
     * @param bool $includeForeignObjects
     * @return array
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        $podcastArray = parent::toArray(BasePeer::TYPE_FIELDNAME);

        $importedPodcast = ImportedPodcastQuery::create()->filterByDbPodcastId($this->getDbId())->findOne();
        $stationPodcast = StationPodcastQuery::create()->filterByDbPodcastId($this->getDbId())->findOne();
        if (!is_null($importedPodcast)) {
            $importedPodcastArray = $importedPodcast->toArray(BasePeer::TYPE_FIELDNAME);

            //unset these values because we already have the podcast id in $podcastArray
            //and we don't need the imported podcast ID
            unset($importedPodcastArray["id"]);
            unset($importedPodcastArray["podcast_id"]);

            return array_merge($podcastArray, $importedPodcastArray);

        } else if (!is_null($stationPodcast)) {
            // For now just return $podcastArray because StationPodcast objects do not have any
            // extra fields we want to return right now. This may change in the future.
            return $podcastArray;
        } else {
            return $podcastArray;
        }

    }

}
