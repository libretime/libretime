<?php


/**
 * Base class that represents a row from the 'cc_files' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcFiles extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'CcFilesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcFilesPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the name field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $name;

    /**
     * The value for the mime field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $mime;

    /**
     * The value for the ftype field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $ftype;

    /**
     * The value for the filepath field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $filepath;

    /**
     * The value for the import_status field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $import_status;

    /**
     * The value for the currentlyaccessing field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $currentlyaccessing;

    /**
     * The value for the editedby field.
     * @var        int
     */
    protected $editedby;

    /**
     * The value for the mtime field.
     * @var        string
     */
    protected $mtime;

    /**
     * The value for the utime field.
     * @var        string
     */
    protected $utime;

    /**
     * The value for the lptime field.
     * @var        string
     */
    protected $lptime;

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
     * The value for the format field.
     * @var        string
     */
    protected $format;

    /**
     * The value for the length field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $length;

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
     * The value for the url field.
     * @var        string
     */
    protected $url;

    /**
     * The value for the bpm field.
     * @var        int
     */
    protected $bpm;

    /**
     * The value for the rating field.
     * @var        string
     */
    protected $rating;

    /**
     * The value for the encoded_by field.
     * @var        string
     */
    protected $encoded_by;

    /**
     * The value for the disc_number field.
     * @var        string
     */
    protected $disc_number;

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
     * The value for the encoder field.
     * @var        string
     */
    protected $encoder;

    /**
     * The value for the checksum field.
     * @var        string
     */
    protected $checksum;

    /**
     * The value for the lyrics field.
     * @var        string
     */
    protected $lyrics;

    /**
     * The value for the orchestra field.
     * @var        string
     */
    protected $orchestra;

    /**
     * The value for the conductor field.
     * @var        string
     */
    protected $conductor;

    /**
     * The value for the lyricist field.
     * @var        string
     */
    protected $lyricist;

    /**
     * The value for the original_lyricist field.
     * @var        string
     */
    protected $original_lyricist;

    /**
     * The value for the radio_station_name field.
     * @var        string
     */
    protected $radio_station_name;

    /**
     * The value for the info_url field.
     * @var        string
     */
    protected $info_url;

    /**
     * The value for the artist_url field.
     * @var        string
     */
    protected $artist_url;

    /**
     * The value for the audio_source_url field.
     * @var        string
     */
    protected $audio_source_url;

    /**
     * The value for the radio_station_url field.
     * @var        string
     */
    protected $radio_station_url;

    /**
     * The value for the buy_this_url field.
     * @var        string
     */
    protected $buy_this_url;

    /**
     * The value for the isrc_number field.
     * @var        string
     */
    protected $isrc_number;

    /**
     * The value for the catalog_number field.
     * @var        string
     */
    protected $catalog_number;

    /**
     * The value for the original_artist field.
     * @var        string
     */
    protected $original_artist;

    /**
     * The value for the copyright field.
     * @var        string
     */
    protected $copyright;

    /**
     * The value for the report_datetime field.
     * @var        string
     */
    protected $report_datetime;

    /**
     * The value for the report_location field.
     * @var        string
     */
    protected $report_location;

    /**
     * The value for the report_organization field.
     * @var        string
     */
    protected $report_organization;

    /**
     * The value for the subject field.
     * @var        string
     */
    protected $subject;

    /**
     * The value for the contributor field.
     * @var        string
     */
    protected $contributor;

    /**
     * The value for the language field.
     * @var        string
     */
    protected $language;

    /**
     * The value for the file_exists field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $file_exists;

    /**
     * The value for the replay_gain field.
     * @var        string
     */
    protected $replay_gain;

    /**
     * The value for the owner_id field.
     * @var        int
     */
    protected $owner_id;

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
     * The value for the filesize field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $filesize;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the artwork field.
     * @var        string
     */
    protected $artwork;

    /**
     * The value for the track_type_id field.
     * @var        int
     */
    protected $track_type_id;

    /**
     * @var        CcSubjs
     */
    protected $aFkOwner;

    /**
     * @var        CcSubjs
     */
    protected $aCcSubjsRelatedByDbEditedby;

    /**
     * @var        CcTracktypes
     */
    protected $aCcTracktypes;

    /**
     * @var        PropelObjectCollection|CcShowInstances[] Collection to store aggregation of CcShowInstances objects.
     */
    protected $collCcShowInstancess;
    protected $collCcShowInstancessPartial;

    /**
     * @var        PropelObjectCollection|CcPlaylistcontents[] Collection to store aggregation of CcPlaylistcontents objects.
     */
    protected $collCcPlaylistcontentss;
    protected $collCcPlaylistcontentssPartial;

    /**
     * @var        PropelObjectCollection|CcBlockcontents[] Collection to store aggregation of CcBlockcontents objects.
     */
    protected $collCcBlockcontentss;
    protected $collCcBlockcontentssPartial;

    /**
     * @var        PropelObjectCollection|CcSchedule[] Collection to store aggregation of CcSchedule objects.
     */
    protected $collCcSchedules;
    protected $collCcSchedulesPartial;

    /**
     * @var        PropelObjectCollection|CcPlayoutHistory[] Collection to store aggregation of CcPlayoutHistory objects.
     */
    protected $collCcPlayoutHistorys;
    protected $collCcPlayoutHistorysPartial;

    /**
     * @var        PropelObjectCollection|ThirdPartyTrackReferences[] Collection to store aggregation of ThirdPartyTrackReferences objects.
     */
    protected $collThirdPartyTrackReferencess;
    protected $collThirdPartyTrackReferencessPartial;

    /**
     * @var        PropelObjectCollection|PodcastEpisodes[] Collection to store aggregation of PodcastEpisodes objects.
     */
    protected $collPodcastEpisodess;
    protected $collPodcastEpisodessPartial;

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
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccShowInstancessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccPlaylistcontentssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccBlockcontentssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccSchedulesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccPlayoutHistorysScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $thirdPartyTrackReferencessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $podcastEpisodessScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->name = '';
        $this->mime = '';
        $this->ftype = '';
        $this->filepath = '';
        $this->import_status = 1;
        $this->currentlyaccessing = 0;
        $this->length = '00:00:00';
        $this->file_exists = true;
        $this->cuein = '00:00:00';
        $this->cueout = '00:00:00';
        $this->silan_check = false;
        $this->hidden = false;
        $this->is_scheduled = false;
        $this->is_playlist = false;
        $this->filesize = 0;
    }

    /**
     * Initializes internal state of BaseCcFiles object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getDbId()
    {

        return $this->id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getDbName()
    {

        return $this->name;
    }

    /**
     * Get the [mime] column value.
     *
     * @return string
     */
    public function getDbMime()
    {

        return $this->mime;
    }

    /**
     * Get the [ftype] column value.
     *
     * @return string
     */
    public function getDbFtype()
    {

        return $this->ftype;
    }

    /**
     * Get the [filepath] column value.
     *
     * @return string
     */
    public function getDbFilepath()
    {

        return $this->filepath;
    }

    /**
     * Get the [import_status] column value.
     *
     * @return int
     */
    public function getDbImportStatus()
    {

        return $this->import_status;
    }

    /**
     * Get the [currentlyaccessing] column value.
     *
     * @return int
     */
    public function getDbCurrentlyaccessing()
    {

        return $this->currentlyaccessing;
    }

    /**
     * Get the [editedby] column value.
     *
     * @return int
     */
    public function getDbEditedby()
    {

        return $this->editedby;
    }

    /**
     * Get the [optionally formatted] temporal [mtime] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbMtime($format = 'Y-m-d H:i:s')
    {
        if ($this->mtime === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->mtime);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->mtime, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            throw new PropelException('strftime format not supported anymore');
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [utime] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbUtime($format = 'Y-m-d H:i:s')
    {
        if ($this->utime === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->utime);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->utime, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            throw new PropelException('strftime format not supported anymore');
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [lptime] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbLPtime($format = 'Y-m-d H:i:s')
    {
        if ($this->lptime === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->lptime);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->lptime, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            throw new PropelException('strftime format not supported anymore');
        }

        return $dt->format($format);

    }

    /**
     * Get the [md5] column value.
     *
     * @return string
     */
    public function getDbMd5()
    {

        return $this->md5;
    }

    /**
     * Get the [track_title] column value.
     *
     * @return string
     */
    public function getDbTrackTitle()
    {

        return $this->track_title;
    }

    /**
     * Get the [artist_name] column value.
     *
     * @return string
     */
    public function getDbArtistName()
    {

        return $this->artist_name;
    }

    /**
     * Get the [bit_rate] column value.
     *
     * @return int
     */
    public function getDbBitRate()
    {

        return $this->bit_rate;
    }

    /**
     * Get the [sample_rate] column value.
     *
     * @return int
     */
    public function getDbSampleRate()
    {

        return $this->sample_rate;
    }

    /**
     * Get the [format] column value.
     *
     * @return string
     */
    public function getDbFormat()
    {

        return $this->format;
    }

    /**
     * Get the [length] column value.
     *
     * @return string
     */
    public function getDbLength()
    {

        return $this->length;
    }

    /**
     * Get the [album_title] column value.
     *
     * @return string
     */
    public function getDbAlbumTitle()
    {

        return $this->album_title;
    }

    /**
     * Get the [genre] column value.
     *
     * @return string
     */
    public function getDbGenre()
    {

        return $this->genre;
    }

    /**
     * Get the [comments] column value.
     *
     * @return string
     */
    public function getDbComments()
    {

        return $this->comments;
    }

    /**
     * Get the [year] column value.
     *
     * @return string
     */
    public function getDbYear()
    {

        return $this->year;
    }

    /**
     * Get the [track_number] column value.
     *
     * @return int
     */
    public function getDbTrackNumber()
    {

        return $this->track_number;
    }

    /**
     * Get the [channels] column value.
     *
     * @return int
     */
    public function getDbChannels()
    {

        return $this->channels;
    }

    /**
     * Get the [url] column value.
     *
     * @return string
     */
    public function getDbUrl()
    {

        return $this->url;
    }

    /**
     * Get the [bpm] column value.
     *
     * @return int
     */
    public function getDbBpm()
    {

        return $this->bpm;
    }

    /**
     * Get the [rating] column value.
     *
     * @return string
     */
    public function getDbRating()
    {

        return $this->rating;
    }

    /**
     * Get the [encoded_by] column value.
     *
     * @return string
     */
    public function getDbEncodedBy()
    {

        return $this->encoded_by;
    }

    /**
     * Get the [disc_number] column value.
     *
     * @return string
     */
    public function getDbDiscNumber()
    {

        return $this->disc_number;
    }

    /**
     * Get the [mood] column value.
     *
     * @return string
     */
    public function getDbMood()
    {

        return $this->mood;
    }

    /**
     * Get the [label] column value.
     *
     * @return string
     */
    public function getDbLabel()
    {

        return $this->label;
    }

    /**
     * Get the [composer] column value.
     *
     * @return string
     */
    public function getDbComposer()
    {

        return $this->composer;
    }

    /**
     * Get the [encoder] column value.
     *
     * @return string
     */
    public function getDbEncoder()
    {

        return $this->encoder;
    }

    /**
     * Get the [checksum] column value.
     *
     * @return string
     */
    public function getDbChecksum()
    {

        return $this->checksum;
    }

    /**
     * Get the [lyrics] column value.
     *
     * @return string
     */
    public function getDbLyrics()
    {

        return $this->lyrics;
    }

    /**
     * Get the [orchestra] column value.
     *
     * @return string
     */
    public function getDbOrchestra()
    {

        return $this->orchestra;
    }

    /**
     * Get the [conductor] column value.
     *
     * @return string
     */
    public function getDbConductor()
    {

        return $this->conductor;
    }

    /**
     * Get the [lyricist] column value.
     *
     * @return string
     */
    public function getDbLyricist()
    {

        return $this->lyricist;
    }

    /**
     * Get the [original_lyricist] column value.
     *
     * @return string
     */
    public function getDbOriginalLyricist()
    {

        return $this->original_lyricist;
    }

    /**
     * Get the [radio_station_name] column value.
     *
     * @return string
     */
    public function getDbRadioStationName()
    {

        return $this->radio_station_name;
    }

    /**
     * Get the [info_url] column value.
     *
     * @return string
     */
    public function getDbInfoUrl()
    {

        return $this->info_url;
    }

    /**
     * Get the [artist_url] column value.
     *
     * @return string
     */
    public function getDbArtistUrl()
    {

        return $this->artist_url;
    }

    /**
     * Get the [audio_source_url] column value.
     *
     * @return string
     */
    public function getDbAudioSourceUrl()
    {

        return $this->audio_source_url;
    }

    /**
     * Get the [radio_station_url] column value.
     *
     * @return string
     */
    public function getDbRadioStationUrl()
    {

        return $this->radio_station_url;
    }

    /**
     * Get the [buy_this_url] column value.
     *
     * @return string
     */
    public function getDbBuyThisUrl()
    {

        return $this->buy_this_url;
    }

    /**
     * Get the [isrc_number] column value.
     *
     * @return string
     */
    public function getDbIsrcNumber()
    {

        return $this->isrc_number;
    }

    /**
     * Get the [catalog_number] column value.
     *
     * @return string
     */
    public function getDbCatalogNumber()
    {

        return $this->catalog_number;
    }

    /**
     * Get the [original_artist] column value.
     *
     * @return string
     */
    public function getDbOriginalArtist()
    {

        return $this->original_artist;
    }

    /**
     * Get the [copyright] column value.
     *
     * @return string
     */
    public function getDbCopyright()
    {

        return $this->copyright;
    }

    /**
     * Get the [report_datetime] column value.
     *
     * @return string
     */
    public function getDbReportDatetime()
    {

        return $this->report_datetime;
    }

    /**
     * Get the [report_location] column value.
     *
     * @return string
     */
    public function getDbReportLocation()
    {

        return $this->report_location;
    }

    /**
     * Get the [report_organization] column value.
     *
     * @return string
     */
    public function getDbReportOrganization()
    {

        return $this->report_organization;
    }

    /**
     * Get the [subject] column value.
     *
     * @return string
     */
    public function getDbSubject()
    {

        return $this->subject;
    }

    /**
     * Get the [contributor] column value.
     *
     * @return string
     */
    public function getDbContributor()
    {

        return $this->contributor;
    }

    /**
     * Get the [language] column value.
     *
     * @return string
     */
    public function getDbLanguage()
    {

        return $this->language;
    }

    /**
     * Get the [file_exists] column value.
     *
     * @return boolean
     */
    public function getDbFileExists()
    {

        return $this->file_exists;
    }

    /**
     * Get the [replay_gain] column value.
     *
     * @return string
     */
    public function getDbReplayGain()
    {

        return $this->replay_gain;
    }

    /**
     * Get the [owner_id] column value.
     *
     * @return int
     */
    public function getDbOwnerId()
    {

        return $this->owner_id;
    }

    /**
     * Get the [cuein] column value.
     *
     * @return string
     */
    public function getDbCuein()
    {

        return $this->cuein;
    }

    /**
     * Get the [cueout] column value.
     *
     * @return string
     */
    public function getDbCueout()
    {

        return $this->cueout;
    }

    /**
     * Get the [silan_check] column value.
     *
     * @return boolean
     */
    public function getDbSilanCheck()
    {

        return $this->silan_check;
    }

    /**
     * Get the [hidden] column value.
     *
     * @return boolean
     */
    public function getDbHidden()
    {

        return $this->hidden;
    }

    /**
     * Get the [is_scheduled] column value.
     *
     * @return boolean
     */
    public function getDbIsScheduled()
    {

        return $this->is_scheduled;
    }

    /**
     * Get the [is_playlist] column value.
     *
     * @return boolean
     */
    public function getDbIsPlaylist()
    {

        return $this->is_playlist;
    }

    /**
     * Get the [filesize] column value.
     *
     * @return int
     */
    public function getDbFilesize()
    {

        return $this->filesize;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDbDescription()
    {

        return $this->description;
    }

    /**
     * Get the [artwork] column value.
     *
     * @return string
     */
    public function getDbArtwork()
    {

        return $this->artwork;
    }

    /**
     * Get the [track_type_id] column value.
     *
     * @return int
     */
    public function getDbTrackTypeId()
    {

        return $this->track_type_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcFilesPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = CcFilesPeer::NAME;
        }


        return $this;
    } // setDbName()

    /**
     * Set the value of [mime] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbMime($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->mime !== $v) {
            $this->mime = $v;
            $this->modifiedColumns[] = CcFilesPeer::MIME;
        }


        return $this;
    } // setDbMime()

    /**
     * Set the value of [ftype] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbFtype($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->ftype !== $v) {
            $this->ftype = $v;
            $this->modifiedColumns[] = CcFilesPeer::FTYPE;
        }


        return $this;
    } // setDbFtype()

    /**
     * Set the value of [filepath] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbFilepath($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->filepath !== $v) {
            $this->filepath = $v;
            $this->modifiedColumns[] = CcFilesPeer::FILEPATH;
        }


        return $this;
    } // setDbFilepath()

    /**
     * Set the value of [import_status] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbImportStatus($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->import_status !== $v) {
            $this->import_status = $v;
            $this->modifiedColumns[] = CcFilesPeer::IMPORT_STATUS;
        }


        return $this;
    } // setDbImportStatus()

    /**
     * Set the value of [currentlyaccessing] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbCurrentlyaccessing($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->currentlyaccessing !== $v) {
            $this->currentlyaccessing = $v;
            $this->modifiedColumns[] = CcFilesPeer::CURRENTLYACCESSING;
        }


        return $this;
    } // setDbCurrentlyaccessing()

    /**
     * Set the value of [editedby] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbEditedby($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->editedby !== $v) {
            $this->editedby = $v;
            $this->modifiedColumns[] = CcFilesPeer::EDITEDBY;
        }

        if ($this->aCcSubjsRelatedByDbEditedby !== null && $this->aCcSubjsRelatedByDbEditedby->getDbId() !== $v) {
            $this->aCcSubjsRelatedByDbEditedby = null;
        }


        return $this;
    } // setDbEditedby()

    /**
     * Sets the value of [mtime] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbMtime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->mtime !== null || $dt !== null) {
            $currentDateAsString = ($this->mtime !== null && $tmpDt = new DateTime($this->mtime)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->mtime = $newDateAsString;
                $this->modifiedColumns[] = CcFilesPeer::MTIME;
            }
        } // if either are not null


        return $this;
    } // setDbMtime()

    /**
     * Sets the value of [utime] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbUtime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->utime !== null || $dt !== null) {
            $currentDateAsString = ($this->utime !== null && $tmpDt = new DateTime($this->utime)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->utime = $newDateAsString;
                $this->modifiedColumns[] = CcFilesPeer::UTIME;
            }
        } // if either are not null


        return $this;
    } // setDbUtime()

    /**
     * Sets the value of [lptime] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbLPtime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->lptime !== null || $dt !== null) {
            $currentDateAsString = ($this->lptime !== null && $tmpDt = new DateTime($this->lptime)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->lptime = $newDateAsString;
                $this->modifiedColumns[] = CcFilesPeer::LPTIME;
            }
        } // if either are not null


        return $this;
    } // setDbLPtime()

    /**
     * Set the value of [md5] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbMd5($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->md5 !== $v) {
            $this->md5 = $v;
            $this->modifiedColumns[] = CcFilesPeer::MD5;
        }


        return $this;
    } // setDbMd5()

    /**
     * Set the value of [track_title] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbTrackTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->track_title !== $v) {
            $this->track_title = $v;
            $this->modifiedColumns[] = CcFilesPeer::TRACK_TITLE;
        }


        return $this;
    } // setDbTrackTitle()

    /**
     * Set the value of [artist_name] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbArtistName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->artist_name !== $v) {
            $this->artist_name = $v;
            $this->modifiedColumns[] = CcFilesPeer::ARTIST_NAME;
        }


        return $this;
    } // setDbArtistName()

    /**
     * Set the value of [bit_rate] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbBitRate($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->bit_rate !== $v) {
            $this->bit_rate = $v;
            $this->modifiedColumns[] = CcFilesPeer::BIT_RATE;
        }


        return $this;
    } // setDbBitRate()

    /**
     * Set the value of [sample_rate] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbSampleRate($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sample_rate !== $v) {
            $this->sample_rate = $v;
            $this->modifiedColumns[] = CcFilesPeer::SAMPLE_RATE;
        }


        return $this;
    } // setDbSampleRate()

    /**
     * Set the value of [format] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbFormat($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->format !== $v) {
            $this->format = $v;
            $this->modifiedColumns[] = CcFilesPeer::FORMAT;
        }


        return $this;
    } // setDbFormat()

    /**
     * Set the value of [length] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbLength($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->length !== $v) {
            $this->length = $v;
            $this->modifiedColumns[] = CcFilesPeer::LENGTH;
        }


        return $this;
    } // setDbLength()

    /**
     * Set the value of [album_title] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbAlbumTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->album_title !== $v) {
            $this->album_title = $v;
            $this->modifiedColumns[] = CcFilesPeer::ALBUM_TITLE;
        }


        return $this;
    } // setDbAlbumTitle()

    /**
     * Set the value of [genre] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbGenre($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->genre !== $v) {
            $this->genre = $v;
            $this->modifiedColumns[] = CcFilesPeer::GENRE;
        }


        return $this;
    } // setDbGenre()

    /**
     * Set the value of [comments] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbComments($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->comments !== $v) {
            $this->comments = $v;
            $this->modifiedColumns[] = CcFilesPeer::COMMENTS;
        }


        return $this;
    } // setDbComments()

    /**
     * Set the value of [year] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbYear($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->year !== $v) {
            $this->year = $v;
            $this->modifiedColumns[] = CcFilesPeer::YEAR;
        }


        return $this;
    } // setDbYear()

    /**
     * Set the value of [track_number] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbTrackNumber($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->track_number !== $v) {
            $this->track_number = $v;
            $this->modifiedColumns[] = CcFilesPeer::TRACK_NUMBER;
        }


        return $this;
    } // setDbTrackNumber()

    /**
     * Set the value of [channels] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbChannels($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->channels !== $v) {
            $this->channels = $v;
            $this->modifiedColumns[] = CcFilesPeer::CHANNELS;
        }


        return $this;
    } // setDbChannels()

    /**
     * Set the value of [url] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[] = CcFilesPeer::URL;
        }


        return $this;
    } // setDbUrl()

    /**
     * Set the value of [bpm] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbBpm($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->bpm !== $v) {
            $this->bpm = $v;
            $this->modifiedColumns[] = CcFilesPeer::BPM;
        }


        return $this;
    } // setDbBpm()

    /**
     * Set the value of [rating] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbRating($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->rating !== $v) {
            $this->rating = $v;
            $this->modifiedColumns[] = CcFilesPeer::RATING;
        }


        return $this;
    } // setDbRating()

    /**
     * Set the value of [encoded_by] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbEncodedBy($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->encoded_by !== $v) {
            $this->encoded_by = $v;
            $this->modifiedColumns[] = CcFilesPeer::ENCODED_BY;
        }


        return $this;
    } // setDbEncodedBy()

    /**
     * Set the value of [disc_number] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbDiscNumber($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->disc_number !== $v) {
            $this->disc_number = $v;
            $this->modifiedColumns[] = CcFilesPeer::DISC_NUMBER;
        }


        return $this;
    } // setDbDiscNumber()

    /**
     * Set the value of [mood] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbMood($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->mood !== $v) {
            $this->mood = $v;
            $this->modifiedColumns[] = CcFilesPeer::MOOD;
        }


        return $this;
    } // setDbMood()

    /**
     * Set the value of [label] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbLabel($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->label !== $v) {
            $this->label = $v;
            $this->modifiedColumns[] = CcFilesPeer::LABEL;
        }


        return $this;
    } // setDbLabel()

    /**
     * Set the value of [composer] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbComposer($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->composer !== $v) {
            $this->composer = $v;
            $this->modifiedColumns[] = CcFilesPeer::COMPOSER;
        }


        return $this;
    } // setDbComposer()

    /**
     * Set the value of [encoder] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbEncoder($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->encoder !== $v) {
            $this->encoder = $v;
            $this->modifiedColumns[] = CcFilesPeer::ENCODER;
        }


        return $this;
    } // setDbEncoder()

    /**
     * Set the value of [checksum] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbChecksum($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->checksum !== $v) {
            $this->checksum = $v;
            $this->modifiedColumns[] = CcFilesPeer::CHECKSUM;
        }


        return $this;
    } // setDbChecksum()

    /**
     * Set the value of [lyrics] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbLyrics($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->lyrics !== $v) {
            $this->lyrics = $v;
            $this->modifiedColumns[] = CcFilesPeer::LYRICS;
        }


        return $this;
    } // setDbLyrics()

    /**
     * Set the value of [orchestra] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbOrchestra($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->orchestra !== $v) {
            $this->orchestra = $v;
            $this->modifiedColumns[] = CcFilesPeer::ORCHESTRA;
        }


        return $this;
    } // setDbOrchestra()

    /**
     * Set the value of [conductor] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbConductor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->conductor !== $v) {
            $this->conductor = $v;
            $this->modifiedColumns[] = CcFilesPeer::CONDUCTOR;
        }


        return $this;
    } // setDbConductor()

    /**
     * Set the value of [lyricist] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbLyricist($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->lyricist !== $v) {
            $this->lyricist = $v;
            $this->modifiedColumns[] = CcFilesPeer::LYRICIST;
        }


        return $this;
    } // setDbLyricist()

    /**
     * Set the value of [original_lyricist] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbOriginalLyricist($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->original_lyricist !== $v) {
            $this->original_lyricist = $v;
            $this->modifiedColumns[] = CcFilesPeer::ORIGINAL_LYRICIST;
        }


        return $this;
    } // setDbOriginalLyricist()

    /**
     * Set the value of [radio_station_name] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbRadioStationName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->radio_station_name !== $v) {
            $this->radio_station_name = $v;
            $this->modifiedColumns[] = CcFilesPeer::RADIO_STATION_NAME;
        }


        return $this;
    } // setDbRadioStationName()

    /**
     * Set the value of [info_url] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbInfoUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->info_url !== $v) {
            $this->info_url = $v;
            $this->modifiedColumns[] = CcFilesPeer::INFO_URL;
        }


        return $this;
    } // setDbInfoUrl()

    /**
     * Set the value of [artist_url] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbArtistUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->artist_url !== $v) {
            $this->artist_url = $v;
            $this->modifiedColumns[] = CcFilesPeer::ARTIST_URL;
        }


        return $this;
    } // setDbArtistUrl()

    /**
     * Set the value of [audio_source_url] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbAudioSourceUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->audio_source_url !== $v) {
            $this->audio_source_url = $v;
            $this->modifiedColumns[] = CcFilesPeer::AUDIO_SOURCE_URL;
        }


        return $this;
    } // setDbAudioSourceUrl()

    /**
     * Set the value of [radio_station_url] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbRadioStationUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->radio_station_url !== $v) {
            $this->radio_station_url = $v;
            $this->modifiedColumns[] = CcFilesPeer::RADIO_STATION_URL;
        }


        return $this;
    } // setDbRadioStationUrl()

    /**
     * Set the value of [buy_this_url] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbBuyThisUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->buy_this_url !== $v) {
            $this->buy_this_url = $v;
            $this->modifiedColumns[] = CcFilesPeer::BUY_THIS_URL;
        }


        return $this;
    } // setDbBuyThisUrl()

    /**
     * Set the value of [isrc_number] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbIsrcNumber($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->isrc_number !== $v) {
            $this->isrc_number = $v;
            $this->modifiedColumns[] = CcFilesPeer::ISRC_NUMBER;
        }


        return $this;
    } // setDbIsrcNumber()

    /**
     * Set the value of [catalog_number] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbCatalogNumber($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->catalog_number !== $v) {
            $this->catalog_number = $v;
            $this->modifiedColumns[] = CcFilesPeer::CATALOG_NUMBER;
        }


        return $this;
    } // setDbCatalogNumber()

    /**
     * Set the value of [original_artist] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbOriginalArtist($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->original_artist !== $v) {
            $this->original_artist = $v;
            $this->modifiedColumns[] = CcFilesPeer::ORIGINAL_ARTIST;
        }


        return $this;
    } // setDbOriginalArtist()

    /**
     * Set the value of [copyright] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbCopyright($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->copyright !== $v) {
            $this->copyright = $v;
            $this->modifiedColumns[] = CcFilesPeer::COPYRIGHT;
        }


        return $this;
    } // setDbCopyright()

    /**
     * Set the value of [report_datetime] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbReportDatetime($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->report_datetime !== $v) {
            $this->report_datetime = $v;
            $this->modifiedColumns[] = CcFilesPeer::REPORT_DATETIME;
        }


        return $this;
    } // setDbReportDatetime()

    /**
     * Set the value of [report_location] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbReportLocation($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->report_location !== $v) {
            $this->report_location = $v;
            $this->modifiedColumns[] = CcFilesPeer::REPORT_LOCATION;
        }


        return $this;
    } // setDbReportLocation()

    /**
     * Set the value of [report_organization] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbReportOrganization($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->report_organization !== $v) {
            $this->report_organization = $v;
            $this->modifiedColumns[] = CcFilesPeer::REPORT_ORGANIZATION;
        }


        return $this;
    } // setDbReportOrganization()

    /**
     * Set the value of [subject] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbSubject($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->subject !== $v) {
            $this->subject = $v;
            $this->modifiedColumns[] = CcFilesPeer::SUBJECT;
        }


        return $this;
    } // setDbSubject()

    /**
     * Set the value of [contributor] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbContributor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->contributor !== $v) {
            $this->contributor = $v;
            $this->modifiedColumns[] = CcFilesPeer::CONTRIBUTOR;
        }


        return $this;
    } // setDbContributor()

    /**
     * Set the value of [language] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbLanguage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->language !== $v) {
            $this->language = $v;
            $this->modifiedColumns[] = CcFilesPeer::LANGUAGE;
        }


        return $this;
    } // setDbLanguage()

    /**
     * Sets the value of the [file_exists] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbFileExists($v)
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
            $this->modifiedColumns[] = CcFilesPeer::FILE_EXISTS;
        }


        return $this;
    } // setDbFileExists()

    /**
     * Set the value of [replay_gain] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbReplayGain($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->replay_gain !== $v) {
            $this->replay_gain = $v;
            $this->modifiedColumns[] = CcFilesPeer::REPLAY_GAIN;
        }


        return $this;
    } // setDbReplayGain()

    /**
     * Set the value of [owner_id] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbOwnerId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->owner_id !== $v) {
            $this->owner_id = $v;
            $this->modifiedColumns[] = CcFilesPeer::OWNER_ID;
        }

        if ($this->aFkOwner !== null && $this->aFkOwner->getDbId() !== $v) {
            $this->aFkOwner = null;
        }


        return $this;
    } // setDbOwnerId()

    /**
     * Set the value of [cuein] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbCuein($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cuein !== $v) {
            $this->cuein = $v;
            $this->modifiedColumns[] = CcFilesPeer::CUEIN;
        }


        return $this;
    } // setDbCuein()

    /**
     * Set the value of [cueout] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbCueout($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cueout !== $v) {
            $this->cueout = $v;
            $this->modifiedColumns[] = CcFilesPeer::CUEOUT;
        }


        return $this;
    } // setDbCueout()

    /**
     * Sets the value of the [silan_check] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbSilanCheck($v)
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
            $this->modifiedColumns[] = CcFilesPeer::SILAN_CHECK;
        }


        return $this;
    } // setDbSilanCheck()

    /**
     * Sets the value of the [hidden] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbHidden($v)
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
            $this->modifiedColumns[] = CcFilesPeer::HIDDEN;
        }


        return $this;
    } // setDbHidden()

    /**
     * Sets the value of the [is_scheduled] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbIsScheduled($v)
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
            $this->modifiedColumns[] = CcFilesPeer::IS_SCHEDULED;
        }


        return $this;
    } // setDbIsScheduled()

    /**
     * Sets the value of the [is_playlist] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbIsPlaylist($v)
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
            $this->modifiedColumns[] = CcFilesPeer::IS_PLAYLIST;
        }


        return $this;
    } // setDbIsPlaylist()

    /**
     * Set the value of [filesize] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbFilesize($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->filesize !== $v) {
            $this->filesize = $v;
            $this->modifiedColumns[] = CcFilesPeer::FILESIZE;
        }


        return $this;
    } // setDbFilesize()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CcFilesPeer::DESCRIPTION;
        }


        return $this;
    } // setDbDescription()

    /**
     * Set the value of [artwork] column.
     *
     * @param  string $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbArtwork($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->artwork !== $v) {
            $this->artwork = $v;
            $this->modifiedColumns[] = CcFilesPeer::ARTWORK;
        }


        return $this;
    } // setDbArtwork()

    /**
     * Set the value of [track_type_id] column.
     *
     * @param  int $v new value
     * @return CcFiles The current object (for fluent API support)
     */
    public function setDbTrackTypeId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->track_type_id !== $v) {
            $this->track_type_id = $v;
            $this->modifiedColumns[] = CcFilesPeer::TRACK_TYPE_ID;
        }

        if ($this->aCcTracktypes !== null && $this->aCcTracktypes->getDbId() !== $v) {
            $this->aCcTracktypes = null;
        }


        return $this;
    } // setDbTrackTypeId()

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
            if ($this->name !== '') {
                return false;
            }

            if ($this->mime !== '') {
                return false;
            }

            if ($this->ftype !== '') {
                return false;
            }

            if ($this->filepath !== '') {
                return false;
            }

            if ($this->import_status !== 1) {
                return false;
            }

            if ($this->currentlyaccessing !== 0) {
                return false;
            }

            if ($this->length !== '00:00:00') {
                return false;
            }

            if ($this->file_exists !== true) {
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

            if ($this->hidden !== false) {
                return false;
            }

            if ($this->is_scheduled !== false) {
                return false;
            }

            if ($this->is_playlist !== false) {
                return false;
            }

            if ($this->filesize !== 0) {
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

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->mime = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->ftype = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->filepath = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->import_status = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->currentlyaccessing = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->editedby = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->mtime = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->utime = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->lptime = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->md5 = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->track_title = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->artist_name = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->bit_rate = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
            $this->sample_rate = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
            $this->format = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
            $this->length = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
            $this->album_title = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
            $this->genre = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
            $this->comments = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
            $this->year = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
            $this->track_number = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
            $this->channels = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
            $this->url = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
            $this->bpm = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
            $this->rating = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
            $this->encoded_by = ($row[$startcol + 27] !== null) ? (string) $row[$startcol + 27] : null;
            $this->disc_number = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
            $this->mood = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
            $this->label = ($row[$startcol + 30] !== null) ? (string) $row[$startcol + 30] : null;
            $this->composer = ($row[$startcol + 31] !== null) ? (string) $row[$startcol + 31] : null;
            $this->encoder = ($row[$startcol + 32] !== null) ? (string) $row[$startcol + 32] : null;
            $this->checksum = ($row[$startcol + 33] !== null) ? (string) $row[$startcol + 33] : null;
            $this->lyrics = ($row[$startcol + 34] !== null) ? (string) $row[$startcol + 34] : null;
            $this->orchestra = ($row[$startcol + 35] !== null) ? (string) $row[$startcol + 35] : null;
            $this->conductor = ($row[$startcol + 36] !== null) ? (string) $row[$startcol + 36] : null;
            $this->lyricist = ($row[$startcol + 37] !== null) ? (string) $row[$startcol + 37] : null;
            $this->original_lyricist = ($row[$startcol + 38] !== null) ? (string) $row[$startcol + 38] : null;
            $this->radio_station_name = ($row[$startcol + 39] !== null) ? (string) $row[$startcol + 39] : null;
            $this->info_url = ($row[$startcol + 40] !== null) ? (string) $row[$startcol + 40] : null;
            $this->artist_url = ($row[$startcol + 41] !== null) ? (string) $row[$startcol + 41] : null;
            $this->audio_source_url = ($row[$startcol + 42] !== null) ? (string) $row[$startcol + 42] : null;
            $this->radio_station_url = ($row[$startcol + 43] !== null) ? (string) $row[$startcol + 43] : null;
            $this->buy_this_url = ($row[$startcol + 44] !== null) ? (string) $row[$startcol + 44] : null;
            $this->isrc_number = ($row[$startcol + 45] !== null) ? (string) $row[$startcol + 45] : null;
            $this->catalog_number = ($row[$startcol + 46] !== null) ? (string) $row[$startcol + 46] : null;
            $this->original_artist = ($row[$startcol + 47] !== null) ? (string) $row[$startcol + 47] : null;
            $this->copyright = ($row[$startcol + 48] !== null) ? (string) $row[$startcol + 48] : null;
            $this->report_datetime = ($row[$startcol + 49] !== null) ? (string) $row[$startcol + 49] : null;
            $this->report_location = ($row[$startcol + 50] !== null) ? (string) $row[$startcol + 50] : null;
            $this->report_organization = ($row[$startcol + 51] !== null) ? (string) $row[$startcol + 51] : null;
            $this->subject = ($row[$startcol + 52] !== null) ? (string) $row[$startcol + 52] : null;
            $this->contributor = ($row[$startcol + 53] !== null) ? (string) $row[$startcol + 53] : null;
            $this->language = ($row[$startcol + 54] !== null) ? (string) $row[$startcol + 54] : null;
            $this->file_exists = ($row[$startcol + 55] !== null) ? (boolean) $row[$startcol + 55] : null;
            $this->replay_gain = ($row[$startcol + 56] !== null) ? (string) $row[$startcol + 56] : null;
            $this->owner_id = ($row[$startcol + 57] !== null) ? (int) $row[$startcol + 57] : null;
            $this->cuein = ($row[$startcol + 58] !== null) ? (string) $row[$startcol + 58] : null;
            $this->cueout = ($row[$startcol + 59] !== null) ? (string) $row[$startcol + 59] : null;
            $this->silan_check = ($row[$startcol + 60] !== null) ? (boolean) $row[$startcol + 60] : null;
            $this->hidden = ($row[$startcol + 61] !== null) ? (boolean) $row[$startcol + 61] : null;
            $this->is_scheduled = ($row[$startcol + 62] !== null) ? (boolean) $row[$startcol + 62] : null;
            $this->is_playlist = ($row[$startcol + 63] !== null) ? (boolean) $row[$startcol + 63] : null;
            $this->filesize = ($row[$startcol + 64] !== null) ? (int) $row[$startcol + 64] : null;
            $this->description = ($row[$startcol + 65] !== null) ? (string) $row[$startcol + 65] : null;
            $this->artwork = ($row[$startcol + 66] !== null) ? (string) $row[$startcol + 66] : null;
            $this->track_type_id = ($row[$startcol + 67] !== null) ? (int) $row[$startcol + 67] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 68; // 68 = CcFilesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcFiles object", $e);
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

        if ($this->aCcSubjsRelatedByDbEditedby !== null && $this->editedby !== $this->aCcSubjsRelatedByDbEditedby->getDbId()) {
            $this->aCcSubjsRelatedByDbEditedby = null;
        }
        if ($this->aFkOwner !== null && $this->owner_id !== $this->aFkOwner->getDbId()) {
            $this->aFkOwner = null;
        }
        if ($this->aCcTracktypes !== null && $this->track_type_id !== $this->aCcTracktypes->getDbId()) {
            $this->aCcTracktypes = null;
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
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcFilesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aFkOwner = null;
            $this->aCcSubjsRelatedByDbEditedby = null;
            $this->aCcTracktypes = null;
            $this->collCcShowInstancess = null;

            $this->collCcPlaylistcontentss = null;

            $this->collCcBlockcontentss = null;

            $this->collCcSchedules = null;

            $this->collCcPlayoutHistorys = null;

            $this->collThirdPartyTrackReferencess = null;

            $this->collPodcastEpisodess = null;

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
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcFilesQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
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
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CcFilesPeer::addInstanceToPool($this);
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

            if ($this->aFkOwner !== null) {
                if ($this->aFkOwner->isModified() || $this->aFkOwner->isNew()) {
                    $affectedRows += $this->aFkOwner->save($con);
                }
                $this->setFkOwner($this->aFkOwner);
            }

            if ($this->aCcSubjsRelatedByDbEditedby !== null) {
                if ($this->aCcSubjsRelatedByDbEditedby->isModified() || $this->aCcSubjsRelatedByDbEditedby->isNew()) {
                    $affectedRows += $this->aCcSubjsRelatedByDbEditedby->save($con);
                }
                $this->setCcSubjsRelatedByDbEditedby($this->aCcSubjsRelatedByDbEditedby);
            }

            if ($this->aCcTracktypes !== null) {
                if ($this->aCcTracktypes->isModified() || $this->aCcTracktypes->isNew()) {
                    $affectedRows += $this->aCcTracktypes->save($con);
                }
                $this->setCcTracktypes($this->aCcTracktypes);
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

            if ($this->ccShowInstancessScheduledForDeletion !== null) {
                if (!$this->ccShowInstancessScheduledForDeletion->isEmpty()) {
                    CcShowInstancesQuery::create()
                        ->filterByPrimaryKeys($this->ccShowInstancessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccShowInstancessScheduledForDeletion = null;
                }
            }

            if ($this->collCcShowInstancess !== null) {
                foreach ($this->collCcShowInstancess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccPlaylistcontentssScheduledForDeletion !== null) {
                if (!$this->ccPlaylistcontentssScheduledForDeletion->isEmpty()) {
                    CcPlaylistcontentsQuery::create()
                        ->filterByPrimaryKeys($this->ccPlaylistcontentssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccPlaylistcontentssScheduledForDeletion = null;
                }
            }

            if ($this->collCcPlaylistcontentss !== null) {
                foreach ($this->collCcPlaylistcontentss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccBlockcontentssScheduledForDeletion !== null) {
                if (!$this->ccBlockcontentssScheduledForDeletion->isEmpty()) {
                    CcBlockcontentsQuery::create()
                        ->filterByPrimaryKeys($this->ccBlockcontentssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccBlockcontentssScheduledForDeletion = null;
                }
            }

            if ($this->collCcBlockcontentss !== null) {
                foreach ($this->collCcBlockcontentss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccSchedulesScheduledForDeletion !== null) {
                if (!$this->ccSchedulesScheduledForDeletion->isEmpty()) {
                    CcScheduleQuery::create()
                        ->filterByPrimaryKeys($this->ccSchedulesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccSchedulesScheduledForDeletion = null;
                }
            }

            if ($this->collCcSchedules !== null) {
                foreach ($this->collCcSchedules as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccPlayoutHistorysScheduledForDeletion !== null) {
                if (!$this->ccPlayoutHistorysScheduledForDeletion->isEmpty()) {
                    CcPlayoutHistoryQuery::create()
                        ->filterByPrimaryKeys($this->ccPlayoutHistorysScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccPlayoutHistorysScheduledForDeletion = null;
                }
            }

            if ($this->collCcPlayoutHistorys !== null) {
                foreach ($this->collCcPlayoutHistorys as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->thirdPartyTrackReferencessScheduledForDeletion !== null) {
                if (!$this->thirdPartyTrackReferencessScheduledForDeletion->isEmpty()) {
                    ThirdPartyTrackReferencesQuery::create()
                        ->filterByPrimaryKeys($this->thirdPartyTrackReferencessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->thirdPartyTrackReferencessScheduledForDeletion = null;
                }
            }

            if ($this->collThirdPartyTrackReferencess !== null) {
                foreach ($this->collThirdPartyTrackReferencess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->podcastEpisodessScheduledForDeletion !== null) {
                if (!$this->podcastEpisodessScheduledForDeletion->isEmpty()) {
                    PodcastEpisodesQuery::create()
                        ->filterByPrimaryKeys($this->podcastEpisodessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->podcastEpisodessScheduledForDeletion = null;
                }
            }

            if ($this->collPodcastEpisodess !== null) {
                foreach ($this->collPodcastEpisodess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
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

        $this->modifiedColumns[] = CcFilesPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcFilesPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_files_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcFilesPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcFilesPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '"name"';
        }
        if ($this->isColumnModified(CcFilesPeer::MIME)) {
            $modifiedColumns[':p' . $index++]  = '"mime"';
        }
        if ($this->isColumnModified(CcFilesPeer::FTYPE)) {
            $modifiedColumns[':p' . $index++]  = '"ftype"';
        }
        if ($this->isColumnModified(CcFilesPeer::FILEPATH)) {
            $modifiedColumns[':p' . $index++]  = '"filepath"';
        }
        if ($this->isColumnModified(CcFilesPeer::IMPORT_STATUS)) {
            $modifiedColumns[':p' . $index++]  = '"import_status"';
        }
        if ($this->isColumnModified(CcFilesPeer::CURRENTLYACCESSING)) {
            $modifiedColumns[':p' . $index++]  = '"currentlyaccessing"';
        }
        if ($this->isColumnModified(CcFilesPeer::EDITEDBY)) {
            $modifiedColumns[':p' . $index++]  = '"editedby"';
        }
        if ($this->isColumnModified(CcFilesPeer::MTIME)) {
            $modifiedColumns[':p' . $index++]  = '"mtime"';
        }
        if ($this->isColumnModified(CcFilesPeer::UTIME)) {
            $modifiedColumns[':p' . $index++]  = '"utime"';
        }
        if ($this->isColumnModified(CcFilesPeer::LPTIME)) {
            $modifiedColumns[':p' . $index++]  = '"lptime"';
        }
        if ($this->isColumnModified(CcFilesPeer::MD5)) {
            $modifiedColumns[':p' . $index++]  = '"md5"';
        }
        if ($this->isColumnModified(CcFilesPeer::TRACK_TITLE)) {
            $modifiedColumns[':p' . $index++]  = '"track_title"';
        }
        if ($this->isColumnModified(CcFilesPeer::ARTIST_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"artist_name"';
        }
        if ($this->isColumnModified(CcFilesPeer::BIT_RATE)) {
            $modifiedColumns[':p' . $index++]  = '"bit_rate"';
        }
        if ($this->isColumnModified(CcFilesPeer::SAMPLE_RATE)) {
            $modifiedColumns[':p' . $index++]  = '"sample_rate"';
        }
        if ($this->isColumnModified(CcFilesPeer::FORMAT)) {
            $modifiedColumns[':p' . $index++]  = '"format"';
        }
        if ($this->isColumnModified(CcFilesPeer::LENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"length"';
        }
        if ($this->isColumnModified(CcFilesPeer::ALBUM_TITLE)) {
            $modifiedColumns[':p' . $index++]  = '"album_title"';
        }
        if ($this->isColumnModified(CcFilesPeer::GENRE)) {
            $modifiedColumns[':p' . $index++]  = '"genre"';
        }
        if ($this->isColumnModified(CcFilesPeer::COMMENTS)) {
            $modifiedColumns[':p' . $index++]  = '"comments"';
        }
        if ($this->isColumnModified(CcFilesPeer::YEAR)) {
            $modifiedColumns[':p' . $index++]  = '"year"';
        }
        if ($this->isColumnModified(CcFilesPeer::TRACK_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = '"track_number"';
        }
        if ($this->isColumnModified(CcFilesPeer::CHANNELS)) {
            $modifiedColumns[':p' . $index++]  = '"channels"';
        }
        if ($this->isColumnModified(CcFilesPeer::URL)) {
            $modifiedColumns[':p' . $index++]  = '"url"';
        }
        if ($this->isColumnModified(CcFilesPeer::BPM)) {
            $modifiedColumns[':p' . $index++]  = '"bpm"';
        }
        if ($this->isColumnModified(CcFilesPeer::RATING)) {
            $modifiedColumns[':p' . $index++]  = '"rating"';
        }
        if ($this->isColumnModified(CcFilesPeer::ENCODED_BY)) {
            $modifiedColumns[':p' . $index++]  = '"encoded_by"';
        }
        if ($this->isColumnModified(CcFilesPeer::DISC_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = '"disc_number"';
        }
        if ($this->isColumnModified(CcFilesPeer::MOOD)) {
            $modifiedColumns[':p' . $index++]  = '"mood"';
        }
        if ($this->isColumnModified(CcFilesPeer::LABEL)) {
            $modifiedColumns[':p' . $index++]  = '"label"';
        }
        if ($this->isColumnModified(CcFilesPeer::COMPOSER)) {
            $modifiedColumns[':p' . $index++]  = '"composer"';
        }
        if ($this->isColumnModified(CcFilesPeer::ENCODER)) {
            $modifiedColumns[':p' . $index++]  = '"encoder"';
        }
        if ($this->isColumnModified(CcFilesPeer::CHECKSUM)) {
            $modifiedColumns[':p' . $index++]  = '"checksum"';
        }
        if ($this->isColumnModified(CcFilesPeer::LYRICS)) {
            $modifiedColumns[':p' . $index++]  = '"lyrics"';
        }
        if ($this->isColumnModified(CcFilesPeer::ORCHESTRA)) {
            $modifiedColumns[':p' . $index++]  = '"orchestra"';
        }
        if ($this->isColumnModified(CcFilesPeer::CONDUCTOR)) {
            $modifiedColumns[':p' . $index++]  = '"conductor"';
        }
        if ($this->isColumnModified(CcFilesPeer::LYRICIST)) {
            $modifiedColumns[':p' . $index++]  = '"lyricist"';
        }
        if ($this->isColumnModified(CcFilesPeer::ORIGINAL_LYRICIST)) {
            $modifiedColumns[':p' . $index++]  = '"original_lyricist"';
        }
        if ($this->isColumnModified(CcFilesPeer::RADIO_STATION_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"radio_station_name"';
        }
        if ($this->isColumnModified(CcFilesPeer::INFO_URL)) {
            $modifiedColumns[':p' . $index++]  = '"info_url"';
        }
        if ($this->isColumnModified(CcFilesPeer::ARTIST_URL)) {
            $modifiedColumns[':p' . $index++]  = '"artist_url"';
        }
        if ($this->isColumnModified(CcFilesPeer::AUDIO_SOURCE_URL)) {
            $modifiedColumns[':p' . $index++]  = '"audio_source_url"';
        }
        if ($this->isColumnModified(CcFilesPeer::RADIO_STATION_URL)) {
            $modifiedColumns[':p' . $index++]  = '"radio_station_url"';
        }
        if ($this->isColumnModified(CcFilesPeer::BUY_THIS_URL)) {
            $modifiedColumns[':p' . $index++]  = '"buy_this_url"';
        }
        if ($this->isColumnModified(CcFilesPeer::ISRC_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = '"isrc_number"';
        }
        if ($this->isColumnModified(CcFilesPeer::CATALOG_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = '"catalog_number"';
        }
        if ($this->isColumnModified(CcFilesPeer::ORIGINAL_ARTIST)) {
            $modifiedColumns[':p' . $index++]  = '"original_artist"';
        }
        if ($this->isColumnModified(CcFilesPeer::COPYRIGHT)) {
            $modifiedColumns[':p' . $index++]  = '"copyright"';
        }
        if ($this->isColumnModified(CcFilesPeer::REPORT_DATETIME)) {
            $modifiedColumns[':p' . $index++]  = '"report_datetime"';
        }
        if ($this->isColumnModified(CcFilesPeer::REPORT_LOCATION)) {
            $modifiedColumns[':p' . $index++]  = '"report_location"';
        }
        if ($this->isColumnModified(CcFilesPeer::REPORT_ORGANIZATION)) {
            $modifiedColumns[':p' . $index++]  = '"report_organization"';
        }
        if ($this->isColumnModified(CcFilesPeer::SUBJECT)) {
            $modifiedColumns[':p' . $index++]  = '"subject"';
        }
        if ($this->isColumnModified(CcFilesPeer::CONTRIBUTOR)) {
            $modifiedColumns[':p' . $index++]  = '"contributor"';
        }
        if ($this->isColumnModified(CcFilesPeer::LANGUAGE)) {
            $modifiedColumns[':p' . $index++]  = '"language"';
        }
        if ($this->isColumnModified(CcFilesPeer::FILE_EXISTS)) {
            $modifiedColumns[':p' . $index++]  = '"file_exists"';
        }
        if ($this->isColumnModified(CcFilesPeer::REPLAY_GAIN)) {
            $modifiedColumns[':p' . $index++]  = '"replay_gain"';
        }
        if ($this->isColumnModified(CcFilesPeer::OWNER_ID)) {
            $modifiedColumns[':p' . $index++]  = '"owner_id"';
        }
        if ($this->isColumnModified(CcFilesPeer::CUEIN)) {
            $modifiedColumns[':p' . $index++]  = '"cuein"';
        }
        if ($this->isColumnModified(CcFilesPeer::CUEOUT)) {
            $modifiedColumns[':p' . $index++]  = '"cueout"';
        }
        if ($this->isColumnModified(CcFilesPeer::SILAN_CHECK)) {
            $modifiedColumns[':p' . $index++]  = '"silan_check"';
        }
        if ($this->isColumnModified(CcFilesPeer::HIDDEN)) {
            $modifiedColumns[':p' . $index++]  = '"hidden"';
        }
        if ($this->isColumnModified(CcFilesPeer::IS_SCHEDULED)) {
            $modifiedColumns[':p' . $index++]  = '"is_scheduled"';
        }
        if ($this->isColumnModified(CcFilesPeer::IS_PLAYLIST)) {
            $modifiedColumns[':p' . $index++]  = '"is_playlist"';
        }
        if ($this->isColumnModified(CcFilesPeer::FILESIZE)) {
            $modifiedColumns[':p' . $index++]  = '"filesize"';
        }
        if ($this->isColumnModified(CcFilesPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(CcFilesPeer::ARTWORK)) {
            $modifiedColumns[':p' . $index++]  = '"artwork"';
        }
        if ($this->isColumnModified(CcFilesPeer::TRACK_TYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"track_type_id"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_files" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"id"':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '"name"':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '"mime"':
                        $stmt->bindValue($identifier, $this->mime, PDO::PARAM_STR);
                        break;
                    case '"ftype"':
                        $stmt->bindValue($identifier, $this->ftype, PDO::PARAM_STR);
                        break;
                    case '"filepath"':
                        $stmt->bindValue($identifier, $this->filepath, PDO::PARAM_STR);
                        break;
                    case '"import_status"':
                        $stmt->bindValue($identifier, $this->import_status, PDO::PARAM_INT);
                        break;
                    case '"currentlyaccessing"':
                        $stmt->bindValue($identifier, $this->currentlyaccessing, PDO::PARAM_INT);
                        break;
                    case '"editedby"':
                        $stmt->bindValue($identifier, $this->editedby, PDO::PARAM_INT);
                        break;
                    case '"mtime"':
                        $stmt->bindValue($identifier, $this->mtime, PDO::PARAM_STR);
                        break;
                    case '"utime"':
                        $stmt->bindValue($identifier, $this->utime, PDO::PARAM_STR);
                        break;
                    case '"lptime"':
                        $stmt->bindValue($identifier, $this->lptime, PDO::PARAM_STR);
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
                    case '"format"':
                        $stmt->bindValue($identifier, $this->format, PDO::PARAM_STR);
                        break;
                    case '"length"':
                        $stmt->bindValue($identifier, $this->length, PDO::PARAM_STR);
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
                    case '"url"':
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case '"bpm"':
                        $stmt->bindValue($identifier, $this->bpm, PDO::PARAM_INT);
                        break;
                    case '"rating"':
                        $stmt->bindValue($identifier, $this->rating, PDO::PARAM_STR);
                        break;
                    case '"encoded_by"':
                        $stmt->bindValue($identifier, $this->encoded_by, PDO::PARAM_STR);
                        break;
                    case '"disc_number"':
                        $stmt->bindValue($identifier, $this->disc_number, PDO::PARAM_STR);
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
                    case '"encoder"':
                        $stmt->bindValue($identifier, $this->encoder, PDO::PARAM_STR);
                        break;
                    case '"checksum"':
                        $stmt->bindValue($identifier, $this->checksum, PDO::PARAM_STR);
                        break;
                    case '"lyrics"':
                        $stmt->bindValue($identifier, $this->lyrics, PDO::PARAM_STR);
                        break;
                    case '"orchestra"':
                        $stmt->bindValue($identifier, $this->orchestra, PDO::PARAM_STR);
                        break;
                    case '"conductor"':
                        $stmt->bindValue($identifier, $this->conductor, PDO::PARAM_STR);
                        break;
                    case '"lyricist"':
                        $stmt->bindValue($identifier, $this->lyricist, PDO::PARAM_STR);
                        break;
                    case '"original_lyricist"':
                        $stmt->bindValue($identifier, $this->original_lyricist, PDO::PARAM_STR);
                        break;
                    case '"radio_station_name"':
                        $stmt->bindValue($identifier, $this->radio_station_name, PDO::PARAM_STR);
                        break;
                    case '"info_url"':
                        $stmt->bindValue($identifier, $this->info_url, PDO::PARAM_STR);
                        break;
                    case '"artist_url"':
                        $stmt->bindValue($identifier, $this->artist_url, PDO::PARAM_STR);
                        break;
                    case '"audio_source_url"':
                        $stmt->bindValue($identifier, $this->audio_source_url, PDO::PARAM_STR);
                        break;
                    case '"radio_station_url"':
                        $stmt->bindValue($identifier, $this->radio_station_url, PDO::PARAM_STR);
                        break;
                    case '"buy_this_url"':
                        $stmt->bindValue($identifier, $this->buy_this_url, PDO::PARAM_STR);
                        break;
                    case '"isrc_number"':
                        $stmt->bindValue($identifier, $this->isrc_number, PDO::PARAM_STR);
                        break;
                    case '"catalog_number"':
                        $stmt->bindValue($identifier, $this->catalog_number, PDO::PARAM_STR);
                        break;
                    case '"original_artist"':
                        $stmt->bindValue($identifier, $this->original_artist, PDO::PARAM_STR);
                        break;
                    case '"copyright"':
                        $stmt->bindValue($identifier, $this->copyright, PDO::PARAM_STR);
                        break;
                    case '"report_datetime"':
                        $stmt->bindValue($identifier, $this->report_datetime, PDO::PARAM_STR);
                        break;
                    case '"report_location"':
                        $stmt->bindValue($identifier, $this->report_location, PDO::PARAM_STR);
                        break;
                    case '"report_organization"':
                        $stmt->bindValue($identifier, $this->report_organization, PDO::PARAM_STR);
                        break;
                    case '"subject"':
                        $stmt->bindValue($identifier, $this->subject, PDO::PARAM_STR);
                        break;
                    case '"contributor"':
                        $stmt->bindValue($identifier, $this->contributor, PDO::PARAM_STR);
                        break;
                    case '"language"':
                        $stmt->bindValue($identifier, $this->language, PDO::PARAM_STR);
                        break;
                    case '"file_exists"':
                        $stmt->bindValue($identifier, $this->file_exists, PDO::PARAM_BOOL);
                        break;
                    case '"replay_gain"':
                        $stmt->bindValue($identifier, $this->replay_gain, PDO::PARAM_INT);
                        break;
                    case '"owner_id"':
                        $stmt->bindValue($identifier, $this->owner_id, PDO::PARAM_INT);
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
                    case '"hidden"':
                        $stmt->bindValue($identifier, $this->hidden, PDO::PARAM_BOOL);
                        break;
                    case '"is_scheduled"':
                        $stmt->bindValue($identifier, $this->is_scheduled, PDO::PARAM_BOOL);
                        break;
                    case '"is_playlist"':
                        $stmt->bindValue($identifier, $this->is_playlist, PDO::PARAM_BOOL);
                        break;
                    case '"filesize"':
                        $stmt->bindValue($identifier, $this->filesize, PDO::PARAM_INT);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"artwork"':
                        $stmt->bindValue($identifier, $this->artwork, PDO::PARAM_STR);
                        break;
                    case '"track_type_id"':
                        $stmt->bindValue($identifier, $this->track_type_id, PDO::PARAM_INT);
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

            if ($this->aFkOwner !== null) {
                if (!$this->aFkOwner->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aFkOwner->getValidationFailures());
                }
            }

            if ($this->aCcSubjsRelatedByDbEditedby !== null) {
                if (!$this->aCcSubjsRelatedByDbEditedby->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcSubjsRelatedByDbEditedby->getValidationFailures());
                }
            }

            if ($this->aCcTracktypes !== null) {
                if (!$this->aCcTracktypes->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcTracktypes->getValidationFailures());
                }
            }


            if (($retval = CcFilesPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcShowInstancess !== null) {
                    foreach ($this->collCcShowInstancess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcPlaylistcontentss !== null) {
                    foreach ($this->collCcPlaylistcontentss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcBlockcontentss !== null) {
                    foreach ($this->collCcBlockcontentss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcSchedules !== null) {
                    foreach ($this->collCcSchedules as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcPlayoutHistorys !== null) {
                    foreach ($this->collCcPlayoutHistorys as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collThirdPartyTrackReferencess !== null) {
                    foreach ($this->collThirdPartyTrackReferencess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPodcastEpisodess !== null) {
                    foreach ($this->collPodcastEpisodess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
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
        $pos = CcFilesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbId();
                break;
            case 1:
                return $this->getDbName();
                break;
            case 2:
                return $this->getDbMime();
                break;
            case 3:
                return $this->getDbFtype();
                break;
            case 4:
                return $this->getDbFilepath();
                break;
            case 5:
                return $this->getDbImportStatus();
                break;
            case 6:
                return $this->getDbCurrentlyaccessing();
                break;
            case 7:
                return $this->getDbEditedby();
                break;
            case 8:
                return $this->getDbMtime();
                break;
            case 9:
                return $this->getDbUtime();
                break;
            case 10:
                return $this->getDbLPtime();
                break;
            case 11:
                return $this->getDbMd5();
                break;
            case 12:
                return $this->getDbTrackTitle();
                break;
            case 13:
                return $this->getDbArtistName();
                break;
            case 14:
                return $this->getDbBitRate();
                break;
            case 15:
                return $this->getDbSampleRate();
                break;
            case 16:
                return $this->getDbFormat();
                break;
            case 17:
                return $this->getDbLength();
                break;
            case 18:
                return $this->getDbAlbumTitle();
                break;
            case 19:
                return $this->getDbGenre();
                break;
            case 20:
                return $this->getDbComments();
                break;
            case 21:
                return $this->getDbYear();
                break;
            case 22:
                return $this->getDbTrackNumber();
                break;
            case 23:
                return $this->getDbChannels();
                break;
            case 24:
                return $this->getDbUrl();
                break;
            case 25:
                return $this->getDbBpm();
                break;
            case 26:
                return $this->getDbRating();
                break;
            case 27:
                return $this->getDbEncodedBy();
                break;
            case 28:
                return $this->getDbDiscNumber();
                break;
            case 29:
                return $this->getDbMood();
                break;
            case 30:
                return $this->getDbLabel();
                break;
            case 31:
                return $this->getDbComposer();
                break;
            case 32:
                return $this->getDbEncoder();
                break;
            case 33:
                return $this->getDbChecksum();
                break;
            case 34:
                return $this->getDbLyrics();
                break;
            case 35:
                return $this->getDbOrchestra();
                break;
            case 36:
                return $this->getDbConductor();
                break;
            case 37:
                return $this->getDbLyricist();
                break;
            case 38:
                return $this->getDbOriginalLyricist();
                break;
            case 39:
                return $this->getDbRadioStationName();
                break;
            case 40:
                return $this->getDbInfoUrl();
                break;
            case 41:
                return $this->getDbArtistUrl();
                break;
            case 42:
                return $this->getDbAudioSourceUrl();
                break;
            case 43:
                return $this->getDbRadioStationUrl();
                break;
            case 44:
                return $this->getDbBuyThisUrl();
                break;
            case 45:
                return $this->getDbIsrcNumber();
                break;
            case 46:
                return $this->getDbCatalogNumber();
                break;
            case 47:
                return $this->getDbOriginalArtist();
                break;
            case 48:
                return $this->getDbCopyright();
                break;
            case 49:
                return $this->getDbReportDatetime();
                break;
            case 50:
                return $this->getDbReportLocation();
                break;
            case 51:
                return $this->getDbReportOrganization();
                break;
            case 52:
                return $this->getDbSubject();
                break;
            case 53:
                return $this->getDbContributor();
                break;
            case 54:
                return $this->getDbLanguage();
                break;
            case 55:
                return $this->getDbFileExists();
                break;
            case 56:
                return $this->getDbReplayGain();
                break;
            case 57:
                return $this->getDbOwnerId();
                break;
            case 58:
                return $this->getDbCuein();
                break;
            case 59:
                return $this->getDbCueout();
                break;
            case 60:
                return $this->getDbSilanCheck();
                break;
            case 61:
                return $this->getDbHidden();
                break;
            case 62:
                return $this->getDbIsScheduled();
                break;
            case 63:
                return $this->getDbIsPlaylist();
                break;
            case 64:
                return $this->getDbFilesize();
                break;
            case 65:
                return $this->getDbDescription();
                break;
            case 66:
                return $this->getDbArtwork();
                break;
            case 67:
                return $this->getDbTrackTypeId();
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
        if (isset($alreadyDumpedObjects['CcFiles'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcFiles'][$this->getPrimaryKey()] = true;
        $keys = CcFilesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbName(),
            $keys[2] => $this->getDbMime(),
            $keys[3] => $this->getDbFtype(),
            $keys[4] => $this->getDbFilepath(),
            $keys[5] => $this->getDbImportStatus(),
            $keys[6] => $this->getDbCurrentlyaccessing(),
            $keys[7] => $this->getDbEditedby(),
            $keys[8] => $this->getDbMtime(),
            $keys[9] => $this->getDbUtime(),
            $keys[10] => $this->getDbLPtime(),
            $keys[11] => $this->getDbMd5(),
            $keys[12] => $this->getDbTrackTitle(),
            $keys[13] => $this->getDbArtistName(),
            $keys[14] => $this->getDbBitRate(),
            $keys[15] => $this->getDbSampleRate(),
            $keys[16] => $this->getDbFormat(),
            $keys[17] => $this->getDbLength(),
            $keys[18] => $this->getDbAlbumTitle(),
            $keys[19] => $this->getDbGenre(),
            $keys[20] => $this->getDbComments(),
            $keys[21] => $this->getDbYear(),
            $keys[22] => $this->getDbTrackNumber(),
            $keys[23] => $this->getDbChannels(),
            $keys[24] => $this->getDbUrl(),
            $keys[25] => $this->getDbBpm(),
            $keys[26] => $this->getDbRating(),
            $keys[27] => $this->getDbEncodedBy(),
            $keys[28] => $this->getDbDiscNumber(),
            $keys[29] => $this->getDbMood(),
            $keys[30] => $this->getDbLabel(),
            $keys[31] => $this->getDbComposer(),
            $keys[32] => $this->getDbEncoder(),
            $keys[33] => $this->getDbChecksum(),
            $keys[34] => $this->getDbLyrics(),
            $keys[35] => $this->getDbOrchestra(),
            $keys[36] => $this->getDbConductor(),
            $keys[37] => $this->getDbLyricist(),
            $keys[38] => $this->getDbOriginalLyricist(),
            $keys[39] => $this->getDbRadioStationName(),
            $keys[40] => $this->getDbInfoUrl(),
            $keys[41] => $this->getDbArtistUrl(),
            $keys[42] => $this->getDbAudioSourceUrl(),
            $keys[43] => $this->getDbRadioStationUrl(),
            $keys[44] => $this->getDbBuyThisUrl(),
            $keys[45] => $this->getDbIsrcNumber(),
            $keys[46] => $this->getDbCatalogNumber(),
            $keys[47] => $this->getDbOriginalArtist(),
            $keys[48] => $this->getDbCopyright(),
            $keys[49] => $this->getDbReportDatetime(),
            $keys[50] => $this->getDbReportLocation(),
            $keys[51] => $this->getDbReportOrganization(),
            $keys[52] => $this->getDbSubject(),
            $keys[53] => $this->getDbContributor(),
            $keys[54] => $this->getDbLanguage(),
            $keys[55] => $this->getDbFileExists(),
            $keys[56] => $this->getDbReplayGain(),
            $keys[57] => $this->getDbOwnerId(),
            $keys[58] => $this->getDbCuein(),
            $keys[59] => $this->getDbCueout(),
            $keys[60] => $this->getDbSilanCheck(),
            $keys[61] => $this->getDbHidden(),
            $keys[62] => $this->getDbIsScheduled(),
            $keys[63] => $this->getDbIsPlaylist(),
            $keys[64] => $this->getDbFilesize(),
            $keys[65] => $this->getDbDescription(),
            $keys[66] => $this->getDbArtwork(),
            $keys[67] => $this->getDbTrackTypeId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aFkOwner) {
                $result['FkOwner'] = $this->aFkOwner->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcSubjsRelatedByDbEditedby) {
                $result['CcSubjsRelatedByDbEditedby'] = $this->aCcSubjsRelatedByDbEditedby->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcTracktypes) {
                $result['CcTracktypes'] = $this->aCcTracktypes->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCcShowInstancess) {
                $result['CcShowInstancess'] = $this->collCcShowInstancess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcPlaylistcontentss) {
                $result['CcPlaylistcontentss'] = $this->collCcPlaylistcontentss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcBlockcontentss) {
                $result['CcBlockcontentss'] = $this->collCcBlockcontentss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcSchedules) {
                $result['CcSchedules'] = $this->collCcSchedules->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcPlayoutHistorys) {
                $result['CcPlayoutHistorys'] = $this->collCcPlayoutHistorys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collThirdPartyTrackReferencess) {
                $result['ThirdPartyTrackReferencess'] = $this->collThirdPartyTrackReferencess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPodcastEpisodess) {
                $result['PodcastEpisodess'] = $this->collPodcastEpisodess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CcFilesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbId($value);
                break;
            case 1:
                $this->setDbName($value);
                break;
            case 2:
                $this->setDbMime($value);
                break;
            case 3:
                $this->setDbFtype($value);
                break;
            case 4:
                $this->setDbFilepath($value);
                break;
            case 5:
                $this->setDbImportStatus($value);
                break;
            case 6:
                $this->setDbCurrentlyaccessing($value);
                break;
            case 7:
                $this->setDbEditedby($value);
                break;
            case 8:
                $this->setDbMtime($value);
                break;
            case 9:
                $this->setDbUtime($value);
                break;
            case 10:
                $this->setDbLPtime($value);
                break;
            case 11:
                $this->setDbMd5($value);
                break;
            case 12:
                $this->setDbTrackTitle($value);
                break;
            case 13:
                $this->setDbArtistName($value);
                break;
            case 14:
                $this->setDbBitRate($value);
                break;
            case 15:
                $this->setDbSampleRate($value);
                break;
            case 16:
                $this->setDbFormat($value);
                break;
            case 17:
                $this->setDbLength($value);
                break;
            case 18:
                $this->setDbAlbumTitle($value);
                break;
            case 19:
                $this->setDbGenre($value);
                break;
            case 20:
                $this->setDbComments($value);
                break;
            case 21:
                $this->setDbYear($value);
                break;
            case 22:
                $this->setDbTrackNumber($value);
                break;
            case 23:
                $this->setDbChannels($value);
                break;
            case 24:
                $this->setDbUrl($value);
                break;
            case 25:
                $this->setDbBpm($value);
                break;
            case 26:
                $this->setDbRating($value);
                break;
            case 27:
                $this->setDbEncodedBy($value);
                break;
            case 28:
                $this->setDbDiscNumber($value);
                break;
            case 29:
                $this->setDbMood($value);
                break;
            case 30:
                $this->setDbLabel($value);
                break;
            case 31:
                $this->setDbComposer($value);
                break;
            case 32:
                $this->setDbEncoder($value);
                break;
            case 33:
                $this->setDbChecksum($value);
                break;
            case 34:
                $this->setDbLyrics($value);
                break;
            case 35:
                $this->setDbOrchestra($value);
                break;
            case 36:
                $this->setDbConductor($value);
                break;
            case 37:
                $this->setDbLyricist($value);
                break;
            case 38:
                $this->setDbOriginalLyricist($value);
                break;
            case 39:
                $this->setDbRadioStationName($value);
                break;
            case 40:
                $this->setDbInfoUrl($value);
                break;
            case 41:
                $this->setDbArtistUrl($value);
                break;
            case 42:
                $this->setDbAudioSourceUrl($value);
                break;
            case 43:
                $this->setDbRadioStationUrl($value);
                break;
            case 44:
                $this->setDbBuyThisUrl($value);
                break;
            case 45:
                $this->setDbIsrcNumber($value);
                break;
            case 46:
                $this->setDbCatalogNumber($value);
                break;
            case 47:
                $this->setDbOriginalArtist($value);
                break;
            case 48:
                $this->setDbCopyright($value);
                break;
            case 49:
                $this->setDbReportDatetime($value);
                break;
            case 50:
                $this->setDbReportLocation($value);
                break;
            case 51:
                $this->setDbReportOrganization($value);
                break;
            case 52:
                $this->setDbSubject($value);
                break;
            case 53:
                $this->setDbContributor($value);
                break;
            case 54:
                $this->setDbLanguage($value);
                break;
            case 55:
                $this->setDbFileExists($value);
                break;
            case 56:
                $this->setDbReplayGain($value);
                break;
            case 57:
                $this->setDbOwnerId($value);
                break;
            case 58:
                $this->setDbCuein($value);
                break;
            case 59:
                $this->setDbCueout($value);
                break;
            case 60:
                $this->setDbSilanCheck($value);
                break;
            case 61:
                $this->setDbHidden($value);
                break;
            case 62:
                $this->setDbIsScheduled($value);
                break;
            case 63:
                $this->setDbIsPlaylist($value);
                break;
            case 64:
                $this->setDbFilesize($value);
                break;
            case 65:
                $this->setDbDescription($value);
                break;
            case 66:
                $this->setDbArtwork($value);
                break;
            case 67:
                $this->setDbTrackTypeId($value);
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
        $keys = CcFilesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbMime($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbFtype($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbFilepath($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbImportStatus($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbCurrentlyaccessing($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbEditedby($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDbMtime($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDbUtime($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDbLPtime($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDbMd5($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setDbTrackTitle($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setDbArtistName($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setDbBitRate($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setDbSampleRate($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setDbFormat($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setDbLength($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setDbAlbumTitle($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setDbGenre($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setDbComments($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setDbYear($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setDbTrackNumber($arr[$keys[22]]);
        if (array_key_exists($keys[23], $arr)) $this->setDbChannels($arr[$keys[23]]);
        if (array_key_exists($keys[24], $arr)) $this->setDbUrl($arr[$keys[24]]);
        if (array_key_exists($keys[25], $arr)) $this->setDbBpm($arr[$keys[25]]);
        if (array_key_exists($keys[26], $arr)) $this->setDbRating($arr[$keys[26]]);
        if (array_key_exists($keys[27], $arr)) $this->setDbEncodedBy($arr[$keys[27]]);
        if (array_key_exists($keys[28], $arr)) $this->setDbDiscNumber($arr[$keys[28]]);
        if (array_key_exists($keys[29], $arr)) $this->setDbMood($arr[$keys[29]]);
        if (array_key_exists($keys[30], $arr)) $this->setDbLabel($arr[$keys[30]]);
        if (array_key_exists($keys[31], $arr)) $this->setDbComposer($arr[$keys[31]]);
        if (array_key_exists($keys[32], $arr)) $this->setDbEncoder($arr[$keys[32]]);
        if (array_key_exists($keys[33], $arr)) $this->setDbChecksum($arr[$keys[33]]);
        if (array_key_exists($keys[34], $arr)) $this->setDbLyrics($arr[$keys[34]]);
        if (array_key_exists($keys[35], $arr)) $this->setDbOrchestra($arr[$keys[35]]);
        if (array_key_exists($keys[36], $arr)) $this->setDbConductor($arr[$keys[36]]);
        if (array_key_exists($keys[37], $arr)) $this->setDbLyricist($arr[$keys[37]]);
        if (array_key_exists($keys[38], $arr)) $this->setDbOriginalLyricist($arr[$keys[38]]);
        if (array_key_exists($keys[39], $arr)) $this->setDbRadioStationName($arr[$keys[39]]);
        if (array_key_exists($keys[40], $arr)) $this->setDbInfoUrl($arr[$keys[40]]);
        if (array_key_exists($keys[41], $arr)) $this->setDbArtistUrl($arr[$keys[41]]);
        if (array_key_exists($keys[42], $arr)) $this->setDbAudioSourceUrl($arr[$keys[42]]);
        if (array_key_exists($keys[43], $arr)) $this->setDbRadioStationUrl($arr[$keys[43]]);
        if (array_key_exists($keys[44], $arr)) $this->setDbBuyThisUrl($arr[$keys[44]]);
        if (array_key_exists($keys[45], $arr)) $this->setDbIsrcNumber($arr[$keys[45]]);
        if (array_key_exists($keys[46], $arr)) $this->setDbCatalogNumber($arr[$keys[46]]);
        if (array_key_exists($keys[47], $arr)) $this->setDbOriginalArtist($arr[$keys[47]]);
        if (array_key_exists($keys[48], $arr)) $this->setDbCopyright($arr[$keys[48]]);
        if (array_key_exists($keys[49], $arr)) $this->setDbReportDatetime($arr[$keys[49]]);
        if (array_key_exists($keys[50], $arr)) $this->setDbReportLocation($arr[$keys[50]]);
        if (array_key_exists($keys[51], $arr)) $this->setDbReportOrganization($arr[$keys[51]]);
        if (array_key_exists($keys[52], $arr)) $this->setDbSubject($arr[$keys[52]]);
        if (array_key_exists($keys[53], $arr)) $this->setDbContributor($arr[$keys[53]]);
        if (array_key_exists($keys[54], $arr)) $this->setDbLanguage($arr[$keys[54]]);
        if (array_key_exists($keys[55], $arr)) $this->setDbFileExists($arr[$keys[55]]);
        if (array_key_exists($keys[56], $arr)) $this->setDbReplayGain($arr[$keys[56]]);
        if (array_key_exists($keys[57], $arr)) $this->setDbOwnerId($arr[$keys[57]]);
        if (array_key_exists($keys[58], $arr)) $this->setDbCuein($arr[$keys[58]]);
        if (array_key_exists($keys[59], $arr)) $this->setDbCueout($arr[$keys[59]]);
        if (array_key_exists($keys[60], $arr)) $this->setDbSilanCheck($arr[$keys[60]]);
        if (array_key_exists($keys[61], $arr)) $this->setDbHidden($arr[$keys[61]]);
        if (array_key_exists($keys[62], $arr)) $this->setDbIsScheduled($arr[$keys[62]]);
        if (array_key_exists($keys[63], $arr)) $this->setDbIsPlaylist($arr[$keys[63]]);
        if (array_key_exists($keys[64], $arr)) $this->setDbFilesize($arr[$keys[64]]);
        if (array_key_exists($keys[65], $arr)) $this->setDbDescription($arr[$keys[65]]);
        if (array_key_exists($keys[66], $arr)) $this->setDbArtwork($arr[$keys[66]]);
        if (array_key_exists($keys[67], $arr)) $this->setDbTrackTypeId($arr[$keys[67]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcFilesPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcFilesPeer::ID)) $criteria->add(CcFilesPeer::ID, $this->id);
        if ($this->isColumnModified(CcFilesPeer::NAME)) $criteria->add(CcFilesPeer::NAME, $this->name);
        if ($this->isColumnModified(CcFilesPeer::MIME)) $criteria->add(CcFilesPeer::MIME, $this->mime);
        if ($this->isColumnModified(CcFilesPeer::FTYPE)) $criteria->add(CcFilesPeer::FTYPE, $this->ftype);
        if ($this->isColumnModified(CcFilesPeer::FILEPATH)) $criteria->add(CcFilesPeer::FILEPATH, $this->filepath);
        if ($this->isColumnModified(CcFilesPeer::IMPORT_STATUS)) $criteria->add(CcFilesPeer::IMPORT_STATUS, $this->import_status);
        if ($this->isColumnModified(CcFilesPeer::CURRENTLYACCESSING)) $criteria->add(CcFilesPeer::CURRENTLYACCESSING, $this->currentlyaccessing);
        if ($this->isColumnModified(CcFilesPeer::EDITEDBY)) $criteria->add(CcFilesPeer::EDITEDBY, $this->editedby);
        if ($this->isColumnModified(CcFilesPeer::MTIME)) $criteria->add(CcFilesPeer::MTIME, $this->mtime);
        if ($this->isColumnModified(CcFilesPeer::UTIME)) $criteria->add(CcFilesPeer::UTIME, $this->utime);
        if ($this->isColumnModified(CcFilesPeer::LPTIME)) $criteria->add(CcFilesPeer::LPTIME, $this->lptime);
        if ($this->isColumnModified(CcFilesPeer::MD5)) $criteria->add(CcFilesPeer::MD5, $this->md5);
        if ($this->isColumnModified(CcFilesPeer::TRACK_TITLE)) $criteria->add(CcFilesPeer::TRACK_TITLE, $this->track_title);
        if ($this->isColumnModified(CcFilesPeer::ARTIST_NAME)) $criteria->add(CcFilesPeer::ARTIST_NAME, $this->artist_name);
        if ($this->isColumnModified(CcFilesPeer::BIT_RATE)) $criteria->add(CcFilesPeer::BIT_RATE, $this->bit_rate);
        if ($this->isColumnModified(CcFilesPeer::SAMPLE_RATE)) $criteria->add(CcFilesPeer::SAMPLE_RATE, $this->sample_rate);
        if ($this->isColumnModified(CcFilesPeer::FORMAT)) $criteria->add(CcFilesPeer::FORMAT, $this->format);
        if ($this->isColumnModified(CcFilesPeer::LENGTH)) $criteria->add(CcFilesPeer::LENGTH, $this->length);
        if ($this->isColumnModified(CcFilesPeer::ALBUM_TITLE)) $criteria->add(CcFilesPeer::ALBUM_TITLE, $this->album_title);
        if ($this->isColumnModified(CcFilesPeer::GENRE)) $criteria->add(CcFilesPeer::GENRE, $this->genre);
        if ($this->isColumnModified(CcFilesPeer::COMMENTS)) $criteria->add(CcFilesPeer::COMMENTS, $this->comments);
        if ($this->isColumnModified(CcFilesPeer::YEAR)) $criteria->add(CcFilesPeer::YEAR, $this->year);
        if ($this->isColumnModified(CcFilesPeer::TRACK_NUMBER)) $criteria->add(CcFilesPeer::TRACK_NUMBER, $this->track_number);
        if ($this->isColumnModified(CcFilesPeer::CHANNELS)) $criteria->add(CcFilesPeer::CHANNELS, $this->channels);
        if ($this->isColumnModified(CcFilesPeer::URL)) $criteria->add(CcFilesPeer::URL, $this->url);
        if ($this->isColumnModified(CcFilesPeer::BPM)) $criteria->add(CcFilesPeer::BPM, $this->bpm);
        if ($this->isColumnModified(CcFilesPeer::RATING)) $criteria->add(CcFilesPeer::RATING, $this->rating);
        if ($this->isColumnModified(CcFilesPeer::ENCODED_BY)) $criteria->add(CcFilesPeer::ENCODED_BY, $this->encoded_by);
        if ($this->isColumnModified(CcFilesPeer::DISC_NUMBER)) $criteria->add(CcFilesPeer::DISC_NUMBER, $this->disc_number);
        if ($this->isColumnModified(CcFilesPeer::MOOD)) $criteria->add(CcFilesPeer::MOOD, $this->mood);
        if ($this->isColumnModified(CcFilesPeer::LABEL)) $criteria->add(CcFilesPeer::LABEL, $this->label);
        if ($this->isColumnModified(CcFilesPeer::COMPOSER)) $criteria->add(CcFilesPeer::COMPOSER, $this->composer);
        if ($this->isColumnModified(CcFilesPeer::ENCODER)) $criteria->add(CcFilesPeer::ENCODER, $this->encoder);
        if ($this->isColumnModified(CcFilesPeer::CHECKSUM)) $criteria->add(CcFilesPeer::CHECKSUM, $this->checksum);
        if ($this->isColumnModified(CcFilesPeer::LYRICS)) $criteria->add(CcFilesPeer::LYRICS, $this->lyrics);
        if ($this->isColumnModified(CcFilesPeer::ORCHESTRA)) $criteria->add(CcFilesPeer::ORCHESTRA, $this->orchestra);
        if ($this->isColumnModified(CcFilesPeer::CONDUCTOR)) $criteria->add(CcFilesPeer::CONDUCTOR, $this->conductor);
        if ($this->isColumnModified(CcFilesPeer::LYRICIST)) $criteria->add(CcFilesPeer::LYRICIST, $this->lyricist);
        if ($this->isColumnModified(CcFilesPeer::ORIGINAL_LYRICIST)) $criteria->add(CcFilesPeer::ORIGINAL_LYRICIST, $this->original_lyricist);
        if ($this->isColumnModified(CcFilesPeer::RADIO_STATION_NAME)) $criteria->add(CcFilesPeer::RADIO_STATION_NAME, $this->radio_station_name);
        if ($this->isColumnModified(CcFilesPeer::INFO_URL)) $criteria->add(CcFilesPeer::INFO_URL, $this->info_url);
        if ($this->isColumnModified(CcFilesPeer::ARTIST_URL)) $criteria->add(CcFilesPeer::ARTIST_URL, $this->artist_url);
        if ($this->isColumnModified(CcFilesPeer::AUDIO_SOURCE_URL)) $criteria->add(CcFilesPeer::AUDIO_SOURCE_URL, $this->audio_source_url);
        if ($this->isColumnModified(CcFilesPeer::RADIO_STATION_URL)) $criteria->add(CcFilesPeer::RADIO_STATION_URL, $this->radio_station_url);
        if ($this->isColumnModified(CcFilesPeer::BUY_THIS_URL)) $criteria->add(CcFilesPeer::BUY_THIS_URL, $this->buy_this_url);
        if ($this->isColumnModified(CcFilesPeer::ISRC_NUMBER)) $criteria->add(CcFilesPeer::ISRC_NUMBER, $this->isrc_number);
        if ($this->isColumnModified(CcFilesPeer::CATALOG_NUMBER)) $criteria->add(CcFilesPeer::CATALOG_NUMBER, $this->catalog_number);
        if ($this->isColumnModified(CcFilesPeer::ORIGINAL_ARTIST)) $criteria->add(CcFilesPeer::ORIGINAL_ARTIST, $this->original_artist);
        if ($this->isColumnModified(CcFilesPeer::COPYRIGHT)) $criteria->add(CcFilesPeer::COPYRIGHT, $this->copyright);
        if ($this->isColumnModified(CcFilesPeer::REPORT_DATETIME)) $criteria->add(CcFilesPeer::REPORT_DATETIME, $this->report_datetime);
        if ($this->isColumnModified(CcFilesPeer::REPORT_LOCATION)) $criteria->add(CcFilesPeer::REPORT_LOCATION, $this->report_location);
        if ($this->isColumnModified(CcFilesPeer::REPORT_ORGANIZATION)) $criteria->add(CcFilesPeer::REPORT_ORGANIZATION, $this->report_organization);
        if ($this->isColumnModified(CcFilesPeer::SUBJECT)) $criteria->add(CcFilesPeer::SUBJECT, $this->subject);
        if ($this->isColumnModified(CcFilesPeer::CONTRIBUTOR)) $criteria->add(CcFilesPeer::CONTRIBUTOR, $this->contributor);
        if ($this->isColumnModified(CcFilesPeer::LANGUAGE)) $criteria->add(CcFilesPeer::LANGUAGE, $this->language);
        if ($this->isColumnModified(CcFilesPeer::FILE_EXISTS)) $criteria->add(CcFilesPeer::FILE_EXISTS, $this->file_exists);
        if ($this->isColumnModified(CcFilesPeer::REPLAY_GAIN)) $criteria->add(CcFilesPeer::REPLAY_GAIN, $this->replay_gain);
        if ($this->isColumnModified(CcFilesPeer::OWNER_ID)) $criteria->add(CcFilesPeer::OWNER_ID, $this->owner_id);
        if ($this->isColumnModified(CcFilesPeer::CUEIN)) $criteria->add(CcFilesPeer::CUEIN, $this->cuein);
        if ($this->isColumnModified(CcFilesPeer::CUEOUT)) $criteria->add(CcFilesPeer::CUEOUT, $this->cueout);
        if ($this->isColumnModified(CcFilesPeer::SILAN_CHECK)) $criteria->add(CcFilesPeer::SILAN_CHECK, $this->silan_check);
        if ($this->isColumnModified(CcFilesPeer::HIDDEN)) $criteria->add(CcFilesPeer::HIDDEN, $this->hidden);
        if ($this->isColumnModified(CcFilesPeer::IS_SCHEDULED)) $criteria->add(CcFilesPeer::IS_SCHEDULED, $this->is_scheduled);
        if ($this->isColumnModified(CcFilesPeer::IS_PLAYLIST)) $criteria->add(CcFilesPeer::IS_PLAYLIST, $this->is_playlist);
        if ($this->isColumnModified(CcFilesPeer::FILESIZE)) $criteria->add(CcFilesPeer::FILESIZE, $this->filesize);
        if ($this->isColumnModified(CcFilesPeer::DESCRIPTION)) $criteria->add(CcFilesPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CcFilesPeer::ARTWORK)) $criteria->add(CcFilesPeer::ARTWORK, $this->artwork);
        if ($this->isColumnModified(CcFilesPeer::TRACK_TYPE_ID)) $criteria->add(CcFilesPeer::TRACK_TYPE_ID, $this->track_type_id);

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
        $criteria = new Criteria(CcFilesPeer::DATABASE_NAME);
        $criteria->add(CcFilesPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getDbId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setDbId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getDbId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of CcFiles (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbName($this->getDbName());
        $copyObj->setDbMime($this->getDbMime());
        $copyObj->setDbFtype($this->getDbFtype());
        $copyObj->setDbFilepath($this->getDbFilepath());
        $copyObj->setDbImportStatus($this->getDbImportStatus());
        $copyObj->setDbCurrentlyaccessing($this->getDbCurrentlyaccessing());
        $copyObj->setDbEditedby($this->getDbEditedby());
        $copyObj->setDbMtime($this->getDbMtime());
        $copyObj->setDbUtime($this->getDbUtime());
        $copyObj->setDbLPtime($this->getDbLPtime());
        $copyObj->setDbMd5($this->getDbMd5());
        $copyObj->setDbTrackTitle($this->getDbTrackTitle());
        $copyObj->setDbArtistName($this->getDbArtistName());
        $copyObj->setDbBitRate($this->getDbBitRate());
        $copyObj->setDbSampleRate($this->getDbSampleRate());
        $copyObj->setDbFormat($this->getDbFormat());
        $copyObj->setDbLength($this->getDbLength());
        $copyObj->setDbAlbumTitle($this->getDbAlbumTitle());
        $copyObj->setDbGenre($this->getDbGenre());
        $copyObj->setDbComments($this->getDbComments());
        $copyObj->setDbYear($this->getDbYear());
        $copyObj->setDbTrackNumber($this->getDbTrackNumber());
        $copyObj->setDbChannels($this->getDbChannels());
        $copyObj->setDbUrl($this->getDbUrl());
        $copyObj->setDbBpm($this->getDbBpm());
        $copyObj->setDbRating($this->getDbRating());
        $copyObj->setDbEncodedBy($this->getDbEncodedBy());
        $copyObj->setDbDiscNumber($this->getDbDiscNumber());
        $copyObj->setDbMood($this->getDbMood());
        $copyObj->setDbLabel($this->getDbLabel());
        $copyObj->setDbComposer($this->getDbComposer());
        $copyObj->setDbEncoder($this->getDbEncoder());
        $copyObj->setDbChecksum($this->getDbChecksum());
        $copyObj->setDbLyrics($this->getDbLyrics());
        $copyObj->setDbOrchestra($this->getDbOrchestra());
        $copyObj->setDbConductor($this->getDbConductor());
        $copyObj->setDbLyricist($this->getDbLyricist());
        $copyObj->setDbOriginalLyricist($this->getDbOriginalLyricist());
        $copyObj->setDbRadioStationName($this->getDbRadioStationName());
        $copyObj->setDbInfoUrl($this->getDbInfoUrl());
        $copyObj->setDbArtistUrl($this->getDbArtistUrl());
        $copyObj->setDbAudioSourceUrl($this->getDbAudioSourceUrl());
        $copyObj->setDbRadioStationUrl($this->getDbRadioStationUrl());
        $copyObj->setDbBuyThisUrl($this->getDbBuyThisUrl());
        $copyObj->setDbIsrcNumber($this->getDbIsrcNumber());
        $copyObj->setDbCatalogNumber($this->getDbCatalogNumber());
        $copyObj->setDbOriginalArtist($this->getDbOriginalArtist());
        $copyObj->setDbCopyright($this->getDbCopyright());
        $copyObj->setDbReportDatetime($this->getDbReportDatetime());
        $copyObj->setDbReportLocation($this->getDbReportLocation());
        $copyObj->setDbReportOrganization($this->getDbReportOrganization());
        $copyObj->setDbSubject($this->getDbSubject());
        $copyObj->setDbContributor($this->getDbContributor());
        $copyObj->setDbLanguage($this->getDbLanguage());
        $copyObj->setDbFileExists($this->getDbFileExists());
        $copyObj->setDbReplayGain($this->getDbReplayGain());
        $copyObj->setDbOwnerId($this->getDbOwnerId());
        $copyObj->setDbCuein($this->getDbCuein());
        $copyObj->setDbCueout($this->getDbCueout());
        $copyObj->setDbSilanCheck($this->getDbSilanCheck());
        $copyObj->setDbHidden($this->getDbHidden());
        $copyObj->setDbIsScheduled($this->getDbIsScheduled());
        $copyObj->setDbIsPlaylist($this->getDbIsPlaylist());
        $copyObj->setDbFilesize($this->getDbFilesize());
        $copyObj->setDbDescription($this->getDbDescription());
        $copyObj->setDbArtwork($this->getDbArtwork());
        $copyObj->setDbTrackTypeId($this->getDbTrackTypeId());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcShowInstancess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcShowInstances($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcPlaylistcontentss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcPlaylistcontents($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcBlockcontentss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcBlockcontents($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcSchedules() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcSchedule($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcPlayoutHistorys() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcPlayoutHistory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getThirdPartyTrackReferencess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addThirdPartyTrackReferences($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPodcastEpisodess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPodcastEpisodes($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setDbId(NULL); // this is a auto-increment column, so set to default value
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
     * @return CcFiles Clone of current object.
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
     * @return CcFilesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcFilesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcSubjs object.
     *
     * @param                  CcSubjs $v
     * @return CcFiles The current object (for fluent API support)
     * @throws PropelException
     */
    public function setFkOwner(CcSubjs $v = null)
    {
        if ($v === null) {
            $this->setDbOwnerId(NULL);
        } else {
            $this->setDbOwnerId($v->getDbId());
        }

        $this->aFkOwner = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcSubjs object, it will not be re-added.
        if ($v !== null) {
            $v->addCcFilesRelatedByDbOwnerId($this);
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
    public function getFkOwner(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aFkOwner === null && ($this->owner_id !== null) && $doQuery) {
            $this->aFkOwner = CcSubjsQuery::create()->findPk($this->owner_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aFkOwner->addCcFilessRelatedByDbOwnerId($this);
             */
        }

        return $this->aFkOwner;
    }

    /**
     * Declares an association between this object and a CcSubjs object.
     *
     * @param                  CcSubjs $v
     * @return CcFiles The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcSubjsRelatedByDbEditedby(CcSubjs $v = null)
    {
        if ($v === null) {
            $this->setDbEditedby(NULL);
        } else {
            $this->setDbEditedby($v->getDbId());
        }

        $this->aCcSubjsRelatedByDbEditedby = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcSubjs object, it will not be re-added.
        if ($v !== null) {
            $v->addCcFilesRelatedByDbEditedby($this);
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
    public function getCcSubjsRelatedByDbEditedby(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcSubjsRelatedByDbEditedby === null && ($this->editedby !== null) && $doQuery) {
            $this->aCcSubjsRelatedByDbEditedby = CcSubjsQuery::create()->findPk($this->editedby, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcSubjsRelatedByDbEditedby->addCcFilessRelatedByDbEditedby($this);
             */
        }

        return $this->aCcSubjsRelatedByDbEditedby;
    }

    /**
     * Declares an association between this object and a CcTracktypes object.
     *
     * @param                  CcTracktypes $v
     * @return CcFiles The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcTracktypes(CcTracktypes $v = null)
    {
        if ($v === null) {
            $this->setDbTrackTypeId(NULL);
        } else {
            $this->setDbTrackTypeId($v->getDbId());
        }

        $this->aCcTracktypes = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcTracktypes object, it will not be re-added.
        if ($v !== null) {
            $v->addCcFiles($this);
        }


        return $this;
    }


    /**
     * Get the associated CcTracktypes object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcTracktypes The associated CcTracktypes object.
     * @throws PropelException
     */
    public function getCcTracktypes(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcTracktypes === null && ($this->track_type_id !== null) && $doQuery) {
            $this->aCcTracktypes = CcTracktypesQuery::create()->findPk($this->track_type_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcTracktypes->addCcFiless($this);
             */
        }

        return $this->aCcTracktypes;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('CcShowInstances' == $relationName) {
            $this->initCcShowInstancess();
        }
        if ('CcPlaylistcontents' == $relationName) {
            $this->initCcPlaylistcontentss();
        }
        if ('CcBlockcontents' == $relationName) {
            $this->initCcBlockcontentss();
        }
        if ('CcSchedule' == $relationName) {
            $this->initCcSchedules();
        }
        if ('CcPlayoutHistory' == $relationName) {
            $this->initCcPlayoutHistorys();
        }
        if ('ThirdPartyTrackReferences' == $relationName) {
            $this->initThirdPartyTrackReferencess();
        }
        if ('PodcastEpisodes' == $relationName) {
            $this->initPodcastEpisodess();
        }
    }

    /**
     * Clears out the collCcShowInstancess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcFiles The current object (for fluent API support)
     * @see        addCcShowInstancess()
     */
    public function clearCcShowInstancess()
    {
        $this->collCcShowInstancess = null; // important to set this to null since that means it is uninitialized
        $this->collCcShowInstancessPartial = null;

        return $this;
    }

    /**
     * reset is the collCcShowInstancess collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcShowInstancess($v = true)
    {
        $this->collCcShowInstancessPartial = $v;
    }

    /**
     * Initializes the collCcShowInstancess collection.
     *
     * By default this just sets the collCcShowInstancess collection to an empty array (like clearcollCcShowInstancess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcShowInstancess($overrideExisting = true)
    {
        if (null !== $this->collCcShowInstancess && !$overrideExisting) {
            return;
        }
        $this->collCcShowInstancess = new PropelObjectCollection();
        $this->collCcShowInstancess->setModel('CcShowInstances');
    }

    /**
     * Gets an array of CcShowInstances objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcFiles is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     * @throws PropelException
     */
    public function getCcShowInstancess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcShowInstancessPartial && !$this->isNew();
        if (null === $this->collCcShowInstancess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcShowInstancess) {
                // return empty collection
                $this->initCcShowInstancess();
            } else {
                $collCcShowInstancess = CcShowInstancesQuery::create(null, $criteria)
                    ->filterByCcFiles($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcShowInstancessPartial && count($collCcShowInstancess)) {
                      $this->initCcShowInstancess(false);

                      foreach ($collCcShowInstancess as $obj) {
                        if (false == $this->collCcShowInstancess->contains($obj)) {
                          $this->collCcShowInstancess->append($obj);
                        }
                      }

                      $this->collCcShowInstancessPartial = true;
                    }

                    $collCcShowInstancess->getInternalIterator()->rewind();

                    return $collCcShowInstancess;
                }

                if ($partial && $this->collCcShowInstancess) {
                    foreach ($this->collCcShowInstancess as $obj) {
                        if ($obj->isNew()) {
                            $collCcShowInstancess[] = $obj;
                        }
                    }
                }

                $this->collCcShowInstancess = $collCcShowInstancess;
                $this->collCcShowInstancessPartial = false;
            }
        }

        return $this->collCcShowInstancess;
    }

    /**
     * Sets a collection of CcShowInstances objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccShowInstancess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcFiles The current object (for fluent API support)
     */
    public function setCcShowInstancess(PropelCollection $ccShowInstancess, PropelPDO $con = null)
    {
        $ccShowInstancessToDelete = $this->getCcShowInstancess(new Criteria(), $con)->diff($ccShowInstancess);


        $this->ccShowInstancessScheduledForDeletion = $ccShowInstancessToDelete;

        foreach ($ccShowInstancessToDelete as $ccShowInstancesRemoved) {
            $ccShowInstancesRemoved->setCcFiles(null);
        }

        $this->collCcShowInstancess = null;
        foreach ($ccShowInstancess as $ccShowInstances) {
            $this->addCcShowInstances($ccShowInstances);
        }

        $this->collCcShowInstancess = $ccShowInstancess;
        $this->collCcShowInstancessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcShowInstances objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcShowInstances objects.
     * @throws PropelException
     */
    public function countCcShowInstancess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcShowInstancessPartial && !$this->isNew();
        if (null === $this->collCcShowInstancess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcShowInstancess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcShowInstancess());
            }
            $query = CcShowInstancesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcFiles($this)
                ->count($con);
        }

        return count($this->collCcShowInstancess);
    }

    /**
     * Method called to associate a CcShowInstances object to this object
     * through the CcShowInstances foreign key attribute.
     *
     * @param    CcShowInstances $l CcShowInstances
     * @return CcFiles The current object (for fluent API support)
     */
    public function addCcShowInstances(CcShowInstances $l)
    {
        if ($this->collCcShowInstancess === null) {
            $this->initCcShowInstancess();
            $this->collCcShowInstancessPartial = true;
        }

        if (!in_array($l, $this->collCcShowInstancess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcShowInstances($l);

            if ($this->ccShowInstancessScheduledForDeletion and $this->ccShowInstancessScheduledForDeletion->contains($l)) {
                $this->ccShowInstancessScheduledForDeletion->remove($this->ccShowInstancessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcShowInstances $ccShowInstances The ccShowInstances object to add.
     */
    protected function doAddCcShowInstances($ccShowInstances)
    {
        $this->collCcShowInstancess[]= $ccShowInstances;
        $ccShowInstances->setCcFiles($this);
    }

    /**
     * @param	CcShowInstances $ccShowInstances The ccShowInstances object to remove.
     * @return CcFiles The current object (for fluent API support)
     */
    public function removeCcShowInstances($ccShowInstances)
    {
        if ($this->getCcShowInstancess()->contains($ccShowInstances)) {
            $this->collCcShowInstancess->remove($this->collCcShowInstancess->search($ccShowInstances));
            if (null === $this->ccShowInstancessScheduledForDeletion) {
                $this->ccShowInstancessScheduledForDeletion = clone $this->collCcShowInstancess;
                $this->ccShowInstancessScheduledForDeletion->clear();
            }
            $this->ccShowInstancessScheduledForDeletion[]= $ccShowInstances;
            $ccShowInstances->setCcFiles(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related CcShowInstancess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     */
    public function getCcShowInstancessJoinCcShow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowInstancesQuery::create(null, $criteria);
        $query->joinWith('CcShow', $join_behavior);

        return $this->getCcShowInstancess($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related CcShowInstancess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     */
    public function getCcShowInstancessJoinCcShowInstancesRelatedByDbOriginalShow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowInstancesQuery::create(null, $criteria);
        $query->joinWith('CcShowInstancesRelatedByDbOriginalShow', $join_behavior);

        return $this->getCcShowInstancess($query, $con);
    }

    /**
     * Clears out the collCcPlaylistcontentss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcFiles The current object (for fluent API support)
     * @see        addCcPlaylistcontentss()
     */
    public function clearCcPlaylistcontentss()
    {
        $this->collCcPlaylistcontentss = null; // important to set this to null since that means it is uninitialized
        $this->collCcPlaylistcontentssPartial = null;

        return $this;
    }

    /**
     * reset is the collCcPlaylistcontentss collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcPlaylistcontentss($v = true)
    {
        $this->collCcPlaylistcontentssPartial = $v;
    }

    /**
     * Initializes the collCcPlaylistcontentss collection.
     *
     * By default this just sets the collCcPlaylistcontentss collection to an empty array (like clearcollCcPlaylistcontentss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcPlaylistcontentss($overrideExisting = true)
    {
        if (null !== $this->collCcPlaylistcontentss && !$overrideExisting) {
            return;
        }
        $this->collCcPlaylistcontentss = new PropelObjectCollection();
        $this->collCcPlaylistcontentss->setModel('CcPlaylistcontents');
    }

    /**
     * Gets an array of CcPlaylistcontents objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcFiles is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcPlaylistcontents[] List of CcPlaylistcontents objects
     * @throws PropelException
     */
    public function getCcPlaylistcontentss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcPlaylistcontentssPartial && !$this->isNew();
        if (null === $this->collCcPlaylistcontentss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcPlaylistcontentss) {
                // return empty collection
                $this->initCcPlaylistcontentss();
            } else {
                $collCcPlaylistcontentss = CcPlaylistcontentsQuery::create(null, $criteria)
                    ->filterByCcFiles($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcPlaylistcontentssPartial && count($collCcPlaylistcontentss)) {
                      $this->initCcPlaylistcontentss(false);

                      foreach ($collCcPlaylistcontentss as $obj) {
                        if (false == $this->collCcPlaylistcontentss->contains($obj)) {
                          $this->collCcPlaylistcontentss->append($obj);
                        }
                      }

                      $this->collCcPlaylistcontentssPartial = true;
                    }

                    $collCcPlaylistcontentss->getInternalIterator()->rewind();

                    return $collCcPlaylistcontentss;
                }

                if ($partial && $this->collCcPlaylistcontentss) {
                    foreach ($this->collCcPlaylistcontentss as $obj) {
                        if ($obj->isNew()) {
                            $collCcPlaylistcontentss[] = $obj;
                        }
                    }
                }

                $this->collCcPlaylistcontentss = $collCcPlaylistcontentss;
                $this->collCcPlaylistcontentssPartial = false;
            }
        }

        return $this->collCcPlaylistcontentss;
    }

    /**
     * Sets a collection of CcPlaylistcontents objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccPlaylistcontentss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcFiles The current object (for fluent API support)
     */
    public function setCcPlaylistcontentss(PropelCollection $ccPlaylistcontentss, PropelPDO $con = null)
    {
        $ccPlaylistcontentssToDelete = $this->getCcPlaylistcontentss(new Criteria(), $con)->diff($ccPlaylistcontentss);


        $this->ccPlaylistcontentssScheduledForDeletion = $ccPlaylistcontentssToDelete;

        foreach ($ccPlaylistcontentssToDelete as $ccPlaylistcontentsRemoved) {
            $ccPlaylistcontentsRemoved->setCcFiles(null);
        }

        $this->collCcPlaylistcontentss = null;
        foreach ($ccPlaylistcontentss as $ccPlaylistcontents) {
            $this->addCcPlaylistcontents($ccPlaylistcontents);
        }

        $this->collCcPlaylistcontentss = $ccPlaylistcontentss;
        $this->collCcPlaylistcontentssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcPlaylistcontents objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcPlaylistcontents objects.
     * @throws PropelException
     */
    public function countCcPlaylistcontentss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcPlaylistcontentssPartial && !$this->isNew();
        if (null === $this->collCcPlaylistcontentss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcPlaylistcontentss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcPlaylistcontentss());
            }
            $query = CcPlaylistcontentsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcFiles($this)
                ->count($con);
        }

        return count($this->collCcPlaylistcontentss);
    }

    /**
     * Method called to associate a CcPlaylistcontents object to this object
     * through the CcPlaylistcontents foreign key attribute.
     *
     * @param    CcPlaylistcontents $l CcPlaylistcontents
     * @return CcFiles The current object (for fluent API support)
     */
    public function addCcPlaylistcontents(CcPlaylistcontents $l)
    {
        if ($this->collCcPlaylistcontentss === null) {
            $this->initCcPlaylistcontentss();
            $this->collCcPlaylistcontentssPartial = true;
        }

        if (!in_array($l, $this->collCcPlaylistcontentss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcPlaylistcontents($l);

            if ($this->ccPlaylistcontentssScheduledForDeletion and $this->ccPlaylistcontentssScheduledForDeletion->contains($l)) {
                $this->ccPlaylistcontentssScheduledForDeletion->remove($this->ccPlaylistcontentssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcPlaylistcontents $ccPlaylistcontents The ccPlaylistcontents object to add.
     */
    protected function doAddCcPlaylistcontents($ccPlaylistcontents)
    {
        $this->collCcPlaylistcontentss[]= $ccPlaylistcontents;
        $ccPlaylistcontents->setCcFiles($this);
    }

    /**
     * @param	CcPlaylistcontents $ccPlaylistcontents The ccPlaylistcontents object to remove.
     * @return CcFiles The current object (for fluent API support)
     */
    public function removeCcPlaylistcontents($ccPlaylistcontents)
    {
        if ($this->getCcPlaylistcontentss()->contains($ccPlaylistcontents)) {
            $this->collCcPlaylistcontentss->remove($this->collCcPlaylistcontentss->search($ccPlaylistcontents));
            if (null === $this->ccPlaylistcontentssScheduledForDeletion) {
                $this->ccPlaylistcontentssScheduledForDeletion = clone $this->collCcPlaylistcontentss;
                $this->ccPlaylistcontentssScheduledForDeletion->clear();
            }
            $this->ccPlaylistcontentssScheduledForDeletion[]= $ccPlaylistcontents;
            $ccPlaylistcontents->setCcFiles(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related CcPlaylistcontentss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcPlaylistcontents[] List of CcPlaylistcontents objects
     */
    public function getCcPlaylistcontentssJoinCcBlock($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcPlaylistcontentsQuery::create(null, $criteria);
        $query->joinWith('CcBlock', $join_behavior);

        return $this->getCcPlaylistcontentss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related CcPlaylistcontentss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcPlaylistcontents[] List of CcPlaylistcontents objects
     */
    public function getCcPlaylistcontentssJoinCcPlaylist($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcPlaylistcontentsQuery::create(null, $criteria);
        $query->joinWith('CcPlaylist', $join_behavior);

        return $this->getCcPlaylistcontentss($query, $con);
    }

    /**
     * Clears out the collCcBlockcontentss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcFiles The current object (for fluent API support)
     * @see        addCcBlockcontentss()
     */
    public function clearCcBlockcontentss()
    {
        $this->collCcBlockcontentss = null; // important to set this to null since that means it is uninitialized
        $this->collCcBlockcontentssPartial = null;

        return $this;
    }

    /**
     * reset is the collCcBlockcontentss collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcBlockcontentss($v = true)
    {
        $this->collCcBlockcontentssPartial = $v;
    }

    /**
     * Initializes the collCcBlockcontentss collection.
     *
     * By default this just sets the collCcBlockcontentss collection to an empty array (like clearcollCcBlockcontentss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcBlockcontentss($overrideExisting = true)
    {
        if (null !== $this->collCcBlockcontentss && !$overrideExisting) {
            return;
        }
        $this->collCcBlockcontentss = new PropelObjectCollection();
        $this->collCcBlockcontentss->setModel('CcBlockcontents');
    }

    /**
     * Gets an array of CcBlockcontents objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcFiles is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcBlockcontents[] List of CcBlockcontents objects
     * @throws PropelException
     */
    public function getCcBlockcontentss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcBlockcontentssPartial && !$this->isNew();
        if (null === $this->collCcBlockcontentss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcBlockcontentss) {
                // return empty collection
                $this->initCcBlockcontentss();
            } else {
                $collCcBlockcontentss = CcBlockcontentsQuery::create(null, $criteria)
                    ->filterByCcFiles($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcBlockcontentssPartial && count($collCcBlockcontentss)) {
                      $this->initCcBlockcontentss(false);

                      foreach ($collCcBlockcontentss as $obj) {
                        if (false == $this->collCcBlockcontentss->contains($obj)) {
                          $this->collCcBlockcontentss->append($obj);
                        }
                      }

                      $this->collCcBlockcontentssPartial = true;
                    }

                    $collCcBlockcontentss->getInternalIterator()->rewind();

                    return $collCcBlockcontentss;
                }

                if ($partial && $this->collCcBlockcontentss) {
                    foreach ($this->collCcBlockcontentss as $obj) {
                        if ($obj->isNew()) {
                            $collCcBlockcontentss[] = $obj;
                        }
                    }
                }

                $this->collCcBlockcontentss = $collCcBlockcontentss;
                $this->collCcBlockcontentssPartial = false;
            }
        }

        return $this->collCcBlockcontentss;
    }

    /**
     * Sets a collection of CcBlockcontents objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccBlockcontentss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcFiles The current object (for fluent API support)
     */
    public function setCcBlockcontentss(PropelCollection $ccBlockcontentss, PropelPDO $con = null)
    {
        $ccBlockcontentssToDelete = $this->getCcBlockcontentss(new Criteria(), $con)->diff($ccBlockcontentss);


        $this->ccBlockcontentssScheduledForDeletion = $ccBlockcontentssToDelete;

        foreach ($ccBlockcontentssToDelete as $ccBlockcontentsRemoved) {
            $ccBlockcontentsRemoved->setCcFiles(null);
        }

        $this->collCcBlockcontentss = null;
        foreach ($ccBlockcontentss as $ccBlockcontents) {
            $this->addCcBlockcontents($ccBlockcontents);
        }

        $this->collCcBlockcontentss = $ccBlockcontentss;
        $this->collCcBlockcontentssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcBlockcontents objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcBlockcontents objects.
     * @throws PropelException
     */
    public function countCcBlockcontentss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcBlockcontentssPartial && !$this->isNew();
        if (null === $this->collCcBlockcontentss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcBlockcontentss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcBlockcontentss());
            }
            $query = CcBlockcontentsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcFiles($this)
                ->count($con);
        }

        return count($this->collCcBlockcontentss);
    }

    /**
     * Method called to associate a CcBlockcontents object to this object
     * through the CcBlockcontents foreign key attribute.
     *
     * @param    CcBlockcontents $l CcBlockcontents
     * @return CcFiles The current object (for fluent API support)
     */
    public function addCcBlockcontents(CcBlockcontents $l)
    {
        if ($this->collCcBlockcontentss === null) {
            $this->initCcBlockcontentss();
            $this->collCcBlockcontentssPartial = true;
        }

        if (!in_array($l, $this->collCcBlockcontentss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcBlockcontents($l);

            if ($this->ccBlockcontentssScheduledForDeletion and $this->ccBlockcontentssScheduledForDeletion->contains($l)) {
                $this->ccBlockcontentssScheduledForDeletion->remove($this->ccBlockcontentssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcBlockcontents $ccBlockcontents The ccBlockcontents object to add.
     */
    protected function doAddCcBlockcontents($ccBlockcontents)
    {
        $this->collCcBlockcontentss[]= $ccBlockcontents;
        $ccBlockcontents->setCcFiles($this);
    }

    /**
     * @param	CcBlockcontents $ccBlockcontents The ccBlockcontents object to remove.
     * @return CcFiles The current object (for fluent API support)
     */
    public function removeCcBlockcontents($ccBlockcontents)
    {
        if ($this->getCcBlockcontentss()->contains($ccBlockcontents)) {
            $this->collCcBlockcontentss->remove($this->collCcBlockcontentss->search($ccBlockcontents));
            if (null === $this->ccBlockcontentssScheduledForDeletion) {
                $this->ccBlockcontentssScheduledForDeletion = clone $this->collCcBlockcontentss;
                $this->ccBlockcontentssScheduledForDeletion->clear();
            }
            $this->ccBlockcontentssScheduledForDeletion[]= $ccBlockcontents;
            $ccBlockcontents->setCcFiles(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related CcBlockcontentss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcBlockcontents[] List of CcBlockcontents objects
     */
    public function getCcBlockcontentssJoinCcBlock($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcBlockcontentsQuery::create(null, $criteria);
        $query->joinWith('CcBlock', $join_behavior);

        return $this->getCcBlockcontentss($query, $con);
    }

    /**
     * Clears out the collCcSchedules collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcFiles The current object (for fluent API support)
     * @see        addCcSchedules()
     */
    public function clearCcSchedules()
    {
        $this->collCcSchedules = null; // important to set this to null since that means it is uninitialized
        $this->collCcSchedulesPartial = null;

        return $this;
    }

    /**
     * reset is the collCcSchedules collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcSchedules($v = true)
    {
        $this->collCcSchedulesPartial = $v;
    }

    /**
     * Initializes the collCcSchedules collection.
     *
     * By default this just sets the collCcSchedules collection to an empty array (like clearcollCcSchedules());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcSchedules($overrideExisting = true)
    {
        if (null !== $this->collCcSchedules && !$overrideExisting) {
            return;
        }
        $this->collCcSchedules = new PropelObjectCollection();
        $this->collCcSchedules->setModel('CcSchedule');
    }

    /**
     * Gets an array of CcSchedule objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcFiles is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcSchedule[] List of CcSchedule objects
     * @throws PropelException
     */
    public function getCcSchedules($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcSchedulesPartial && !$this->isNew();
        if (null === $this->collCcSchedules || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcSchedules) {
                // return empty collection
                $this->initCcSchedules();
            } else {
                $collCcSchedules = CcScheduleQuery::create(null, $criteria)
                    ->filterByCcFiles($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcSchedulesPartial && count($collCcSchedules)) {
                      $this->initCcSchedules(false);

                      foreach ($collCcSchedules as $obj) {
                        if (false == $this->collCcSchedules->contains($obj)) {
                          $this->collCcSchedules->append($obj);
                        }
                      }

                      $this->collCcSchedulesPartial = true;
                    }

                    $collCcSchedules->getInternalIterator()->rewind();

                    return $collCcSchedules;
                }

                if ($partial && $this->collCcSchedules) {
                    foreach ($this->collCcSchedules as $obj) {
                        if ($obj->isNew()) {
                            $collCcSchedules[] = $obj;
                        }
                    }
                }

                $this->collCcSchedules = $collCcSchedules;
                $this->collCcSchedulesPartial = false;
            }
        }

        return $this->collCcSchedules;
    }

    /**
     * Sets a collection of CcSchedule objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccSchedules A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcFiles The current object (for fluent API support)
     */
    public function setCcSchedules(PropelCollection $ccSchedules, PropelPDO $con = null)
    {
        $ccSchedulesToDelete = $this->getCcSchedules(new Criteria(), $con)->diff($ccSchedules);


        $this->ccSchedulesScheduledForDeletion = $ccSchedulesToDelete;

        foreach ($ccSchedulesToDelete as $ccScheduleRemoved) {
            $ccScheduleRemoved->setCcFiles(null);
        }

        $this->collCcSchedules = null;
        foreach ($ccSchedules as $ccSchedule) {
            $this->addCcSchedule($ccSchedule);
        }

        $this->collCcSchedules = $ccSchedules;
        $this->collCcSchedulesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcSchedule objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcSchedule objects.
     * @throws PropelException
     */
    public function countCcSchedules(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcSchedulesPartial && !$this->isNew();
        if (null === $this->collCcSchedules || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcSchedules) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcSchedules());
            }
            $query = CcScheduleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcFiles($this)
                ->count($con);
        }

        return count($this->collCcSchedules);
    }

    /**
     * Method called to associate a CcSchedule object to this object
     * through the CcSchedule foreign key attribute.
     *
     * @param    CcSchedule $l CcSchedule
     * @return CcFiles The current object (for fluent API support)
     */
    public function addCcSchedule(CcSchedule $l)
    {
        if ($this->collCcSchedules === null) {
            $this->initCcSchedules();
            $this->collCcSchedulesPartial = true;
        }

        if (!in_array($l, $this->collCcSchedules->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcSchedule($l);

            if ($this->ccSchedulesScheduledForDeletion and $this->ccSchedulesScheduledForDeletion->contains($l)) {
                $this->ccSchedulesScheduledForDeletion->remove($this->ccSchedulesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcSchedule $ccSchedule The ccSchedule object to add.
     */
    protected function doAddCcSchedule($ccSchedule)
    {
        $this->collCcSchedules[]= $ccSchedule;
        $ccSchedule->setCcFiles($this);
    }

    /**
     * @param	CcSchedule $ccSchedule The ccSchedule object to remove.
     * @return CcFiles The current object (for fluent API support)
     */
    public function removeCcSchedule($ccSchedule)
    {
        if ($this->getCcSchedules()->contains($ccSchedule)) {
            $this->collCcSchedules->remove($this->collCcSchedules->search($ccSchedule));
            if (null === $this->ccSchedulesScheduledForDeletion) {
                $this->ccSchedulesScheduledForDeletion = clone $this->collCcSchedules;
                $this->ccSchedulesScheduledForDeletion->clear();
            }
            $this->ccSchedulesScheduledForDeletion[]= $ccSchedule;
            $ccSchedule->setCcFiles(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcSchedule[] List of CcSchedule objects
     */
    public function getCcSchedulesJoinCcShowInstances($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcScheduleQuery::create(null, $criteria);
        $query->joinWith('CcShowInstances', $join_behavior);

        return $this->getCcSchedules($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcSchedule[] List of CcSchedule objects
     */
    public function getCcSchedulesJoinCcWebstream($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcScheduleQuery::create(null, $criteria);
        $query->joinWith('CcWebstream', $join_behavior);

        return $this->getCcSchedules($query, $con);
    }

    /**
     * Clears out the collCcPlayoutHistorys collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcFiles The current object (for fluent API support)
     * @see        addCcPlayoutHistorys()
     */
    public function clearCcPlayoutHistorys()
    {
        $this->collCcPlayoutHistorys = null; // important to set this to null since that means it is uninitialized
        $this->collCcPlayoutHistorysPartial = null;

        return $this;
    }

    /**
     * reset is the collCcPlayoutHistorys collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcPlayoutHistorys($v = true)
    {
        $this->collCcPlayoutHistorysPartial = $v;
    }

    /**
     * Initializes the collCcPlayoutHistorys collection.
     *
     * By default this just sets the collCcPlayoutHistorys collection to an empty array (like clearcollCcPlayoutHistorys());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcPlayoutHistorys($overrideExisting = true)
    {
        if (null !== $this->collCcPlayoutHistorys && !$overrideExisting) {
            return;
        }
        $this->collCcPlayoutHistorys = new PropelObjectCollection();
        $this->collCcPlayoutHistorys->setModel('CcPlayoutHistory');
    }

    /**
     * Gets an array of CcPlayoutHistory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcFiles is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcPlayoutHistory[] List of CcPlayoutHistory objects
     * @throws PropelException
     */
    public function getCcPlayoutHistorys($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcPlayoutHistorysPartial && !$this->isNew();
        if (null === $this->collCcPlayoutHistorys || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcPlayoutHistorys) {
                // return empty collection
                $this->initCcPlayoutHistorys();
            } else {
                $collCcPlayoutHistorys = CcPlayoutHistoryQuery::create(null, $criteria)
                    ->filterByCcFiles($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcPlayoutHistorysPartial && count($collCcPlayoutHistorys)) {
                      $this->initCcPlayoutHistorys(false);

                      foreach ($collCcPlayoutHistorys as $obj) {
                        if (false == $this->collCcPlayoutHistorys->contains($obj)) {
                          $this->collCcPlayoutHistorys->append($obj);
                        }
                      }

                      $this->collCcPlayoutHistorysPartial = true;
                    }

                    $collCcPlayoutHistorys->getInternalIterator()->rewind();

                    return $collCcPlayoutHistorys;
                }

                if ($partial && $this->collCcPlayoutHistorys) {
                    foreach ($this->collCcPlayoutHistorys as $obj) {
                        if ($obj->isNew()) {
                            $collCcPlayoutHistorys[] = $obj;
                        }
                    }
                }

                $this->collCcPlayoutHistorys = $collCcPlayoutHistorys;
                $this->collCcPlayoutHistorysPartial = false;
            }
        }

        return $this->collCcPlayoutHistorys;
    }

    /**
     * Sets a collection of CcPlayoutHistory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccPlayoutHistorys A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcFiles The current object (for fluent API support)
     */
    public function setCcPlayoutHistorys(PropelCollection $ccPlayoutHistorys, PropelPDO $con = null)
    {
        $ccPlayoutHistorysToDelete = $this->getCcPlayoutHistorys(new Criteria(), $con)->diff($ccPlayoutHistorys);


        $this->ccPlayoutHistorysScheduledForDeletion = $ccPlayoutHistorysToDelete;

        foreach ($ccPlayoutHistorysToDelete as $ccPlayoutHistoryRemoved) {
            $ccPlayoutHistoryRemoved->setCcFiles(null);
        }

        $this->collCcPlayoutHistorys = null;
        foreach ($ccPlayoutHistorys as $ccPlayoutHistory) {
            $this->addCcPlayoutHistory($ccPlayoutHistory);
        }

        $this->collCcPlayoutHistorys = $ccPlayoutHistorys;
        $this->collCcPlayoutHistorysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcPlayoutHistory objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcPlayoutHistory objects.
     * @throws PropelException
     */
    public function countCcPlayoutHistorys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcPlayoutHistorysPartial && !$this->isNew();
        if (null === $this->collCcPlayoutHistorys || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcPlayoutHistorys) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcPlayoutHistorys());
            }
            $query = CcPlayoutHistoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcFiles($this)
                ->count($con);
        }

        return count($this->collCcPlayoutHistorys);
    }

    /**
     * Method called to associate a CcPlayoutHistory object to this object
     * through the CcPlayoutHistory foreign key attribute.
     *
     * @param    CcPlayoutHistory $l CcPlayoutHistory
     * @return CcFiles The current object (for fluent API support)
     */
    public function addCcPlayoutHistory(CcPlayoutHistory $l)
    {
        if ($this->collCcPlayoutHistorys === null) {
            $this->initCcPlayoutHistorys();
            $this->collCcPlayoutHistorysPartial = true;
        }

        if (!in_array($l, $this->collCcPlayoutHistorys->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcPlayoutHistory($l);

            if ($this->ccPlayoutHistorysScheduledForDeletion and $this->ccPlayoutHistorysScheduledForDeletion->contains($l)) {
                $this->ccPlayoutHistorysScheduledForDeletion->remove($this->ccPlayoutHistorysScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcPlayoutHistory $ccPlayoutHistory The ccPlayoutHistory object to add.
     */
    protected function doAddCcPlayoutHistory($ccPlayoutHistory)
    {
        $this->collCcPlayoutHistorys[]= $ccPlayoutHistory;
        $ccPlayoutHistory->setCcFiles($this);
    }

    /**
     * @param	CcPlayoutHistory $ccPlayoutHistory The ccPlayoutHistory object to remove.
     * @return CcFiles The current object (for fluent API support)
     */
    public function removeCcPlayoutHistory($ccPlayoutHistory)
    {
        if ($this->getCcPlayoutHistorys()->contains($ccPlayoutHistory)) {
            $this->collCcPlayoutHistorys->remove($this->collCcPlayoutHistorys->search($ccPlayoutHistory));
            if (null === $this->ccPlayoutHistorysScheduledForDeletion) {
                $this->ccPlayoutHistorysScheduledForDeletion = clone $this->collCcPlayoutHistorys;
                $this->ccPlayoutHistorysScheduledForDeletion->clear();
            }
            $this->ccPlayoutHistorysScheduledForDeletion[]= $ccPlayoutHistory;
            $ccPlayoutHistory->setCcFiles(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related CcPlayoutHistorys from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcPlayoutHistory[] List of CcPlayoutHistory objects
     */
    public function getCcPlayoutHistorysJoinCcShowInstances($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcPlayoutHistoryQuery::create(null, $criteria);
        $query->joinWith('CcShowInstances', $join_behavior);

        return $this->getCcPlayoutHistorys($query, $con);
    }

    /**
     * Clears out the collThirdPartyTrackReferencess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcFiles The current object (for fluent API support)
     * @see        addThirdPartyTrackReferencess()
     */
    public function clearThirdPartyTrackReferencess()
    {
        $this->collThirdPartyTrackReferencess = null; // important to set this to null since that means it is uninitialized
        $this->collThirdPartyTrackReferencessPartial = null;

        return $this;
    }

    /**
     * reset is the collThirdPartyTrackReferencess collection loaded partially
     *
     * @return void
     */
    public function resetPartialThirdPartyTrackReferencess($v = true)
    {
        $this->collThirdPartyTrackReferencessPartial = $v;
    }

    /**
     * Initializes the collThirdPartyTrackReferencess collection.
     *
     * By default this just sets the collThirdPartyTrackReferencess collection to an empty array (like clearcollThirdPartyTrackReferencess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initThirdPartyTrackReferencess($overrideExisting = true)
    {
        if (null !== $this->collThirdPartyTrackReferencess && !$overrideExisting) {
            return;
        }
        $this->collThirdPartyTrackReferencess = new PropelObjectCollection();
        $this->collThirdPartyTrackReferencess->setModel('ThirdPartyTrackReferences');
    }

    /**
     * Gets an array of ThirdPartyTrackReferences objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcFiles is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ThirdPartyTrackReferences[] List of ThirdPartyTrackReferences objects
     * @throws PropelException
     */
    public function getThirdPartyTrackReferencess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collThirdPartyTrackReferencessPartial && !$this->isNew();
        if (null === $this->collThirdPartyTrackReferencess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collThirdPartyTrackReferencess) {
                // return empty collection
                $this->initThirdPartyTrackReferencess();
            } else {
                $collThirdPartyTrackReferencess = ThirdPartyTrackReferencesQuery::create(null, $criteria)
                    ->filterByCcFiles($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collThirdPartyTrackReferencessPartial && count($collThirdPartyTrackReferencess)) {
                      $this->initThirdPartyTrackReferencess(false);

                      foreach ($collThirdPartyTrackReferencess as $obj) {
                        if (false == $this->collThirdPartyTrackReferencess->contains($obj)) {
                          $this->collThirdPartyTrackReferencess->append($obj);
                        }
                      }

                      $this->collThirdPartyTrackReferencessPartial = true;
                    }

                    $collThirdPartyTrackReferencess->getInternalIterator()->rewind();

                    return $collThirdPartyTrackReferencess;
                }

                if ($partial && $this->collThirdPartyTrackReferencess) {
                    foreach ($this->collThirdPartyTrackReferencess as $obj) {
                        if ($obj->isNew()) {
                            $collThirdPartyTrackReferencess[] = $obj;
                        }
                    }
                }

                $this->collThirdPartyTrackReferencess = $collThirdPartyTrackReferencess;
                $this->collThirdPartyTrackReferencessPartial = false;
            }
        }

        return $this->collThirdPartyTrackReferencess;
    }

    /**
     * Sets a collection of ThirdPartyTrackReferences objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $thirdPartyTrackReferencess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcFiles The current object (for fluent API support)
     */
    public function setThirdPartyTrackReferencess(PropelCollection $thirdPartyTrackReferencess, PropelPDO $con = null)
    {
        $thirdPartyTrackReferencessToDelete = $this->getThirdPartyTrackReferencess(new Criteria(), $con)->diff($thirdPartyTrackReferencess);


        $this->thirdPartyTrackReferencessScheduledForDeletion = $thirdPartyTrackReferencessToDelete;

        foreach ($thirdPartyTrackReferencessToDelete as $thirdPartyTrackReferencesRemoved) {
            $thirdPartyTrackReferencesRemoved->setCcFiles(null);
        }

        $this->collThirdPartyTrackReferencess = null;
        foreach ($thirdPartyTrackReferencess as $thirdPartyTrackReferences) {
            $this->addThirdPartyTrackReferences($thirdPartyTrackReferences);
        }

        $this->collThirdPartyTrackReferencess = $thirdPartyTrackReferencess;
        $this->collThirdPartyTrackReferencessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ThirdPartyTrackReferences objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ThirdPartyTrackReferences objects.
     * @throws PropelException
     */
    public function countThirdPartyTrackReferencess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collThirdPartyTrackReferencessPartial && !$this->isNew();
        if (null === $this->collThirdPartyTrackReferencess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collThirdPartyTrackReferencess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getThirdPartyTrackReferencess());
            }
            $query = ThirdPartyTrackReferencesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcFiles($this)
                ->count($con);
        }

        return count($this->collThirdPartyTrackReferencess);
    }

    /**
     * Method called to associate a ThirdPartyTrackReferences object to this object
     * through the ThirdPartyTrackReferences foreign key attribute.
     *
     * @param    ThirdPartyTrackReferences $l ThirdPartyTrackReferences
     * @return CcFiles The current object (for fluent API support)
     */
    public function addThirdPartyTrackReferences(ThirdPartyTrackReferences $l)
    {
        if ($this->collThirdPartyTrackReferencess === null) {
            $this->initThirdPartyTrackReferencess();
            $this->collThirdPartyTrackReferencessPartial = true;
        }

        if (!in_array($l, $this->collThirdPartyTrackReferencess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddThirdPartyTrackReferences($l);

            if ($this->thirdPartyTrackReferencessScheduledForDeletion and $this->thirdPartyTrackReferencessScheduledForDeletion->contains($l)) {
                $this->thirdPartyTrackReferencessScheduledForDeletion->remove($this->thirdPartyTrackReferencessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	ThirdPartyTrackReferences $thirdPartyTrackReferences The thirdPartyTrackReferences object to add.
     */
    protected function doAddThirdPartyTrackReferences($thirdPartyTrackReferences)
    {
        $this->collThirdPartyTrackReferencess[]= $thirdPartyTrackReferences;
        $thirdPartyTrackReferences->setCcFiles($this);
    }

    /**
     * @param	ThirdPartyTrackReferences $thirdPartyTrackReferences The thirdPartyTrackReferences object to remove.
     * @return CcFiles The current object (for fluent API support)
     */
    public function removeThirdPartyTrackReferences($thirdPartyTrackReferences)
    {
        if ($this->getThirdPartyTrackReferencess()->contains($thirdPartyTrackReferences)) {
            $this->collThirdPartyTrackReferencess->remove($this->collThirdPartyTrackReferencess->search($thirdPartyTrackReferences));
            if (null === $this->thirdPartyTrackReferencessScheduledForDeletion) {
                $this->thirdPartyTrackReferencessScheduledForDeletion = clone $this->collThirdPartyTrackReferencess;
                $this->thirdPartyTrackReferencessScheduledForDeletion->clear();
            }
            $this->thirdPartyTrackReferencessScheduledForDeletion[]= $thirdPartyTrackReferences;
            $thirdPartyTrackReferences->setCcFiles(null);
        }

        return $this;
    }

    /**
     * Clears out the collPodcastEpisodess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcFiles The current object (for fluent API support)
     * @see        addPodcastEpisodess()
     */
    public function clearPodcastEpisodess()
    {
        $this->collPodcastEpisodess = null; // important to set this to null since that means it is uninitialized
        $this->collPodcastEpisodessPartial = null;

        return $this;
    }

    /**
     * reset is the collPodcastEpisodess collection loaded partially
     *
     * @return void
     */
    public function resetPartialPodcastEpisodess($v = true)
    {
        $this->collPodcastEpisodessPartial = $v;
    }

    /**
     * Initializes the collPodcastEpisodess collection.
     *
     * By default this just sets the collPodcastEpisodess collection to an empty array (like clearcollPodcastEpisodess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPodcastEpisodess($overrideExisting = true)
    {
        if (null !== $this->collPodcastEpisodess && !$overrideExisting) {
            return;
        }
        $this->collPodcastEpisodess = new PropelObjectCollection();
        $this->collPodcastEpisodess->setModel('PodcastEpisodes');
    }

    /**
     * Gets an array of PodcastEpisodes objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcFiles is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|PodcastEpisodes[] List of PodcastEpisodes objects
     * @throws PropelException
     */
    public function getPodcastEpisodess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPodcastEpisodessPartial && !$this->isNew();
        if (null === $this->collPodcastEpisodess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPodcastEpisodess) {
                // return empty collection
                $this->initPodcastEpisodess();
            } else {
                $collPodcastEpisodess = PodcastEpisodesQuery::create(null, $criteria)
                    ->filterByCcFiles($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPodcastEpisodessPartial && count($collPodcastEpisodess)) {
                      $this->initPodcastEpisodess(false);

                      foreach ($collPodcastEpisodess as $obj) {
                        if (false == $this->collPodcastEpisodess->contains($obj)) {
                          $this->collPodcastEpisodess->append($obj);
                        }
                      }

                      $this->collPodcastEpisodessPartial = true;
                    }

                    $collPodcastEpisodess->getInternalIterator()->rewind();

                    return $collPodcastEpisodess;
                }

                if ($partial && $this->collPodcastEpisodess) {
                    foreach ($this->collPodcastEpisodess as $obj) {
                        if ($obj->isNew()) {
                            $collPodcastEpisodess[] = $obj;
                        }
                    }
                }

                $this->collPodcastEpisodess = $collPodcastEpisodess;
                $this->collPodcastEpisodessPartial = false;
            }
        }

        return $this->collPodcastEpisodess;
    }

    /**
     * Sets a collection of PodcastEpisodes objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $podcastEpisodess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcFiles The current object (for fluent API support)
     */
    public function setPodcastEpisodess(PropelCollection $podcastEpisodess, PropelPDO $con = null)
    {
        $podcastEpisodessToDelete = $this->getPodcastEpisodess(new Criteria(), $con)->diff($podcastEpisodess);


        $this->podcastEpisodessScheduledForDeletion = $podcastEpisodessToDelete;

        foreach ($podcastEpisodessToDelete as $podcastEpisodesRemoved) {
            $podcastEpisodesRemoved->setCcFiles(null);
        }

        $this->collPodcastEpisodess = null;
        foreach ($podcastEpisodess as $podcastEpisodes) {
            $this->addPodcastEpisodes($podcastEpisodes);
        }

        $this->collPodcastEpisodess = $podcastEpisodess;
        $this->collPodcastEpisodessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PodcastEpisodes objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related PodcastEpisodes objects.
     * @throws PropelException
     */
    public function countPodcastEpisodess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPodcastEpisodessPartial && !$this->isNew();
        if (null === $this->collPodcastEpisodess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPodcastEpisodess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPodcastEpisodess());
            }
            $query = PodcastEpisodesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcFiles($this)
                ->count($con);
        }

        return count($this->collPodcastEpisodess);
    }

    /**
     * Method called to associate a PodcastEpisodes object to this object
     * through the PodcastEpisodes foreign key attribute.
     *
     * @param    PodcastEpisodes $l PodcastEpisodes
     * @return CcFiles The current object (for fluent API support)
     */
    public function addPodcastEpisodes(PodcastEpisodes $l)
    {
        if ($this->collPodcastEpisodess === null) {
            $this->initPodcastEpisodess();
            $this->collPodcastEpisodessPartial = true;
        }

        if (!in_array($l, $this->collPodcastEpisodess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPodcastEpisodes($l);

            if ($this->podcastEpisodessScheduledForDeletion and $this->podcastEpisodessScheduledForDeletion->contains($l)) {
                $this->podcastEpisodessScheduledForDeletion->remove($this->podcastEpisodessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	PodcastEpisodes $podcastEpisodes The podcastEpisodes object to add.
     */
    protected function doAddPodcastEpisodes($podcastEpisodes)
    {
        $this->collPodcastEpisodess[]= $podcastEpisodes;
        $podcastEpisodes->setCcFiles($this);
    }

    /**
     * @param	PodcastEpisodes $podcastEpisodes The podcastEpisodes object to remove.
     * @return CcFiles The current object (for fluent API support)
     */
    public function removePodcastEpisodes($podcastEpisodes)
    {
        if ($this->getPodcastEpisodess()->contains($podcastEpisodes)) {
            $this->collPodcastEpisodess->remove($this->collPodcastEpisodess->search($podcastEpisodes));
            if (null === $this->podcastEpisodessScheduledForDeletion) {
                $this->podcastEpisodessScheduledForDeletion = clone $this->collPodcastEpisodess;
                $this->podcastEpisodessScheduledForDeletion->clear();
            }
            $this->podcastEpisodessScheduledForDeletion[]= $podcastEpisodes;
            $podcastEpisodes->setCcFiles(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcFiles is new, it will return
     * an empty collection; or if this CcFiles has previously
     * been saved, it will retrieve related PodcastEpisodess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcFiles.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PodcastEpisodes[] List of PodcastEpisodes objects
     */
    public function getPodcastEpisodessJoinPodcast($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PodcastEpisodesQuery::create(null, $criteria);
        $query->joinWith('Podcast', $join_behavior);

        return $this->getPodcastEpisodess($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->mime = null;
        $this->ftype = null;
        $this->filepath = null;
        $this->import_status = null;
        $this->currentlyaccessing = null;
        $this->editedby = null;
        $this->mtime = null;
        $this->utime = null;
        $this->lptime = null;
        $this->md5 = null;
        $this->track_title = null;
        $this->artist_name = null;
        $this->bit_rate = null;
        $this->sample_rate = null;
        $this->format = null;
        $this->length = null;
        $this->album_title = null;
        $this->genre = null;
        $this->comments = null;
        $this->year = null;
        $this->track_number = null;
        $this->channels = null;
        $this->url = null;
        $this->bpm = null;
        $this->rating = null;
        $this->encoded_by = null;
        $this->disc_number = null;
        $this->mood = null;
        $this->label = null;
        $this->composer = null;
        $this->encoder = null;
        $this->checksum = null;
        $this->lyrics = null;
        $this->orchestra = null;
        $this->conductor = null;
        $this->lyricist = null;
        $this->original_lyricist = null;
        $this->radio_station_name = null;
        $this->info_url = null;
        $this->artist_url = null;
        $this->audio_source_url = null;
        $this->radio_station_url = null;
        $this->buy_this_url = null;
        $this->isrc_number = null;
        $this->catalog_number = null;
        $this->original_artist = null;
        $this->copyright = null;
        $this->report_datetime = null;
        $this->report_location = null;
        $this->report_organization = null;
        $this->subject = null;
        $this->contributor = null;
        $this->language = null;
        $this->file_exists = null;
        $this->replay_gain = null;
        $this->owner_id = null;
        $this->cuein = null;
        $this->cueout = null;
        $this->silan_check = null;
        $this->hidden = null;
        $this->is_scheduled = null;
        $this->is_playlist = null;
        $this->filesize = null;
        $this->description = null;
        $this->artwork = null;
        $this->track_type_id = null;
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
            if ($this->collCcShowInstancess) {
                foreach ($this->collCcShowInstancess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcPlaylistcontentss) {
                foreach ($this->collCcPlaylistcontentss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcBlockcontentss) {
                foreach ($this->collCcBlockcontentss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcSchedules) {
                foreach ($this->collCcSchedules as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcPlayoutHistorys) {
                foreach ($this->collCcPlayoutHistorys as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collThirdPartyTrackReferencess) {
                foreach ($this->collThirdPartyTrackReferencess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPodcastEpisodess) {
                foreach ($this->collPodcastEpisodess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aFkOwner instanceof Persistent) {
              $this->aFkOwner->clearAllReferences($deep);
            }
            if ($this->aCcSubjsRelatedByDbEditedby instanceof Persistent) {
              $this->aCcSubjsRelatedByDbEditedby->clearAllReferences($deep);
            }
            if ($this->aCcTracktypes instanceof Persistent) {
              $this->aCcTracktypes->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcShowInstancess instanceof PropelCollection) {
            $this->collCcShowInstancess->clearIterator();
        }
        $this->collCcShowInstancess = null;
        if ($this->collCcPlaylistcontentss instanceof PropelCollection) {
            $this->collCcPlaylistcontentss->clearIterator();
        }
        $this->collCcPlaylistcontentss = null;
        if ($this->collCcBlockcontentss instanceof PropelCollection) {
            $this->collCcBlockcontentss->clearIterator();
        }
        $this->collCcBlockcontentss = null;
        if ($this->collCcSchedules instanceof PropelCollection) {
            $this->collCcSchedules->clearIterator();
        }
        $this->collCcSchedules = null;
        if ($this->collCcPlayoutHistorys instanceof PropelCollection) {
            $this->collCcPlayoutHistorys->clearIterator();
        }
        $this->collCcPlayoutHistorys = null;
        if ($this->collThirdPartyTrackReferencess instanceof PropelCollection) {
            $this->collThirdPartyTrackReferencess->clearIterator();
        }
        $this->collThirdPartyTrackReferencess = null;
        if ($this->collPodcastEpisodess instanceof PropelCollection) {
            $this->collPodcastEpisodess->clearIterator();
        }
        $this->collPodcastEpisodess = null;
        $this->aFkOwner = null;
        $this->aCcSubjsRelatedByDbEditedby = null;
        $this->aCcTracktypes = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcFilesPeer::DEFAULT_STRING_FORMAT);
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

}
