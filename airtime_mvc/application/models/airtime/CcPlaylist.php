<?php



/**
 * Skeleton subclass for representing a row from the 'cc_playlist' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.campcaster
 */
class CcPlaylist extends BaseCcPlaylist {


 	public function computeLastPosition()
    {
            $con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME);

            $sql = 'SELECT MAX('.CcPlaylistcontentsPeer::POSITION.') AS pos' 
            . ' FROM ' .CcPlaylistcontentsPeer::TABLE_NAME
            . ' WHERE ' .CcPlaylistcontentsPeer::PLAYLIST_ID. ' = :p1';

            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p1', $this->getDbId());
            $stmt->execute();
            return $stmt->fetchColumn();
    }

	public function computeLength()
    {
            $con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME);

            $sql = 'SELECT SUM('.CcPlaylistcontentsPeer::CLIPLENGTH.') AS length'
            . ' FROM ' .CcPlaylistcontentsPeer::TABLE_NAME
            . ' WHERE ' .CcPlaylistcontentsPeer::PLAYLIST_ID. ' = :p1';

            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p1', $this->getDbId());
            $stmt->execute();
            return $stmt->fetchColumn();
    }


} // CcPlaylist
