<?php

declare(strict_types=1);

/**
 * Skeleton subclass for representing a row from the 'imported_podcast' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class ImportedPodcast extends BaseImportedPodcast
{
    /*public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        $importedPodcastArray = parent::toArray(BasePeer::TYPE_FIELDNAME);
        //unset this ID so we only pass back the Podcast ID
        unset($importedPodcastArray["id"]);

        $podcastArray = $this->getPodcast()->toArray(BasePeer::TYPE_FIELDNAME);

        $array = array_merge($podcastArray, $importedPodcastArray);

        //unset podcast_id because we already have that value in $importedPodcastArray
        unset($array["podcast_id"]);

        return $array;
    }*/
}
