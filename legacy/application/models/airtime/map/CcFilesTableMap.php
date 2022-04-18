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
class CcFilesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcFilesTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
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
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'DbName', 'VARCHAR', true, 255, '');
        $this->addColumn('mime', 'DbMime', 'VARCHAR', true, 255, '');
        $this->addColumn('ftype', 'DbFtype', 'VARCHAR', true, 128, '');
        $this->addColumn('filepath', 'DbFilepath', 'LONGVARCHAR', false, null, '');
        $this->addColumn('import_status', 'DbImportStatus', 'INTEGER', true, null, 1);
        $this->addColumn('currentlyaccessing', 'DbCurrentlyaccessing', 'INTEGER', true, null, 0);
        $this->addForeignKey('editedby', 'DbEditedby', 'INTEGER', 'cc_subjs', 'id', false, null, null);
        $this->addColumn('mtime', 'DbMtime', 'TIMESTAMP', false, 6, null);
        $this->addColumn('utime', 'DbUtime', 'TIMESTAMP', false, 6, null);
        $this->addColumn('lptime', 'DbLPtime', 'TIMESTAMP', false, 6, null);
        $this->addColumn('md5', 'DbMd5', 'CHAR', false, 32, null);
        $this->addColumn('track_title', 'DbTrackTitle', 'VARCHAR', false, 512, null);
        $this->addColumn('artist_name', 'DbArtistName', 'VARCHAR', false, 512, null);
        $this->addColumn('bit_rate', 'DbBitRate', 'INTEGER', false, null, null);
        $this->addColumn('sample_rate', 'DbSampleRate', 'INTEGER', false, null, null);
        $this->addColumn('format', 'DbFormat', 'VARCHAR', false, 128, null);
        $this->addColumn('length', 'DbLength', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('album_title', 'DbAlbumTitle', 'VARCHAR', false, 512, null);
        $this->addColumn('genre', 'DbGenre', 'VARCHAR', false, 64, null);
        $this->addColumn('comments', 'DbComments', 'LONGVARCHAR', false, null, null);
        $this->addColumn('year', 'DbYear', 'VARCHAR', false, 16, null);
        $this->addColumn('track_number', 'DbTrackNumber', 'INTEGER', false, null, null);
        $this->addColumn('channels', 'DbChannels', 'INTEGER', false, null, null);
        $this->addColumn('url', 'DbUrl', 'VARCHAR', false, 1024, null);
        $this->addColumn('bpm', 'DbBpm', 'INTEGER', false, null, null);
        $this->addColumn('rating', 'DbRating', 'VARCHAR', false, 8, null);
        $this->addColumn('encoded_by', 'DbEncodedBy', 'VARCHAR', false, 255, null);
        $this->addColumn('disc_number', 'DbDiscNumber', 'VARCHAR', false, 8, null);
        $this->addColumn('mood', 'DbMood', 'VARCHAR', false, 64, null);
        $this->addColumn('label', 'DbLabel', 'VARCHAR', false, 512, null);
        $this->addColumn('composer', 'DbComposer', 'VARCHAR', false, 512, null);
        $this->addColumn('encoder', 'DbEncoder', 'VARCHAR', false, 64, null);
        $this->addColumn('checksum', 'DbChecksum', 'VARCHAR', false, 256, null);
        $this->addColumn('lyrics', 'DbLyrics', 'LONGVARCHAR', false, null, null);
        $this->addColumn('orchestra', 'DbOrchestra', 'VARCHAR', false, 512, null);
        $this->addColumn('conductor', 'DbConductor', 'VARCHAR', false, 512, null);
        $this->addColumn('lyricist', 'DbLyricist', 'VARCHAR', false, 512, null);
        $this->addColumn('original_lyricist', 'DbOriginalLyricist', 'VARCHAR', false, 512, null);
        $this->addColumn('radio_station_name', 'DbRadioStationName', 'VARCHAR', false, 512, null);
        $this->addColumn('info_url', 'DbInfoUrl', 'VARCHAR', false, 512, null);
        $this->addColumn('artist_url', 'DbArtistUrl', 'VARCHAR', false, 512, null);
        $this->addColumn('audio_source_url', 'DbAudioSourceUrl', 'VARCHAR', false, 512, null);
        $this->addColumn('radio_station_url', 'DbRadioStationUrl', 'VARCHAR', false, 512, null);
        $this->addColumn('buy_this_url', 'DbBuyThisUrl', 'VARCHAR', false, 512, null);
        $this->addColumn('isrc_number', 'DbIsrcNumber', 'VARCHAR', false, 512, null);
        $this->addColumn('catalog_number', 'DbCatalogNumber', 'VARCHAR', false, 512, null);
        $this->addColumn('original_artist', 'DbOriginalArtist', 'VARCHAR', false, 512, null);
        $this->addColumn('copyright', 'DbCopyright', 'VARCHAR', false, 512, null);
        $this->addColumn('report_datetime', 'DbReportDatetime', 'VARCHAR', false, 32, null);
        $this->addColumn('report_location', 'DbReportLocation', 'VARCHAR', false, 512, null);
        $this->addColumn('report_organization', 'DbReportOrganization', 'VARCHAR', false, 512, null);
        $this->addColumn('subject', 'DbSubject', 'VARCHAR', false, 512, null);
        $this->addColumn('contributor', 'DbContributor', 'VARCHAR', false, 512, null);
        $this->addColumn('language', 'DbLanguage', 'VARCHAR', false, 512, null);
        $this->addColumn('file_exists', 'DbFileExists', 'BOOLEAN', false, null, true);
        $this->addColumn('replay_gain', 'DbReplayGain', 'NUMERIC', false, null, null);
        $this->addForeignKey('owner_id', 'DbOwnerId', 'INTEGER', 'cc_subjs', 'id', false, null, null);
        $this->addColumn('cuein', 'DbCuein', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('cueout', 'DbCueout', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('silan_check', 'DbSilanCheck', 'BOOLEAN', false, null, false);
        $this->addColumn('hidden', 'DbHidden', 'BOOLEAN', false, null, false);
        $this->addColumn('is_scheduled', 'DbIsScheduled', 'BOOLEAN', false, null, false);
        $this->addColumn('is_playlist', 'DbIsPlaylist', 'BOOLEAN', false, null, false);
        $this->addColumn('filesize', 'DbFilesize', 'INTEGER', true, null, 0);
        $this->addColumn('description', 'DbDescription', 'VARCHAR', false, 512, null);
        $this->addColumn('artwork', 'DbArtwork', 'VARCHAR', false, 4096, null);
        $this->addColumn('track_type', 'DbTrackType', 'VARCHAR', false, 16, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('FkOwner', 'CcSubjs', RelationMap::MANY_TO_ONE, array('owner_id' => 'id', ), null, null);
        $this->addRelation('CcSubjsRelatedByDbEditedby', 'CcSubjs', RelationMap::MANY_TO_ONE, array('editedby' => 'id', ), null, null);
        $this->addRelation('CloudFile', 'CloudFile', RelationMap::ONE_TO_MANY, array('id' => 'cc_file_id', ), 'CASCADE', null, 'CloudFiles');
        $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null, 'CcShowInstancess');
        $this->addRelation('CcPlaylistcontents', 'CcPlaylistcontents', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null, 'CcPlaylistcontentss');
        $this->addRelation('CcBlockcontents', 'CcBlockcontents', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null, 'CcBlockcontentss');
        $this->addRelation('CcSchedule', 'CcSchedule', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null, 'CcSchedules');
        $this->addRelation('CcPlayoutHistory', 'CcPlayoutHistory', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null, 'CcPlayoutHistorys');
        $this->addRelation('ThirdPartyTrackReferences', 'ThirdPartyTrackReferences', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null, 'ThirdPartyTrackReferencess');
        $this->addRelation('PodcastEpisodes', 'PodcastEpisodes', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null, 'PodcastEpisodess');
    } // buildRelations()

} // CcFilesTableMap
