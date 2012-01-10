<?php


/**
 * Base static class for performing query and update operations on the 'cc_files' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcFilesPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'airtime';

	/** the table name for this class */
	const TABLE_NAME = 'cc_files';

	/** the related Propel class for this table */
	const OM_CLASS = 'CcFiles';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'airtime.CcFiles';

	/** the related TableMap class for this table */
	const TM_CLASS = 'CcFilesTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 61;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'cc_files.ID';

	/** the column name for the GUNID field */
	const GUNID = 'cc_files.GUNID';

	/** the column name for the NAME field */
	const NAME = 'cc_files.NAME';

	/** the column name for the MIME field */
	const MIME = 'cc_files.MIME';

	/** the column name for the FTYPE field */
	const FTYPE = 'cc_files.FTYPE';

	/** the column name for the DIRECTORY field */
	const DIRECTORY = 'cc_files.DIRECTORY';

	/** the column name for the FILEPATH field */
	const FILEPATH = 'cc_files.FILEPATH';

	/** the column name for the STATE field */
	const STATE = 'cc_files.STATE';

	/** the column name for the CURRENTLYACCESSING field */
	const CURRENTLYACCESSING = 'cc_files.CURRENTLYACCESSING';

	/** the column name for the EDITEDBY field */
	const EDITEDBY = 'cc_files.EDITEDBY';

	/** the column name for the MTIME field */
	const MTIME = 'cc_files.MTIME';

	/** the column name for the UTIME field */
	const UTIME = 'cc_files.UTIME';

	/** the column name for the LPTIME field */
	const LPTIME = 'cc_files.LPTIME';

	/** the column name for the MD5 field */
	const MD5 = 'cc_files.MD5';

	/** the column name for the TRACK_TITLE field */
	const TRACK_TITLE = 'cc_files.TRACK_TITLE';

	/** the column name for the ARTIST_NAME field */
	const ARTIST_NAME = 'cc_files.ARTIST_NAME';

	/** the column name for the BIT_RATE field */
	const BIT_RATE = 'cc_files.BIT_RATE';

	/** the column name for the SAMPLE_RATE field */
	const SAMPLE_RATE = 'cc_files.SAMPLE_RATE';

	/** the column name for the FORMAT field */
	const FORMAT = 'cc_files.FORMAT';

	/** the column name for the LENGTH field */
	const LENGTH = 'cc_files.LENGTH';

	/** the column name for the ALBUM_TITLE field */
	const ALBUM_TITLE = 'cc_files.ALBUM_TITLE';

	/** the column name for the GENRE field */
	const GENRE = 'cc_files.GENRE';

	/** the column name for the COMMENTS field */
	const COMMENTS = 'cc_files.COMMENTS';

	/** the column name for the YEAR field */
	const YEAR = 'cc_files.YEAR';

	/** the column name for the TRACK_NUMBER field */
	const TRACK_NUMBER = 'cc_files.TRACK_NUMBER';

	/** the column name for the CHANNELS field */
	const CHANNELS = 'cc_files.CHANNELS';

	/** the column name for the URL field */
	const URL = 'cc_files.URL';

	/** the column name for the BPM field */
	const BPM = 'cc_files.BPM';

	/** the column name for the RATING field */
	const RATING = 'cc_files.RATING';

	/** the column name for the ENCODED_BY field */
	const ENCODED_BY = 'cc_files.ENCODED_BY';

	/** the column name for the DISC_NUMBER field */
	const DISC_NUMBER = 'cc_files.DISC_NUMBER';

	/** the column name for the MOOD field */
	const MOOD = 'cc_files.MOOD';

	/** the column name for the LABEL field */
	const LABEL = 'cc_files.LABEL';

	/** the column name for the COMPOSER field */
	const COMPOSER = 'cc_files.COMPOSER';

	/** the column name for the ENCODER field */
	const ENCODER = 'cc_files.ENCODER';

	/** the column name for the CHECKSUM field */
	const CHECKSUM = 'cc_files.CHECKSUM';

	/** the column name for the LYRICS field */
	const LYRICS = 'cc_files.LYRICS';

	/** the column name for the ORCHESTRA field */
	const ORCHESTRA = 'cc_files.ORCHESTRA';

	/** the column name for the CONDUCTOR field */
	const CONDUCTOR = 'cc_files.CONDUCTOR';

	/** the column name for the LYRICIST field */
	const LYRICIST = 'cc_files.LYRICIST';

	/** the column name for the ORIGINAL_LYRICIST field */
	const ORIGINAL_LYRICIST = 'cc_files.ORIGINAL_LYRICIST';

	/** the column name for the RADIO_STATION_NAME field */
	const RADIO_STATION_NAME = 'cc_files.RADIO_STATION_NAME';

	/** the column name for the INFO_URL field */
	const INFO_URL = 'cc_files.INFO_URL';

	/** the column name for the ARTIST_URL field */
	const ARTIST_URL = 'cc_files.ARTIST_URL';

	/** the column name for the AUDIO_SOURCE_URL field */
	const AUDIO_SOURCE_URL = 'cc_files.AUDIO_SOURCE_URL';

	/** the column name for the RADIO_STATION_URL field */
	const RADIO_STATION_URL = 'cc_files.RADIO_STATION_URL';

	/** the column name for the BUY_THIS_URL field */
	const BUY_THIS_URL = 'cc_files.BUY_THIS_URL';

	/** the column name for the ISRC_NUMBER field */
	const ISRC_NUMBER = 'cc_files.ISRC_NUMBER';

	/** the column name for the CATALOG_NUMBER field */
	const CATALOG_NUMBER = 'cc_files.CATALOG_NUMBER';

	/** the column name for the ORIGINAL_ARTIST field */
	const ORIGINAL_ARTIST = 'cc_files.ORIGINAL_ARTIST';

	/** the column name for the COPYRIGHT field */
	const COPYRIGHT = 'cc_files.COPYRIGHT';

	/** the column name for the REPORT_DATETIME field */
	const REPORT_DATETIME = 'cc_files.REPORT_DATETIME';

	/** the column name for the REPORT_LOCATION field */
	const REPORT_LOCATION = 'cc_files.REPORT_LOCATION';

	/** the column name for the REPORT_ORGANIZATION field */
	const REPORT_ORGANIZATION = 'cc_files.REPORT_ORGANIZATION';

	/** the column name for the SUBJECT field */
	const SUBJECT = 'cc_files.SUBJECT';

	/** the column name for the CONTRIBUTOR field */
	const CONTRIBUTOR = 'cc_files.CONTRIBUTOR';

	/** the column name for the LANGUAGE field */
	const LANGUAGE = 'cc_files.LANGUAGE';

	/** the column name for the SOUNDCLOUD_ID field */
	const SOUNDCLOUD_ID = 'cc_files.SOUNDCLOUD_ID';

	/** the column name for the SOUNDCLOUD_ERROR_CODE field */
	const SOUNDCLOUD_ERROR_CODE = 'cc_files.SOUNDCLOUD_ERROR_CODE';

	/** the column name for the SOUNDCLOUD_ERROR_MSG field */
	const SOUNDCLOUD_ERROR_MSG = 'cc_files.SOUNDCLOUD_ERROR_MSG';

	/** the column name for the SOUNDCLOUD_LINK_TO_FILE field */
	const SOUNDCLOUD_LINK_TO_FILE = 'cc_files.SOUNDCLOUD_LINK_TO_FILE';

	/**
	 * An identiy map to hold any loaded instances of CcFiles objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CcFiles[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('DbId', 'DbGunid', 'DbName', 'DbMime', 'DbFtype', 'DbDirectory', 'DbFilepath', 'DbState', 'DbCurrentlyaccessing', 'DbEditedby', 'DbMtime', 'DbUtime', 'DbLPtime', 'DbMd5', 'DbTrackTitle', 'DbArtistName', 'DbBitRate', 'DbSampleRate', 'DbFormat', 'DbLength', 'DbAlbumTitle', 'DbGenre', 'DbComments', 'DbYear', 'DbTrackNumber', 'DbChannels', 'DbUrl', 'DbBpm', 'DbRating', 'DbEncodedBy', 'DbDiscNumber', 'DbMood', 'DbLabel', 'DbComposer', 'DbEncoder', 'DbChecksum', 'DbLyrics', 'DbOrchestra', 'DbConductor', 'DbLyricist', 'DbOriginalLyricist', 'DbRadioStationName', 'DbInfoUrl', 'DbArtistUrl', 'DbAudioSourceUrl', 'DbRadioStationUrl', 'DbBuyThisUrl', 'DbIsrcNumber', 'DbCatalogNumber', 'DbOriginalArtist', 'DbCopyright', 'DbReportDatetime', 'DbReportLocation', 'DbReportOrganization', 'DbSubject', 'DbContributor', 'DbLanguage', 'DbSoundcloudId', 'DbSoundcloudErrorCode', 'DbSoundcloudErrorMsg', 'DbSoundcloudLinkToFile', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbGunid', 'dbName', 'dbMime', 'dbFtype', 'dbDirectory', 'dbFilepath', 'dbState', 'dbCurrentlyaccessing', 'dbEditedby', 'dbMtime', 'dbUtime', 'dbLPtime', 'dbMd5', 'dbTrackTitle', 'dbArtistName', 'dbBitRate', 'dbSampleRate', 'dbFormat', 'dbLength', 'dbAlbumTitle', 'dbGenre', 'dbComments', 'dbYear', 'dbTrackNumber', 'dbChannels', 'dbUrl', 'dbBpm', 'dbRating', 'dbEncodedBy', 'dbDiscNumber', 'dbMood', 'dbLabel', 'dbComposer', 'dbEncoder', 'dbChecksum', 'dbLyrics', 'dbOrchestra', 'dbConductor', 'dbLyricist', 'dbOriginalLyricist', 'dbRadioStationName', 'dbInfoUrl', 'dbArtistUrl', 'dbAudioSourceUrl', 'dbRadioStationUrl', 'dbBuyThisUrl', 'dbIsrcNumber', 'dbCatalogNumber', 'dbOriginalArtist', 'dbCopyright', 'dbReportDatetime', 'dbReportLocation', 'dbReportOrganization', 'dbSubject', 'dbContributor', 'dbLanguage', 'dbSoundcloudId', 'dbSoundcloudErrorCode', 'dbSoundcloudErrorMsg', 'dbSoundcloudLinkToFile', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::GUNID, self::NAME, self::MIME, self::FTYPE, self::DIRECTORY, self::FILEPATH, self::STATE, self::CURRENTLYACCESSING, self::EDITEDBY, self::MTIME, self::UTIME, self::LPTIME, self::MD5, self::TRACK_TITLE, self::ARTIST_NAME, self::BIT_RATE, self::SAMPLE_RATE, self::FORMAT, self::LENGTH, self::ALBUM_TITLE, self::GENRE, self::COMMENTS, self::YEAR, self::TRACK_NUMBER, self::CHANNELS, self::URL, self::BPM, self::RATING, self::ENCODED_BY, self::DISC_NUMBER, self::MOOD, self::LABEL, self::COMPOSER, self::ENCODER, self::CHECKSUM, self::LYRICS, self::ORCHESTRA, self::CONDUCTOR, self::LYRICIST, self::ORIGINAL_LYRICIST, self::RADIO_STATION_NAME, self::INFO_URL, self::ARTIST_URL, self::AUDIO_SOURCE_URL, self::RADIO_STATION_URL, self::BUY_THIS_URL, self::ISRC_NUMBER, self::CATALOG_NUMBER, self::ORIGINAL_ARTIST, self::COPYRIGHT, self::REPORT_DATETIME, self::REPORT_LOCATION, self::REPORT_ORGANIZATION, self::SUBJECT, self::CONTRIBUTOR, self::LANGUAGE, self::SOUNDCLOUD_ID, self::SOUNDCLOUD_ERROR_CODE, self::SOUNDCLOUD_ERROR_MSG, self::SOUNDCLOUD_LINK_TO_FILE, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'GUNID', 'NAME', 'MIME', 'FTYPE', 'DIRECTORY', 'FILEPATH', 'STATE', 'CURRENTLYACCESSING', 'EDITEDBY', 'MTIME', 'UTIME', 'LPTIME', 'MD5', 'TRACK_TITLE', 'ARTIST_NAME', 'BIT_RATE', 'SAMPLE_RATE', 'FORMAT', 'LENGTH', 'ALBUM_TITLE', 'GENRE', 'COMMENTS', 'YEAR', 'TRACK_NUMBER', 'CHANNELS', 'URL', 'BPM', 'RATING', 'ENCODED_BY', 'DISC_NUMBER', 'MOOD', 'LABEL', 'COMPOSER', 'ENCODER', 'CHECKSUM', 'LYRICS', 'ORCHESTRA', 'CONDUCTOR', 'LYRICIST', 'ORIGINAL_LYRICIST', 'RADIO_STATION_NAME', 'INFO_URL', 'ARTIST_URL', 'AUDIO_SOURCE_URL', 'RADIO_STATION_URL', 'BUY_THIS_URL', 'ISRC_NUMBER', 'CATALOG_NUMBER', 'ORIGINAL_ARTIST', 'COPYRIGHT', 'REPORT_DATETIME', 'REPORT_LOCATION', 'REPORT_ORGANIZATION', 'SUBJECT', 'CONTRIBUTOR', 'LANGUAGE', 'SOUNDCLOUD_ID', 'SOUNDCLOUD_ERROR_CODE', 'SOUNDCLOUD_ERROR_MSG', 'SOUNDCLOUD_LINK_TO_FILE', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'gunid', 'name', 'mime', 'ftype', 'directory', 'filepath', 'state', 'currentlyaccessing', 'editedby', 'mtime', 'utime', 'lptime', 'md5', 'track_title', 'artist_name', 'bit_rate', 'sample_rate', 'format', 'length', 'album_title', 'genre', 'comments', 'year', 'track_number', 'channels', 'url', 'bpm', 'rating', 'encoded_by', 'disc_number', 'mood', 'label', 'composer', 'encoder', 'checksum', 'lyrics', 'orchestra', 'conductor', 'lyricist', 'original_lyricist', 'radio_station_name', 'info_url', 'artist_url', 'audio_source_url', 'radio_station_url', 'buy_this_url', 'isrc_number', 'catalog_number', 'original_artist', 'copyright', 'report_datetime', 'report_location', 'report_organization', 'subject', 'contributor', 'language', 'soundcloud_id', 'soundcloud_error_code', 'soundcloud_error_msg', 'soundcloud_link_to_file', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbGunid' => 1, 'DbName' => 2, 'DbMime' => 3, 'DbFtype' => 4, 'DbDirectory' => 5, 'DbFilepath' => 6, 'DbState' => 7, 'DbCurrentlyaccessing' => 8, 'DbEditedby' => 9, 'DbMtime' => 10, 'DbUtime' => 11, 'DbLPtime' => 12, 'DbMd5' => 13, 'DbTrackTitle' => 14, 'DbArtistName' => 15, 'DbBitRate' => 16, 'DbSampleRate' => 17, 'DbFormat' => 18, 'DbLength' => 19, 'DbAlbumTitle' => 20, 'DbGenre' => 21, 'DbComments' => 22, 'DbYear' => 23, 'DbTrackNumber' => 24, 'DbChannels' => 25, 'DbUrl' => 26, 'DbBpm' => 27, 'DbRating' => 28, 'DbEncodedBy' => 29, 'DbDiscNumber' => 30, 'DbMood' => 31, 'DbLabel' => 32, 'DbComposer' => 33, 'DbEncoder' => 34, 'DbChecksum' => 35, 'DbLyrics' => 36, 'DbOrchestra' => 37, 'DbConductor' => 38, 'DbLyricist' => 39, 'DbOriginalLyricist' => 40, 'DbRadioStationName' => 41, 'DbInfoUrl' => 42, 'DbArtistUrl' => 43, 'DbAudioSourceUrl' => 44, 'DbRadioStationUrl' => 45, 'DbBuyThisUrl' => 46, 'DbIsrcNumber' => 47, 'DbCatalogNumber' => 48, 'DbOriginalArtist' => 49, 'DbCopyright' => 50, 'DbReportDatetime' => 51, 'DbReportLocation' => 52, 'DbReportOrganization' => 53, 'DbSubject' => 54, 'DbContributor' => 55, 'DbLanguage' => 56, 'DbSoundcloudId' => 57, 'DbSoundcloudErrorCode' => 58, 'DbSoundcloudErrorMsg' => 59, 'DbSoundcloudLinkToFile' => 60, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbGunid' => 1, 'dbName' => 2, 'dbMime' => 3, 'dbFtype' => 4, 'dbDirectory' => 5, 'dbFilepath' => 6, 'dbState' => 7, 'dbCurrentlyaccessing' => 8, 'dbEditedby' => 9, 'dbMtime' => 10, 'dbUtime' => 11, 'dbLPtime' => 12, 'dbMd5' => 13, 'dbTrackTitle' => 14, 'dbArtistName' => 15, 'dbBitRate' => 16, 'dbSampleRate' => 17, 'dbFormat' => 18, 'dbLength' => 19, 'dbAlbumTitle' => 20, 'dbGenre' => 21, 'dbComments' => 22, 'dbYear' => 23, 'dbTrackNumber' => 24, 'dbChannels' => 25, 'dbUrl' => 26, 'dbBpm' => 27, 'dbRating' => 28, 'dbEncodedBy' => 29, 'dbDiscNumber' => 30, 'dbMood' => 31, 'dbLabel' => 32, 'dbComposer' => 33, 'dbEncoder' => 34, 'dbChecksum' => 35, 'dbLyrics' => 36, 'dbOrchestra' => 37, 'dbConductor' => 38, 'dbLyricist' => 39, 'dbOriginalLyricist' => 40, 'dbRadioStationName' => 41, 'dbInfoUrl' => 42, 'dbArtistUrl' => 43, 'dbAudioSourceUrl' => 44, 'dbRadioStationUrl' => 45, 'dbBuyThisUrl' => 46, 'dbIsrcNumber' => 47, 'dbCatalogNumber' => 48, 'dbOriginalArtist' => 49, 'dbCopyright' => 50, 'dbReportDatetime' => 51, 'dbReportLocation' => 52, 'dbReportOrganization' => 53, 'dbSubject' => 54, 'dbContributor' => 55, 'dbLanguage' => 56, 'dbSoundcloudId' => 57, 'dbSoundcloudErrorCode' => 58, 'dbSoundcloudErrorMsg' => 59, 'dbSoundcloudLinkToFile' => 60, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::GUNID => 1, self::NAME => 2, self::MIME => 3, self::FTYPE => 4, self::DIRECTORY => 5, self::FILEPATH => 6, self::STATE => 7, self::CURRENTLYACCESSING => 8, self::EDITEDBY => 9, self::MTIME => 10, self::UTIME => 11, self::LPTIME => 12, self::MD5 => 13, self::TRACK_TITLE => 14, self::ARTIST_NAME => 15, self::BIT_RATE => 16, self::SAMPLE_RATE => 17, self::FORMAT => 18, self::LENGTH => 19, self::ALBUM_TITLE => 20, self::GENRE => 21, self::COMMENTS => 22, self::YEAR => 23, self::TRACK_NUMBER => 24, self::CHANNELS => 25, self::URL => 26, self::BPM => 27, self::RATING => 28, self::ENCODED_BY => 29, self::DISC_NUMBER => 30, self::MOOD => 31, self::LABEL => 32, self::COMPOSER => 33, self::ENCODER => 34, self::CHECKSUM => 35, self::LYRICS => 36, self::ORCHESTRA => 37, self::CONDUCTOR => 38, self::LYRICIST => 39, self::ORIGINAL_LYRICIST => 40, self::RADIO_STATION_NAME => 41, self::INFO_URL => 42, self::ARTIST_URL => 43, self::AUDIO_SOURCE_URL => 44, self::RADIO_STATION_URL => 45, self::BUY_THIS_URL => 46, self::ISRC_NUMBER => 47, self::CATALOG_NUMBER => 48, self::ORIGINAL_ARTIST => 49, self::COPYRIGHT => 50, self::REPORT_DATETIME => 51, self::REPORT_LOCATION => 52, self::REPORT_ORGANIZATION => 53, self::SUBJECT => 54, self::CONTRIBUTOR => 55, self::LANGUAGE => 56, self::SOUNDCLOUD_ID => 57, self::SOUNDCLOUD_ERROR_CODE => 58, self::SOUNDCLOUD_ERROR_MSG => 59, self::SOUNDCLOUD_LINK_TO_FILE => 60, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'GUNID' => 1, 'NAME' => 2, 'MIME' => 3, 'FTYPE' => 4, 'DIRECTORY' => 5, 'FILEPATH' => 6, 'STATE' => 7, 'CURRENTLYACCESSING' => 8, 'EDITEDBY' => 9, 'MTIME' => 10, 'UTIME' => 11, 'LPTIME' => 12, 'MD5' => 13, 'TRACK_TITLE' => 14, 'ARTIST_NAME' => 15, 'BIT_RATE' => 16, 'SAMPLE_RATE' => 17, 'FORMAT' => 18, 'LENGTH' => 19, 'ALBUM_TITLE' => 20, 'GENRE' => 21, 'COMMENTS' => 22, 'YEAR' => 23, 'TRACK_NUMBER' => 24, 'CHANNELS' => 25, 'URL' => 26, 'BPM' => 27, 'RATING' => 28, 'ENCODED_BY' => 29, 'DISC_NUMBER' => 30, 'MOOD' => 31, 'LABEL' => 32, 'COMPOSER' => 33, 'ENCODER' => 34, 'CHECKSUM' => 35, 'LYRICS' => 36, 'ORCHESTRA' => 37, 'CONDUCTOR' => 38, 'LYRICIST' => 39, 'ORIGINAL_LYRICIST' => 40, 'RADIO_STATION_NAME' => 41, 'INFO_URL' => 42, 'ARTIST_URL' => 43, 'AUDIO_SOURCE_URL' => 44, 'RADIO_STATION_URL' => 45, 'BUY_THIS_URL' => 46, 'ISRC_NUMBER' => 47, 'CATALOG_NUMBER' => 48, 'ORIGINAL_ARTIST' => 49, 'COPYRIGHT' => 50, 'REPORT_DATETIME' => 51, 'REPORT_LOCATION' => 52, 'REPORT_ORGANIZATION' => 53, 'SUBJECT' => 54, 'CONTRIBUTOR' => 55, 'LANGUAGE' => 56, 'SOUNDCLOUD_ID' => 57, 'SOUNDCLOUD_ERROR_CODE' => 58, 'SOUNDCLOUD_ERROR_MSG' => 59, 'SOUNDCLOUD_LINK_TO_FILE' => 60, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'gunid' => 1, 'name' => 2, 'mime' => 3, 'ftype' => 4, 'directory' => 5, 'filepath' => 6, 'state' => 7, 'currentlyaccessing' => 8, 'editedby' => 9, 'mtime' => 10, 'utime' => 11, 'lptime' => 12, 'md5' => 13, 'track_title' => 14, 'artist_name' => 15, 'bit_rate' => 16, 'sample_rate' => 17, 'format' => 18, 'length' => 19, 'album_title' => 20, 'genre' => 21, 'comments' => 22, 'year' => 23, 'track_number' => 24, 'channels' => 25, 'url' => 26, 'bpm' => 27, 'rating' => 28, 'encoded_by' => 29, 'disc_number' => 30, 'mood' => 31, 'label' => 32, 'composer' => 33, 'encoder' => 34, 'checksum' => 35, 'lyrics' => 36, 'orchestra' => 37, 'conductor' => 38, 'lyricist' => 39, 'original_lyricist' => 40, 'radio_station_name' => 41, 'info_url' => 42, 'artist_url' => 43, 'audio_source_url' => 44, 'radio_station_url' => 45, 'buy_this_url' => 46, 'isrc_number' => 47, 'catalog_number' => 48, 'original_artist' => 49, 'copyright' => 50, 'report_datetime' => 51, 'report_location' => 52, 'report_organization' => 53, 'subject' => 54, 'contributor' => 55, 'language' => 56, 'soundcloud_id' => 57, 'soundcloud_error_code' => 58, 'soundcloud_error_msg' => 59, 'soundcloud_link_to_file' => 60, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
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
	 * @return     string
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
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria, $alias = null)
	{
		if (null === $alias) {
			$criteria->addSelectColumn(CcFilesPeer::ID);
			$criteria->addSelectColumn(CcFilesPeer::GUNID);
			$criteria->addSelectColumn(CcFilesPeer::NAME);
			$criteria->addSelectColumn(CcFilesPeer::MIME);
			$criteria->addSelectColumn(CcFilesPeer::FTYPE);
			$criteria->addSelectColumn(CcFilesPeer::DIRECTORY);
			$criteria->addSelectColumn(CcFilesPeer::FILEPATH);
			$criteria->addSelectColumn(CcFilesPeer::STATE);
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
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_ID);
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_ERROR_CODE);
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_ERROR_MSG);
			$criteria->addSelectColumn(CcFilesPeer::SOUNDCLOUD_LINK_TO_FILE);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.GUNID');
			$criteria->addSelectColumn($alias . '.NAME');
			$criteria->addSelectColumn($alias . '.MIME');
			$criteria->addSelectColumn($alias . '.FTYPE');
			$criteria->addSelectColumn($alias . '.DIRECTORY');
			$criteria->addSelectColumn($alias . '.FILEPATH');
			$criteria->addSelectColumn($alias . '.STATE');
			$criteria->addSelectColumn($alias . '.CURRENTLYACCESSING');
			$criteria->addSelectColumn($alias . '.EDITEDBY');
			$criteria->addSelectColumn($alias . '.MTIME');
			$criteria->addSelectColumn($alias . '.UTIME');
			$criteria->addSelectColumn($alias . '.LPTIME');
			$criteria->addSelectColumn($alias . '.MD5');
			$criteria->addSelectColumn($alias . '.TRACK_TITLE');
			$criteria->addSelectColumn($alias . '.ARTIST_NAME');
			$criteria->addSelectColumn($alias . '.BIT_RATE');
			$criteria->addSelectColumn($alias . '.SAMPLE_RATE');
			$criteria->addSelectColumn($alias . '.FORMAT');
			$criteria->addSelectColumn($alias . '.LENGTH');
			$criteria->addSelectColumn($alias . '.ALBUM_TITLE');
			$criteria->addSelectColumn($alias . '.GENRE');
			$criteria->addSelectColumn($alias . '.COMMENTS');
			$criteria->addSelectColumn($alias . '.YEAR');
			$criteria->addSelectColumn($alias . '.TRACK_NUMBER');
			$criteria->addSelectColumn($alias . '.CHANNELS');
			$criteria->addSelectColumn($alias . '.URL');
			$criteria->addSelectColumn($alias . '.BPM');
			$criteria->addSelectColumn($alias . '.RATING');
			$criteria->addSelectColumn($alias . '.ENCODED_BY');
			$criteria->addSelectColumn($alias . '.DISC_NUMBER');
			$criteria->addSelectColumn($alias . '.MOOD');
			$criteria->addSelectColumn($alias . '.LABEL');
			$criteria->addSelectColumn($alias . '.COMPOSER');
			$criteria->addSelectColumn($alias . '.ENCODER');
			$criteria->addSelectColumn($alias . '.CHECKSUM');
			$criteria->addSelectColumn($alias . '.LYRICS');
			$criteria->addSelectColumn($alias . '.ORCHESTRA');
			$criteria->addSelectColumn($alias . '.CONDUCTOR');
			$criteria->addSelectColumn($alias . '.LYRICIST');
			$criteria->addSelectColumn($alias . '.ORIGINAL_LYRICIST');
			$criteria->addSelectColumn($alias . '.RADIO_STATION_NAME');
			$criteria->addSelectColumn($alias . '.INFO_URL');
			$criteria->addSelectColumn($alias . '.ARTIST_URL');
			$criteria->addSelectColumn($alias . '.AUDIO_SOURCE_URL');
			$criteria->addSelectColumn($alias . '.RADIO_STATION_URL');
			$criteria->addSelectColumn($alias . '.BUY_THIS_URL');
			$criteria->addSelectColumn($alias . '.ISRC_NUMBER');
			$criteria->addSelectColumn($alias . '.CATALOG_NUMBER');
			$criteria->addSelectColumn($alias . '.ORIGINAL_ARTIST');
			$criteria->addSelectColumn($alias . '.COPYRIGHT');
			$criteria->addSelectColumn($alias . '.REPORT_DATETIME');
			$criteria->addSelectColumn($alias . '.REPORT_LOCATION');
			$criteria->addSelectColumn($alias . '.REPORT_ORGANIZATION');
			$criteria->addSelectColumn($alias . '.SUBJECT');
			$criteria->addSelectColumn($alias . '.CONTRIBUTOR');
			$criteria->addSelectColumn($alias . '.LANGUAGE');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_ID');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_ERROR_CODE');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_ERROR_MSG');
			$criteria->addSelectColumn($alias . '.SOUNDCLOUD_LINK_TO_FILE');
		}
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

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
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     CcFiles
	 * @throws     PropelException Any exceptions caught during processing will be
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
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return CcFilesPeer::populateObjects(CcFilesPeer::doSelectStmt($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * @param      CcFiles $value A CcFiles object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(CcFiles $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getDbId();
			} // if key === null
			self::$instances[$key] = $obj;
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

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     CcFiles Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to cc_files
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in CcShowInstancesPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcShowInstancesPeer::clearInstancePool();
		// Invalidate objects in CcPlaylistcontentsPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcPlaylistcontentsPeer::clearInstancePool();
		// Invalidate objects in CcSchedulePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcSchedulePeer::clearInstancePool();
	}

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
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
	 * @return     mixed The primary key of the row
	 */
	public static function getPrimaryKeyFromRow($row, $startcol = 0)
	{
		return (int) $row[$startcol];
	}
	
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = CcFilesPeer::getOMClass(false);
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
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     array (CcFiles object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = CcFilesPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + CcFilesPeer::NUM_COLUMNS;
		} else {
			$cls = CcFilesPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			CcFilesPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}

	/**
	 * Returns the number of rows matching criteria, joining the related CcSubjs table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcSubjs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * Returns the number of rows matching criteria, joining the related CcMusicDirs table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcMusicDirs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
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
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

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
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcSubjs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);
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

				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcSubjsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcSubjsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcSubjsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcFiles) to $obj2 (CcSubjs)
				$obj2->addCcFiles($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcFiles objects pre-filled with their CcMusicDirs objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcMusicDirs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);
		CcMusicDirsPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcMusicDirsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcFiles) to $obj2 (CcMusicDirs)
				$obj2->addCcFiles($obj1);

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
	 * @return     int Number of matching rows.
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
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

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
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol2 = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcSubjsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcSubjsPeer::NUM_COLUMNS - CcSubjsPeer::NUM_LAZY_LOAD_COLUMNS);

		CcMusicDirsPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcMusicDirsPeer::NUM_COLUMNS - CcMusicDirsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcFilesPeer::EDITEDBY, CcSubjsPeer::ID, $join_behavior);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined CcSubjs rows

			$key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = CcSubjsPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcSubjsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcSubjsPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (CcFiles) to the collection in $obj2 (CcSubjs)
				$obj2->addCcFiles($obj1);
			} // if joined row not null

			// Add objects for joined CcMusicDirs rows

			$key3 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = CcMusicDirsPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = CcMusicDirsPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcMusicDirsPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (CcFiles) to the collection in $obj3 (CcMusicDirs)
				$obj3->addCcFiles($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcSubjs table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcSubjs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
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
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CcMusicDirs table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcMusicDirs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
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
		$criteria->setDbName(self::DATABASE_NAME);

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
	 * Selects a collection of CcFiles objects pre-filled with all related objects except CcSubjs.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcSubjs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol2 = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcMusicDirsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcMusicDirsPeer::NUM_COLUMNS - CcMusicDirsPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcFilesPeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcFilesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcFilesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined CcMusicDirs rows

				$key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CcMusicDirsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CcFiles) to the collection in $obj2 (CcMusicDirs)
				$obj2->addCcFiles($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcFiles objects pre-filled with all related objects except CcMusicDirs.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcFiles objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcMusicDirs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcFilesPeer::addSelectColumns($criteria);
		$startcol2 = (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcSubjsPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcSubjsPeer::NUM_COLUMNS - CcSubjsPeer::NUM_LAZY_LOAD_COLUMNS);

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
				$cls = CcFilesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcFilesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined CcSubjs rows

				$key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CcSubjsPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CcSubjsPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcSubjsPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CcFiles) to the collection in $obj2 (CcSubjs)
				$obj2->addCcFiles($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseCcFilesPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseCcFilesPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new CcFilesTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean $withPrefix Whether or not to return the path with the class name
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? CcFilesPeer::CLASS_DEFAULT : CcFilesPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a CcFiles or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcFiles object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
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
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollBack();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a CcFiles or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcFiles object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

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
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the cc_files table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
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
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a CcFiles or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CcFiles object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      PropelPDO $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
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
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CcFilesPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				CcFilesPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			CcFilesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
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
	 * @param      CcFiles $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(CcFiles $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CcFilesPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CcFilesPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach ($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
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
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CcFiles
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
	 * @throws     PropelException Any exceptions caught during processing will be
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

