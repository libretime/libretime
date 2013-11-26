<?php

namespace Airtime\MediaItem\om;

use \Criteria;
use \Exception;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Airtime\CcMusicDirs;
use Airtime\CcSubjs;
use Airtime\MediaItem;
use Airtime\MediaItemQuery;
use Airtime\MediaItem\AudioFile;
use Airtime\MediaItem\AudioFilePeer;
use Airtime\MediaItem\AudioFileQuery;

/**
 * Base class that represents a query for the 'media_audiofile' table.
 *
 *
 *
 * @method AudioFileQuery orderByDirectory($order = Criteria::ASC) Order by the directory column
 * @method AudioFileQuery orderByFilepath($order = Criteria::ASC) Order by the filepath column
 * @method AudioFileQuery orderByMd5($order = Criteria::ASC) Order by the md5 column
 * @method AudioFileQuery orderByTrackTitle($order = Criteria::ASC) Order by the track_title column
 * @method AudioFileQuery orderByArtistName($order = Criteria::ASC) Order by the artist_name column
 * @method AudioFileQuery orderByBitRate($order = Criteria::ASC) Order by the bit_rate column
 * @method AudioFileQuery orderBySampleRate($order = Criteria::ASC) Order by the sample_rate column
 * @method AudioFileQuery orderByAlbumTitle($order = Criteria::ASC) Order by the album_title column
 * @method AudioFileQuery orderByGenre($order = Criteria::ASC) Order by the genre column
 * @method AudioFileQuery orderByComments($order = Criteria::ASC) Order by the comments column
 * @method AudioFileQuery orderByYear($order = Criteria::ASC) Order by the year column
 * @method AudioFileQuery orderByTrackNumber($order = Criteria::ASC) Order by the track_number column
 * @method AudioFileQuery orderByChannels($order = Criteria::ASC) Order by the channels column
 * @method AudioFileQuery orderByBpm($order = Criteria::ASC) Order by the bpm column
 * @method AudioFileQuery orderByEncodedBy($order = Criteria::ASC) Order by the encoded_by column
 * @method AudioFileQuery orderByMood($order = Criteria::ASC) Order by the mood column
 * @method AudioFileQuery orderByLabel($order = Criteria::ASC) Order by the label column
 * @method AudioFileQuery orderByComposer($order = Criteria::ASC) Order by the composer column
 * @method AudioFileQuery orderByCopyright($order = Criteria::ASC) Order by the copyright column
 * @method AudioFileQuery orderByConductor($order = Criteria::ASC) Order by the conductor column
 * @method AudioFileQuery orderByIsrcNumber($order = Criteria::ASC) Order by the isrc_number column
 * @method AudioFileQuery orderByInfoUrl($order = Criteria::ASC) Order by the info_url column
 * @method AudioFileQuery orderByLanguage($order = Criteria::ASC) Order by the language column
 * @method AudioFileQuery orderByReplayGain($order = Criteria::ASC) Order by the replay_gain column
 * @method AudioFileQuery orderByCuein($order = Criteria::ASC) Order by the cuein column
 * @method AudioFileQuery orderByCueout($order = Criteria::ASC) Order by the cueout column
 * @method AudioFileQuery orderByIsSilanChecked($order = Criteria::ASC) Order by the silan_check column
 * @method AudioFileQuery orderByFileExists($order = Criteria::ASC) Order by the file_exists column
 * @method AudioFileQuery orderByFileHidden($order = Criteria::ASC) Order by the hidden column
 * @method AudioFileQuery orderByIsScheduled($order = Criteria::ASC) Order by the is_scheduled column
 * @method AudioFileQuery orderByIsPlaylist($order = Criteria::ASC) Order by the is_playlist column
 * @method AudioFileQuery orderById($order = Criteria::ASC) Order by the id column
 * @method AudioFileQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method AudioFileQuery orderByOwnerId($order = Criteria::ASC) Order by the owner_id column
 * @method AudioFileQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method AudioFileQuery orderByLastPlayedTime($order = Criteria::ASC) Order by the last_played column
 * @method AudioFileQuery orderByPlayCount($order = Criteria::ASC) Order by the play_count column
 * @method AudioFileQuery orderByLength($order = Criteria::ASC) Order by the length column
 * @method AudioFileQuery orderByMime($order = Criteria::ASC) Order by the mime column
 * @method AudioFileQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method AudioFileQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method AudioFileQuery groupByDirectory() Group by the directory column
 * @method AudioFileQuery groupByFilepath() Group by the filepath column
 * @method AudioFileQuery groupByMd5() Group by the md5 column
 * @method AudioFileQuery groupByTrackTitle() Group by the track_title column
 * @method AudioFileQuery groupByArtistName() Group by the artist_name column
 * @method AudioFileQuery groupByBitRate() Group by the bit_rate column
 * @method AudioFileQuery groupBySampleRate() Group by the sample_rate column
 * @method AudioFileQuery groupByAlbumTitle() Group by the album_title column
 * @method AudioFileQuery groupByGenre() Group by the genre column
 * @method AudioFileQuery groupByComments() Group by the comments column
 * @method AudioFileQuery groupByYear() Group by the year column
 * @method AudioFileQuery groupByTrackNumber() Group by the track_number column
 * @method AudioFileQuery groupByChannels() Group by the channels column
 * @method AudioFileQuery groupByBpm() Group by the bpm column
 * @method AudioFileQuery groupByEncodedBy() Group by the encoded_by column
 * @method AudioFileQuery groupByMood() Group by the mood column
 * @method AudioFileQuery groupByLabel() Group by the label column
 * @method AudioFileQuery groupByComposer() Group by the composer column
 * @method AudioFileQuery groupByCopyright() Group by the copyright column
 * @method AudioFileQuery groupByConductor() Group by the conductor column
 * @method AudioFileQuery groupByIsrcNumber() Group by the isrc_number column
 * @method AudioFileQuery groupByInfoUrl() Group by the info_url column
 * @method AudioFileQuery groupByLanguage() Group by the language column
 * @method AudioFileQuery groupByReplayGain() Group by the replay_gain column
 * @method AudioFileQuery groupByCuein() Group by the cuein column
 * @method AudioFileQuery groupByCueout() Group by the cueout column
 * @method AudioFileQuery groupByIsSilanChecked() Group by the silan_check column
 * @method AudioFileQuery groupByFileExists() Group by the file_exists column
 * @method AudioFileQuery groupByFileHidden() Group by the hidden column
 * @method AudioFileQuery groupByIsScheduled() Group by the is_scheduled column
 * @method AudioFileQuery groupByIsPlaylist() Group by the is_playlist column
 * @method AudioFileQuery groupById() Group by the id column
 * @method AudioFileQuery groupByName() Group by the name column
 * @method AudioFileQuery groupByOwnerId() Group by the owner_id column
 * @method AudioFileQuery groupByDescription() Group by the description column
 * @method AudioFileQuery groupByLastPlayedTime() Group by the last_played column
 * @method AudioFileQuery groupByPlayCount() Group by the play_count column
 * @method AudioFileQuery groupByLength() Group by the length column
 * @method AudioFileQuery groupByMime() Group by the mime column
 * @method AudioFileQuery groupByCreatedAt() Group by the created_at column
 * @method AudioFileQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method AudioFileQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AudioFileQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AudioFileQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AudioFileQuery leftJoinCcMusicDirs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcMusicDirs relation
 * @method AudioFileQuery rightJoinCcMusicDirs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcMusicDirs relation
 * @method AudioFileQuery innerJoinCcMusicDirs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcMusicDirs relation
 *
 * @method AudioFileQuery leftJoinMediaItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the MediaItem relation
 * @method AudioFileQuery rightJoinMediaItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MediaItem relation
 * @method AudioFileQuery innerJoinMediaItem($relationAlias = null) Adds a INNER JOIN clause to the query using the MediaItem relation
 *
 * @method AudioFileQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method AudioFileQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method AudioFileQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method AudioFile findOne(PropelPDO $con = null) Return the first AudioFile matching the query
 * @method AudioFile findOneOrCreate(PropelPDO $con = null) Return the first AudioFile matching the query, or a new AudioFile object populated from the query conditions when no match is found
 *
 * @method AudioFile findOneByDirectory(int $directory) Return the first AudioFile filtered by the directory column
 * @method AudioFile findOneByFilepath(string $filepath) Return the first AudioFile filtered by the filepath column
 * @method AudioFile findOneByMd5(string $md5) Return the first AudioFile filtered by the md5 column
 * @method AudioFile findOneByTrackTitle(string $track_title) Return the first AudioFile filtered by the track_title column
 * @method AudioFile findOneByArtistName(string $artist_name) Return the first AudioFile filtered by the artist_name column
 * @method AudioFile findOneByBitRate(int $bit_rate) Return the first AudioFile filtered by the bit_rate column
 * @method AudioFile findOneBySampleRate(int $sample_rate) Return the first AudioFile filtered by the sample_rate column
 * @method AudioFile findOneByAlbumTitle(string $album_title) Return the first AudioFile filtered by the album_title column
 * @method AudioFile findOneByGenre(string $genre) Return the first AudioFile filtered by the genre column
 * @method AudioFile findOneByComments(string $comments) Return the first AudioFile filtered by the comments column
 * @method AudioFile findOneByYear(string $year) Return the first AudioFile filtered by the year column
 * @method AudioFile findOneByTrackNumber(int $track_number) Return the first AudioFile filtered by the track_number column
 * @method AudioFile findOneByChannels(int $channels) Return the first AudioFile filtered by the channels column
 * @method AudioFile findOneByBpm(int $bpm) Return the first AudioFile filtered by the bpm column
 * @method AudioFile findOneByEncodedBy(string $encoded_by) Return the first AudioFile filtered by the encoded_by column
 * @method AudioFile findOneByMood(string $mood) Return the first AudioFile filtered by the mood column
 * @method AudioFile findOneByLabel(string $label) Return the first AudioFile filtered by the label column
 * @method AudioFile findOneByComposer(string $composer) Return the first AudioFile filtered by the composer column
 * @method AudioFile findOneByCopyright(string $copyright) Return the first AudioFile filtered by the copyright column
 * @method AudioFile findOneByConductor(string $conductor) Return the first AudioFile filtered by the conductor column
 * @method AudioFile findOneByIsrcNumber(string $isrc_number) Return the first AudioFile filtered by the isrc_number column
 * @method AudioFile findOneByInfoUrl(string $info_url) Return the first AudioFile filtered by the info_url column
 * @method AudioFile findOneByLanguage(string $language) Return the first AudioFile filtered by the language column
 * @method AudioFile findOneByReplayGain(string $replay_gain) Return the first AudioFile filtered by the replay_gain column
 * @method AudioFile findOneByCuein(string $cuein) Return the first AudioFile filtered by the cuein column
 * @method AudioFile findOneByCueout(string $cueout) Return the first AudioFile filtered by the cueout column
 * @method AudioFile findOneByIsSilanChecked(boolean $silan_check) Return the first AudioFile filtered by the silan_check column
 * @method AudioFile findOneByFileExists(boolean $file_exists) Return the first AudioFile filtered by the file_exists column
 * @method AudioFile findOneByFileHidden(boolean $hidden) Return the first AudioFile filtered by the hidden column
 * @method AudioFile findOneByIsScheduled(boolean $is_scheduled) Return the first AudioFile filtered by the is_scheduled column
 * @method AudioFile findOneByIsPlaylist(boolean $is_playlist) Return the first AudioFile filtered by the is_playlist column
 * @method AudioFile findOneByName(string $name) Return the first AudioFile filtered by the name column
 * @method AudioFile findOneByOwnerId(int $owner_id) Return the first AudioFile filtered by the owner_id column
 * @method AudioFile findOneByDescription(string $description) Return the first AudioFile filtered by the description column
 * @method AudioFile findOneByLastPlayedTime(string $last_played) Return the first AudioFile filtered by the last_played column
 * @method AudioFile findOneByPlayCount(int $play_count) Return the first AudioFile filtered by the play_count column
 * @method AudioFile findOneByLength(string $length) Return the first AudioFile filtered by the length column
 * @method AudioFile findOneByMime(string $mime) Return the first AudioFile filtered by the mime column
 * @method AudioFile findOneByCreatedAt(string $created_at) Return the first AudioFile filtered by the created_at column
 * @method AudioFile findOneByUpdatedAt(string $updated_at) Return the first AudioFile filtered by the updated_at column
 *
 * @method array findByDirectory(int $directory) Return AudioFile objects filtered by the directory column
 * @method array findByFilepath(string $filepath) Return AudioFile objects filtered by the filepath column
 * @method array findByMd5(string $md5) Return AudioFile objects filtered by the md5 column
 * @method array findByTrackTitle(string $track_title) Return AudioFile objects filtered by the track_title column
 * @method array findByArtistName(string $artist_name) Return AudioFile objects filtered by the artist_name column
 * @method array findByBitRate(int $bit_rate) Return AudioFile objects filtered by the bit_rate column
 * @method array findBySampleRate(int $sample_rate) Return AudioFile objects filtered by the sample_rate column
 * @method array findByAlbumTitle(string $album_title) Return AudioFile objects filtered by the album_title column
 * @method array findByGenre(string $genre) Return AudioFile objects filtered by the genre column
 * @method array findByComments(string $comments) Return AudioFile objects filtered by the comments column
 * @method array findByYear(string $year) Return AudioFile objects filtered by the year column
 * @method array findByTrackNumber(int $track_number) Return AudioFile objects filtered by the track_number column
 * @method array findByChannels(int $channels) Return AudioFile objects filtered by the channels column
 * @method array findByBpm(int $bpm) Return AudioFile objects filtered by the bpm column
 * @method array findByEncodedBy(string $encoded_by) Return AudioFile objects filtered by the encoded_by column
 * @method array findByMood(string $mood) Return AudioFile objects filtered by the mood column
 * @method array findByLabel(string $label) Return AudioFile objects filtered by the label column
 * @method array findByComposer(string $composer) Return AudioFile objects filtered by the composer column
 * @method array findByCopyright(string $copyright) Return AudioFile objects filtered by the copyright column
 * @method array findByConductor(string $conductor) Return AudioFile objects filtered by the conductor column
 * @method array findByIsrcNumber(string $isrc_number) Return AudioFile objects filtered by the isrc_number column
 * @method array findByInfoUrl(string $info_url) Return AudioFile objects filtered by the info_url column
 * @method array findByLanguage(string $language) Return AudioFile objects filtered by the language column
 * @method array findByReplayGain(string $replay_gain) Return AudioFile objects filtered by the replay_gain column
 * @method array findByCuein(string $cuein) Return AudioFile objects filtered by the cuein column
 * @method array findByCueout(string $cueout) Return AudioFile objects filtered by the cueout column
 * @method array findByIsSilanChecked(boolean $silan_check) Return AudioFile objects filtered by the silan_check column
 * @method array findByFileExists(boolean $file_exists) Return AudioFile objects filtered by the file_exists column
 * @method array findByFileHidden(boolean $hidden) Return AudioFile objects filtered by the hidden column
 * @method array findByIsScheduled(boolean $is_scheduled) Return AudioFile objects filtered by the is_scheduled column
 * @method array findByIsPlaylist(boolean $is_playlist) Return AudioFile objects filtered by the is_playlist column
 * @method array findById(int $id) Return AudioFile objects filtered by the id column
 * @method array findByName(string $name) Return AudioFile objects filtered by the name column
 * @method array findByOwnerId(int $owner_id) Return AudioFile objects filtered by the owner_id column
 * @method array findByDescription(string $description) Return AudioFile objects filtered by the description column
 * @method array findByLastPlayedTime(string $last_played) Return AudioFile objects filtered by the last_played column
 * @method array findByPlayCount(int $play_count) Return AudioFile objects filtered by the play_count column
 * @method array findByLength(string $length) Return AudioFile objects filtered by the length column
 * @method array findByMime(string $mime) Return AudioFile objects filtered by the mime column
 * @method array findByCreatedAt(string $created_at) Return AudioFile objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return AudioFile objects filtered by the updated_at column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseAudioFileQuery extends MediaItemQuery
{
    /**
     * Initializes internal state of BaseAudioFileQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'airtime';
        }
        if (null === $modelName) {
            $modelName = 'Airtime\\MediaItem\\AudioFile';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AudioFileQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AudioFileQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AudioFileQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AudioFileQuery) {
            return $criteria;
        }
        $query = new AudioFileQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   AudioFile|AudioFile[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AudioFilePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AudioFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 AudioFile A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 AudioFile A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "directory", "filepath", "md5", "track_title", "artist_name", "bit_rate", "sample_rate", "album_title", "genre", "comments", "year", "track_number", "channels", "bpm", "encoded_by", "mood", "label", "composer", "copyright", "conductor", "isrc_number", "info_url", "language", "replay_gain", "cuein", "cueout", "silan_check", "file_exists", "hidden", "is_scheduled", "is_playlist", "id", "name", "owner_id", "description", "last_played", "play_count", "length", "mime", "created_at", "updated_at" FROM "media_audiofile" WHERE "id" = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new AudioFile();
            $obj->hydrate($row);
            AudioFilePeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return AudioFile|AudioFile[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|AudioFile[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AudioFilePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AudioFilePeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the directory column
     *
     * Example usage:
     * <code>
     * $query->filterByDirectory(1234); // WHERE directory = 1234
     * $query->filterByDirectory(array(12, 34)); // WHERE directory IN (12, 34)
     * $query->filterByDirectory(array('min' => 12)); // WHERE directory >= 12
     * $query->filterByDirectory(array('max' => 12)); // WHERE directory <= 12
     * </code>
     *
     * @see       filterByCcMusicDirs()
     *
     * @param     mixed $directory The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByDirectory($directory = null, $comparison = null)
    {
        if (is_array($directory)) {
            $useMinMax = false;
            if (isset($directory['min'])) {
                $this->addUsingAlias(AudioFilePeer::DIRECTORY, $directory['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($directory['max'])) {
                $this->addUsingAlias(AudioFilePeer::DIRECTORY, $directory['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::DIRECTORY, $directory, $comparison);
    }

    /**
     * Filter the query on the filepath column
     *
     * Example usage:
     * <code>
     * $query->filterByFilepath('fooValue');   // WHERE filepath = 'fooValue'
     * $query->filterByFilepath('%fooValue%'); // WHERE filepath LIKE '%fooValue%'
     * </code>
     *
     * @param     string $filepath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByFilepath($filepath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($filepath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $filepath)) {
                $filepath = str_replace('*', '%', $filepath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::FILEPATH, $filepath, $comparison);
    }

    /**
     * Filter the query on the md5 column
     *
     * Example usage:
     * <code>
     * $query->filterByMd5('fooValue');   // WHERE md5 = 'fooValue'
     * $query->filterByMd5('%fooValue%'); // WHERE md5 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $md5 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByMd5($md5 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($md5)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $md5)) {
                $md5 = str_replace('*', '%', $md5);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::MD5, $md5, $comparison);
    }

    /**
     * Filter the query on the track_title column
     *
     * Example usage:
     * <code>
     * $query->filterByTrackTitle('fooValue');   // WHERE track_title = 'fooValue'
     * $query->filterByTrackTitle('%fooValue%'); // WHERE track_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $trackTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByTrackTitle($trackTitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($trackTitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $trackTitle)) {
                $trackTitle = str_replace('*', '%', $trackTitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::TRACK_TITLE, $trackTitle, $comparison);
    }

    /**
     * Filter the query on the artist_name column
     *
     * Example usage:
     * <code>
     * $query->filterByArtistName('fooValue');   // WHERE artist_name = 'fooValue'
     * $query->filterByArtistName('%fooValue%'); // WHERE artist_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $artistName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByArtistName($artistName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($artistName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $artistName)) {
                $artistName = str_replace('*', '%', $artistName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::ARTIST_NAME, $artistName, $comparison);
    }

    /**
     * Filter the query on the bit_rate column
     *
     * Example usage:
     * <code>
     * $query->filterByBitRate(1234); // WHERE bit_rate = 1234
     * $query->filterByBitRate(array(12, 34)); // WHERE bit_rate IN (12, 34)
     * $query->filterByBitRate(array('min' => 12)); // WHERE bit_rate >= 12
     * $query->filterByBitRate(array('max' => 12)); // WHERE bit_rate <= 12
     * </code>
     *
     * @param     mixed $bitRate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByBitRate($bitRate = null, $comparison = null)
    {
        if (is_array($bitRate)) {
            $useMinMax = false;
            if (isset($bitRate['min'])) {
                $this->addUsingAlias(AudioFilePeer::BIT_RATE, $bitRate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($bitRate['max'])) {
                $this->addUsingAlias(AudioFilePeer::BIT_RATE, $bitRate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::BIT_RATE, $bitRate, $comparison);
    }

    /**
     * Filter the query on the sample_rate column
     *
     * Example usage:
     * <code>
     * $query->filterBySampleRate(1234); // WHERE sample_rate = 1234
     * $query->filterBySampleRate(array(12, 34)); // WHERE sample_rate IN (12, 34)
     * $query->filterBySampleRate(array('min' => 12)); // WHERE sample_rate >= 12
     * $query->filterBySampleRate(array('max' => 12)); // WHERE sample_rate <= 12
     * </code>
     *
     * @param     mixed $sampleRate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterBySampleRate($sampleRate = null, $comparison = null)
    {
        if (is_array($sampleRate)) {
            $useMinMax = false;
            if (isset($sampleRate['min'])) {
                $this->addUsingAlias(AudioFilePeer::SAMPLE_RATE, $sampleRate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sampleRate['max'])) {
                $this->addUsingAlias(AudioFilePeer::SAMPLE_RATE, $sampleRate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::SAMPLE_RATE, $sampleRate, $comparison);
    }

    /**
     * Filter the query on the album_title column
     *
     * Example usage:
     * <code>
     * $query->filterByAlbumTitle('fooValue');   // WHERE album_title = 'fooValue'
     * $query->filterByAlbumTitle('%fooValue%'); // WHERE album_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $albumTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByAlbumTitle($albumTitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($albumTitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $albumTitle)) {
                $albumTitle = str_replace('*', '%', $albumTitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::ALBUM_TITLE, $albumTitle, $comparison);
    }

    /**
     * Filter the query on the genre column
     *
     * Example usage:
     * <code>
     * $query->filterByGenre('fooValue');   // WHERE genre = 'fooValue'
     * $query->filterByGenre('%fooValue%'); // WHERE genre LIKE '%fooValue%'
     * </code>
     *
     * @param     string $genre The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByGenre($genre = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($genre)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $genre)) {
                $genre = str_replace('*', '%', $genre);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::GENRE, $genre, $comparison);
    }

    /**
     * Filter the query on the comments column
     *
     * Example usage:
     * <code>
     * $query->filterByComments('fooValue');   // WHERE comments = 'fooValue'
     * $query->filterByComments('%fooValue%'); // WHERE comments LIKE '%fooValue%'
     * </code>
     *
     * @param     string $comments The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByComments($comments = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($comments)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $comments)) {
                $comments = str_replace('*', '%', $comments);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::COMMENTS, $comments, $comparison);
    }

    /**
     * Filter the query on the year column
     *
     * Example usage:
     * <code>
     * $query->filterByYear('fooValue');   // WHERE year = 'fooValue'
     * $query->filterByYear('%fooValue%'); // WHERE year LIKE '%fooValue%'
     * </code>
     *
     * @param     string $year The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByYear($year = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($year)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $year)) {
                $year = str_replace('*', '%', $year);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::YEAR, $year, $comparison);
    }

    /**
     * Filter the query on the track_number column
     *
     * Example usage:
     * <code>
     * $query->filterByTrackNumber(1234); // WHERE track_number = 1234
     * $query->filterByTrackNumber(array(12, 34)); // WHERE track_number IN (12, 34)
     * $query->filterByTrackNumber(array('min' => 12)); // WHERE track_number >= 12
     * $query->filterByTrackNumber(array('max' => 12)); // WHERE track_number <= 12
     * </code>
     *
     * @param     mixed $trackNumber The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByTrackNumber($trackNumber = null, $comparison = null)
    {
        if (is_array($trackNumber)) {
            $useMinMax = false;
            if (isset($trackNumber['min'])) {
                $this->addUsingAlias(AudioFilePeer::TRACK_NUMBER, $trackNumber['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($trackNumber['max'])) {
                $this->addUsingAlias(AudioFilePeer::TRACK_NUMBER, $trackNumber['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::TRACK_NUMBER, $trackNumber, $comparison);
    }

    /**
     * Filter the query on the channels column
     *
     * Example usage:
     * <code>
     * $query->filterByChannels(1234); // WHERE channels = 1234
     * $query->filterByChannels(array(12, 34)); // WHERE channels IN (12, 34)
     * $query->filterByChannels(array('min' => 12)); // WHERE channels >= 12
     * $query->filterByChannels(array('max' => 12)); // WHERE channels <= 12
     * </code>
     *
     * @param     mixed $channels The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByChannels($channels = null, $comparison = null)
    {
        if (is_array($channels)) {
            $useMinMax = false;
            if (isset($channels['min'])) {
                $this->addUsingAlias(AudioFilePeer::CHANNELS, $channels['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($channels['max'])) {
                $this->addUsingAlias(AudioFilePeer::CHANNELS, $channels['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::CHANNELS, $channels, $comparison);
    }

    /**
     * Filter the query on the bpm column
     *
     * Example usage:
     * <code>
     * $query->filterByBpm(1234); // WHERE bpm = 1234
     * $query->filterByBpm(array(12, 34)); // WHERE bpm IN (12, 34)
     * $query->filterByBpm(array('min' => 12)); // WHERE bpm >= 12
     * $query->filterByBpm(array('max' => 12)); // WHERE bpm <= 12
     * </code>
     *
     * @param     mixed $bpm The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByBpm($bpm = null, $comparison = null)
    {
        if (is_array($bpm)) {
            $useMinMax = false;
            if (isset($bpm['min'])) {
                $this->addUsingAlias(AudioFilePeer::BPM, $bpm['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($bpm['max'])) {
                $this->addUsingAlias(AudioFilePeer::BPM, $bpm['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::BPM, $bpm, $comparison);
    }

    /**
     * Filter the query on the encoded_by column
     *
     * Example usage:
     * <code>
     * $query->filterByEncodedBy('fooValue');   // WHERE encoded_by = 'fooValue'
     * $query->filterByEncodedBy('%fooValue%'); // WHERE encoded_by LIKE '%fooValue%'
     * </code>
     *
     * @param     string $encodedBy The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByEncodedBy($encodedBy = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($encodedBy)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $encodedBy)) {
                $encodedBy = str_replace('*', '%', $encodedBy);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::ENCODED_BY, $encodedBy, $comparison);
    }

    /**
     * Filter the query on the mood column
     *
     * Example usage:
     * <code>
     * $query->filterByMood('fooValue');   // WHERE mood = 'fooValue'
     * $query->filterByMood('%fooValue%'); // WHERE mood LIKE '%fooValue%'
     * </code>
     *
     * @param     string $mood The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByMood($mood = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($mood)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $mood)) {
                $mood = str_replace('*', '%', $mood);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::MOOD, $mood, $comparison);
    }

    /**
     * Filter the query on the label column
     *
     * Example usage:
     * <code>
     * $query->filterByLabel('fooValue');   // WHERE label = 'fooValue'
     * $query->filterByLabel('%fooValue%'); // WHERE label LIKE '%fooValue%'
     * </code>
     *
     * @param     string $label The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByLabel($label = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($label)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $label)) {
                $label = str_replace('*', '%', $label);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::LABEL, $label, $comparison);
    }

    /**
     * Filter the query on the composer column
     *
     * Example usage:
     * <code>
     * $query->filterByComposer('fooValue');   // WHERE composer = 'fooValue'
     * $query->filterByComposer('%fooValue%'); // WHERE composer LIKE '%fooValue%'
     * </code>
     *
     * @param     string $composer The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByComposer($composer = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($composer)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $composer)) {
                $composer = str_replace('*', '%', $composer);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::COMPOSER, $composer, $comparison);
    }

    /**
     * Filter the query on the copyright column
     *
     * Example usage:
     * <code>
     * $query->filterByCopyright('fooValue');   // WHERE copyright = 'fooValue'
     * $query->filterByCopyright('%fooValue%'); // WHERE copyright LIKE '%fooValue%'
     * </code>
     *
     * @param     string $copyright The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByCopyright($copyright = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($copyright)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $copyright)) {
                $copyright = str_replace('*', '%', $copyright);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::COPYRIGHT, $copyright, $comparison);
    }

    /**
     * Filter the query on the conductor column
     *
     * Example usage:
     * <code>
     * $query->filterByConductor('fooValue');   // WHERE conductor = 'fooValue'
     * $query->filterByConductor('%fooValue%'); // WHERE conductor LIKE '%fooValue%'
     * </code>
     *
     * @param     string $conductor The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByConductor($conductor = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($conductor)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $conductor)) {
                $conductor = str_replace('*', '%', $conductor);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::CONDUCTOR, $conductor, $comparison);
    }

    /**
     * Filter the query on the isrc_number column
     *
     * Example usage:
     * <code>
     * $query->filterByIsrcNumber('fooValue');   // WHERE isrc_number = 'fooValue'
     * $query->filterByIsrcNumber('%fooValue%'); // WHERE isrc_number LIKE '%fooValue%'
     * </code>
     *
     * @param     string $isrcNumber The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByIsrcNumber($isrcNumber = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($isrcNumber)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $isrcNumber)) {
                $isrcNumber = str_replace('*', '%', $isrcNumber);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::ISRC_NUMBER, $isrcNumber, $comparison);
    }

    /**
     * Filter the query on the info_url column
     *
     * Example usage:
     * <code>
     * $query->filterByInfoUrl('fooValue');   // WHERE info_url = 'fooValue'
     * $query->filterByInfoUrl('%fooValue%'); // WHERE info_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $infoUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByInfoUrl($infoUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($infoUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $infoUrl)) {
                $infoUrl = str_replace('*', '%', $infoUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::INFO_URL, $infoUrl, $comparison);
    }

    /**
     * Filter the query on the language column
     *
     * Example usage:
     * <code>
     * $query->filterByLanguage('fooValue');   // WHERE language = 'fooValue'
     * $query->filterByLanguage('%fooValue%'); // WHERE language LIKE '%fooValue%'
     * </code>
     *
     * @param     string $language The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByLanguage($language = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($language)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $language)) {
                $language = str_replace('*', '%', $language);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::LANGUAGE, $language, $comparison);
    }

    /**
     * Filter the query on the replay_gain column
     *
     * Example usage:
     * <code>
     * $query->filterByReplayGain(1234); // WHERE replay_gain = 1234
     * $query->filterByReplayGain(array(12, 34)); // WHERE replay_gain IN (12, 34)
     * $query->filterByReplayGain(array('min' => 12)); // WHERE replay_gain >= 12
     * $query->filterByReplayGain(array('max' => 12)); // WHERE replay_gain <= 12
     * </code>
     *
     * @param     mixed $replayGain The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByReplayGain($replayGain = null, $comparison = null)
    {
        if (is_array($replayGain)) {
            $useMinMax = false;
            if (isset($replayGain['min'])) {
                $this->addUsingAlias(AudioFilePeer::REPLAY_GAIN, $replayGain['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($replayGain['max'])) {
                $this->addUsingAlias(AudioFilePeer::REPLAY_GAIN, $replayGain['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::REPLAY_GAIN, $replayGain, $comparison);
    }

    /**
     * Filter the query on the cuein column
     *
     * Example usage:
     * <code>
     * $query->filterByCuein('fooValue');   // WHERE cuein = 'fooValue'
     * $query->filterByCuein('%fooValue%'); // WHERE cuein LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cuein The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByCuein($cuein = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cuein)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cuein)) {
                $cuein = str_replace('*', '%', $cuein);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::CUEIN, $cuein, $comparison);
    }

    /**
     * Filter the query on the cueout column
     *
     * Example usage:
     * <code>
     * $query->filterByCueout('fooValue');   // WHERE cueout = 'fooValue'
     * $query->filterByCueout('%fooValue%'); // WHERE cueout LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cueout The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByCueout($cueout = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cueout)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cueout)) {
                $cueout = str_replace('*', '%', $cueout);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::CUEOUT, $cueout, $comparison);
    }

    /**
     * Filter the query on the silan_check column
     *
     * Example usage:
     * <code>
     * $query->filterByIsSilanChecked(true); // WHERE silan_check = true
     * $query->filterByIsSilanChecked('yes'); // WHERE silan_check = true
     * </code>
     *
     * @param     boolean|string $isSilanChecked The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByIsSilanChecked($isSilanChecked = null, $comparison = null)
    {
        if (is_string($isSilanChecked)) {
            $isSilanChecked = in_array(strtolower($isSilanChecked), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(AudioFilePeer::SILAN_CHECK, $isSilanChecked, $comparison);
    }

    /**
     * Filter the query on the file_exists column
     *
     * Example usage:
     * <code>
     * $query->filterByFileExists(true); // WHERE file_exists = true
     * $query->filterByFileExists('yes'); // WHERE file_exists = true
     * </code>
     *
     * @param     boolean|string $fileExists The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByFileExists($fileExists = null, $comparison = null)
    {
        if (is_string($fileExists)) {
            $fileExists = in_array(strtolower($fileExists), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(AudioFilePeer::FILE_EXISTS, $fileExists, $comparison);
    }

    /**
     * Filter the query on the hidden column
     *
     * Example usage:
     * <code>
     * $query->filterByFileHidden(true); // WHERE hidden = true
     * $query->filterByFileHidden('yes'); // WHERE hidden = true
     * </code>
     *
     * @param     boolean|string $fileHidden The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByFileHidden($fileHidden = null, $comparison = null)
    {
        if (is_string($fileHidden)) {
            $fileHidden = in_array(strtolower($fileHidden), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(AudioFilePeer::HIDDEN, $fileHidden, $comparison);
    }

    /**
     * Filter the query on the is_scheduled column
     *
     * Example usage:
     * <code>
     * $query->filterByIsScheduled(true); // WHERE is_scheduled = true
     * $query->filterByIsScheduled('yes'); // WHERE is_scheduled = true
     * </code>
     *
     * @param     boolean|string $isScheduled The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByIsScheduled($isScheduled = null, $comparison = null)
    {
        if (is_string($isScheduled)) {
            $isScheduled = in_array(strtolower($isScheduled), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(AudioFilePeer::IS_SCHEDULED, $isScheduled, $comparison);
    }

    /**
     * Filter the query on the is_playlist column
     *
     * Example usage:
     * <code>
     * $query->filterByIsPlaylist(true); // WHERE is_playlist = true
     * $query->filterByIsPlaylist('yes'); // WHERE is_playlist = true
     * </code>
     *
     * @param     boolean|string $isPlaylist The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByIsPlaylist($isPlaylist = null, $comparison = null)
    {
        if (is_string($isPlaylist)) {
            $isPlaylist = in_array(strtolower($isPlaylist), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(AudioFilePeer::IS_PLAYLIST, $isPlaylist, $comparison);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @see       filterByMediaItem()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(AudioFilePeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(AudioFilePeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the owner_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOwnerId(1234); // WHERE owner_id = 1234
     * $query->filterByOwnerId(array(12, 34)); // WHERE owner_id IN (12, 34)
     * $query->filterByOwnerId(array('min' => 12)); // WHERE owner_id >= 12
     * $query->filterByOwnerId(array('max' => 12)); // WHERE owner_id <= 12
     * </code>
     *
     * @see       filterByCcSubjs()
     *
     * @param     mixed $ownerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByOwnerId($ownerId = null, $comparison = null)
    {
        if (is_array($ownerId)) {
            $useMinMax = false;
            if (isset($ownerId['min'])) {
                $this->addUsingAlias(AudioFilePeer::OWNER_ID, $ownerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ownerId['max'])) {
                $this->addUsingAlias(AudioFilePeer::OWNER_ID, $ownerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::OWNER_ID, $ownerId, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the last_played column
     *
     * Example usage:
     * <code>
     * $query->filterByLastPlayedTime('2011-03-14'); // WHERE last_played = '2011-03-14'
     * $query->filterByLastPlayedTime('now'); // WHERE last_played = '2011-03-14'
     * $query->filterByLastPlayedTime(array('max' => 'yesterday')); // WHERE last_played < '2011-03-13'
     * </code>
     *
     * @param     mixed $lastPlayedTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByLastPlayedTime($lastPlayedTime = null, $comparison = null)
    {
        if (is_array($lastPlayedTime)) {
            $useMinMax = false;
            if (isset($lastPlayedTime['min'])) {
                $this->addUsingAlias(AudioFilePeer::LAST_PLAYED, $lastPlayedTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastPlayedTime['max'])) {
                $this->addUsingAlias(AudioFilePeer::LAST_PLAYED, $lastPlayedTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::LAST_PLAYED, $lastPlayedTime, $comparison);
    }

    /**
     * Filter the query on the play_count column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayCount(1234); // WHERE play_count = 1234
     * $query->filterByPlayCount(array(12, 34)); // WHERE play_count IN (12, 34)
     * $query->filterByPlayCount(array('min' => 12)); // WHERE play_count >= 12
     * $query->filterByPlayCount(array('max' => 12)); // WHERE play_count <= 12
     * </code>
     *
     * @param     mixed $playCount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByPlayCount($playCount = null, $comparison = null)
    {
        if (is_array($playCount)) {
            $useMinMax = false;
            if (isset($playCount['min'])) {
                $this->addUsingAlias(AudioFilePeer::PLAY_COUNT, $playCount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playCount['max'])) {
                $this->addUsingAlias(AudioFilePeer::PLAY_COUNT, $playCount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::PLAY_COUNT, $playCount, $comparison);
    }

    /**
     * Filter the query on the length column
     *
     * Example usage:
     * <code>
     * $query->filterByLength('fooValue');   // WHERE length = 'fooValue'
     * $query->filterByLength('%fooValue%'); // WHERE length LIKE '%fooValue%'
     * </code>
     *
     * @param     string $length The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByLength($length = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($length)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $length)) {
                $length = str_replace('*', '%', $length);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::LENGTH, $length, $comparison);
    }

    /**
     * Filter the query on the mime column
     *
     * Example usage:
     * <code>
     * $query->filterByMime('fooValue');   // WHERE mime = 'fooValue'
     * $query->filterByMime('%fooValue%'); // WHERE mime LIKE '%fooValue%'
     * </code>
     *
     * @param     string $mime The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByMime($mime = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($mime)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $mime)) {
                $mime = str_replace('*', '%', $mime);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::MIME, $mime, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(AudioFilePeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(AudioFilePeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(AudioFilePeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(AudioFilePeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AudioFilePeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related CcMusicDirs object
     *
     * @param   CcMusicDirs|PropelObjectCollection $ccMusicDirs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AudioFileQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcMusicDirs($ccMusicDirs, $comparison = null)
    {
        if ($ccMusicDirs instanceof CcMusicDirs) {
            return $this
                ->addUsingAlias(AudioFilePeer::DIRECTORY, $ccMusicDirs->getId(), $comparison);
        } elseif ($ccMusicDirs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AudioFilePeer::DIRECTORY, $ccMusicDirs->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCcMusicDirs() only accepts arguments of type CcMusicDirs or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcMusicDirs relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function joinCcMusicDirs($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcMusicDirs');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CcMusicDirs');
        }

        return $this;
    }

    /**
     * Use the CcMusicDirs relation CcMusicDirs object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\CcMusicDirsQuery A secondary query class using the current class as primary query
     */
    public function useCcMusicDirsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcMusicDirs($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcMusicDirs', '\Airtime\CcMusicDirsQuery');
    }

    /**
     * Filter the query by a related MediaItem object
     *
     * @param   MediaItem|PropelObjectCollection $mediaItem The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AudioFileQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByMediaItem($mediaItem, $comparison = null)
    {
        if ($mediaItem instanceof MediaItem) {
            return $this
                ->addUsingAlias(AudioFilePeer::ID, $mediaItem->getId(), $comparison);
        } elseif ($mediaItem instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AudioFilePeer::ID, $mediaItem->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByMediaItem() only accepts arguments of type MediaItem or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MediaItem relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function joinMediaItem($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MediaItem');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'MediaItem');
        }

        return $this;
    }

    /**
     * Use the MediaItem relation MediaItem object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\MediaItemQuery A secondary query class using the current class as primary query
     */
    public function useMediaItemQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMediaItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MediaItem', '\Airtime\MediaItemQuery');
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AudioFileQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(AudioFilePeer::OWNER_ID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AudioFilePeer::OWNER_ID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcSubjs() only accepts arguments of type CcSubjs or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcSubjs relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function joinCcSubjs($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcSubjs');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CcSubjs');
        }

        return $this;
    }

    /**
     * Use the CcSubjs relation CcSubjs object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\CcSubjsQuery A secondary query class using the current class as primary query
     */
    public function useCcSubjsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcSubjs($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSubjs', '\Airtime\CcSubjsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   AudioFile $audioFile Object to remove from the list of results
     *
     * @return AudioFileQuery The current query, for fluid interface
     */
    public function prune($audioFile = null)
    {
        if ($audioFile) {
            $this->addUsingAlias(AudioFilePeer::ID, $audioFile->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     AudioFileQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(AudioFilePeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     AudioFileQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(AudioFilePeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     AudioFileQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(AudioFilePeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     AudioFileQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(AudioFilePeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     AudioFileQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(AudioFilePeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     AudioFileQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(AudioFilePeer::CREATED_AT);
    }
}
