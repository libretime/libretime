<?php

namespace Airtime\MediaItem\om;

use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelDateTime;
use \PropelException;
use \PropelPDO;
use Airtime\CcMusicDirs;
use Airtime\CcMusicDirsQuery;
use Airtime\CcSubjs;
use Airtime\CcSubjsQuery;
use Airtime\MediaItem;
use Airtime\MediaItemQuery;
use Airtime\MediaItem\AudioFile;
use Airtime\MediaItem\AudioFilePeer;
use Airtime\MediaItem\AudioFileQuery;

/**
 * Base class that represents a row from the 'media_audiofile' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseAudioFile extends MediaItem implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Airtime\\MediaItem\\AudioFilePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AudioFilePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the directory field.
     * @var        int
     */
    protected $directory;

    /**
     * The value for the filepath field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $filepath;

    /**
     * The value for the md5 field.
     * @var        string
     */
    protected $md5;

    /**
     * The value for the track_title field.
     * @var        string
     */
    protected $track_title;

    /**
     * The value for the artist_name field.
     * @var        string
     */
    protected $artist_name;

    /**
     * The value for the bit_rate field.
     * @var        int
     */
    protected $bit_rate;

    /**
     * The value for the sample_rate field.
     * @var        int
     */
    protected $sample_rate;

    /**
     * The value for the album_title field.
     * @var        string
     */
    protected $album_title;

    /**
     * The value for the genre field.
     * @var        string
     */
    protected $genre;

    /**
     * The value for the comments field.
     * @var        string
     */
    protected $comments;

    /**
     * The value for the year field.
     * @var        string
     */
    protected $year;

    /**
     * The value for the track_number field.
     * @var        int
     */
    protected $track_number;

    /**
     * The value for the channels field.
     * @var        int
     */
    protected $channels;

    /**
     * The value for the bpm field.
     * @var        int
     */
    protected $bpm;

    /**
     * The value for the encoded_by field.
     * @var        string
     */
    protected $encoded_by;

    /**
     * The value for the mood field.
     * @var        string
     */
    protected $mood;

    /**
     * The value for the label field.
     * @var        string
     */
    protected $label;

    /**
     * The value for the composer field.
     * @var        string
     */
    protected $composer;

    /**
     * The value for the copyright field.
     * @var        string
     */
    protected $copyright;

    /**
     * The value for the conductor field.
     * @var        string
     */
    protected $conductor;

    /**
     * The value for the isrc_number field.
     * @var        string
     */
    protected $isrc_number;

    /**
     * The value for the info_url field.
     * @var        string
     */
    protected $info_url;

    /**
     * The value for the language field.
     * @var        string
     */
    protected $language;

    /**
     * The value for the replay_gain field.
     * @var        string
     */
    protected $replay_gain;

    /**
     * The value for the cuein field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $cuein;

    /**
     * The value for the cueout field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $cueout;

    /**
     * The value for the silan_check field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $silan_check;

    /**
     * The value for the file_exists field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $file_exists;

    /**
     * The value for the hidden field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $hidden;

    /**
     * The value for the is_scheduled field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_scheduled;

    /**
     * The value for the is_playlist field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_playlist;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the owner_id field.
     * @var        int
     */
    protected $owner_id;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the last_played field.
     * @var        string
     */
    protected $last_played;

    /**
     * The value for the play_count field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $play_count;

    /**
     * The value for the length field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $length;

    /**
     * The value for the mime field.
     * @var        string
     */
    protected $mime;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        CcMusicDirs
     */
    protected $aCcMusicDirs;

    /**
     * @var        MediaItem
     */
    protected $aMediaItem;

    /**
     * @var        CcSubjs
     */
    protected $aCcSubjs;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->filepath = '';
        $this->cuein = '00:00:00';
        $this->cueout = '00:00:00';
        $this->silan_check = false;
        $this->file_exists = true;
        $this->hidden = false;
        $this->is_scheduled = false;
        $this->is_playlist = false;
        $this->play_count = 0;
        $this->length = '00:00:00';
    }

    /**
     * Initializes internal state of BaseAudioFile object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [directory] column value.
     *
     * @return int
     */
    public function getDirectory()
    {

        return $this->directory;
    }

    /**
     * Get the [filepath] column value.
     *
     * @return string
     */
    public function getFilepath()
    {

        return $this->filepath;
    }

    /**
     * Get the [md5] column value.
     *
     * @return string
     */
    public function getMd5()
    {

        return $this->md5;
    }

    /**
     * Get the [track_title] column value.
     *
     * @return string
     */
    public function getTrackTitle()
    {

        return $this->track_title;
    }

    /**
     * Get the [artist_name] column value.
     *
     * @return string
     */
    public function getArtistName()
    {

        return $this->artist_name;
    }

    /**
     * Get the [bit_rate] column value.
     *
     * @return int
     */
    public function getBitRate()
    {

        return $this->bit_rate;
    }

    /**
     * Get the [sample_rate] column value.
     *
     * @return int
     */
    public function getSampleRate()
    {

        return $this->sample_rate;
    }

    /**
     * Get the [album_title] column value.
     *
     * @return string
     */
    public function getAlbumTitle()
    {

        return $this->album_title;
    }

    /**
     * Get the [genre] column value.
     *
     * @return string
     */
    public function getGenre()
    {

        return $this->genre;
    }

    /**
     * Get the [comments] column value.
     *
     * @return string
     */
    public function getComments()
    {

        return $this->comments;
    }

    /**
     * Get the [year] column value.
     *
     * @return string
     */
    public function getYear()
    {

        return $this->year;
    }

    /**
     * Get the [track_number] column value.
     *
     * @return int
     */
    public function getTrackNumber()
    {

        return $this->track_number;
    }

    /**
     * Get the [channels] column value.
     *
     * @return int
     */
    public function getChannels()
    {

        return $this->channels;
    }

    /**
     * Get the [bpm] column value.
     *
     * @return int
     */
    public function getBpm()
    {

        return $this->bpm;
    }

    /**
     * Get the [encoded_by] column value.
     *
     * @return string
     */
    public function getEncodedBy()
    {

        return $this->encoded_by;
    }

    /**
     * Get the [mood] column value.
     *
     * @return string
     */
    public function getMood()
    {

        return $this->mood;
    }

    /**
     * Get the [label] column value.
     *
     * @return string
     */
    public function getLabel()
    {

        return $this->label;
    }

    /**
     * Get the [composer] column value.
     *
     * @return string
     */
    public function getComposer()
    {

        return $this->composer;
    }

    /**
     * Get the [copyright] column value.
     *
     * @return string
     */
    public function getCopyright()
    {

        return $this->copyright;
    }

    /**
     * Get the [conductor] column value.
     *
     * @return string
     */
    public function getConductor()
    {

        return $this->conductor;
    }

    /**
     * Get the [isrc_number] column value.
     *
     * @return string
     */
    public function getIsrcNumber()
    {

        return $this->isrc_number;
    }

    /**
     * Get the [info_url] column value.
     *
     * @return string
     */
    public function getInfoUrl()
    {

        return $this->info_url;
    }

    /**
     * Get the [language] column value.
     *
     * @return string
     */
    public function getLanguage()
    {

        return $this->language;
    }

    /**
     * Get the [replay_gain] column value.
     *
     * @return string
     */
    public function getReplayGain()
    {

        return $this->replay_gain;
    }

    /**
     * Get the [cuein] column value.
     *
     * @return string
     */
    public function getCuein()
    {

        return $this->cuein;
    }

    /**
     * Get the [cueout] column value.
     *
     * @return string
     */
    public function getCueout()
    {

        return $this->cueout;
    }

    /**
     * Get the [silan_check] column value.
     *
     * @return boolean
     */
    public function getIsSilanChecked()
    {

        return $this->silan_check;
    }

    /**
     * Get the [file_exists] column value.
     *
     * @return boolean
     */
    public function getFileExists()
    {

        return $this->file_exists;
    }

    /**
     * Get the [hidden] column value.
     *
     * @return boolean
     */
    public function getFileHidden()
    {

        return $this->hidden;
    }

    /**
     * Get the [is_scheduled] column value.
     *
     * @return boolean
     */
    public function getIsScheduled()
    {

        return $this->is_scheduled;
    }

    /**
     * Get the [is_playlist] column value.
     *
     * @return boolean
     */
    public function getIsPlaylist()
    {

        return $this->is_playlist;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [owner_id] column value.
     *
     * @return int
     */
    public function getOwnerId()
    {

        return $this->owner_id;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {

        return $this->description;
    }

    /**
     * Get the [optionally formatted] temporal [last_played] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or \DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastPlayedTime($format = 'Y-m-d H:i:s')
    {
        if ($this->last_played === null) {
            return null;
        }


        try {
            $dt = new \DateTime($this->last_played);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to \DateTime: " . var_export($this->last_played, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a \DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [play_count] column value.
     *
     * @return int
     */
    public function getPlayCount()
    {

        return $this->play_count;
    }

    /**
     * Get the [length] column value.
     *
     * @return string
     */
    public function getLength()
    {

        return $this->length;
    }

    /**
     * Get the [mime] column value.
     *
     * @return string
     */
    public function getMime()
    {

        return $this->mime;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or \DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = 'Y-m-d H:i:s')
    {
        if ($this->created_at === null) {
            return null;
        }


        try {
            $dt = new \DateTime($this->created_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to \DateTime: " . var_export($this->created_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a \DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or \DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = 'Y-m-d H:i:s')
    {
        if ($this->updated_at === null) {
            return null;
        }


        try {
            $dt = new \DateTime($this->updated_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to \DateTime: " . var_export($this->updated_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a \DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [directory] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setDirectory($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->directory !== $v) {
            $this->directory = $v;
            $this->modifiedColumns[] = AudioFilePeer::DIRECTORY;
        }

        if ($this->aCcMusicDirs !== null && $this->aCcMusicDirs->getId() !== $v) {
            $this->aCcMusicDirs = null;
        }


        return $this;
    } // setDirectory()

    /**
     * Set the value of [filepath] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setFilepath($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->filepath !== $v) {
            $this->filepath = $v;
            $this->modifiedColumns[] = AudioFilePeer::FILEPATH;
        }


        return $this;
    } // setFilepath()

    /**
     * Set the value of [md5] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setMd5($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->md5 !== $v) {
            $this->md5 = $v;
            $this->modifiedColumns[] = AudioFilePeer::MD5;
        }


        return $this;
    } // setMd5()

    /**
     * Set the value of [track_title] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setTrackTitle($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->track_title !== $v) {
            $this->track_title = $v;
            $this->modifiedColumns[] = AudioFilePeer::TRACK_TITLE;
        }


        return $this;
    } // setTrackTitle()

    /**
     * Set the value of [artist_name] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setArtistName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->artist_name !== $v) {
            $this->artist_name = $v;
            $this->modifiedColumns[] = AudioFilePeer::ARTIST_NAME;
        }


        return $this;
    } // setArtistName()

    /**
     * Set the value of [bit_rate] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setBitRate($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->bit_rate !== $v) {
            $this->bit_rate = $v;
            $this->modifiedColumns[] = AudioFilePeer::BIT_RATE;
        }


        return $this;
    } // setBitRate()

    /**
     * Set the value of [sample_rate] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setSampleRate($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sample_rate !== $v) {
            $this->sample_rate = $v;
            $this->modifiedColumns[] = AudioFilePeer::SAMPLE_RATE;
        }


        return $this;
    } // setSampleRate()

    /**
     * Set the value of [album_title] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setAlbumTitle($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->album_title !== $v) {
            $this->album_title = $v;
            $this->modifiedColumns[] = AudioFilePeer::ALBUM_TITLE;
        }


        return $this;
    } // setAlbumTitle()

    /**
     * Set the value of [genre] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setGenre($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->genre !== $v) {
            $this->genre = $v;
            $this->modifiedColumns[] = AudioFilePeer::GENRE;
        }


        return $this;
    } // setGenre()

    /**
     * Set the value of [comments] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setComments($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->comments !== $v) {
            $this->comments = $v;
            $this->modifiedColumns[] = AudioFilePeer::COMMENTS;
        }


        return $this;
    } // setComments()

    /**
     * Set the value of [year] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setYear($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->year !== $v) {
            $this->year = $v;
            $this->modifiedColumns[] = AudioFilePeer::YEAR;
        }


        return $this;
    } // setYear()

    /**
     * Set the value of [track_number] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setTrackNumber($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->track_number !== $v) {
            $this->track_number = $v;
            $this->modifiedColumns[] = AudioFilePeer::TRACK_NUMBER;
        }


        return $this;
    } // setTrackNumber()

    /**
     * Set the value of [channels] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setChannels($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->channels !== $v) {
            $this->channels = $v;
            $this->modifiedColumns[] = AudioFilePeer::CHANNELS;
        }


        return $this;
    } // setChannels()

    /**
     * Set the value of [bpm] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setBpm($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->bpm !== $v) {
            $this->bpm = $v;
            $this->modifiedColumns[] = AudioFilePeer::BPM;
        }


        return $this;
    } // setBpm()

    /**
     * Set the value of [encoded_by] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setEncodedBy($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->encoded_by !== $v) {
            $this->encoded_by = $v;
            $this->modifiedColumns[] = AudioFilePeer::ENCODED_BY;
        }


        return $this;
    } // setEncodedBy()

    /**
     * Set the value of [mood] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setMood($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->mood !== $v) {
            $this->mood = $v;
            $this->modifiedColumns[] = AudioFilePeer::MOOD;
        }


        return $this;
    } // setMood()

    /**
     * Set the value of [label] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setLabel($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->label !== $v) {
            $this->label = $v;
            $this->modifiedColumns[] = AudioFilePeer::LABEL;
        }


        return $this;
    } // setLabel()

    /**
     * Set the value of [composer] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setComposer($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->composer !== $v) {
            $this->composer = $v;
            $this->modifiedColumns[] = AudioFilePeer::COMPOSER;
        }


        return $this;
    } // setComposer()

    /**
     * Set the value of [copyright] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setCopyright($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->copyright !== $v) {
            $this->copyright = $v;
            $this->modifiedColumns[] = AudioFilePeer::COPYRIGHT;
        }


        return $this;
    } // setCopyright()

    /**
     * Set the value of [conductor] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setConductor($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->conductor !== $v) {
            $this->conductor = $v;
            $this->modifiedColumns[] = AudioFilePeer::CONDUCTOR;
        }


        return $this;
    } // setConductor()

    /**
     * Set the value of [isrc_number] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setIsrcNumber($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->isrc_number !== $v) {
            $this->isrc_number = $v;
            $this->modifiedColumns[] = AudioFilePeer::ISRC_NUMBER;
        }


        return $this;
    } // setIsrcNumber()

    /**
     * Set the value of [info_url] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setInfoUrl($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->info_url !== $v) {
            $this->info_url = $v;
            $this->modifiedColumns[] = AudioFilePeer::INFO_URL;
        }


        return $this;
    } // setInfoUrl()

    /**
     * Set the value of [language] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setLanguage($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->language !== $v) {
            $this->language = $v;
            $this->modifiedColumns[] = AudioFilePeer::LANGUAGE;
        }


        return $this;
    } // setLanguage()

    /**
     * Set the value of [replay_gain] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setReplayGain($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->replay_gain !== $v) {
            $this->replay_gain = $v;
            $this->modifiedColumns[] = AudioFilePeer::REPLAY_GAIN;
        }


        return $this;
    } // setReplayGain()

    /**
     * Set the value of [cuein] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setCuein($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->cuein !== $v) {
            $this->cuein = $v;
            $this->modifiedColumns[] = AudioFilePeer::CUEIN;
        }


        return $this;
    } // setCuein()

    /**
     * Set the value of [cueout] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setCueout($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->cueout !== $v) {
            $this->cueout = $v;
            $this->modifiedColumns[] = AudioFilePeer::CUEOUT;
        }


        return $this;
    } // setCueout()

    /**
     * Sets the value of the [silan_check] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setIsSilanChecked($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->silan_check !== $v) {
            $this->silan_check = $v;
            $this->modifiedColumns[] = AudioFilePeer::SILAN_CHECK;
        }


        return $this;
    } // setIsSilanChecked()

    /**
     * Sets the value of the [file_exists] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setFileExists($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->file_exists !== $v) {
            $this->file_exists = $v;
            $this->modifiedColumns[] = AudioFilePeer::FILE_EXISTS;
        }


        return $this;
    } // setFileExists()

    /**
     * Sets the value of the [hidden] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setFileHidden($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->hidden !== $v) {
            $this->hidden = $v;
            $this->modifiedColumns[] = AudioFilePeer::HIDDEN;
        }


        return $this;
    } // setFileHidden()

    /**
     * Sets the value of the [is_scheduled] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setIsScheduled($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_scheduled !== $v) {
            $this->is_scheduled = $v;
            $this->modifiedColumns[] = AudioFilePeer::IS_SCHEDULED;
        }


        return $this;
    } // setIsScheduled()

    /**
     * Sets the value of the [is_playlist] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setIsPlaylist($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_playlist !== $v) {
            $this->is_playlist = $v;
            $this->modifiedColumns[] = AudioFilePeer::IS_PLAYLIST;
        }


        return $this;
    } // setIsPlaylist()

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = AudioFilePeer::ID;
        }

        if ($this->aMediaItem !== null && $this->aMediaItem->getId() !== $v) {
            $this->aMediaItem = null;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = AudioFilePeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [owner_id] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setOwnerId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->owner_id !== $v) {
            $this->owner_id = $v;
            $this->modifiedColumns[] = AudioFilePeer::OWNER_ID;
        }

        if ($this->aCcSubjs !== null && $this->aCcSubjs->getDbId() !== $v) {
            $this->aCcSubjs = null;
        }


        return $this;
    } // setOwnerId()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = AudioFilePeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Sets the value of [last_played] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return AudioFile The current object (for fluent API support)
     */
    public function setLastPlayedTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->last_played !== null || $dt !== null) {
            $currentDateAsString = ($this->last_played !== null && $tmpDt = new \DateTime($this->last_played)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_played = $newDateAsString;
                $this->modifiedColumns[] = AudioFilePeer::LAST_PLAYED;
            }
        } // if either are not null


        return $this;
    } // setLastPlayedTime()

    /**
     * Set the value of [play_count] column.
     *
     * @param  int $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setPlayCount($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->play_count !== $v) {
            $this->play_count = $v;
            $this->modifiedColumns[] = AudioFilePeer::PLAY_COUNT;
        }


        return $this;
    } // setPlayCount()

    /**
     * Set the value of [length] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setLength($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->length !== $v) {
            $this->length = $v;
            $this->modifiedColumns[] = AudioFilePeer::LENGTH;
        }


        return $this;
    } // setLength()

    /**
     * Set the value of [mime] column.
     *
     * @param  string $v new value
     * @return AudioFile The current object (for fluent API support)
     */
    public function setMime($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->mime !== $v) {
            $this->mime = $v;
            $this->modifiedColumns[] = AudioFilePeer::MIME;
        }


        return $this;
    } // setMime()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return AudioFile The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new \DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = AudioFilePeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return AudioFile The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new \DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = AudioFilePeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->filepath !== '') {
                return false;
            }

            if ($this->cuein !== '00:00:00') {
                return false;
            }

            if ($this->cueout !== '00:00:00') {
                return false;
            }

            if ($this->silan_check !== false) {
                return false;
            }

            if ($this->file_exists !== true) {
                return false;
            }

            if ($this->hidden !== false) {
                return false;
            }

            if ($this->is_scheduled !== false) {
                return false;
            }

            if ($this->is_playlist !== false) {
                return false;
            }

            if ($this->play_count !== 0) {
                return false;
            }

            if ($this->length !== '00:00:00') {
                return false;
            }

        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->directory = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->filepath = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->md5 = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->track_title = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->artist_name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->bit_rate = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->sample_rate = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->album_title = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->genre = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->comments = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->year = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->track_number = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
            $this->channels = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->bpm = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->encoded_by = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
            $this->mood = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->label = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
            $this->composer = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
            $this->copyright = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
            $this->conductor = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
            $this->isrc_number = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
            $this->info_url = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
            $this->language = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
            $this->replay_gain = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
            $this->cuein = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
            $this->cueout = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
            $this->silan_check = ($row[$startcol + 26] !== null) ? (boolean) $row[$startcol + 26] : null;
            $this->file_exists = ($row[$startcol + 27] !== null) ? (boolean) $row[$startcol + 27] : null;
            $this->hidden = ($row[$startcol + 28] !== null) ? (boolean) $row[$startcol + 28] : null;
            $this->is_scheduled = ($row[$startcol + 29] !== null) ? (boolean) $row[$startcol + 29] : null;
            $this->is_playlist = ($row[$startcol + 30] !== null) ? (boolean) $row[$startcol + 30] : null;
            $this->id = ($row[$startcol + 31] !== null) ? (int) $row[$startcol + 31] : null;
            $this->name = ($row[$startcol + 32] !== null) ? (string) $row[$startcol + 32] : null;
            $this->owner_id = ($row[$startcol + 33] !== null) ? (int) $row[$startcol + 33] : null;
            $this->description = ($row[$startcol + 34] !== null) ? (string) $row[$startcol + 34] : null;
            $this->last_played = ($row[$startcol + 35] !== null) ? (string) $row[$startcol + 35] : null;
            $this->play_count = ($row[$startcol + 36] !== null) ? (int) $row[$startcol + 36] : null;
            $this->length = ($row[$startcol + 37] !== null) ? (string) $row[$startcol + 37] : null;
            $this->mime = ($row[$startcol + 38] !== null) ? (string) $row[$startcol + 38] : null;
            $this->created_at = ($row[$startcol + 39] !== null) ? (string) $row[$startcol + 39] : null;
            $this->updated_at = ($row[$startcol + 40] !== null) ? (string) $row[$startcol + 40] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 41; // 41 = AudioFilePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating AudioFile object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aCcMusicDirs !== null && $this->directory !== $this->aCcMusicDirs->getId()) {
            $this->aCcMusicDirs = null;
        }
        if ($this->aMediaItem !== null && $this->id !== $this->aMediaItem->getId()) {
            $this->aMediaItem = null;
        }
        if ($this->aCcSubjs !== null && $this->owner_id !== $this->aCcSubjs->getDbId()) {
            $this->aCcSubjs = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = AudioFilePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcMusicDirs = null;
            $this->aMediaItem = null;
            $this->aCcSubjs = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = AudioFileQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                // concrete_inheritance behavior
                $this->getParentOrCreate($con)->delete($con);

                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // concrete_inheritance behavior
            $parent = $this->getSyncParent($con);
            $parent->save($con);
            $this->setPrimaryKey($parent->getPrimaryKey());

            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(AudioFilePeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(AudioFilePeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(AudioFilePeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                AudioFilePeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCcMusicDirs !== null) {
                if ($this->aCcMusicDirs->isModified() || $this->aCcMusicDirs->isNew()) {
                    $affectedRows += $this->aCcMusicDirs->save($con);
                }
                $this->setCcMusicDirs($this->aCcMusicDirs);
            }

            if ($this->aMediaItem !== null) {
                if ($this->aMediaItem->isModified() || $this->aMediaItem->isNew()) {
                    $affectedRows += $this->aMediaItem->save($con);
                }
                $this->setMediaItem($this->aMediaItem);
            }

            if ($this->aCcSubjs !== null) {
                if ($this->aCcSubjs->isModified() || $this->aCcSubjs->isNew()) {
                    $affectedRows += $this->aCcSubjs->save($con);
                }
                $this->setCcSubjs($this->aCcSubjs);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AudioFilePeer::DIRECTORY)) {
            $modifiedColumns[':p' . $index++]  = '"directory"';
        }
        if ($this->isColumnModified(AudioFilePeer::FILEPATH)) {
            $modifiedColumns[':p' . $index++]  = '"filepath"';
        }
        if ($this->isColumnModified(AudioFilePeer::MD5)) {
            $modifiedColumns[':p' . $index++]  = '"md5"';
        }
        if ($this->isColumnModified(AudioFilePeer::TRACK_TITLE)) {
            $modifiedColumns[':p' . $index++]  = '"track_title"';
        }
        if ($this->isColumnModified(AudioFilePeer::ARTIST_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"artist_name"';
        }
        if ($this->isColumnModified(AudioFilePeer::BIT_RATE)) {
            $modifiedColumns[':p' . $index++]  = '"bit_rate"';
        }
        if ($this->isColumnModified(AudioFilePeer::SAMPLE_RATE)) {
            $modifiedColumns[':p' . $index++]  = '"sample_rate"';
        }
        if ($this->isColumnModified(AudioFilePeer::ALBUM_TITLE)) {
            $modifiedColumns[':p' . $index++]  = '"album_title"';
        }
        if ($this->isColumnModified(AudioFilePeer::GENRE)) {
            $modifiedColumns[':p' . $index++]  = '"genre"';
        }
        if ($this->isColumnModified(AudioFilePeer::COMMENTS)) {
            $modifiedColumns[':p' . $index++]  = '"comments"';
        }
        if ($this->isColumnModified(AudioFilePeer::YEAR)) {
            $modifiedColumns[':p' . $index++]  = '"year"';
        }
        if ($this->isColumnModified(AudioFilePeer::TRACK_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = '"track_number"';
        }
        if ($this->isColumnModified(AudioFilePeer::CHANNELS)) {
            $modifiedColumns[':p' . $index++]  = '"channels"';
        }
        if ($this->isColumnModified(AudioFilePeer::BPM)) {
            $modifiedColumns[':p' . $index++]  = '"bpm"';
        }
        if ($this->isColumnModified(AudioFilePeer::ENCODED_BY)) {
            $modifiedColumns[':p' . $index++]  = '"encoded_by"';
        }
        if ($this->isColumnModified(AudioFilePeer::MOOD)) {
            $modifiedColumns[':p' . $index++]  = '"mood"';
        }
        if ($this->isColumnModified(AudioFilePeer::LABEL)) {
            $modifiedColumns[':p' . $index++]  = '"label"';
        }
        if ($this->isColumnModified(AudioFilePeer::COMPOSER)) {
            $modifiedColumns[':p' . $index++]  = '"composer"';
        }
        if ($this->isColumnModified(AudioFilePeer::COPYRIGHT)) {
            $modifiedColumns[':p' . $index++]  = '"copyright"';
        }
        if ($this->isColumnModified(AudioFilePeer::CONDUCTOR)) {
            $modifiedColumns[':p' . $index++]  = '"conductor"';
        }
        if ($this->isColumnModified(AudioFilePeer::ISRC_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = '"isrc_number"';
        }
        if ($this->isColumnModified(AudioFilePeer::INFO_URL)) {
            $modifiedColumns[':p' . $index++]  = '"info_url"';
        }
        if ($this->isColumnModified(AudioFilePeer::LANGUAGE)) {
            $modifiedColumns[':p' . $index++]  = '"language"';
        }
        if ($this->isColumnModified(AudioFilePeer::REPLAY_GAIN)) {
            $modifiedColumns[':p' . $index++]  = '"replay_gain"';
        }
        if ($this->isColumnModified(AudioFilePeer::CUEIN)) {
            $modifiedColumns[':p' . $index++]  = '"cuein"';
        }
        if ($this->isColumnModified(AudioFilePeer::CUEOUT)) {
            $modifiedColumns[':p' . $index++]  = '"cueout"';
        }
        if ($this->isColumnModified(AudioFilePeer::SILAN_CHECK)) {
            $modifiedColumns[':p' . $index++]  = '"silan_check"';
        }
        if ($this->isColumnModified(AudioFilePeer::FILE_EXISTS)) {
            $modifiedColumns[':p' . $index++]  = '"file_exists"';
        }
        if ($this->isColumnModified(AudioFilePeer::HIDDEN)) {
            $modifiedColumns[':p' . $index++]  = '"hidden"';
        }
        if ($this->isColumnModified(AudioFilePeer::IS_SCHEDULED)) {
            $modifiedColumns[':p' . $index++]  = '"is_scheduled"';
        }
        if ($this->isColumnModified(AudioFilePeer::IS_PLAYLIST)) {
            $modifiedColumns[':p' . $index++]  = '"is_playlist"';
        }
        if ($this->isColumnModified(AudioFilePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(AudioFilePeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '"name"';
        }
        if ($this->isColumnModified(AudioFilePeer::OWNER_ID)) {
            $modifiedColumns[':p' . $index++]  = '"owner_id"';
        }
        if ($this->isColumnModified(AudioFilePeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(AudioFilePeer::LAST_PLAYED)) {
            $modifiedColumns[':p' . $index++]  = '"last_played"';
        }
        if ($this->isColumnModified(AudioFilePeer::PLAY_COUNT)) {
            $modifiedColumns[':p' . $index++]  = '"play_count"';
        }
        if ($this->isColumnModified(AudioFilePeer::LENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"length"';
        }
        if ($this->isColumnModified(AudioFilePeer::MIME)) {
            $modifiedColumns[':p' . $index++]  = '"mime"';
        }
        if ($this->isColumnModified(AudioFilePeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '"created_at"';
        }
        if ($this->isColumnModified(AudioFilePeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '"updated_at"';
        }

        $sql = sprintf(
            'INSERT INTO "media_audiofile" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"directory"':
                        $stmt->bindValue($identifier, $this->directory, PDO::PARAM_INT);
                        break;
                    case '"filepath"':
                        $stmt->bindValue($identifier, $this->filepath, PDO::PARAM_STR);
                        break;
                    case '"md5"':
                        $stmt->bindValue($identifier, $this->md5, PDO::PARAM_STR);
                        break;
                    case '"track_title"':
                        $stmt->bindValue($identifier, $this->track_title, PDO::PARAM_STR);
                        break;
                    case '"artist_name"':
                        $stmt->bindValue($identifier, $this->artist_name, PDO::PARAM_STR);
                        break;
                    case '"bit_rate"':
                        $stmt->bindValue($identifier, $this->bit_rate, PDO::PARAM_INT);
                        break;
                    case '"sample_rate"':
                        $stmt->bindValue($identifier, $this->sample_rate, PDO::PARAM_INT);
                        break;
                    case '"album_title"':
                        $stmt->bindValue($identifier, $this->album_title, PDO::PARAM_STR);
                        break;
                    case '"genre"':
                        $stmt->bindValue($identifier, $this->genre, PDO::PARAM_STR);
                        break;
                    case '"comments"':
                        $stmt->bindValue($identifier, $this->comments, PDO::PARAM_STR);
                        break;
                    case '"year"':
                        $stmt->bindValue($identifier, $this->year, PDO::PARAM_STR);
                        break;
                    case '"track_number"':
                        $stmt->bindValue($identifier, $this->track_number, PDO::PARAM_INT);
                        break;
                    case '"channels"':
                        $stmt->bindValue($identifier, $this->channels, PDO::PARAM_INT);
                        break;
                    case '"bpm"':
                        $stmt->bindValue($identifier, $this->bpm, PDO::PARAM_INT);
                        break;
                    case '"encoded_by"':
                        $stmt->bindValue($identifier, $this->encoded_by, PDO::PARAM_STR);
                        break;
                    case '"mood"':
                        $stmt->bindValue($identifier, $this->mood, PDO::PARAM_STR);
                        break;
                    case '"label"':
                        $stmt->bindValue($identifier, $this->label, PDO::PARAM_STR);
                        break;
                    case '"composer"':
                        $stmt->bindValue($identifier, $this->composer, PDO::PARAM_STR);
                        break;
                    case '"copyright"':
                        $stmt->bindValue($identifier, $this->copyright, PDO::PARAM_STR);
                        break;
                    case '"conductor"':
                        $stmt->bindValue($identifier, $this->conductor, PDO::PARAM_STR);
                        break;
                    case '"isrc_number"':
                        $stmt->bindValue($identifier, $this->isrc_number, PDO::PARAM_STR);
                        break;
                    case '"info_url"':
                        $stmt->bindValue($identifier, $this->info_url, PDO::PARAM_STR);
                        break;
                    case '"language"':
                        $stmt->bindValue($identifier, $this->language, PDO::PARAM_STR);
                        break;
                    case '"replay_gain"':
                        $stmt->bindValue($identifier, $this->replay_gain, PDO::PARAM_INT);
                        break;
                    case '"cuein"':
                        $stmt->bindValue($identifier, $this->cuein, PDO::PARAM_STR);
                        break;
                    case '"cueout"':
                        $stmt->bindValue($identifier, $this->cueout, PDO::PARAM_STR);
                        break;
                    case '"silan_check"':
                        $stmt->bindValue($identifier, $this->silan_check, PDO::PARAM_BOOL);
                        break;
                    case '"file_exists"':
                        $stmt->bindValue($identifier, $this->file_exists, PDO::PARAM_BOOL);
                        break;
                    case '"hidden"':
                        $stmt->bindValue($identifier, $this->hidden, PDO::PARAM_BOOL);
                        break;
                    case '"is_scheduled"':
                        $stmt->bindValue($identifier, $this->is_scheduled, PDO::PARAM_BOOL);
                        break;
                    case '"is_playlist"':
                        $stmt->bindValue($identifier, $this->is_playlist, PDO::PARAM_BOOL);
                        break;
                    case '"id"':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '"name"':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '"owner_id"':
                        $stmt->bindValue($identifier, $this->owner_id, PDO::PARAM_INT);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"last_played"':
                        $stmt->bindValue($identifier, $this->last_played, PDO::PARAM_STR);
                        break;
                    case '"play_count"':
                        $stmt->bindValue($identifier, $this->play_count, PDO::PARAM_INT);
                        break;
                    case '"length"':
                        $stmt->bindValue($identifier, $this->length, PDO::PARAM_STR);
                        break;
                    case '"mime"':
                        $stmt->bindValue($identifier, $this->mime, PDO::PARAM_STR);
                        break;
                    case '"created_at"':
                        $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
                        break;
                    case '"updated_at"':
                        $stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCcMusicDirs !== null) {
                if (!$this->aCcMusicDirs->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcMusicDirs->getValidationFailures());
                }
            }

            if ($this->aMediaItem !== null) {
                if (!$this->aMediaItem->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aMediaItem->getValidationFailures());
                }
            }

            if ($this->aCcSubjs !== null) {
                if (!$this->aCcSubjs->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcSubjs->getValidationFailures());
                }
            }


            if (($retval = AudioFilePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = AudioFilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getDirectory();
                break;
            case 1:
                return $this->getFilepath();
                break;
            case 2:
                return $this->getMd5();
                break;
            case 3:
                return $this->getTrackTitle();
                break;
            case 4:
                return $this->getArtistName();
                break;
            case 5:
                return $this->getBitRate();
                break;
            case 6:
                return $this->getSampleRate();
                break;
            case 7:
                return $this->getAlbumTitle();
                break;
            case 8:
                return $this->getGenre();
                break;
            case 9:
                return $this->getComments();
                break;
            case 10:
                return $this->getYear();
                break;
            case 11:
                return $this->getTrackNumber();
                break;
            case 12:
                return $this->getChannels();
                break;
            case 13:
                return $this->getBpm();
                break;
            case 14:
                return $this->getEncodedBy();
                break;
            case 15:
                return $this->getMood();
                break;
            case 16:
                return $this->getLabel();
                break;
            case 17:
                return $this->getComposer();
                break;
            case 18:
                return $this->getCopyright();
                break;
            case 19:
                return $this->getConductor();
                break;
            case 20:
                return $this->getIsrcNumber();
                break;
            case 21:
                return $this->getInfoUrl();
                break;
            case 22:
                return $this->getLanguage();
                break;
            case 23:
                return $this->getReplayGain();
                break;
            case 24:
                return $this->getCuein();
                break;
            case 25:
                return $this->getCueout();
                break;
            case 26:
                return $this->getIsSilanChecked();
                break;
            case 27:
                return $this->getFileExists();
                break;
            case 28:
                return $this->getFileHidden();
                break;
            case 29:
                return $this->getIsScheduled();
                break;
            case 30:
                return $this->getIsPlaylist();
                break;
            case 31:
                return $this->getId();
                break;
            case 32:
                return $this->getName();
                break;
            case 33:
                return $this->getOwnerId();
                break;
            case 34:
                return $this->getDescription();
                break;
            case 35:
                return $this->getLastPlayedTime();
                break;
            case 36:
                return $this->getPlayCount();
                break;
            case 37:
                return $this->getLength();
                break;
            case 38:
                return $this->getMime();
                break;
            case 39:
                return $this->getCreatedAt();
                break;
            case 40:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['AudioFile'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['AudioFile'][$this->getPrimaryKey()] = true;
        $keys = AudioFilePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDirectory(),
            $keys[1] => $this->getFilepath(),
            $keys[2] => $this->getMd5(),
            $keys[3] => $this->getTrackTitle(),
            $keys[4] => $this->getArtistName(),
            $keys[5] => $this->getBitRate(),
            $keys[6] => $this->getSampleRate(),
            $keys[7] => $this->getAlbumTitle(),
            $keys[8] => $this->getGenre(),
            $keys[9] => $this->getComments(),
            $keys[10] => $this->getYear(),
            $keys[11] => $this->getTrackNumber(),
            $keys[12] => $this->getChannels(),
            $keys[13] => $this->getBpm(),
            $keys[14] => $this->getEncodedBy(),
            $keys[15] => $this->getMood(),
            $keys[16] => $this->getLabel(),
            $keys[17] => $this->getComposer(),
            $keys[18] => $this->getCopyright(),
            $keys[19] => $this->getConductor(),
            $keys[20] => $this->getIsrcNumber(),
            $keys[21] => $this->getInfoUrl(),
            $keys[22] => $this->getLanguage(),
            $keys[23] => $this->getReplayGain(),
            $keys[24] => $this->getCuein(),
            $keys[25] => $this->getCueout(),
            $keys[26] => $this->getIsSilanChecked(),
            $keys[27] => $this->getFileExists(),
            $keys[28] => $this->getFileHidden(),
            $keys[29] => $this->getIsScheduled(),
            $keys[30] => $this->getIsPlaylist(),
            $keys[31] => $this->getId(),
            $keys[32] => $this->getName(),
            $keys[33] => $this->getOwnerId(),
            $keys[34] => $this->getDescription(),
            $keys[35] => $this->getLastPlayedTime(),
            $keys[36] => $this->getPlayCount(),
            $keys[37] => $this->getLength(),
            $keys[38] => $this->getMime(),
            $keys[39] => $this->getCreatedAt(),
            $keys[40] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcMusicDirs) {
                $result['CcMusicDirs'] = $this->aCcMusicDirs->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aMediaItem) {
                $result['MediaItem'] = $this->aMediaItem->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcSubjs) {
                $result['CcSubjs'] = $this->aCcSubjs->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = AudioFilePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setDirectory($value);
                break;
            case 1:
                $this->setFilepath($value);
                break;
            case 2:
                $this->setMd5($value);
                break;
            case 3:
                $this->setTrackTitle($value);
                break;
            case 4:
                $this->setArtistName($value);
                break;
            case 5:
                $this->setBitRate($value);
                break;
            case 6:
                $this->setSampleRate($value);
                break;
            case 7:
                $this->setAlbumTitle($value);
                break;
            case 8:
                $this->setGenre($value);
                break;
            case 9:
                $this->setComments($value);
                break;
            case 10:
                $this->setYear($value);
                break;
            case 11:
                $this->setTrackNumber($value);
                break;
            case 12:
                $this->setChannels($value);
                break;
            case 13:
                $this->setBpm($value);
                break;
            case 14:
                $this->setEncodedBy($value);
                break;
            case 15:
                $this->setMood($value);
                break;
            case 16:
                $this->setLabel($value);
                break;
            case 17:
                $this->setComposer($value);
                break;
            case 18:
                $this->setCopyright($value);
                break;
            case 19:
                $this->setConductor($value);
                break;
            case 20:
                $this->setIsrcNumber($value);
                break;
            case 21:
                $this->setInfoUrl($value);
                break;
            case 22:
                $this->setLanguage($value);
                break;
            case 23:
                $this->setReplayGain($value);
                break;
            case 24:
                $this->setCuein($value);
                break;
            case 25:
                $this->setCueout($value);
                break;
            case 26:
                $this->setIsSilanChecked($value);
                break;
            case 27:
                $this->setFileExists($value);
                break;
            case 28:
                $this->setFileHidden($value);
                break;
            case 29:
                $this->setIsScheduled($value);
                break;
            case 30:
                $this->setIsPlaylist($value);
                break;
            case 31:
                $this->setId($value);
                break;
            case 32:
                $this->setName($value);
                break;
            case 33:
                $this->setOwnerId($value);
                break;
            case 34:
                $this->setDescription($value);
                break;
            case 35:
                $this->setLastPlayedTime($value);
                break;
            case 36:
                $this->setPlayCount($value);
                break;
            case 37:
                $this->setLength($value);
                break;
            case 38:
                $this->setMime($value);
                break;
            case 39:
                $this->setCreatedAt($value);
                break;
            case 40:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = AudioFilePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDirectory($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setFilepath($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setMd5($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setTrackTitle($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setArtistName($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setBitRate($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setSampleRate($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setAlbumTitle($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setGenre($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setComments($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setYear($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setTrackNumber($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setChannels($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setBpm($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setEncodedBy($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setMood($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setLabel($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setComposer($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setCopyright($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setConductor($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setIsrcNumber($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setInfoUrl($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setLanguage($arr[$keys[22]]);
        if (array_key_exists($keys[23], $arr)) $this->setReplayGain($arr[$keys[23]]);
        if (array_key_exists($keys[24], $arr)) $this->setCuein($arr[$keys[24]]);
        if (array_key_exists($keys[25], $arr)) $this->setCueout($arr[$keys[25]]);
        if (array_key_exists($keys[26], $arr)) $this->setIsSilanChecked($arr[$keys[26]]);
        if (array_key_exists($keys[27], $arr)) $this->setFileExists($arr[$keys[27]]);
        if (array_key_exists($keys[28], $arr)) $this->setFileHidden($arr[$keys[28]]);
        if (array_key_exists($keys[29], $arr)) $this->setIsScheduled($arr[$keys[29]]);
        if (array_key_exists($keys[30], $arr)) $this->setIsPlaylist($arr[$keys[30]]);
        if (array_key_exists($keys[31], $arr)) $this->setId($arr[$keys[31]]);
        if (array_key_exists($keys[32], $arr)) $this->setName($arr[$keys[32]]);
        if (array_key_exists($keys[33], $arr)) $this->setOwnerId($arr[$keys[33]]);
        if (array_key_exists($keys[34], $arr)) $this->setDescription($arr[$keys[34]]);
        if (array_key_exists($keys[35], $arr)) $this->setLastPlayedTime($arr[$keys[35]]);
        if (array_key_exists($keys[36], $arr)) $this->setPlayCount($arr[$keys[36]]);
        if (array_key_exists($keys[37], $arr)) $this->setLength($arr[$keys[37]]);
        if (array_key_exists($keys[38], $arr)) $this->setMime($arr[$keys[38]]);
        if (array_key_exists($keys[39], $arr)) $this->setCreatedAt($arr[$keys[39]]);
        if (array_key_exists($keys[40], $arr)) $this->setUpdatedAt($arr[$keys[40]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AudioFilePeer::DATABASE_NAME);

        if ($this->isColumnModified(AudioFilePeer::DIRECTORY)) $criteria->add(AudioFilePeer::DIRECTORY, $this->directory);
        if ($this->isColumnModified(AudioFilePeer::FILEPATH)) $criteria->add(AudioFilePeer::FILEPATH, $this->filepath);
        if ($this->isColumnModified(AudioFilePeer::MD5)) $criteria->add(AudioFilePeer::MD5, $this->md5);
        if ($this->isColumnModified(AudioFilePeer::TRACK_TITLE)) $criteria->add(AudioFilePeer::TRACK_TITLE, $this->track_title);
        if ($this->isColumnModified(AudioFilePeer::ARTIST_NAME)) $criteria->add(AudioFilePeer::ARTIST_NAME, $this->artist_name);
        if ($this->isColumnModified(AudioFilePeer::BIT_RATE)) $criteria->add(AudioFilePeer::BIT_RATE, $this->bit_rate);
        if ($this->isColumnModified(AudioFilePeer::SAMPLE_RATE)) $criteria->add(AudioFilePeer::SAMPLE_RATE, $this->sample_rate);
        if ($this->isColumnModified(AudioFilePeer::ALBUM_TITLE)) $criteria->add(AudioFilePeer::ALBUM_TITLE, $this->album_title);
        if ($this->isColumnModified(AudioFilePeer::GENRE)) $criteria->add(AudioFilePeer::GENRE, $this->genre);
        if ($this->isColumnModified(AudioFilePeer::COMMENTS)) $criteria->add(AudioFilePeer::COMMENTS, $this->comments);
        if ($this->isColumnModified(AudioFilePeer::YEAR)) $criteria->add(AudioFilePeer::YEAR, $this->year);
        if ($this->isColumnModified(AudioFilePeer::TRACK_NUMBER)) $criteria->add(AudioFilePeer::TRACK_NUMBER, $this->track_number);
        if ($this->isColumnModified(AudioFilePeer::CHANNELS)) $criteria->add(AudioFilePeer::CHANNELS, $this->channels);
        if ($this->isColumnModified(AudioFilePeer::BPM)) $criteria->add(AudioFilePeer::BPM, $this->bpm);
        if ($this->isColumnModified(AudioFilePeer::ENCODED_BY)) $criteria->add(AudioFilePeer::ENCODED_BY, $this->encoded_by);
        if ($this->isColumnModified(AudioFilePeer::MOOD)) $criteria->add(AudioFilePeer::MOOD, $this->mood);
        if ($this->isColumnModified(AudioFilePeer::LABEL)) $criteria->add(AudioFilePeer::LABEL, $this->label);
        if ($this->isColumnModified(AudioFilePeer::COMPOSER)) $criteria->add(AudioFilePeer::COMPOSER, $this->composer);
        if ($this->isColumnModified(AudioFilePeer::COPYRIGHT)) $criteria->add(AudioFilePeer::COPYRIGHT, $this->copyright);
        if ($this->isColumnModified(AudioFilePeer::CONDUCTOR)) $criteria->add(AudioFilePeer::CONDUCTOR, $this->conductor);
        if ($this->isColumnModified(AudioFilePeer::ISRC_NUMBER)) $criteria->add(AudioFilePeer::ISRC_NUMBER, $this->isrc_number);
        if ($this->isColumnModified(AudioFilePeer::INFO_URL)) $criteria->add(AudioFilePeer::INFO_URL, $this->info_url);
        if ($this->isColumnModified(AudioFilePeer::LANGUAGE)) $criteria->add(AudioFilePeer::LANGUAGE, $this->language);
        if ($this->isColumnModified(AudioFilePeer::REPLAY_GAIN)) $criteria->add(AudioFilePeer::REPLAY_GAIN, $this->replay_gain);
        if ($this->isColumnModified(AudioFilePeer::CUEIN)) $criteria->add(AudioFilePeer::CUEIN, $this->cuein);
        if ($this->isColumnModified(AudioFilePeer::CUEOUT)) $criteria->add(AudioFilePeer::CUEOUT, $this->cueout);
        if ($this->isColumnModified(AudioFilePeer::SILAN_CHECK)) $criteria->add(AudioFilePeer::SILAN_CHECK, $this->silan_check);
        if ($this->isColumnModified(AudioFilePeer::FILE_EXISTS)) $criteria->add(AudioFilePeer::FILE_EXISTS, $this->file_exists);
        if ($this->isColumnModified(AudioFilePeer::HIDDEN)) $criteria->add(AudioFilePeer::HIDDEN, $this->hidden);
        if ($this->isColumnModified(AudioFilePeer::IS_SCHEDULED)) $criteria->add(AudioFilePeer::IS_SCHEDULED, $this->is_scheduled);
        if ($this->isColumnModified(AudioFilePeer::IS_PLAYLIST)) $criteria->add(AudioFilePeer::IS_PLAYLIST, $this->is_playlist);
        if ($this->isColumnModified(AudioFilePeer::ID)) $criteria->add(AudioFilePeer::ID, $this->id);
        if ($this->isColumnModified(AudioFilePeer::NAME)) $criteria->add(AudioFilePeer::NAME, $this->name);
        if ($this->isColumnModified(AudioFilePeer::OWNER_ID)) $criteria->add(AudioFilePeer::OWNER_ID, $this->owner_id);
        if ($this->isColumnModified(AudioFilePeer::DESCRIPTION)) $criteria->add(AudioFilePeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(AudioFilePeer::LAST_PLAYED)) $criteria->add(AudioFilePeer::LAST_PLAYED, $this->last_played);
        if ($this->isColumnModified(AudioFilePeer::PLAY_COUNT)) $criteria->add(AudioFilePeer::PLAY_COUNT, $this->play_count);
        if ($this->isColumnModified(AudioFilePeer::LENGTH)) $criteria->add(AudioFilePeer::LENGTH, $this->length);
        if ($this->isColumnModified(AudioFilePeer::MIME)) $criteria->add(AudioFilePeer::MIME, $this->mime);
        if ($this->isColumnModified(AudioFilePeer::CREATED_AT)) $criteria->add(AudioFilePeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(AudioFilePeer::UPDATED_AT)) $criteria->add(AudioFilePeer::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(AudioFilePeer::DATABASE_NAME);
        $criteria->add(AudioFilePeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of AudioFile (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDirectory($this->getDirectory());
        $copyObj->setFilepath($this->getFilepath());
        $copyObj->setMd5($this->getMd5());
        $copyObj->setTrackTitle($this->getTrackTitle());
        $copyObj->setArtistName($this->getArtistName());
        $copyObj->setBitRate($this->getBitRate());
        $copyObj->setSampleRate($this->getSampleRate());
        $copyObj->setAlbumTitle($this->getAlbumTitle());
        $copyObj->setGenre($this->getGenre());
        $copyObj->setComments($this->getComments());
        $copyObj->setYear($this->getYear());
        $copyObj->setTrackNumber($this->getTrackNumber());
        $copyObj->setChannels($this->getChannels());
        $copyObj->setBpm($this->getBpm());
        $copyObj->setEncodedBy($this->getEncodedBy());
        $copyObj->setMood($this->getMood());
        $copyObj->setLabel($this->getLabel());
        $copyObj->setComposer($this->getComposer());
        $copyObj->setCopyright($this->getCopyright());
        $copyObj->setConductor($this->getConductor());
        $copyObj->setIsrcNumber($this->getIsrcNumber());
        $copyObj->setInfoUrl($this->getInfoUrl());
        $copyObj->setLanguage($this->getLanguage());
        $copyObj->setReplayGain($this->getReplayGain());
        $copyObj->setCuein($this->getCuein());
        $copyObj->setCueout($this->getCueout());
        $copyObj->setIsSilanChecked($this->getIsSilanChecked());
        $copyObj->setFileExists($this->getFileExists());
        $copyObj->setFileHidden($this->getFileHidden());
        $copyObj->setIsScheduled($this->getIsScheduled());
        $copyObj->setIsPlaylist($this->getIsPlaylist());
        $copyObj->setName($this->getName());
        $copyObj->setOwnerId($this->getOwnerId());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setLastPlayedTime($this->getLastPlayedTime());
        $copyObj->setPlayCount($this->getPlayCount());
        $copyObj->setLength($this->getLength());
        $copyObj->setMime($this->getMime());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            $relObj = $this->getMediaItem();
            if ($relObj) {
                $copyObj->setMediaItem($relObj->copy($deepCopy));
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return AudioFile Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return AudioFilePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AudioFilePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcMusicDirs object.
     *
     * @param                  CcMusicDirs $v
     * @return AudioFile The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcMusicDirs(CcMusicDirs $v = null)
    {
        if ($v === null) {
            $this->setDirectory(NULL);
        } else {
            $this->setDirectory($v->getId());
        }

        $this->aCcMusicDirs = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcMusicDirs object, it will not be re-added.
        if ($v !== null) {
            $v->addAudioFile($this);
        }


        return $this;
    }


    /**
     * Get the associated CcMusicDirs object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcMusicDirs The associated CcMusicDirs object.
     * @throws PropelException
     */
    public function getCcMusicDirs(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcMusicDirs === null && ($this->directory !== null) && $doQuery) {
            $this->aCcMusicDirs = CcMusicDirsQuery::create()->findPk($this->directory, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcMusicDirs->addAudioFiles($this);
             */
        }

        return $this->aCcMusicDirs;
    }

    /**
     * Declares an association between this object and a MediaItem object.
     *
     * @param                  MediaItem $v
     * @return AudioFile The current object (for fluent API support)
     * @throws PropelException
     */
    public function setMediaItem(MediaItem $v = null)
    {
        if ($v === null) {
            $this->setId(NULL);
        } else {
            $this->setId($v->getId());
        }

        $this->aMediaItem = $v;

        // Add binding for other direction of this 1:1 relationship.
        if ($v !== null) {
            $v->setAudioFile($this);
        }


        return $this;
    }


    /**
     * Get the associated MediaItem object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return MediaItem The associated MediaItem object.
     * @throws PropelException
     */
    public function getMediaItem(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aMediaItem === null && ($this->id !== null) && $doQuery) {
            $this->aMediaItem = MediaItemQuery::create()->findPk($this->id, $con);
            // Because this foreign key represents a one-to-one relationship, we will create a bi-directional association.
            $this->aMediaItem->setAudioFile($this);
        }

        return $this->aMediaItem;
    }

    /**
     * Declares an association between this object and a CcSubjs object.
     *
     * @param                  CcSubjs $v
     * @return AudioFile The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcSubjs(CcSubjs $v = null)
    {
        if ($v === null) {
            $this->setOwnerId(NULL);
        } else {
            $this->setOwnerId($v->getDbId());
        }

        $this->aCcSubjs = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcSubjs object, it will not be re-added.
        if ($v !== null) {
            $v->addAudioFile($this);
        }


        return $this;
    }


    /**
     * Get the associated CcSubjs object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcSubjs The associated CcSubjs object.
     * @throws PropelException
     */
    public function getCcSubjs(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcSubjs === null && ($this->owner_id !== null) && $doQuery) {
            $this->aCcSubjs = CcSubjsQuery::create()->findPk($this->owner_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcSubjs->addAudioFiles($this);
             */
        }

        return $this->aCcSubjs;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->directory = null;
        $this->filepath = null;
        $this->md5 = null;
        $this->track_title = null;
        $this->artist_name = null;
        $this->bit_rate = null;
        $this->sample_rate = null;
        $this->album_title = null;
        $this->genre = null;
        $this->comments = null;
        $this->year = null;
        $this->track_number = null;
        $this->channels = null;
        $this->bpm = null;
        $this->encoded_by = null;
        $this->mood = null;
        $this->label = null;
        $this->composer = null;
        $this->copyright = null;
        $this->conductor = null;
        $this->isrc_number = null;
        $this->info_url = null;
        $this->language = null;
        $this->replay_gain = null;
        $this->cuein = null;
        $this->cueout = null;
        $this->silan_check = null;
        $this->file_exists = null;
        $this->hidden = null;
        $this->is_scheduled = null;
        $this->is_playlist = null;
        $this->id = null;
        $this->name = null;
        $this->owner_id = null;
        $this->description = null;
        $this->last_played = null;
        $this->play_count = null;
        $this->length = null;
        $this->mime = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->aCcMusicDirs instanceof Persistent) {
              $this->aCcMusicDirs->clearAllReferences($deep);
            }
            if ($this->aMediaItem instanceof Persistent) {
              $this->aMediaItem->clearAllReferences($deep);
            }
            if ($this->aCcSubjs instanceof Persistent) {
              $this->aCcSubjs->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aCcMusicDirs = null;
        $this->aMediaItem = null;
        $this->aCcSubjs = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(AudioFilePeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

    // concrete_inheritance behavior

    /**
     * Get or Create the parent MediaItem object of the current object
     *
     * @return    MediaItem The parent object
     */
    public function getParentOrCreate($con = null)
    {
        if ($this->isNew()) {
            if ($this->isPrimaryKeyNull()) {
                //this prevent issue with deep copy & save parent object
                if (null === ($parent = $this->getMediaItem($con))) {
                    $parent = new MediaItem();
                }
                $parent->setDescendantClass('Airtime\MediaItem\AudioFile');

                return $parent;
            } else {
                $parent = MediaItemQuery::create()->findPk($this->getPrimaryKey(), $con);
                if (null === $parent || null !== $parent->getDescendantClass()) {
                    $parent = new MediaItem();
                    $parent->setPrimaryKey($this->getPrimaryKey());
                    $parent->setDescendantClass('Airtime\MediaItem\AudioFile');
                }

                return $parent;
            }
        }

        return MediaItemQuery::create()->findPk($this->getPrimaryKey(), $con);
    }

    /**
     * Create or Update the parent MediaItem object
     * And return its primary key
     *
     * @return    int The primary key of the parent object
     */
    public function getSyncParent($con = null)
    {
        $parent = $this->getParentOrCreate($con);
        $parent->setName($this->getName());
        $parent->setOwnerId($this->getOwnerId());
        $parent->setDescription($this->getDescription());
        $parent->setLastPlayedTime($this->getLastPlayedTime());
        $parent->setPlayCount($this->getPlayCount());
        $parent->setLength($this->getLength());
        $parent->setMime($this->getMime());
        $parent->setCreatedAt($this->getCreatedAt());
        $parent->setUpdatedAt($this->getUpdatedAt());
        if ($this->getCcSubjs() && $this->getCcSubjs()->isNew()) {
            $parent->setCcSubjs($this->getCcSubjs());
        }

        return $parent;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     AudioFile The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = AudioFilePeer::UPDATED_AT;

        return $this;
    }

}
