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
 * @package    propel.generator.campcaster.map
 */
class CcFilesTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'campcaster.map.CcFilesTableMap';

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
		$this->setPackage('campcaster');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_files_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('GUNID', 'Gunid', 'BIGINT', true, null, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', true, 255, '');
		$this->addColumn('MIME', 'Mime', 'VARCHAR', true, 255, '');
		$this->addColumn('FTYPE', 'Ftype', 'VARCHAR', true, 128, '');
		$this->addColumn('STATE', 'State', 'VARCHAR', true, 128, 'empty');
		$this->addColumn('CURRENTLYACCESSING', 'Currentlyaccessing', 'INTEGER', true, null, 0);
		$this->addForeignKey('EDITEDBY', 'Editedby', 'INTEGER', 'cc_subjs', 'ID', false, null, null);
		$this->addColumn('MTIME', 'Mtime', 'TIMESTAMP', false, 6, null);
		$this->addColumn('MD5', 'Md5', 'CHAR', false, 32, null);
		$this->addColumn('TRACK_TITLE', 'TrackTitle', 'VARCHAR', false, 512, null);
		$this->addColumn('ARTIST_NAME', 'ArtistName', 'VARCHAR', false, 512, null);
		$this->addColumn('BIT_RATE', 'BitRate', 'VARCHAR', false, 32, null);
		$this->addColumn('SAMPLE_RATE', 'SampleRate', 'VARCHAR', false, 32, null);
		$this->addColumn('FORMAT', 'Format', 'VARCHAR', false, 128, null);
		$this->addColumn('LENGTH', 'Length', 'TIME', false, null, null);
		$this->addColumn('ALBUM_TITLE', 'AlbumTitle', 'VARCHAR', false, 512, null);
		$this->addColumn('GENRE', 'Genre', 'VARCHAR', false, 64, null);
		$this->addColumn('COMMENTS', 'Comments', 'LONGVARCHAR', false, null, null);
		$this->addColumn('YEAR', 'Year', 'VARCHAR', false, 16, null);
		$this->addColumn('TRACK_NUMBER', 'TrackNumber', 'INTEGER', false, null, null);
		$this->addColumn('CHANNELS', 'Channels', 'INTEGER', false, null, null);
		$this->addColumn('URL', 'Url', 'VARCHAR', false, 1024, null);
		$this->addColumn('BPM', 'Bpm', 'VARCHAR', false, 8, null);
		$this->addColumn('RATING', 'Rating', 'VARCHAR', false, 8, null);
		$this->addColumn('ENCODED_BY', 'EncodedBy', 'VARCHAR', false, 255, null);
		$this->addColumn('DISC_NUMBER', 'DiscNumber', 'VARCHAR', false, 8, null);
		$this->addColumn('MOOD', 'Mood', 'VARCHAR', false, 64, null);
		$this->addColumn('LABEL', 'Label', 'VARCHAR', false, 512, null);
		$this->addColumn('COMPOSER', 'Composer', 'VARCHAR', false, 512, null);
		$this->addColumn('ENCODER', 'Encoder', 'VARCHAR', false, 64, null);
		$this->addColumn('CHECKSUM', 'Checksum', 'VARCHAR', false, 256, null);
		$this->addColumn('LYRICS', 'Lyrics', 'LONGVARCHAR', false, null, null);
		$this->addColumn('ORCHESTRA', 'Orchestra', 'VARCHAR', false, 512, null);
		$this->addColumn('CONDUCTOR', 'Conductor', 'VARCHAR', false, 512, null);
		$this->addColumn('LYRICIST', 'Lyricist', 'VARCHAR', false, 512, null);
		$this->addColumn('ORIGINAL_LYRICIST', 'OriginalLyricist', 'VARCHAR', false, 512, null);
		$this->addColumn('RADIO_STATION_NAME', 'RadioStationName', 'VARCHAR', false, 512, null);
		$this->addColumn('INFO_URL', 'InfoUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('ARTIST_URL', 'ArtistUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('AUDIO_SOURCE_URL', 'AudioSourceUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('RADIO_STATION_URL', 'RadioStationUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('BUY_THIS_URL', 'BuyThisUrl', 'VARCHAR', false, 512, null);
		$this->addColumn('ISRC_NUMBER', 'IsrcNumber', 'VARCHAR', false, 512, null);
		$this->addColumn('CATALOG_NUMBER', 'CatalogNumber', 'VARCHAR', false, 512, null);
		$this->addColumn('ORIGINAL_ARTIST', 'OriginalArtist', 'VARCHAR', false, 512, null);
		$this->addColumn('COPYRIGHT', 'Copyright', 'VARCHAR', false, 512, null);
		$this->addColumn('REPORT_DATETIME', 'ReportDatetime', 'VARCHAR', false, 32, null);
		$this->addColumn('REPORT_LOCATION', 'ReportLocation', 'VARCHAR', false, 512, null);
		$this->addColumn('REPORT_ORGANIZATION', 'ReportOrganization', 'VARCHAR', false, 512, null);
		$this->addColumn('SUBJECT', 'Subject', 'VARCHAR', false, 512, null);
		$this->addColumn('CONTRIBUTOR', 'Contributor', 'VARCHAR', false, 512, null);
		$this->addColumn('LANGUAGE', 'Language', 'VARCHAR', false, 512, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcSubjs', 'CcSubjs', RelationMap::MANY_TO_ONE, array('editedby' => 'id', ), null, null);
    $this->addRelation('CcPlaylistcontents', 'CcPlaylistcontents', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null);
	} // buildRelations()

} // CcFilesTableMap
