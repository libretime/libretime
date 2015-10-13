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
    /*public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        $podcastArray = parent::toArray(BasePeer::TYPE_FIELDNAME);
        $podcastArray["url"] = $this->getDbUrl();

        //$array = array_merge($podcastArray, $importedPodcastArray);

        //unset podcast_id because we already have that value in $importedPodcastArray
        //unset($array["podcast_id"]);

        return $podcastArray;
    }

    public function getDbUrl()
    {
        return $this->getImportedPodcasts()->getFirst()->getDbUrl();
    }*/
}
