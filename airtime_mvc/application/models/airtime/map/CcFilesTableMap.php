<?php



/**
 * This class defines the structure of the 'cc_files' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.airtime.map
 */
class CcFilesTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcFilesTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('cc_files');
		$this->setPhpName('CcFiles');
		$this->setClassname('CcFiles');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_files_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('NAME', 'DbName', 'VARCHAR', true, 255, '');
		$this->addColumn('MIME', 'DbMime', 'VARCHAR', true, 255, '');
		$this->addColumn('FTYPE', 'DbFtype', 'VARCHAR', true, 128, '');
		$this->addForeignKey('DIRECTORY', 'DbDirectory', 'INTEGER', 'cc_music_dirs', 'ID', false, null, null);
		$this->addColumn('FILEPATH', 'DbFilepath', 'LONGVARCHAR', false, null, '');
		$this->addColumn('STATE', 'DbState', 'VARCHAR', true, 128, 'empty');
		$this->addColumn('CURRENTLYACCESSING', 'DbCurrentlyaccessing', 'INTEGER', true, null, 0);
		$this->addForeignKey('EDITEDBY', 'DbEditedby', 'INTEGER', 'cc_subjs', 'ID', false, null, null);
		$this->addColumn('MTIME', 'DbMtime', 'TIMESTAMP', false, 6, null);
		$this->addColumn('UTIME', 'DbUtime', 'TIMESTAMP', false, 6, null);
		$this->addColumn('LPTIME', 'DbLPtime', 'TIMESTAMP', false, 6, null);
		$this->addColumn('MD5', 'DbMd5', 'CHAR', false, 32, null);
		$this->addColumn('TRACK_TITLE', 'DbTrackTitle', 'VARCHAR', false, 512, null);
		$this->addColumn('ARTIST_NAME', 'DbArtistName', 'VARCHAR', false, 512, null);
		$this->addColumn('BIT_RATE', 'DbBitRate', 'INTEGER', false, null, null);
		$this->addColumn('SAMPLE_RATE', 'DbSampleRate', 'INTEGER', false, null, null);
		$this->addColumn('FORMAT', 'DbFormat', 'VARCHAR', false, 128, null);
		$this->addColumn('LENGTH', 'DbLength', 'VARCHAR', false, null, '00:00:00');
		$this->addColumn('ALBUM_TITLE', 'DbAlbumTitle', 'VARCHAR', false, 512, null);
		$this->addColumn('GENRE', 'DbGenre', 'VARCHAR', false, 64, null);
		$this->addColumn('COMMENTS', 'DbComments', 'LONGVARCHAR', false, null, null);
		$this->addColumn('YEAR', 'DbYear', 'VARCHAR', false, 16, null);
		$this->addColumn('TRACK_NUMBER', 'DbTrackNumber', 'INTEGER', false, null, null);
		$this->addColumn('CHANNELS', 'DbChannels', 'INTEGER', false, null, null);
		$this->addColumn('URL', 'DbUrl', 'VARCHAR', false, 1024, null);
		$this->addColumn('BPM', 'DbBpm', 'INTEGER', false, null, null);
		$this->addColumn('RATING', 'DbRating', 'VARCHAR', false, 8, null);
		$this->addColumn('ENCODED_BY', 'DbEncodedBy', 'VARCHAR', false, 255, null);
		$this->addColumn('DISC_NUMBER', 'DbDiscNumber', 'VARCHAR', false, 8, null);
		$this->addColumn('MOOD', 'DbMood', 'VARCHAR', false, 64, null);
		$this->addColumn('LABEL', 'DbLabel', 'VARCHAR', false, 512, null);
		$this->addColumn('COMPOSER', 'DbComposer', 'VARCHAR', false, 512, null);
		$this->addColumn('ENCODER', 'DbEncoder', 'VARCHAR', false, 64, null);
		$this->addColumn('CHECKSUM', 'DbChecksum', 'VARCHAR', false, 256, null);
		$this->addColumn('LYRICS', 'DbLyrics', 'LONGVARCHAR', false, null, null);
		$this->addColumn('ORCHESTRA', 'DbOrchestra', 'VARCHAR', false, 512, null);
		$this->addColumn('CONDUCTOR', 'DbConductor', 'VARCHAR', false, 512, null);
		$this->addColumn('LYRICIST', 'DbLyricist', 'VARCHAR', false, 512, null);
		$this->addColumn('ORIGINAL_LYRICIST', 'DbOriginalLyricist', 'VARCHAR', false, 512, null);
		$this->addColumn('RADIO_STATION_NAME', 'DbRadioStationName', 'VARCHAR', false, 512, null);
		$this->addColumn('INFO_URL', 'DbInfoUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('ARTIST_URL', 'DbArtistUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('AUDIO_SOURCE_URL', 'DbAudioSourceUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('RADIO_STATION_URL', 'DbRadioStationUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('BUY_THIS_URL', 'DbBuyThisUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('ISRC_NUMBER', 'DbIsrcNumber', 'VARCHAR', false, 512, null);
		$this->addColumn('CATALOG_NUMBER', 'DbCatalogNumber', 'VARCHAR', false, 512, null);
		$this->addColumn('ORIGINAL_ARTIST', 'DbOriginalArtist', 'VARCHAR', false, 512, null);
		$this->addColumn('COPYRIGHT', 'DbCopyright', 'VARCHAR', false, 512, null);
		$this->addColumn('REPORT_DATETIME', 'DbReportDatetime', 'VARCHAR', false, 32, null);
		$this->addColumn('REPORT_LOCATION', 'DbReportLocation', 'VARCHAR', false, 512, null);
		$this->addColumn('REPORT_ORGANIZATION', 'DbReportOrganization', 'VARCHAR', false, 512, null);
		$this->addColumn('SUBJECT', 'DbSubject', 'VARCHAR', false, 512, null);
		$this->addColumn('CONTRIBUTOR', 'DbContributor', 'VARCHAR', false, 512, null);
		$this->addColumn('LANGUAGE', 'DbLanguage', 'VARCHAR', false, 512, null);
		$this->addColumn('FILE_EXISTS', 'DbFileExists', 'BOOLEAN', false, null, true);
		$this->addColumn('SOUNDCLOUD_ID', 'DbSoundcloudId', 'INTEGER', false, null, null);
		$this->addColumn('SOUNDCLOUD_ERROR_CODE', 'DbSoundcloudErrorCode', 'INTEGER', false, null, null);
		$this->addColumn('SOUNDCLOUD_ERROR_MSG', 'DbSoundcloudErrorMsg', 'VARCHAR', false, 512, null);
		$this->addColumn('SOUNDCLOUD_LINK_TO_FILE', 'DbSoundcloudLinkToFile', 'VARCHAR', false, 4096, null);
		$this->addColumn('SOUNDCLOUD_UPLOAD_TIME', 'DbSoundCloundUploadTime', 'TIMESTAMP', false, 6, null);
		$this->addColumn('REPLAY_GAIN', 'DbReplayGain', 'NUMERIC', false, null, null);
		$this->addForeignKey('OWNER_ID', 'DbOwnerId', 'INTEGER', 'cc_subjs', 'ID', false, null, null);
		$this->addColumn('CUEIN', 'DbCuein', 'VARCHAR', false, null, '00:00:00');
		$this->addColumn('CUEOUT', 'DbCueout', 'VARCHAR', false, null, '00:00:00');
		$this->addColumn('SILAN_CHECK', 'DbSilanCheck', 'BOOLEAN', false, null, false);
		$this->addColumn('HIDDEN', 'DbHidden', 'BOOLEAN', false, null, false);
		$this->addColumn('IS_SCHEDULED', 'DbIsScheduled', 'BOOLEAN', false, null, false);
		$this->addColumn('IS_PLAYLIST', 'DbIsPlaylist', 'BOOLEAN', false, null, false);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('FkOwner', 'CcSubjs', RelationMap::MANY_TO_ONE, array('owner_id' => 'id', ), null, null);
    $this->addRelation('CcSubjsRelatedByDbEditedby', 'CcSubjs', RelationMap::MANY_TO_ONE, array('editedby' => 'id', ), null, null);
    $this->addRelation('CcMusicDirs', 'CcMusicDirs', RelationMap::MANY_TO_ONE, array('directory' => 'id', ), null, null);
    $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null);
    $this->addRelation('CcPlaylistcontents', 'CcPlaylistcontents', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null);
    $this->addRelation('CcBlockcontents', 'CcBlockcontents', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null);
    $this->addRelation('CcSchedule', 'CcSchedule', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null);
    $this->addRelation('CcPlayoutHistory', 'CcPlayoutHistory', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null);
	} // buildRelations()

} // CcFilesTableMap
