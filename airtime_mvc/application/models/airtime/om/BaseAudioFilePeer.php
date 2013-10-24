<?php

namespace Airtime\MediaItem\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Airtime\CcMusicDirsPeer;
use Airtime\CcSubjsPeer;
use Airtime\MediaItemPeer;
use Airtime\MediaItem\AudioFile;
use Airtime\MediaItem\AudioFilePeer;
use Airtime\MediaItem\map\AudioFileTableMap;

/**
 * Base static class for performing query and update operations on the 'audio_file' table.
 *
 *
 *
 * @package propel.generator.airtime.om
 */
abstract class BaseAudioFilePeer extends MediaItemPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'airtime';

    /** the table name for this class */
    const TABLE_NAME = 'audio_file';

    /** the related Propel class for this table */
    const OM_CLASS = 'Airtime\\MediaItem\\AudioFile';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Airtime\\MediaItem\\map\\AudioFileTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 41;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 41;

    /** the column name for the mime field */
    const MIME = 'audio_file.mime';

    /** the column name for the directory field */
    const DIRECTORY = 'audio_file.directory';

    /** the column name for the filepath field */
    const FILEPATH = 'audio_file.filepath';

    /** the column name for the md5 field */
    const MD5 = 'audio_file.md5';

    /** the column name for the track_title field */
    const TRACK_TITLE = 'audio_file.track_title';

    /** the column name for the artist_name field */
    const ARTIST_NAME = 'audio_file.artist_name';

    /** the column name for the bit_rate field */
    const BIT_RATE = 'audio_file.bit_rate';

    /** the column name for the sample_rate field */
    const SAMPLE_RATE = 'audio_file.sample_rate';

    /** the column name for the album_title field */
    const ALBUM_TITLE = 'audio_file.album_title';

    /** the column name for the genre field */
    const GENRE = 'audio_file.genre';

    /** the column name for the comments field */
    const COMMENTS = 'audio_file.comments';

    /** the column name for the year field */
    const YEAR = 'audio_file.year';

    /** the column name for the track_number field */
    const TRACK_NUMBER = 'audio_file.track_number';

    /** the column name for the channels field */
    const CHANNELS = 'audio_file.channels';

    /** the column name for the url field */
    const URL = 'audio_file.url';

    /** the column name for the bpm field */
    const BPM = 'audio_file.bpm';

    /** the column name for the encoded_by field */
    const ENCODED_BY = 'audio_file.encoded_by';

    /** the column name for the mood field */
    const MOOD = 'audio_file.mood';

    /** the column name for the label field */
    const LABEL = 'audio_file.label';

    /** the column name for the composer field */
    const COMPOSER = 'audio_file.composer';

    /** the column name for the copyright field */
    const COPYRIGHT = 'audio_file.copyright';

    /** the column name for the isrc_number field */
    const ISRC_NUMBER = 'audio_file.isrc_number';

    /** the column name for the info_url field */
    const INFO_URL = 'audio_file.info_url';

    /** the column name for the language field */
    const LANGUAGE = 'audio_file.language';

    /** the column name for the replay_gain field */
    const REPLAY_GAIN = 'audio_file.replay_gain';

    /** the column name for the cuein field */
    const CUEIN = 'audio_file.cuein';

    /** the column name for the cueout field */
    const CUEOUT = 'audio_file.cueout';

    /** the column name for the silan_check field */
    const SILAN_CHECK = 'audio_file.silan_check';

    /** the column name for the file_exists field */
    const FILE_EXISTS = 'audio_file.file_exists';

    /** the column name for the hidden field */
    const HIDDEN = 'audio_file.hidden';

    /** the column name for the is_scheduled field */
    const IS_SCHEDULED = 'audio_file.is_scheduled';

    /** the column name for the is_playlist field */
    const IS_PLAYLIST = 'audio_file.is_playlist';

    /** the column name for the id field */
    const ID = 'audio_file.id';

    /** the column name for the name field */
    const NAME = 'audio_file.name';

    /** the column name for the owner_id field */
    const OWNER_ID = 'audio_file.owner_id';

    /** the column name for the description field */
    const DESCRIPTION = 'audio_file.description';

    /** the column name for the last_played field */
    const LAST_PLAYED = 'audio_file.last_played';

    /** the column name for the play_count field */
    const PLAY_COUNT = 'audio_file.play_count';

    /** the column name for the length field */
    const LENGTH = 'audio_file.length';

    /** the column name for the created_at field */
    const CREATED_AT = 'audio_file.created_at';

    /** the column name for the updated_at field */
    const UPDATED_AT = 'audio_file.updated_at';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of AudioFile objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array AudioFile[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. AudioFilePeer::$fieldNames[AudioFilePeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Mime', 'Directory', 'Filepath', 'Md5', 'TrackTitle', 'ArtistName', 'BitRate', 'SampleRate', 'AlbumTitle', 'Genre', 'Comments', 'Year', 'TrackNumber', 'Channels', 'Url', 'Bpm', 'EncodedBy', 'Mood', 'Label', 'Composer', 'Copyright', 'IsrcNumber', 'InfoUrl', 'Language', 'ReplayGain', 'Cuein', 'Cueout', 'IsSilanChecked', 'FileExists', 'IsHidden', 'IsScheduled', 'IsPlaylist', 'Id', 'Name', 'OwnerId', 'Description', 'LastPlayedTime', 'PlayCount', 'Length', 'CreatedAt', 'UpdatedAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('mime', 'directory', 'filepath', 'md5', 'trackTitle', 'artistName', 'bitRate', 'sampleRate', 'albumTitle', 'genre', 'comments', 'year', 'trackNumber', 'channels', 'url', 'bpm', 'encodedBy', 'mood', 'label', 'composer', 'copyright', 'isrcNumber', 'infoUrl', 'language', 'replayGain', 'cuein', 'cueout', 'isSilanChecked', 'fileExists', 'isHidden', 'isScheduled', 'isPlaylist', 'id', 'name', 'ownerId', 'description', 'lastPlayedTime', 'playCount', 'length', 'createdAt', 'updatedAt', ),
        BasePeer::TYPE_COLNAME => array (AudioFilePeer::MIME, AudioFilePeer::DIRECTORY, AudioFilePeer::FILEPATH, AudioFilePeer::MD5, AudioFilePeer::TRACK_TITLE, AudioFilePeer::ARTIST_NAME, AudioFilePeer::BIT_RATE, AudioFilePeer::SAMPLE_RATE, AudioFilePeer::ALBUM_TITLE, AudioFilePeer::GENRE, AudioFilePeer::COMMENTS, AudioFilePeer::YEAR, AudioFilePeer::TRACK_NUMBER, AudioFilePeer::CHANNELS, AudioFilePeer::URL, AudioFilePeer::BPM, AudioFilePeer::ENCODED_BY, AudioFilePeer::MOOD, AudioFilePeer::LABEL, AudioFilePeer::COMPOSER, AudioFilePeer::COPYRIGHT, AudioFilePeer::ISRC_NUMBER, AudioFilePeer::INFO_URL, AudioFilePeer::LANGUAGE, AudioFilePeer::REPLAY_GAIN, AudioFilePeer::CUEIN, AudioFilePeer::CUEOUT, AudioFilePeer::SILAN_CHECK, AudioFilePeer::FILE_EXISTS, AudioFilePeer::HIDDEN, AudioFilePeer::IS_SCHEDULED, AudioFilePeer::IS_PLAYLIST, AudioFilePeer::ID, AudioFilePeer::NAME, AudioFilePeer::OWNER_ID, AudioFilePeer::DESCRIPTION, AudioFilePeer::LAST_PLAYED, AudioFilePeer::PLAY_COUNT, AudioFilePeer::LENGTH, AudioFilePeer::CREATED_AT, AudioFilePeer::UPDATED_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('MIME', 'DIRECTORY', 'FILEPATH', 'MD5', 'TRACK_TITLE', 'ARTIST_NAME', 'BIT_RATE', 'SAMPLE_RATE', 'ALBUM_TITLE', 'GENRE', 'COMMENTS', 'YEAR', 'TRACK_NUMBER', 'CHANNELS', 'URL', 'BPM', 'ENCODED_BY', 'MOOD', 'LABEL', 'COMPOSER', 'COPYRIGHT', 'ISRC_NUMBER', 'INFO_URL', 'LANGUAGE', 'REPLAY_GAIN', 'CUEIN', 'CUEOUT', 'SILAN_CHECK', 'FILE_EXISTS', 'HIDDEN', 'IS_SCHEDULED', 'IS_PLAYLIST', 'ID', 'NAME', 'OWNER_ID', 'DESCRIPTION', 'LAST_PLAYED', 'PLAY_COUNT', 'LENGTH', 'CREATED_AT', 'UPDATED_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('mime', 'directory', 'filepath', 'md5', 'track_title', 'artist_name', 'bit_rate', 'sample_rate', 'album_title', 'genre', 'comments', 'year', 'track_number', 'channels', 'url', 'bpm', 'encoded_by', 'mood', 'label', 'composer', 'copyright', 'isrc_number', 'info_url', 'language', 'replay_gain', 'cuein', 'cueout', 'silan_check', 'file_exists', 'hidden', 'is_scheduled', 'is_playlist', 'id', 'name', 'owner_id', 'description', 'last_played', 'play_count', 'length', 'created_at', 'updated_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. AudioFilePeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Mime' => 0, 'Directory' => 1, 'Filepath' => 2, 'Md5' => 3, 'TrackTitle' => 4, 'ArtistName' => 5, 'BitRate' => 6, 'SampleRate' => 7, 'AlbumTitle' => 8, 'Genre' => 9, 'Comments' => 10, 'Year' => 11, 'TrackNumber' => 12, 'Channels' => 13, 'Url' => 14, 'Bpm' => 15, 'EncodedBy' => 16, 'Mood' => 17, 'Label' => 18, 'Composer' => 19, 'Copyright' => 20, 'IsrcNumber' => 21, 'InfoUrl' => 22, 'Language' => 23, 'ReplayGain' => 24, 'Cuein' => 25, 'Cueout' => 26, 'IsSilanChecked' => 27, 'FileExists' => 28, 'IsHidden' => 29, 'IsScheduled' => 30, 'IsPlaylist' => 31, 'Id' => 32, 'Name' => 33, 'OwnerId' => 34, 'Description' => 35, 'LastPlayedTime' => 36, 'PlayCount' => 37, 'Length' => 38, 'CreatedAt' => 39, 'UpdatedAt' => 40, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('mime' => 0, 'directory' => 1, 'filepath' => 2, 'md5' => 3, 'trackTitle' => 4, 'artistName' => 5, 'bitRate' => 6, 'sampleRate' => 7, 'albumTitle' => 8, 'genre' => 9, 'comments' => 10, 'year' => 11, 'trackNumber' => 12, 'channels' => 13, 'url' => 14, 'bpm' => 15, 'encodedBy' => 16, 'mood' => 17, 'label' => 18, 'composer' => 19, 'copyright' => 20, 'isrcNumber' => 21, 'infoUrl' => 22, 'language' => 23, 'replayGain' => 24, 'cuein' => 25, 'cueout' => 26, 'isSilanChecked' => 27, 'fileExists' => 28, 'isHidden' => 29, 'isScheduled' => 30, 'isPlaylist' => 31, 'id' => 32, 'name' => 33, 'ownerId' => 34, 'description' => 35, 'lastPlayedTime' => 36, 'playCount' => 37, 'length' => 38, 'createdAt' => 39, 'updatedAt' => 40, ),
        BasePeer::TYPE_COLNAME => array (AudioFilePeer::MIME => 0, AudioFilePeer::DIRECTORY => 1, AudioFilePeer::FILEPATH => 2, AudioFilePeer::MD5 => 3, AudioFilePeer::TRACK_TITLE => 4, AudioFilePeer::ARTIST_NAME => 5, AudioFilePeer::BIT_RATE => 6, AudioFilePeer::SAMPLE_RATE => 7, AudioFilePeer::ALBUM_TITLE => 8, AudioFilePeer::GENRE => 9, AudioFilePeer::COMMENTS => 10, AudioFilePeer::YEAR => 11, AudioFilePeer::TRACK_NUMBER => 12, AudioFilePeer::CHANNELS => 13, AudioFilePeer::URL => 14, AudioFilePeer::BPM => 15, AudioFilePeer::ENCODED_BY => 16, AudioFilePeer::MOOD => 17, AudioFilePeer::LABEL => 18, AudioFilePeer::COMPOSER => 19, AudioFilePeer::COPYRIGHT => 20, AudioFilePeer::ISRC_NUMBER => 21, AudioFilePeer::INFO_URL => 22, AudioFilePeer::LANGUAGE => 23, AudioFilePeer::REPLAY_GAIN => 24, AudioFilePeer::CUEIN => 25, AudioFilePeer::CUEOUT => 26, AudioFilePeer::SILAN_CHECK => 27, AudioFilePeer::FILE_EXISTS => 28, AudioFilePeer::HIDDEN => 29, AudioFilePeer::IS_SCHEDULED => 30, AudioFilePeer::IS_PLAYLIST => 31, AudioFilePeer::ID => 32, AudioFilePeer::NAME => 33, AudioFilePeer::OWNER_ID => 34, AudioFilePeer::DESCRIPTION => 35, AudioFilePeer::LAST_PLAYED => 36, AudioFilePeer::PLAY_COUNT => 37, AudioFilePeer::LENGTH => 38, AudioFilePeer::CREATED_AT => 39, AudioFilePeer::UPDATED_AT => 40, ),
        BasePeer::TYPE_RAW_COLNAME => array ('MIME' => 0, 'DIRECTORY' => 1, 'FILEPATH' => 2, 'MD5' => 3, 'TRACK_TITLE' => 4, 'ARTIST_NAME' => 5, 'BIT_RATE' => 6, 'SAMPLE_RATE' => 7, 'ALBUM_TITLE' => 8, 'GENRE' => 9, 'COMMENTS' => 10, 'YEAR' => 11, 'TRACK_NUMBER' => 12, 'CHANNELS' => 13, 'URL' => 14, 'BPM' => 15, 'ENCODED_BY' => 16, 'MOOD' => 17, 'LABEL' => 18, 'COMPOSER' => 19, 'COPYRIGHT' => 20, 'ISRC_NUMBER' => 21, 'INFO_URL' => 22, 'LANGUAGE' => 23, 'REPLAY_GAIN' => 24, 'CUEIN' => 25, 'CUEOUT' => 26, 'SILAN_CHECK' => 27, 'FILE_EXISTS' => 28, 'HIDDEN' => 29, 'IS_SCHEDULED' => 30, 'IS_PLAYLIST' => 31, 'ID' => 32, 'NAME' => 33, 'OWNER_ID' => 34, 'DESCRIPTION' => 35, 'LAST_PLAYED' => 36, 'PLAY_COUNT' => 37, 'LENGTH' => 38, 'CREATED_AT' => 39, 'UPDATED_AT' => 40, ),
        BasePeer::TYPE_FIELDNAME => array ('mime' => 0, 'directory' => 1, 'filepath' => 2, 'md5' => 3, 'track_title' => 4, 'artist_name' => 5, 'bit_rate' => 6, 'sample_rate' => 7, 'album_title' => 8, 'genre' => 9, 'comments' => 10, 'year' => 11, 'track_number' => 12, 'channels' => 13, 'url' => 14, 'bpm' => 15, 'encoded_by' => 16, 'mood' => 17, 'label' => 18, 'composer' => 19, 'copyright' => 20, 'isrc_number' => 21, 'info_url' => 22, 'language' => 23, 'replay_gain' => 24, 'cuein' => 25, 'cueout' => 26, 'silan_check' => 27, 'file_exists' => 28, 'hidden' => 29, 'is_scheduled' => 30, 'is_playlist' => 31, 'id' => 32, 'name' => 33, 'owner_id' => 34, 'description' => 35, 'last_played' => 36, 'play_count' => 37, 'length' => 38, 'created_at' => 39, 'updated_at' => 40, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, )
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
        $toNames = AudioFilePeer::getFieldNames($toType);
        $key = isset(AudioFilePeer::$fieldKeys[$fromType][$name]) ? AudioFilePeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(AudioFilePeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, AudioFilePeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return AudioFilePeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. AudioFilePeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AudioFilePeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(AudioFilePeer::MIME);
            $criteria->addSelectColumn(AudioFilePeer::DIRECTORY);
            $criteria->addSelectColumn(AudioFilePeer::FILEPATH);
            $criteria->addSelectColumn(AudioFilePeer::MD5);
            $criteria->addSelectColumn(AudioFilePeer::TRACK_TITLE);
            $criteria->addSelectColumn(AudioFilePeer::ARTIST_NAME);
            $criteria->addSelectColumn(AudioFilePeer::BIT_RATE);
            $criteria->addSelectColumn(AudioFilePeer::SAMPLE_RATE);
            $criteria->addSelectColumn(AudioFilePeer::ALBUM_TITLE);
            $criteria->addSelectColumn(AudioFilePeer::GENRE);
            $criteria->addSelectColumn(AudioFilePeer::COMMENTS);
            $criteria->addSelectColumn(AudioFilePeer::YEAR);
            $criteria->addSelectColumn(AudioFilePeer::TRACK_NUMBER);
            $criteria->addSelectColumn(AudioFilePeer::CHANNELS);
            $criteria->addSelectColumn(AudioFilePeer::URL);
            $criteria->addSelectColumn(AudioFilePeer::BPM);
            $criteria->addSelectColumn(AudioFilePeer::ENCODED_BY);
            $criteria->addSelectColumn(AudioFilePeer::MOOD);
            $criteria->addSelectColumn(AudioFilePeer::LABEL);
            $criteria->addSelectColumn(AudioFilePeer::COMPOSER);
            $criteria->addSelectColumn(AudioFilePeer::COPYRIGHT);
            $criteria->addSelectColumn(AudioFilePeer::ISRC_NUMBER);
            $criteria->addSelectColumn(AudioFilePeer::INFO_URL);
            $criteria->addSelectColumn(AudioFilePeer::LANGUAGE);
            $criteria->addSelectColumn(AudioFilePeer::REPLAY_GAIN);
            $criteria->addSelectColumn(AudioFilePeer::CUEIN);
            $criteria->addSelectColumn(AudioFilePeer::CUEOUT);
            $criteria->addSelectColumn(AudioFilePeer::SILAN_CHECK);
            $criteria->addSelectColumn(AudioFilePeer::FILE_EXISTS);
            $criteria->addSelectColumn(AudioFilePeer::HIDDEN);
            $criteria->addSelectColumn(AudioFilePeer::IS_SCHEDULED);
            $criteria->addSelectColumn(AudioFilePeer::IS_PLAYLIST);
            $criteria->addSelectColumn(AudioFilePeer::ID);
            $criteria->addSelectColumn(AudioFilePeer::NAME);
            $criteria->addSelectColumn(AudioFilePeer::OWNER_ID);
            $criteria->addSelectColumn(AudioFilePeer::DESCRIPTION);
            $criteria->addSelectColumn(AudioFilePeer::LAST_PLAYED);
            $criteria->addSelectColumn(AudioFilePeer::PLAY_COUNT);
            $criteria->addSelectColumn(AudioFilePeer::LENGTH);
            $criteria->addSelectColumn(AudioFilePeer::CREATED_AT);
            $criteria->addSelectColumn(AudioFilePeer::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.mime');
            $criteria->addSelectColumn($alias . '.directory');
            $criteria->addSelectColumn($alias . '.filepath');
            $criteria->addSelectColumn($alias . '.md5');
            $criteria->addSelectColumn($alias . '.track_title');
            $criteria->addSelectColumn($alias . '.artist_name');
            $criteria->addSelectColumn($alias . '.bit_rate');
            $criteria->addSelectColumn($alias . '.sample_rate');
            $criteria->addSelectColumn($alias . '.album_title');
            $criteria->addSelectColumn($alias . '.genre');
            $criteria->addSelectColumn($alias . '.comments');
            $criteria->addSelectColumn($alias . '.year');
            $criteria->addSelectColumn($alias . '.track_number');
            $criteria->addSelectColumn($alias . '.channels');
            $criteria->addSelectColumn($alias . '.url');
            $criteria->addSelectColumn($alias . '.bpm');
            $criteria->addSelectColumn($alias . '.encoded_by');
            $criteria->addSelectColumn($alias . '.mood');
            $criteria->addSelectColumn($alias . '.label');
            $criteria->addSelectColumn($alias . '.composer');
            $criteria->addSelectColumn($alias . '.copyright');
            $criteria->addSelectColumn($alias . '.isrc_number');
            $criteria->addSelectColumn($alias . '.info_url');
            $criteria->addSelectColumn($alias . '.language');
            $criteria->addSelectColumn($alias . '.replay_gain');
            $criteria->addSelectColumn($alias . '.cuein');
            $criteria->addSelectColumn($alias . '.cueout');
            $criteria->addSelectColumn($alias . '.silan_check');
            $criteria->addSelectColumn($alias . '.file_exists');
            $criteria->addSelectColumn($alias . '.hidden');
            $criteria->addSelectColumn($alias . '.is_scheduled');
            $criteria->addSelectColumn($alias . '.is_playlist');
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.owner_id');
            $criteria->addSelectColumn($alias . '.description');
            $criteria->addSelectColumn($alias . '.last_played');
            $criteria->addSelectColumn($alias . '.play_count');
            $criteria->addSelectColumn($alias . '.length');
            $criteria->addSelectColumn($alias . '.created_at');
            $criteria->addSelectColumn($alias . '.updated_at');
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
        $criteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AudioFilePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return AudioFile
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AudioFilePeer::doSelect($critcopy, $con);
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
        return AudioFilePeer::populateObjects(AudioFilePeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            AudioFilePeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

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
     * @param AudioFile $obj A AudioFile object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            AudioFilePeer::$instances[$key] = $obj;
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
     * @param      mixed $value A AudioFile object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof AudioFile) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or AudioFile object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(AudioFilePeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return AudioFile Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(AudioFilePeer::$instances[$key])) {
                return AudioFilePeer::$instances[$key];
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
        foreach (AudioFilePeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        AudioFilePeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to audio_file
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
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
        if ($row[$startcol + 32] === null) {
            return null;
        }

        return (string) $row[$startcol + 32];
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

        return (int) $row[$startcol + 32];
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
        $cls = AudioFilePeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = AudioFilePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = AudioFilePeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                AudioFilePeer::addInstanceToPool($obj, $key);
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
     * @return array (AudioFile object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = AudioFilePeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = AudioFilePeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + AudioFilePeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = AudioFilePeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            AudioFilePeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related CcMusicDirs table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcMusicDirs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AudioFilePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AudioFilePeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related MediaItem table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinMediaItem(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AudioFilePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AudioFilePeer::ID, MediaItemPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related CcSubjs table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcSubjs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AudioFilePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AudioFilePeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

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
     * Selects a collection of AudioFile objects pre-filled with their CcMusicDirs objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of AudioFile objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcMusicDirs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AudioFilePeer::DATABASE_NAME);
        }

        AudioFilePeer::addSelectColumns($criteria);
        $startcol = AudioFilePeer::NUM_HYDRATE_COLUMNS;
        CcMusicDirsPeer::addSelectColumns($criteria);

        $criteria->addJoin(AudioFilePeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AudioFilePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AudioFilePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = AudioFilePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AudioFilePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcMusicDirsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (AudioFile) to $obj2 (CcMusicDirs)
                $obj2->addAudioFile($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of AudioFile objects pre-filled with their MediaItem objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of AudioFile objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinMediaItem(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AudioFilePeer::DATABASE_NAME);
        }

        AudioFilePeer::addSelectColumns($criteria);
        $startcol = AudioFilePeer::NUM_HYDRATE_COLUMNS;
        MediaItemPeer::addSelectColumns($criteria);

        $criteria->addJoin(AudioFilePeer::ID, MediaItemPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AudioFilePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AudioFilePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = AudioFilePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AudioFilePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = MediaItemPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = MediaItemPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = MediaItemPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    MediaItemPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (AudioFile) to $obj2 (MediaItem)
                // one to one relationship
                $obj1->setMediaItem($obj2);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of AudioFile objects pre-filled with their CcSubjs objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of AudioFile objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcSubjs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AudioFilePeer::DATABASE_NAME);
        }

        AudioFilePeer::addSelectColumns($criteria);
        $startcol = AudioFilePeer::NUM_HYDRATE_COLUMNS;
        CcSubjsPeer::addSelectColumns($criteria);

        $criteria->addJoin(AudioFilePeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AudioFilePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AudioFilePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = AudioFilePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AudioFilePeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (AudioFile) to $obj2 (CcSubjs)
                $obj2->addAudioFile($obj1);

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
        $criteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AudioFilePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AudioFilePeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::ID, MediaItemPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

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
     * Selects a collection of AudioFile objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of AudioFile objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AudioFilePeer::DATABASE_NAME);
        }

        AudioFilePeer::addSelectColumns($criteria);
        $startcol2 = AudioFilePeer::NUM_HYDRATE_COLUMNS;

        CcMusicDirsPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcMusicDirsPeer::NUM_HYDRATE_COLUMNS;

        MediaItemPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + MediaItemPeer::NUM_HYDRATE_COLUMNS;

        CcSubjsPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + CcSubjsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AudioFilePeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::ID, MediaItemPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AudioFilePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AudioFilePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AudioFilePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AudioFilePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined CcMusicDirs rows

            $key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcMusicDirsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (AudioFile) to the collection in $obj2 (CcMusicDirs)
                $obj2->addAudioFile($obj1);
            } // if joined row not null

            // Add objects for joined MediaItem rows

            $key3 = MediaItemPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = MediaItemPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = MediaItemPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    MediaItemPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (AudioFile) to the collection in $obj3 (MediaItem)
                $obj1->setMediaItem($obj3);
            } // if joined row not null

            // Add objects for joined CcSubjs rows

            $key4 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol4);
            if ($key4 !== null) {
                $obj4 = CcSubjsPeer::getInstanceFromPool($key4);
                if (!$obj4) {

                    $cls = CcSubjsPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    CcSubjsPeer::addInstanceToPool($obj4, $key4);
                } // if obj4 loaded

                // Add the $obj1 (AudioFile) to the collection in $obj4 (CcSubjs)
                $obj4->addAudioFile($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CcMusicDirs table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcMusicDirs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AudioFilePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AudioFilePeer::ID, MediaItemPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related MediaItem table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptMediaItem(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AudioFilePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AudioFilePeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related CcSubjs table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcSubjs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AudioFilePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AudioFilePeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::ID, MediaItemPeer::ID, $join_behavior);

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
     * Selects a collection of AudioFile objects pre-filled with all related objects except CcMusicDirs.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of AudioFile objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCcMusicDirs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AudioFilePeer::DATABASE_NAME);
        }

        AudioFilePeer::addSelectColumns($criteria);
        $startcol2 = AudioFilePeer::NUM_HYDRATE_COLUMNS;

        MediaItemPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + MediaItemPeer::NUM_HYDRATE_COLUMNS;

        CcSubjsPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcSubjsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AudioFilePeer::ID, MediaItemPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AudioFilePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AudioFilePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AudioFilePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AudioFilePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined MediaItem rows

                $key2 = MediaItemPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = MediaItemPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = MediaItemPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    MediaItemPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (AudioFile) to the collection in $obj2 (MediaItem)
                $obj1->setMediaItem($obj2);

            } // if joined row is not null

                // Add objects for joined CcSubjs rows

                $key3 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CcSubjsPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CcSubjsPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcSubjsPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (AudioFile) to the collection in $obj3 (CcSubjs)
                $obj3->addAudioFile($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of AudioFile objects pre-filled with all related objects except MediaItem.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of AudioFile objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptMediaItem(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AudioFilePeer::DATABASE_NAME);
        }

        AudioFilePeer::addSelectColumns($criteria);
        $startcol2 = AudioFilePeer::NUM_HYDRATE_COLUMNS;

        CcMusicDirsPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcMusicDirsPeer::NUM_HYDRATE_COLUMNS;

        CcSubjsPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcSubjsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AudioFilePeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::OWNER_ID, CcSubjsPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AudioFilePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AudioFilePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AudioFilePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AudioFilePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined CcMusicDirs rows

                $key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CcMusicDirsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (AudioFile) to the collection in $obj2 (CcMusicDirs)
                $obj2->addAudioFile($obj1);

            } // if joined row is not null

                // Add objects for joined CcSubjs rows

                $key3 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CcSubjsPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CcSubjsPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcSubjsPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (AudioFile) to the collection in $obj3 (CcSubjs)
                $obj3->addAudioFile($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of AudioFile objects pre-filled with all related objects except CcSubjs.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of AudioFile objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCcSubjs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AudioFilePeer::DATABASE_NAME);
        }

        AudioFilePeer::addSelectColumns($criteria);
        $startcol2 = AudioFilePeer::NUM_HYDRATE_COLUMNS;

        CcMusicDirsPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcMusicDirsPeer::NUM_HYDRATE_COLUMNS;

        MediaItemPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + MediaItemPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AudioFilePeer::DIRECTORY, CcMusicDirsPeer::ID, $join_behavior);

        $criteria->addJoin(AudioFilePeer::ID, MediaItemPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AudioFilePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AudioFilePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AudioFilePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AudioFilePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined CcMusicDirs rows

                $key2 = CcMusicDirsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CcMusicDirsPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CcMusicDirsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcMusicDirsPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (AudioFile) to the collection in $obj2 (CcMusicDirs)
                $obj2->addAudioFile($obj1);

            } // if joined row is not null

                // Add objects for joined MediaItem rows

                $key3 = MediaItemPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = MediaItemPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = MediaItemPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    MediaItemPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (AudioFile) to the collection in $obj3 (MediaItem)
                $obj1->setMediaItem($obj3);

            } // if joined row is not null

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
        return Propel::getDatabaseMap(AudioFilePeer::DATABASE_NAME)->getTable(AudioFilePeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseAudioFilePeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseAudioFilePeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Airtime\MediaItem\map\AudioFileTableMap());
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
        return AudioFilePeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a AudioFile or Criteria object.
     *
     * @param      mixed $values Criteria or AudioFile object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from AudioFile object
        }


        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a AudioFile or Criteria object.
     *
     * @param      mixed $values Criteria or AudioFile object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(AudioFilePeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(AudioFilePeer::ID);
            $value = $criteria->remove(AudioFilePeer::ID);
            if ($value) {
                $selectCriteria->add(AudioFilePeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(AudioFilePeer::TABLE_NAME);
            }

        } else { // $values is AudioFile object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the audio_file table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(AudioFilePeer::TABLE_NAME, $con, AudioFilePeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            AudioFilePeer::clearInstancePool();
            AudioFilePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a AudioFile or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or AudioFile object or primary key or array of primary keys
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
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            AudioFilePeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof AudioFile) { // it's a model object
            // invalidate the cache for this single object
            AudioFilePeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(AudioFilePeer::DATABASE_NAME);
            $criteria->add(AudioFilePeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                AudioFilePeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(AudioFilePeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            AudioFilePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given AudioFile object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param AudioFile $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AudioFilePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AudioFilePeer::TABLE_NAME);

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

        return BasePeer::doValidate(AudioFilePeer::DATABASE_NAME, AudioFilePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return AudioFile
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = AudioFilePeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(AudioFilePeer::DATABASE_NAME);
        $criteria->add(AudioFilePeer::ID, $pk);

        $v = AudioFilePeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return AudioFile[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(AudioFilePeer::DATABASE_NAME);
            $criteria->add(AudioFilePeer::ID, $pks, Criteria::IN);
            $objs = AudioFilePeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseAudioFilePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAudioFilePeer::buildTableMap();

