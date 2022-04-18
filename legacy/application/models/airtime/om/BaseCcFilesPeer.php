<?php


/**
 * Base static class for performing query and update operations on the 'cc_files' table.
 *
 *
 *
 * @package propel.generator.airtime.om
 */
abstract class BaseCcFilesPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'airtime';

    /** the table name for this class */
    const TABLE_NAME = 'cc_files';

    /** the related Propel class for this table */
    const OM_CLASS = 'CcFiles';

    /** the related TableMap class for this table */
    const TM_CLASS = 'CcFilesTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 68;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 68;

    /** the column name for the id field */
    const ID = 'cc_files.id';

    /** the column name for the name field */
    const NAME = 'cc_files.name';

    /** the column name for the mime field */
    const MIME = 'cc_files.mime';

    /** the column name for the ftype field */
    const FTYPE = 'cc_files.ftype';

    /** the column name for the filepath field */
    const FILEPATH = 'cc_files.filepath';

    /** the column name for the import_status field */
    const IMPORT_STATUS = 'cc_files.import_status';

    /** the column name for the currentlyaccessing field */
    const CURRENTLYACCESSING = 'cc_files.currentlyaccessing';

    /** the column name for the editedby field */
    const EDITEDBY = 'cc_files.editedby';

    /** the column name for the mtime field */
    const MTIME = 'cc_files.mtime';

    /** the column name for the utime field */
    const UTIME = 'cc_files.utime';

    /** the column name for the lptime field */
    const LPTIME = 'cc_files.lptime';

    /** the column name for the md5 field */
    const MD5 = 'cc_files.md5';

    /** the column name for the track_title field */
    const TRACK_TITLE = 'cc_files.track_title';

    /** the column name for the artist_name field */
    const ARTIST_NAME = 'cc_files.artist_name';

    /** the column name for the bit_rate field */
    const BIT_RATE = 'cc_files.bit_rate';

    /** the column name for the sample_rate field */
    const SAMPLE_RATE = 'cc_files.sample_rate';

    /** the column name for the format field */
    const FORMAT = 'cc_files.format';

    /** the column name for the length field */
    const LENGTH = 'cc_files.length';

    /** the column name for the album_title field */
    const ALBUM_TITLE = 'cc_files.album_title';

    /** the column name for the genre field */
    const GENRE = 'cc_files.genre';

    /** the column name for the comments field */
    const COMMENTS = 'cc_files.comments';

    /** the column name for the year field */
    const YEAR = 'cc_files.year';

    /** the column name for the track_number field */
    const TRACK_NUMBER = 'cc_files.track_number';

    /** the column name for the channels field */
    const CHANNELS = 'cc_files.channels';

    /** the column name for the url field */
    const URL = 'cc_files.url';

    /** the column name for the bpm field */
    const BPM = 'cc_files.bpm';

    /** the column name for the rating field */
    const RATING = 'cc_files.rating';

    /** the column name for the encoded_by field */
    const ENCODED_BY = 'cc_files.encoded_by';

    /** the column name for the disc_number field */
    const DISC_NUMBER = 'cc_files.disc_number';

    /** the column name for the mood field */
    const MOOD = 'cc_files.mood';

    /** the column name for the label field */
    const LABEL = 'cc_files.label';

    /** the column name for the composer field */
    const COMPOSER = 'cc_files.composer';

    /** the column name for the encoder field */
    const ENCODER = 'cc_files.encoder';

    /** the column name for the checksum field */
    const CHECKSUM = 'cc_files.checksum';

    /** the column name for the lyrics field */
    const LYRICS = 'cc_files.lyrics';

    /** the column name for the orchestra field */
    const ORCHESTRA = 'cc_files.orchestra';

    /** the column name for the conductor field */
    const CONDUCTOR = 'cc_files.conductor';

    /** the column name for the lyricist field */
    const LYRICIST = 'cc_files.lyricist';

    /** the column name for the original_lyricist field */
    const ORIGINAL_LYRICIST = 'cc_files.original_lyricist';

    /** the column name for the radio_station_name field */
    const RADIO_STATION_NAME = 'cc_files.radio_station_name';

    /** the column name for the info_url field */
    const INFO_URL = 'cc_files.info_url';

    /** the column name for the artist_url field */
    const ARTIST_URL = 'cc_files.artist_url';

    /** the column name for the audio_source_url field */
    const AUDIO_SOURCE_URL = 'cc_files.audio_source_url';

    /** the column name for the radio_station_url field */
    const RADIO_STATION_URL = 'cc_files.radio_station_url';

    /** the column name for the buy_this_url field */
    const BUY_THIS_URL = 'cc_files.buy_this_url';

    /** the column name for the isrc_number field */
    const ISRC_NUMBER = 'cc_files.isrc_number';

    /** the column name for the catalog_number field */
    const CATALOG_NUMBER = 'cc_files.catalog_number';

    /** the column name for the original_artist field */
    const ORIGINAL_ARTIST = 'cc_files.original_artist';

    /** the column name for the copyright field */
    const COPYRIGHT = 'cc_files.copyright';

    /** the column name for the report_datetime field */
    const REPORT_DATETIME = 'cc_files.report_datetime';

    /** the column name for the report_location field */
    const REPORT_LOCATION = 'cc_files.report_location';

    /** the column name for the report_organization field */
    const REPORT_ORGANIZATION = 'cc_files.report_organization';

    /** the column name for the subject field */
    const SUBJECT = 'cc_files.subject';

    /** the column name for the contributor field */
    const CONTRIBUTOR = 'cc_files.contributor';

    /** the column name for the language field */
    const LANGUAGE = 'cc_files.language';

    /** the column name for the file_exists field */
    const FILE_EXISTS = 'cc_files.file_exists';

    /** the column name for the replay_gain field */
    const REPLAY_GAIN = 'cc_files.replay_gain';

    /** the column name for the owner_id field */
    const OWNER_ID = 'cc_files.owner_id';

    /** the column name for the cuein field */
    const CUEIN = 'cc_files.cuein';

    /** the column name for the cueout field */
    const CUEOUT = 'cc_files.cueout';

    /** the column name for the silan_check field */
    const SILAN_CHECK = 'cc_files.silan_check';

    /** the column name for the hidden field */
    const HIDDEN = 'cc_files.hidden';

    /** the column name for the is_scheduled field */
    const IS_SCHEDULED = 'cc_files.is_scheduled';

    /** the column name for the is_playlist field */
    const IS_PLAYLIST = 'cc_files.is_playlist';

    /** the column name for the filesize field */
    const FILESIZE = 'cc_files.filesize';

    /** the column name for the description field */
    const DESCRIPTION = 'cc_files.description';

    /** the column name for the artwork field */
    const ARTWORK = 'cc_files.artwork';

    /** the column name for the track_type field */
    const TRACK_TYPE = 'cc_files.track_type';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of CcFiles objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array CcFiles[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. CcFilesPeer::$fieldNames[CcFilesPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DbId', 'DbName', 'DbMime', 'DbFtype', 'DbFilepath', 'DbImportStatus', 'DbCurrentlyaccessing', 'DbEditedby', 'DbMtime', 'DbUtime', 'DbLPtime', 'DbMd5', 'DbTrackTitle', 'DbArtistName', 'DbBitRate', 'DbSampleRate', 'DbFormat', 'DbLength', 'DbAlbumTitle', 'DbGenre', 'DbComments', 'DbYear', 'DbTrackNumber', 'DbChannels', 'DbUrl', 'DbBpm', 'DbRating', 'DbEncodedBy', 'DbDiscNumber', 'DbMood', 'DbLabel', 'DbComposer', 'DbEncoder', 'DbChecksum', 'DbLyrics', 'DbOrchestra', 'DbConductor', 'DbLyricist', 'DbOriginalLyricist', 'DbRadioStationName', 'DbInfoUrl', 'DbArtistUrl', 'DbAudioSourceUrl', 'DbRadioStationUrl', 'DbBuyThisUrl', 'DbIsrcNumber', 'DbCatalogNumber', 'DbOriginalArtist', 'DbCopyright', 'DbReportDatetime', 'DbReportLocation', 'DbReportOrganization', 'DbSubject', 'DbContributor', 'DbLanguage', 'DbFileExists', 'DbReplayGain', 'DbOwnerId', 'DbCuein', 'DbCueout', 'DbSilanCheck', 'DbHidden', 'DbIsScheduled', 'DbIsPlaylist', 'DbFilesize', 'DbDescription', 'DbArtwork', 'DbTrackType', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbName', 'dbMime', 'dbFtype', 'dbFilepath', 'dbImportStatus', 'dbCurrentlyaccessing', 'dbEditedby', 'dbMtime', 'dbUtime', 'dbLPtime', 'dbMd5', 'dbTrackTitle', 'dbArtistName', 'dbBitRate', 'dbSampleRate', 'dbFormat', 'dbLength', 'dbAlbumTitle', 'dbGenre', 'dbComments', 'dbYear', 'dbTrackNumber', 'dbChannels', 'dbUrl', 'dbBpm', 'dbRating', 'dbEncodedBy', 'dbDiscNumber', 'dbMood', 'dbLabel', 'dbComposer', 'dbEncoder', 'dbChecksum', 'dbLyrics', 'dbOrchestra', 'dbConductor', 'dbLyricist', 'dbOriginalLyricist', 'dbRadioStationName', 'dbInfoUrl', 'dbArtistUrl', 'dbAudioSourceUrl', 'dbRadioStationUrl', 'dbBuyThisUrl', 'dbIsrcNumber', 'dbCatalogNumber', 'dbOriginalArtist', 'dbCopyright', 'dbReportDatetime', 'dbReportLocation', 'dbReportOrganization', 'dbSubject', 'dbContributor', 'dbLanguage', 'dbFileExists', 'dbReplayGain', 'dbOwnerId', 'dbCuein', 'dbCueout', 'dbSilanCheck', 'dbHidden', 'dbIsScheduled', 'dbIsPlaylist', 'dbFilesize', 'dbDescription', 'dbArtwork', 'dbTrackType', ),
        BasePeer::TYPE_COLNAME => array (CcFilesPeer::ID, CcFilesPeer::NAME, CcFilesPeer::MIME, CcFilesPeer::FTYPE, CcFilesPeer::FILEPATH, CcFilesPeer::IMPORT_STATUS, CcFilesPeer::CURRENTLYACCESSING, CcFilesPeer::EDITEDBY, CcFilesPeer::MTIME, CcFilesPeer::UTIME, CcFilesPeer::LPTIME, CcFilesPeer::MD5, CcFilesPeer::TRACK_TITLE, CcFilesPeer::ARTIST_NAME, CcFilesPeer::BIT_RATE, CcFilesPeer::SAMPLE_RATE, CcFilesPeer::FORMAT, CcFilesPeer::LENGTH, CcFilesPeer::ALBUM_TITLE, CcFilesPeer::GENRE, CcFilesPeer::COMMENTS, CcFilesPeer::YEAR, CcFilesPeer::TRACK_NUMBER, CcFilesPeer::CHANNELS, CcFilesPeer::URL, CcFilesPeer::BPM, CcFilesPeer::RATING, CcFilesPeer::ENCODED_BY, CcFilesPeer::DISC_NUMBER, CcFilesPeer::MOOD, CcFilesPeer::LABEL, CcFilesPeer::COMPOSER, CcFilesPeer::ENCODER, CcFilesPeer::CHECKSUM, CcFilesPeer::LYRICS, CcFilesPeer::ORCHESTRA, CcFilesPeer::CONDUCTOR, CcFilesPeer::LYRICIST, CcFilesPeer::ORIGINAL_LYRICIST, CcFilesPeer::RADIO_STATION_NAME, CcFilesPeer::INFO_URL, CcFilesPeer::ARTIST_URL, CcFilesPeer::AUDIO_SOURCE_URL, CcFilesPeer::RADIO_STATION_URL, CcFilesPeer::BUY_THIS_URL, CcFilesPeer::ISRC_NUMBER, CcFilesPeer::CATALOG_NUMBER, CcFilesPeer::ORIGINAL_ARTIST, CcFilesPeer::COPYRIGHT, CcFilesPeer::REPORT_DATETIME, CcFilesPeer::REPORT_LOCATION, CcFilesPeer::REPORT_ORGANIZATION, CcFilesPeer::SUBJECT, CcFilesPeer::CONTRIBUTOR, CcFilesPeer::LANGUAGE, CcFilesPeer::FILE_EXISTS, CcFilesPeer::REPLAY_GAIN, CcFilesPeer::OWNER_ID, CcFilesPeer::CUEIN, CcFilesPeer::CUEOUT, CcFilesPeer::SILAN_CHECK, CcFilesPeer::HIDDEN, CcFilesPeer::IS_SCHEDULED, CcFilesPeer::IS_PLAYLIST, CcFilesPeer::FILESIZE, CcFilesPeer::DESCRIPTION, CcFilesPeer::ARTWORK, CcFilesPeer::TRACK_TYPE, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'NAME', 'MIME', 'FTYPE', 'FILEPATH', 'IMPORT_STATUS', 'CURRENTLYACCESSING', 'EDITEDBY', 'MTIME', 'UTIME', 'LPTIME', 'MD5', 'TRACK_TITLE', 'ARTIST_NAME', 'BIT_RATE', 'SAMPLE_RATE', 'FORMAT', 'LENGTH', 'ALBUM_TITLE', 'GENRE', 'COMMENTS', 'YEAR', 'TRACK_NUMBER', 'CHANNELS', 'URL', 'BPM', 'RATING', 'ENCODED_BY', 'DISC_NUMBER', 'MOOD', 'LABEL', 'COMPOSER', 'ENCODER', 'CHECKSUM', 'LYRICS', 'ORCHESTRA', 'CONDUCTOR', 'LYRICIST', 'ORIGINAL_LYRICIST', 'RADIO_STATION_NAME', 'INFO_URL', 'ARTIST_URL', 'AUDIO_SOURCE_URL', 'RADIO_STATION_URL', 'BUY_THIS_URL', 'ISRC_NUMBER', 'CATALOG_NUMBER', 'ORIGINAL_ARTIST', 'COPYRIGHT', 'REPORT_DATETIME', 'REPORT_LOCATION', 'REPORT_ORGANIZATION', 'SUBJECT', 'CONTRIBUTOR', 'LANGUAGE', 'FILE_EXISTS', 'REPLAY_GAIN', 'OWNER_ID', 'CUEIN', 'CUEOUT', 'SILAN_CHECK', 'HIDDEN', 'IS_SCHEDULED', 'IS_PLAYLIST', 'FILESIZE', 'DESCRIPTION', 'ARTWORK', 'TRACK_TYPE', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'name', 'mime', 'ftype', 'filepath', 'import_status', 'currentlyaccessing', 'editedby', 'mtime', 'utime', 'lptime', 'md5', 'track_title', 'artist_name', 'bit_rate', 'sample_rate', 'format', 'length', 'album_title', 'genre', 'comments', 'year', 'track_number', 'channels', 'url', 'bpm', 'rating', 'encoded_by', 'disc_number', 'mood', 'label', 'composer', 'encoder', 'checksum', 'lyrics', 'orchestra', 'conductor', 'lyricist', 'original_lyricist', 'radio_station_name', 'info_url', 'artist_url', 'audio_source_url', 'radio_station_url', 'buy_this_url', 'isrc_number', 'catalog_number', 'original_artist', 'copyright', 'report_datetime', 'report_location', 'report_organization', 'subject', 'contributor', 'language', 'file_exists', 'replay_gain', 'owner_id', 'cuein', 'cueout', 'silan_check', 'hidden', 'is_scheduled', 'is_playlist', 'filesize', 'description', 'artwork', 'track_type', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. CcFilesPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbName' => 1, 'DbMime' => 2, 'DbFtype' => 3, 'DbFilepath' => 4, 'DbImportStatus' => 5, 'DbCurrentlyaccessing' => 6, 'DbEditedby' => 7, 'DbMtime' => 8, 'DbUtime' => 9, 'DbLPtime' => 10, 'DbMd5' => 11, 'DbTrackTitle' => 12, 'DbArtistName' => 13, 'DbBitRate' => 14, 'DbSampleRate' => 15, 'DbFormat' => 16, 'DbLength' => 17, 'DbAlbumTitle' => 18, 'DbGenre' => 19, 'DbComments' => 20, 'DbYear' => 21, 'DbTrackNumber' => 22, 'DbChannels' => 23, 'DbUrl' => 24, 'DbBpm' => 25, 'DbRating' => 26, 'DbEncodedBy' => 27, 'DbDiscNumber' => 28, 'DbMood' => 29, 'DbLabel' => 30, 'DbComposer' => 31, 'DbEncoder' => 32, 'DbChecksum' => 33, 'DbLyrics' => 34, 'DbOrchestra' => 35, 'DbConductor' => 36, 'DbLyricist' => 37, 'DbOriginalLyricist' => 38, 'DbRadioStationName' => 39, 'DbInfoUrl' => 40, 'DbArtistUrl' => 41, 'DbAudioSourceUrl' => 42, 'DbRadioStationUrl' => 43, 'DbBuyThisUrl' => 44, 'DbIsrcNumber' => 45, 'DbCatalogNumber' => 46, 'DbOriginalArtist' => 47, 'DbCopyright' => 48, 'DbReportDatetime' => 49, 'DbReportLocation' => 50, 'DbReportOrganization' => 51, 'DbSubject' => 52, 'DbContributor' => 53, 'DbLanguage' => 54, 'DbFileExists' => 55, 'DbReplayGain' => 56, 'DbOwnerId' => 57, 'DbCuein' => 58, 'DbCueout' => 59, 'DbSilanCheck' => 60, 'DbHidden' => 61, 'DbIsScheduled' => 62, 'DbIsPlaylist' => 63, 'DbFilesize' => 64, 'DbDescription' => 65, 'DbArtwork' => 66, 'DbTrackType' => 67, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbName' => 1, 'dbMime' => 2, 'dbFtype' => 3, 'dbFilepath' => 4, 'dbImportStatus' => 5, 'dbCurrentlyaccessing' => 6, 'dbEditedby' => 7, 'dbMtime' => 8, 'dbUtime' => 9, 'dbLPtime' => 10, 'dbMd5' => 11, 'dbTrackTitle' => 12, 'dbArtistName' => 13, 'dbBitRate' => 14, 'dbSampleRate' => 15, 'dbFormat' => 16, 'dbLength' => 17, 'dbAlbumTitle' => 18, 'dbGenre' => 19, 'dbComments' => 20, 'dbYear' => 21, 'dbTrackNumber' => 22, 'dbChannels' => 23, 'dbUrl' => 24, 'dbBpm' => 25, 'dbRating' => 26, 'dbEncodedBy' => 27, 'dbDiscNumber' => 28, 'dbMood' => 29, 'dbLabel' => 30, 'dbComposer' => 31, 'dbEncoder' => 32, 'dbChecksum' => 33, 'dbLyrics' => 34, 'dbOrchestra' => 35, 'dbConductor' => 36, 'dbLyricist' => 37, 'dbOriginalLyricist' => 38, 'dbRadioStationName' => 39, 'dbInfoUrl' => 40, 'dbArtistUrl' => 41, 'dbAudioSourceUrl' => 42, 'dbRadioStationUrl' => 43, 'dbBuyThisUrl' => 44, 'dbIsrcNumber' => 45, 'dbCatalogNumber' => 46, 'dbOriginalArtist' => 47, 'dbCopyright' => 48, 'dbReportDatetime' => 49, 'dbReportLocation' => 50, 'dbReportOrganization' => 51, 'dbSubject' => 52, 'dbContributor' => 53, 'dbLanguage' => 54, 'dbFileExists' => 55, 'dbReplayGain' => 56, 'dbOwnerId' => 57, 'dbCuein' => 58, 'dbCueout' => 59, 'dbSilanCheck' => 60, 'dbHidden' => 61, 'dbIsScheduled' => 62, 'dbIsPlaylist' => 63, 'dbFilesize' => 64, 'dbDescription' => 65, 'dbArtwork' => 66, 'dbTrackType' => 67, ),
        BasePeer::TYPE_COLNAME => array (CcFilesPeer::ID => 0, CcFilesPeer::NAME => 1, CcFilesPeer::MIME => 2, CcFilesPeer::FTYPE => 3, CcFilesPeer::FILEPATH => 4, CcFilesPeer::IMPORT_STATUS => 5, CcFilesPeer::CURRENTLYACCESSING => 6, CcFilesPeer::EDITEDBY => 7, CcFilesPeer::MTIME => 8, CcFilesPeer::UTIME => 9, CcFilesPeer::LPTIME => 10, CcFilesPeer::MD5 => 11, CcFilesPeer::TRACK_TITLE => 12, CcFilesPeer::ARTIST_NAME => 13, CcFilesPeer::BIT_RATE => 14, CcFilesPeer::SAMPLE_RATE => 15, CcFilesPeer::FORMAT => 16, CcFilesPeer::LENGTH => 17, CcFilesPeer::ALBUM_TITLE => 18, CcFilesPeer::GENRE => 19, CcFilesPeer::COMMENTS => 20, CcFilesPeer::YEAR => 21, CcFilesPeer::TRACK_NUMBER => 22, CcFilesPeer::CHANNELS => 23, CcFilesPeer::URL => 24, CcFilesPeer::BPM => 25, CcFilesPeer::RATING => 26, CcFilesPeer::ENCODED_BY => 27, CcFilesPeer::DISC_NUMBER => 28, CcFilesPeer::MOOD => 29, CcFilesPeer::LABEL => 30, CcFilesPeer::COMPOSER => 31, CcFilesPeer::ENCODER => 32, CcFilesPeer::CHECKSUM => 33, CcFilesPeer::LYRICS => 34, CcFilesPeer::ORCHESTRA => 35, CcFilesPeer::CONDUCTOR => 36, CcFilesPeer::LYRICIST => 37, CcFilesPeer::ORIGINAL_LYRICIST => 38, CcFilesPeer::RADIO_STATION_NAME => 39, CcFilesPeer::INFO_URL => 40, CcFilesPeer::ARTIST_URL => 41, CcFilesPeer::AUDIO_SOURCE_URL => 42, CcFilesPeer::RADIO_STATION_URL => 43, CcFilesPeer::BUY_THIS_URL => 44, CcFilesPeer::ISRC_NUMBER => 45, CcFilesPeer::CATALOG_NUMBER => 46, CcFilesPeer::ORIGINAL_ARTIST => 47, CcFilesPeer::COPYRIGHT => 48, CcFilesPeer::REPORT_DATETIME => 49, CcFilesPeer::REPORT_LOCATION => 50, CcFilesPeer::REPORT_ORGANIZATION => 51, CcFilesPeer::SUBJECT => 52, CcFilesPeer::CONTRIBUTOR => 53, CcFilesPeer::LANGUAGE => 54, CcFilesPeer::FILE_EXISTS => 55, CcFilesPeer::REPLAY_GAIN => 56, CcFilesPeer::OWNER_ID => 57, CcFilesPeer::CUEIN => 58, CcFilesPeer::CUEOUT => 59, CcFilesPeer::SILAN_CHECK => 60, CcFilesPeer::HIDDEN => 61, CcFilesPeer::IS_SCHEDULED => 62, CcFilesPeer::IS_PLAYLIST => 63, CcFilesPeer::FILESIZE => 64, CcFilesPeer::DESCRIPTION => 65, CcFilesPeer::ARTWORK => 66, CcFilesPeer::TRACK_TYPE => 67, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'NAME' => 1, 'MIME' => 2, 'FTYPE' => 3, 'FILEPATH' => 4, 'IMPORT_STATUS' => 5, 'CURRENTLYACCESSING' => 6, 'EDITEDBY' => 7, 'MTIME' => 8, 'UTIME' => 9, 'LPTIME' => 10, 'MD5' => 11, 'TRACK_TITLE' => 12, 'ARTIST_NAME' => 13, 'BIT_RATE' => 14, 'SAMPLE_RATE' => 15, 'FORMAT' => 16, 'LENGTH' => 17, 'ALBUM_TITLE' => 18, 'GENRE' => 19, 'COMMENTS' => 20, 'YEAR' => 21, 'TRACK_NUMBER' => 22, 'CHANNELS' => 23, 'URL' => 24, 'BPM' => 25, 'RATING' => 26, 'ENCODED_BY' => 27, 'DISC_NUMBER' => 28, 'MOOD' => 29, 'LABEL' => 30, 'COMPOSER' => 31, 'ENCODER' => 32, 'CHECKSUM' => 33, 'LYRICS' => 34, 'ORCHESTRA' => 35, 'CONDUCTOR' => 36, 'LYRICIST' => 37, 'ORIGINAL_LYRICIST' => 38, 'RADIO_STATION_NAME' => 39, 'INFO_URL' => 40, 'ARTIST_URL' => 41, 'AUDIO_SOURCE_URL' => 42, 'RADIO_STATION_URL' => 43, 'BUY_THIS_URL' => 44, 'ISRC_NUMBER' => 45, 'CATALOG_NUMBER' => 46, 'ORIGINAL_ARTIST' => 47, 'COPYRIGHT' => 48, 'REPORT_DATETIME' => 49, 'REPORT_LOCATION' => 50, 'REPORT_ORGANIZATION' => 51, 'SUBJECT' => 52, 'CONTRIBUTOR' => 53, 'LANGUAGE' => 54, 'FILE_EXISTS' => 55, 'REPLAY_GAIN' => 56, 'OWNER_ID' => 57, 'CUEIN' => 58, 'CUEOUT' => 59, 'SILAN_CHECK' => 60, 'HIDDEN' => 61, 'IS_SCHEDULED' => 62, 'IS_PLAYLIST' => 63, 'FILESIZE' => 64, 'DESCRIPTION' => 65, 'ARTWORK' => 66, 'TRACK_TYPE' => 67, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'name' => 1, 'mime' => 2, 'ftype' => 3, 'filepath' => 4, 'import_status' => 5, 'currentlyaccessing' => 6, 'editedby' => 7, 'mtime' => 8, 'utime' => 9, 'lptime' => 10, 'md5' => 11, 'track_title' => 12, 'artist_name' => 13, 'bit_rate' => 14, 'sample_rate' => 15, 'format' => 16, 'length' => 17, 'album_title' => 18, 'genre' => 19, 'comments' => 20, 'year' => 21, 'track_number' => 22, 'channels' => 23, 'url' => 24, 'bpm' => 25, 'rating' => 26, 'encoded_by' => 27, 'disc_number' => 28, 'mood' => 29, 'label' => 30, 'composer' => 31, 'encoder' => 32, 'checksum' => 33, 'lyrics' => 34, 'orchestra' => 35, 'conductor' => 36, 'lyricist' => 37, 'original_lyricist' => 38, 'radio_station_name' => 39, 'info_url' => 40, 'artist_url' => 41, 'audio_source_url' => 42, 'radio_station_url' => 43, 'buy_this_url' => 44, 'isrc_number' => 45, 'catalog_number' => 46, 'original_artist' => 47, 'copyright' => 48, 'report_datetime' => 49, 'report_location' => 50, 'report_organization' => 51, 'subject' => 52, 'contributor' => 53, 'language' => 54, 'file_exists' => 55, 'replay_gain' => 56, 'owner_id' => 57, 'cuein' => 58, 'cueout' => 59, 'silan_check' => 60, 'hidden' => 61, 'is_scheduled' => 62, 'is_playlist' => 63, 'filesize' => 64, 'description' => 65, 'artwork' => 66, 'track_type' => 67, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = CcFilesPeer::getFieldNames($toType);
        $key = isset(CcFilesPeer::$fieldKeys[$fromType][$name]) ? CcFilesPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(CcFilesPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, CcFilesPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return CcFilesPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. CcFilesPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(CcFilesPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CcFilesPeer::ID);
            $criteria->addSelectColumn(CcFilesPeer::NAME);
            $criteria->addSelectColumn(CcFilesPeer::MIME);
            $criteria->addSelectColumn(CcFilesPeer::FTYPE);
            $criteria->addSelectColumn(CcFilesPeer::FILEPATH);
            $criteria->addSelectColumn(CcFilesPeer::IMPORT_STATUS);
            $criteria->addSelectColumn(CcFilesPeer::CURRENTLYACCESSING);
            $criteria->addSelectColumn(CcFilesPeer::EDITEDBY);
            $criteria->addSelectColumn(CcFilesPeer::MTIME);
            $criteria->addSelectColumn(CcFilesPeer::UTIME);
            $criteria->addSelectColumn(CcFilesPeer::LPTIME);
            $criteria->addSelectColumn(CcFilesPeer::MD5);
            $criteria->addSelectColumn(CcFilesPeer::TRACK_TITLE);
            $criteria->addSelectColumn(CcFilesPeer::ARTIST_NAME);
            $criteria->addSelectColumn(CcFilesPeer::BIT_RATE);
            $criteria->addSelectColumn(CcFilesPeer::SAMPLE_RATE);
            $criteria->addSelectColumn(CcFilesPeer::FORMAT);
            $criteria->addSelectColumn(CcFilesPeer::LENGTH);
            $criteria->addSelectColumn(CcFilesPeer::ALBUM_TITLE);
            $criteria->addSelectColumn(CcFilesPeer::GENRE);
            $criteria->addSelectColumn(CcFilesPeer::COMMENTS);
            $criteria->addSelectColumn(CcFilesPeer::YEAR);
            $criteria->addSelectColumn(CcFilesPeer::TRACK_NUMBER);
            $criteria->addSelectColumn(CcFilesPeer::CHANNELS);
            $criteria->addSelectColumn(CcFilesPeer::URL);
            $criteria->addSelectColumn(CcFilesPeer::BPM);
            $criteria->addSelectColumn(CcFilesPeer::RATING);
            $criteria->addSelectColumn(CcFilesPeer::ENCODED_BY);
            $criteria->addSelectColumn(CcFilesPeer::DISC_NUMBER);
            $criteria->addSelectColumn(CcFilesPeer::MOOD);
            $criteria->addSelectColumn(CcFilesPeer::LABEL);
            $criteria->addSelectColumn(CcFilesPeer::COMPOSER);
            $criteria->addSelectColumn(CcFilesPeer::ENCODER);
            $criteria->addSelectColumn(CcFilesPeer::CHECKSUM);
            $criteria->addSelectColumn(CcFilesPeer::LYRICS);
            $criteria->addSelectColumn(CcFilesPeer::ORCHESTRA);
            $criteria->addSelectColumn(CcFilesPeer::CONDUCTOR);
            $criteria->addSelectColumn(CcFilesPeer::LYRICIST);
            $criteria->addSelectColumn(CcFilesPeer::ORIGINAL_LYRICIST);
            $criteria->addSelectColumn(CcFilesPeer::RADIO_STATION_NAME);
            $criteria->addSelectColumn(CcFilesPeer::INFO_URL);
            $criteria->addSelectColumn(CcFilesPeer::ARTIST_URL);
            $criteria->addSelectColumn(CcFilesPeer::AUDIO_SOURCE_URL);
            $criteria->addSelectColumn(CcFilesPeer::RADIO_STATION_URL);
            $criteria->addSelectColumn(CcFilesPeer::BUY_THIS_URL);
            $criteria->addSelectColumn(CcFilesPeer::ISRC_NUMBER);
            $criteria->addSelectColumn(CcFilesPeer::CATALOG_NUMBER);
            $criteria->addSelectColumn(CcFilesPeer::ORIGINAL_ARTIST);
            $criteria->addSelectColumn(CcFilesPeer::COPYRIGHT);
            $criteria->addSelectColumn(CcFilesPeer::REPORT_DATETIME);
            $criteria->addSelectColumn(CcFilesPeer::REPORT_LOCATION);
            $criteria->addSelectColumn(CcFilesPeer::REPORT_ORGANIZATION);
            $criteria->addSelectColumn(CcFilesPeer::SUBJECT);
            $criteria->addSelectColumn(CcFilesPeer::CONTRIBUTOR);
            $criteria->addSelectColumn(CcFilesPeer::LANGUAGE);
            $criteria->addSelectColumn(CcFilesPeer::FILE_EXISTS);
            $criteria->addSelectColumn(CcFilesPeer::REPLAY_GAIN);
            $criteria->addSelectColumn(CcFilesPeer::OWNER_ID);
            $criteria->addSelectColumn(CcFilesPeer::CUEIN);
            $criteria->addSelectColumn(CcFilesPeer::CUEOUT);
            $criteria->addSelectColumn(CcFilesPeer::SILAN_CHECK);
            $criteria->addSelectColumn(CcFilesPeer::HIDDEN);
            $criteria->addSelectColumn(CcFilesPeer::IS_SCHEDULED);
            $criteria->addSelectColumn(CcFilesPeer::IS_PLAYLIST);
            $criteria->addSelectColumn(CcFilesPeer::FILESIZE);
            $criteria->addSelectColumn(CcFilesPeer::DESCRIPTION);
            $criteria->addSelectColumn(CcFilesPeer::ARTWORK);
            $criteria->addSelectColumn(CcFilesPeer::TRACK_TYPE);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.mime');
            $criteria->addSelectColumn($alias . '.ftype');
            $criteria->addSelectColumn($alias . '.filepath');
            $criteria->addSelectColumn($alias . '.import_status');
            $criteria->addSelectColumn($alias . '.currentlyaccessing');
            $criteria->addSelectColumn($alias . '.editedby');
            $criteria->addSelectColumn($alias . '.mtime');
            $criteria->addSelectColumn($alias . '.utime');
            $criteria->addSelectColumn($alias . '.lptime');
            $criteria->addSelectColumn($alias . '.md5');
            $criteria->addSelectColumn($alias . '.track_title');
            $criteria->addSelectColumn($alias . '.artist_name');
            $criteria->addSelectColumn($alias . '.bit_rate');
            $criteria->addSelectColumn($alias . '.sample_rate');
            $criteria->addSelectColumn($alias . '.format');
            $criteria->addSelectColumn($alias . '.length');
            $criteria->addSelectColumn($alias . '.album_title');
            $criteria->addSelectColumn($alias . '.genre');
            $criteria->addSelectColumn($alias . '.comments');
            $criteria->addSelectColumn($alias . '.year');
            $criteria->addSelectColumn($alias . '.track_number');
            $criteria->addSelectColumn($alias . '.channels');
            $criteria->addSelectColumn($alias . '.url');
            $criteria->addSelectColumn($alias . '.bpm');
            $criteria->addSelectColumn($alias . '.rating');
            $criteria->addSelectColumn($alias . '.encoded_by');
            $criteria->addSelectColumn($alias . '.disc_number');
            $criteria->addSelectColumn($alias . '.mood');
            $criteria->addSelectColumn($alias . '.label');
            $criteria->addSelectColumn($alias . '.composer');
            $criteria->addSelectColumn($alias . '.encoder');
            $criteria->addSelectColumn($alias . '.checksum');
            $criteria->addSelectColumn($alias . '.lyrics');
            $criteria->addSelectColumn($alias . '.orchestra');
            $criteria->addSelectColumn($alias . '.conductor');
            $criteria->addSelectColumn($alias . '.lyricist');
            $criteria->addSelectColumn($alias . '.original_lyricist');
            $criteria->addSelectColumn($alias . '.radio_station_name');
            $criteria->addSelectColumn($alias . '.info_url');
            $criteria->addSelectColumn($alias . '.artist_url');
            $criteria->addSelectColumn($alias . '.audio_source_url');
            $criteria->addSelectColumn($alias . '.radio_station_url');
            $criteria->addSelectColumn($alias . '.buy_this_url');
            $criteria->addSelectColumn($alias . '.isrc_number');
            $criteria->addSelectColumn($alias . '.catalog_number');
            $criteria->addSelectColumn($alias . '.original_artist');
            $criteria->addSelectColumn($alias . '.copyright');
            $criteria->addSelectColumn($alias . '.report_datetime');
            $criteria->addSelectColumn($alias . '.report_location');
            $criteria->addSelectColumn($alias . '.report_organization');
            $criteria->addSelectColumn($alias . '.subject');
            $criteria->addSelectColumn($alias . '.contributor');
            $criteria->addSelectColumn($alias . '.language');
            $criteria->addSelectColumn($alias . '.file_exists');
            $criteria->addSelectColumn($alias . '.replay_gain');
            $criteria->addSelectColumn($alias . '.owner_id');
            $criteria->addSelectColumn($alias . '.cuein');
            $criteria->addSelectColumn($alias . '.cueout');
            $criteria->addSelectColumn($alias . '.silan_check');
            $criteria->addSelectColumn($alias . '.hidden');
            $criteria->addSelectColumn($alias . '.is_scheduled');
            $criteria->addSelectColumn($alias . '.is_playlist');
            $criteria->addSelectColumn($alias . '.filesize');
            $criteria->addSelectColumn($alias . '.description');
            $criteria->addSelectColumn($alias . '.artwork');
            $criteria->addSelectColumn($alias . '.track_type');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcFilesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return CcFiles
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = CcFilesPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return CcFilesPeer::populateObjects(CcFilesPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            CcFilesPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param CcFiles $obj A CcFiles object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getDbId();
            } // if key === null
            CcFilesPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A CcFiles object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof CcFiles) {
                $key = (string) $value->getDbId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcFiles object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(CcFilesPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return CcFiles Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(CcFilesPeer::$instances[$key])) {
                return CcFilesPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (CcFilesPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        CcFilesPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to cc_files
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in CloudFilePeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CloudFilePeer::clearInstancePool();
        // Invalidate objects in CcShowInstancesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcShowInstancesPeer::clearInstancePool();
        // Invalidate objects in CcPlaylistcontentsPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcPlaylistcontentsPeer::clearInstancePool();
        // Invalidate objects in CcBlockcontentsPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcBlockcontentsPeer::clearInstancePool();
        // Invalidate objects in CcSchedulePeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcSchedulePeer::clearInstancePool();
        // Invalidate objects in CcPlayoutHistoryPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcPlayoutHistoryPeer::clearInstancePool();
        // Invalidate objects in ThirdPartyTrackReferencesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        ThirdPartyTrackReferencesPeer::clearInstancePool();
        // Invalidate objects in PodcastEpisodesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        PodcastEpisodesPeer::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = CcFilesPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = CcFilesPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CcFilesPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (CcFiles object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = CcFilesPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + CcFilesPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CcFilesPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            CcFilesPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related FkOwner table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinFkOwner(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcFilesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CcSubjsRelatedByDbEditedby table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcSubjsRelatedByDbEditedby(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcFilesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of CcFiles objects pre-filled with their CcSubjs objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcFiles objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinFkOwner(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcFilesPeer::DATABASE_NAME);
        }

        CcFilesPeer::addSelectColumns($criteria);
        $startcol = CcFilesPeer::NUM_HYDRATE_COLUMNS;
        CcSubjsPeer::addSelectColumns($criteria);

        $criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = CcFilesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcFilesPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcSubjsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcSubjsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcSubjsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (CcFiles) to $obj2 (CcSubjs)
                $obj2->addCcFilesRelatedByDbOwnerId($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcFiles objects pre-filled with their CcSubjs objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcFiles objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcSubjsRelatedByDbEditedby(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcFilesPeer::DATABASE_NAME);
        }

        CcFilesPeer::addSelectColumns($criteria);
        $startcol = CcFilesPeer::NUM_HYDRATE_COLUMNS;
        CcSubjsPeer::addSelectColumns($criteria);

        $criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = CcFilesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcFilesPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcSubjsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcSubjsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcSubjsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (CcFiles) to $obj2 (CcSubjs)
                $obj2->addCcFilesRelatedByDbEditedby($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcFilesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

        $criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Selects a collection of CcFiles objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcFiles objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcFilesPeer::DATABASE_NAME);
        }

        CcFilesPeer::addSelectColumns($criteria);
        $startcol2 = CcFilesPeer::NUM_HYDRATE_COLUMNS;

        CcSubjsPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcSubjsPeer::NUM_HYDRATE_COLUMNS;

        CcSubjsPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcSubjsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcFilesPeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

        $criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcFilesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcFilesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined CcSubjs rows

            $key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = CcSubjsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcSubjsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcSubjsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (CcFiles) to the collection in $obj2 (CcSubjs)
                $obj2->addCcFilesRelatedByDbOwnerId($obj1);
            } // if joined row not null

            // Add objects for joined CcSubjs rows

            $key3 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = CcSubjsPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = CcSubjsPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcSubjsPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (CcFiles) to the collection in $obj3 (CcSubjs)
                $obj3->addCcFilesRelatedByDbEditedby($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related FkOwner table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptFkOwner(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcFilesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CcSubjsRelatedByDbEditedby table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcSubjsRelatedByDbEditedby(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcFilesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of CcFiles objects pre-filled with all related objects except FkOwner.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcFiles objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptFkOwner(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcFilesPeer::DATABASE_NAME);
        }

        CcFilesPeer::addSelectColumns($criteria);
        $startcol2 = CcFilesPeer::NUM_HYDRATE_COLUMNS;


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcFilesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcFilesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcFiles objects pre-filled with all related objects except CcSubjsRelatedByDbEditedby.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcFiles objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCcSubjsRelatedByDbEditedby(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcFilesPeer::DATABASE_NAME);
        }

        CcFilesPeer::addSelectColumns($criteria);
        $startcol2 = CcFilesPeer::NUM_HYDRATE_COLUMNS;


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcFilesPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcFilesPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(CcFilesPeer::DATABASE_NAME)->getTable(CcFilesPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseCcFilesPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseCcFilesPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \CcFilesTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return CcFilesPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a CcFiles or Criteria object.
     *
     * @param      mixed $values Criteria or CcFiles object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from CcFiles object
        }

        if ($criteria->containsKey(CcFilesPeer::ID) && $criteria->keyContainsValue(CcFilesPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcFilesPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a CcFiles or Criteria object.
     *
     * @param      mixed $values Criteria or CcFiles object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(CcFilesPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(CcFilesPeer::ID);
            $value = $criteria->remove(CcFilesPeer::ID);
            if ($value) {
                $selectCriteria->add(CcFilesPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(CcFilesPeer::TABLE_NAME);
            }

        } else { // $values is CcFiles object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the cc_files table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(CcFilesPeer::TABLE_NAME, $con, CcFilesPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CcFilesPeer::clearInstancePool();
            CcFilesPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a CcFiles or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or CcFiles object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            CcFilesPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof CcFiles) { // it's a model object
            // invalidate the cache for this single object
            CcFilesPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CcFilesPeer::DATABASE_NAME);
            $criteria->add(CcFilesPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                CcFilesPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(CcFilesPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            CcFilesPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given CcFiles object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param CcFiles $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(CcFilesPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(CcFilesPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(CcFilesPeer::DATABASE_NAME, CcFilesPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return CcFiles
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = CcFilesPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(CcFilesPeer::DATABASE_NAME);
        $criteria->add(CcFilesPeer::ID, $pk);

        $v = CcFilesPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return CcFiles[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(CcFilesPeer::DATABASE_NAME);
            $criteria->add(CcFilesPeer::ID, $pks, Criteria::IN);
            $objs = CcFilesPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseCcFilesPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcFilesPeer::buildTableMap();
