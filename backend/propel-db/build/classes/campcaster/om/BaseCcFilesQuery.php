<?php


/**
 * Base class that represents a query for the 'cc_files' table.
 *
 * 
 *
 * @method     CcFilesQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcFilesQuery orderByGunid($order = Criteria::ASC) Order by the gunid column
 * @method     CcFilesQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     CcFilesQuery orderByMime($order = Criteria::ASC) Order by the mime column
 * @method     CcFilesQuery orderByFtype($order = Criteria::ASC) Order by the ftype column
 * @method     CcFilesQuery orderByfilepath($order = Criteria::ASC) Order by the filepath column
 * @method     CcFilesQuery orderByState($order = Criteria::ASC) Order by the state column
 * @method     CcFilesQuery orderByCurrentlyaccessing($order = Criteria::ASC) Order by the currentlyaccessing column
 * @method     CcFilesQuery orderByEditedby($order = Criteria::ASC) Order by the editedby column
 * @method     CcFilesQuery orderByMtime($order = Criteria::ASC) Order by the mtime column
 * @method     CcFilesQuery orderByMd5($order = Criteria::ASC) Order by the md5 column
 * @method     CcFilesQuery orderByTrackTitle($order = Criteria::ASC) Order by the track_title column
 * @method     CcFilesQuery orderByArtistName($order = Criteria::ASC) Order by the artist_name column
 * @method     CcFilesQuery orderByBitRate($order = Criteria::ASC) Order by the bit_rate column
 * @method     CcFilesQuery orderBySampleRate($order = Criteria::ASC) Order by the sample_rate column
 * @method     CcFilesQuery orderByFormat($order = Criteria::ASC) Order by the format column
 * @method     CcFilesQuery orderByDbLength($order = Criteria::ASC) Order by the length column
 * @method     CcFilesQuery orderByAlbumTitle($order = Criteria::ASC) Order by the album_title column
 * @method     CcFilesQuery orderByGenre($order = Criteria::ASC) Order by the genre column
 * @method     CcFilesQuery orderByComments($order = Criteria::ASC) Order by the comments column
 * @method     CcFilesQuery orderByYear($order = Criteria::ASC) Order by the year column
 * @method     CcFilesQuery orderByTrackNumber($order = Criteria::ASC) Order by the track_number column
 * @method     CcFilesQuery orderByChannels($order = Criteria::ASC) Order by the channels column
 * @method     CcFilesQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     CcFilesQuery orderByBpm($order = Criteria::ASC) Order by the bpm column
 * @method     CcFilesQuery orderByRating($order = Criteria::ASC) Order by the rating column
 * @method     CcFilesQuery orderByEncodedBy($order = Criteria::ASC) Order by the encoded_by column
 * @method     CcFilesQuery orderByDiscNumber($order = Criteria::ASC) Order by the disc_number column
 * @method     CcFilesQuery orderByMood($order = Criteria::ASC) Order by the mood column
 * @method     CcFilesQuery orderByLabel($order = Criteria::ASC) Order by the label column
 * @method     CcFilesQuery orderByComposer($order = Criteria::ASC) Order by the composer column
 * @method     CcFilesQuery orderByEncoder($order = Criteria::ASC) Order by the encoder column
 * @method     CcFilesQuery orderByChecksum($order = Criteria::ASC) Order by the checksum column
 * @method     CcFilesQuery orderByLyrics($order = Criteria::ASC) Order by the lyrics column
 * @method     CcFilesQuery orderByOrchestra($order = Criteria::ASC) Order by the orchestra column
 * @method     CcFilesQuery orderByConductor($order = Criteria::ASC) Order by the conductor column
 * @method     CcFilesQuery orderByLyricist($order = Criteria::ASC) Order by the lyricist column
 * @method     CcFilesQuery orderByOriginalLyricist($order = Criteria::ASC) Order by the original_lyricist column
 * @method     CcFilesQuery orderByRadioStationName($order = Criteria::ASC) Order by the radio_station_name column
 * @method     CcFilesQuery orderByInfoUrl($order = Criteria::ASC) Order by the info_url column
 * @method     CcFilesQuery orderByArtistUrl($order = Criteria::ASC) Order by the artist_url column
 * @method     CcFilesQuery orderByAudioSourceUrl($order = Criteria::ASC) Order by the audio_source_url column
 * @method     CcFilesQuery orderByRadioStationUrl($order = Criteria::ASC) Order by the radio_station_url column
 * @method     CcFilesQuery orderByBuyThisUrl($order = Criteria::ASC) Order by the buy_this_url column
 * @method     CcFilesQuery orderByIsrcNumber($order = Criteria::ASC) Order by the isrc_number column
 * @method     CcFilesQuery orderByCatalogNumber($order = Criteria::ASC) Order by the catalog_number column
 * @method     CcFilesQuery orderByOriginalArtist($order = Criteria::ASC) Order by the original_artist column
 * @method     CcFilesQuery orderByCopyright($order = Criteria::ASC) Order by the copyright column
 * @method     CcFilesQuery orderByReportDatetime($order = Criteria::ASC) Order by the report_datetime column
 * @method     CcFilesQuery orderByReportLocation($order = Criteria::ASC) Order by the report_location column
 * @method     CcFilesQuery orderByReportOrganization($order = Criteria::ASC) Order by the report_organization column
 * @method     CcFilesQuery orderBySubject($order = Criteria::ASC) Order by the subject column
 * @method     CcFilesQuery orderByContributor($order = Criteria::ASC) Order by the contributor column
 * @method     CcFilesQuery orderByLanguage($order = Criteria::ASC) Order by the language column
 *
 * @method     CcFilesQuery groupByDbId() Group by the id column
 * @method     CcFilesQuery groupByGunid() Group by the gunid column
 * @method     CcFilesQuery groupByName() Group by the name column
 * @method     CcFilesQuery groupByMime() Group by the mime column
 * @method     CcFilesQuery groupByFtype() Group by the ftype column
 * @method     CcFilesQuery groupByfilepath() Group by the filepath column
 * @method     CcFilesQuery groupByState() Group by the state column
 * @method     CcFilesQuery groupByCurrentlyaccessing() Group by the currentlyaccessing column
 * @method     CcFilesQuery groupByEditedby() Group by the editedby column
 * @method     CcFilesQuery groupByMtime() Group by the mtime column
 * @method     CcFilesQuery groupByMd5() Group by the md5 column
 * @method     CcFilesQuery groupByTrackTitle() Group by the track_title column
 * @method     CcFilesQuery groupByArtistName() Group by the artist_name column
 * @method     CcFilesQuery groupByBitRate() Group by the bit_rate column
 * @method     CcFilesQuery groupBySampleRate() Group by the sample_rate column
 * @method     CcFilesQuery groupByFormat() Group by the format column
 * @method     CcFilesQuery groupByDbLength() Group by the length column
 * @method     CcFilesQuery groupByAlbumTitle() Group by the album_title column
 * @method     CcFilesQuery groupByGenre() Group by the genre column
 * @method     CcFilesQuery groupByComments() Group by the comments column
 * @method     CcFilesQuery groupByYear() Group by the year column
 * @method     CcFilesQuery groupByTrackNumber() Group by the track_number column
 * @method     CcFilesQuery groupByChannels() Group by the channels column
 * @method     CcFilesQuery groupByUrl() Group by the url column
 * @method     CcFilesQuery groupByBpm() Group by the bpm column
 * @method     CcFilesQuery groupByRating() Group by the rating column
 * @method     CcFilesQuery groupByEncodedBy() Group by the encoded_by column
 * @method     CcFilesQuery groupByDiscNumber() Group by the disc_number column
 * @method     CcFilesQuery groupByMood() Group by the mood column
 * @method     CcFilesQuery groupByLabel() Group by the label column
 * @method     CcFilesQuery groupByComposer() Group by the composer column
 * @method     CcFilesQuery groupByEncoder() Group by the encoder column
 * @method     CcFilesQuery groupByChecksum() Group by the checksum column
 * @method     CcFilesQuery groupByLyrics() Group by the lyrics column
 * @method     CcFilesQuery groupByOrchestra() Group by the orchestra column
 * @method     CcFilesQuery groupByConductor() Group by the conductor column
 * @method     CcFilesQuery groupByLyricist() Group by the lyricist column
 * @method     CcFilesQuery groupByOriginalLyricist() Group by the original_lyricist column
 * @method     CcFilesQuery groupByRadioStationName() Group by the radio_station_name column
 * @method     CcFilesQuery groupByInfoUrl() Group by the info_url column
 * @method     CcFilesQuery groupByArtistUrl() Group by the artist_url column
 * @method     CcFilesQuery groupByAudioSourceUrl() Group by the audio_source_url column
 * @method     CcFilesQuery groupByRadioStationUrl() Group by the radio_station_url column
 * @method     CcFilesQuery groupByBuyThisUrl() Group by the buy_this_url column
 * @method     CcFilesQuery groupByIsrcNumber() Group by the isrc_number column
 * @method     CcFilesQuery groupByCatalogNumber() Group by the catalog_number column
 * @method     CcFilesQuery groupByOriginalArtist() Group by the original_artist column
 * @method     CcFilesQuery groupByCopyright() Group by the copyright column
 * @method     CcFilesQuery groupByReportDatetime() Group by the report_datetime column
 * @method     CcFilesQuery groupByReportLocation() Group by the report_location column
 * @method     CcFilesQuery groupByReportOrganization() Group by the report_organization column
 * @method     CcFilesQuery groupBySubject() Group by the subject column
 * @method     CcFilesQuery groupByContributor() Group by the contributor column
 * @method     CcFilesQuery groupByLanguage() Group by the language column
 *
 * @method     CcFilesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcFilesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcFilesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcFilesQuery leftJoinCcSubjs($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method     CcFilesQuery rightJoinCcSubjs($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method     CcFilesQuery innerJoinCcSubjs($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method     CcFilesQuery leftJoinCcPlaylistcontents($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylistcontents relation
 * @method     CcFilesQuery rightJoinCcPlaylistcontents($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylistcontents relation
 * @method     CcFilesQuery innerJoinCcPlaylistcontents($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylistcontents relation
 *
 * @method     CcFiles findOne(PropelPDO $con = null) Return the first CcFiles matching the query
 * @method     CcFiles findOneOrCreate(PropelPDO $con = null) Return the first CcFiles matching the query, or a new CcFiles object populated from the query conditions when no match is found
 *
 * @method     CcFiles findOneByDbId(int $id) Return the first CcFiles filtered by the id column
 * @method     CcFiles findOneByGunid(string $gunid) Return the first CcFiles filtered by the gunid column
 * @method     CcFiles findOneByName(string $name) Return the first CcFiles filtered by the name column
 * @method     CcFiles findOneByMime(string $mime) Return the first CcFiles filtered by the mime column
 * @method     CcFiles findOneByFtype(string $ftype) Return the first CcFiles filtered by the ftype column
 * @method     CcFiles findOneByfilepath(string $filepath) Return the first CcFiles filtered by the filepath column
 * @method     CcFiles findOneByState(string $state) Return the first CcFiles filtered by the state column
 * @method     CcFiles findOneByCurrentlyaccessing(int $currentlyaccessing) Return the first CcFiles filtered by the currentlyaccessing column
 * @method     CcFiles findOneByEditedby(int $editedby) Return the first CcFiles filtered by the editedby column
 * @method     CcFiles findOneByMtime(string $mtime) Return the first CcFiles filtered by the mtime column
 * @method     CcFiles findOneByMd5(string $md5) Return the first CcFiles filtered by the md5 column
 * @method     CcFiles findOneByTrackTitle(string $track_title) Return the first CcFiles filtered by the track_title column
 * @method     CcFiles findOneByArtistName(string $artist_name) Return the first CcFiles filtered by the artist_name column
 * @method     CcFiles findOneByBitRate(string $bit_rate) Return the first CcFiles filtered by the bit_rate column
 * @method     CcFiles findOneBySampleRate(string $sample_rate) Return the first CcFiles filtered by the sample_rate column
 * @method     CcFiles findOneByFormat(string $format) Return the first CcFiles filtered by the format column
 * @method     CcFiles findOneByDbLength(string $length) Return the first CcFiles filtered by the length column
 * @method     CcFiles findOneByAlbumTitle(string $album_title) Return the first CcFiles filtered by the album_title column
 * @method     CcFiles findOneByGenre(string $genre) Return the first CcFiles filtered by the genre column
 * @method     CcFiles findOneByComments(string $comments) Return the first CcFiles filtered by the comments column
 * @method     CcFiles findOneByYear(string $year) Return the first CcFiles filtered by the year column
 * @method     CcFiles findOneByTrackNumber(int $track_number) Return the first CcFiles filtered by the track_number column
 * @method     CcFiles findOneByChannels(int $channels) Return the first CcFiles filtered by the channels column
 * @method     CcFiles findOneByUrl(string $url) Return the first CcFiles filtered by the url column
 * @method     CcFiles findOneByBpm(string $bpm) Return the first CcFiles filtered by the bpm column
 * @method     CcFiles findOneByRating(string $rating) Return the first CcFiles filtered by the rating column
 * @method     CcFiles findOneByEncodedBy(string $encoded_by) Return the first CcFiles filtered by the encoded_by column
 * @method     CcFiles findOneByDiscNumber(string $disc_number) Return the first CcFiles filtered by the disc_number column
 * @method     CcFiles findOneByMood(string $mood) Return the first CcFiles filtered by the mood column
 * @method     CcFiles findOneByLabel(string $label) Return the first CcFiles filtered by the label column
 * @method     CcFiles findOneByComposer(string $composer) Return the first CcFiles filtered by the composer column
 * @method     CcFiles findOneByEncoder(string $encoder) Return the first CcFiles filtered by the encoder column
 * @method     CcFiles findOneByChecksum(string $checksum) Return the first CcFiles filtered by the checksum column
 * @method     CcFiles findOneByLyrics(string $lyrics) Return the first CcFiles filtered by the lyrics column
 * @method     CcFiles findOneByOrchestra(string $orchestra) Return the first CcFiles filtered by the orchestra column
 * @method     CcFiles findOneByConductor(string $conductor) Return the first CcFiles filtered by the conductor column
 * @method     CcFiles findOneByLyricist(string $lyricist) Return the first CcFiles filtered by the lyricist column
 * @method     CcFiles findOneByOriginalLyricist(string $original_lyricist) Return the first CcFiles filtered by the original_lyricist column
 * @method     CcFiles findOneByRadioStationName(string $radio_station_name) Return the first CcFiles filtered by the radio_station_name column
 * @method     CcFiles findOneByInfoUrl(string $info_url) Return the first CcFiles filtered by the info_url column
 * @method     CcFiles findOneByArtistUrl(string $artist_url) Return the first CcFiles filtered by the artist_url column
 * @method     CcFiles findOneByAudioSourceUrl(string $audio_source_url) Return the first CcFiles filtered by the audio_source_url column
 * @method     CcFiles findOneByRadioStationUrl(string $radio_station_url) Return the first CcFiles filtered by the radio_station_url column
 * @method     CcFiles findOneByBuyThisUrl(string $buy_this_url) Return the first CcFiles filtered by the buy_this_url column
 * @method     CcFiles findOneByIsrcNumber(string $isrc_number) Return the first CcFiles filtered by the isrc_number column
 * @method     CcFiles findOneByCatalogNumber(string $catalog_number) Return the first CcFiles filtered by the catalog_number column
 * @method     CcFiles findOneByOriginalArtist(string $original_artist) Return the first CcFiles filtered by the original_artist column
 * @method     CcFiles findOneByCopyright(string $copyright) Return the first CcFiles filtered by the copyright column
 * @method     CcFiles findOneByReportDatetime(string $report_datetime) Return the first CcFiles filtered by the report_datetime column
 * @method     CcFiles findOneByReportLocation(string $report_location) Return the first CcFiles filtered by the report_location column
 * @method     CcFiles findOneByReportOrganization(string $report_organization) Return the first CcFiles filtered by the report_organization column
 * @method     CcFiles findOneBySubject(string $subject) Return the first CcFiles filtered by the subject column
 * @method     CcFiles findOneByContributor(string $contributor) Return the first CcFiles filtered by the contributor column
 * @method     CcFiles findOneByLanguage(string $language) Return the first CcFiles filtered by the language column
 *
 * @method     array findByDbId(int $id) Return CcFiles objects filtered by the id column
 * @method     array findByGunid(string $gunid) Return CcFiles objects filtered by the gunid column
 * @method     array findByName(string $name) Return CcFiles objects filtered by the name column
 * @method     array findByMime(string $mime) Return CcFiles objects filtered by the mime column
 * @method     array findByFtype(string $ftype) Return CcFiles objects filtered by the ftype column
 * @method     array findByfilepath(string $filepath) Return CcFiles objects filtered by the filepath column
 * @method     array findByState(string $state) Return CcFiles objects filtered by the state column
 * @method     array findByCurrentlyaccessing(int $currentlyaccessing) Return CcFiles objects filtered by the currentlyaccessing column
 * @method     array findByEditedby(int $editedby) Return CcFiles objects filtered by the editedby column
 * @method     array findByMtime(string $mtime) Return CcFiles objects filtered by the mtime column
 * @method     array findByMd5(string $md5) Return CcFiles objects filtered by the md5 column
 * @method     array findByTrackTitle(string $track_title) Return CcFiles objects filtered by the track_title column
 * @method     array findByArtistName(string $artist_name) Return CcFiles objects filtered by the artist_name column
 * @method     array findByBitRate(string $bit_rate) Return CcFiles objects filtered by the bit_rate column
 * @method     array findBySampleRate(string $sample_rate) Return CcFiles objects filtered by the sample_rate column
 * @method     array findByFormat(string $format) Return CcFiles objects filtered by the format column
 * @method     array findByDbLength(string $length) Return CcFiles objects filtered by the length column
 * @method     array findByAlbumTitle(string $album_title) Return CcFiles objects filtered by the album_title column
 * @method     array findByGenre(string $genre) Return CcFiles objects filtered by the genre column
 * @method     array findByComments(string $comments) Return CcFiles objects filtered by the comments column
 * @method     array findByYear(string $year) Return CcFiles objects filtered by the year column
 * @method     array findByTrackNumber(int $track_number) Return CcFiles objects filtered by the track_number column
 * @method     array findByChannels(int $channels) Return CcFiles objects filtered by the channels column
 * @method     array findByUrl(string $url) Return CcFiles objects filtered by the url column
 * @method     array findByBpm(string $bpm) Return CcFiles objects filtered by the bpm column
 * @method     array findByRating(string $rating) Return CcFiles objects filtered by the rating column
 * @method     array findByEncodedBy(string $encoded_by) Return CcFiles objects filtered by the encoded_by column
 * @method     array findByDiscNumber(string $disc_number) Return CcFiles objects filtered by the disc_number column
 * @method     array findByMood(string $mood) Return CcFiles objects filtered by the mood column
 * @method     array findByLabel(string $label) Return CcFiles objects filtered by the label column
 * @method     array findByComposer(string $composer) Return CcFiles objects filtered by the composer column
 * @method     array findByEncoder(string $encoder) Return CcFiles objects filtered by the encoder column
 * @method     array findByChecksum(string $checksum) Return CcFiles objects filtered by the checksum column
 * @method     array findByLyrics(string $lyrics) Return CcFiles objects filtered by the lyrics column
 * @method     array findByOrchestra(string $orchestra) Return CcFiles objects filtered by the orchestra column
 * @method     array findByConductor(string $conductor) Return CcFiles objects filtered by the conductor column
 * @method     array findByLyricist(string $lyricist) Return CcFiles objects filtered by the lyricist column
 * @method     array findByOriginalLyricist(string $original_lyricist) Return CcFiles objects filtered by the original_lyricist column
 * @method     array findByRadioStationName(string $radio_station_name) Return CcFiles objects filtered by the radio_station_name column
 * @method     array findByInfoUrl(string $info_url) Return CcFiles objects filtered by the info_url column
 * @method     array findByArtistUrl(string $artist_url) Return CcFiles objects filtered by the artist_url column
 * @method     array findByAudioSourceUrl(string $audio_source_url) Return CcFiles objects filtered by the audio_source_url column
 * @method     array findByRadioStationUrl(string $radio_station_url) Return CcFiles objects filtered by the radio_station_url column
 * @method     array findByBuyThisUrl(string $buy_this_url) Return CcFiles objects filtered by the buy_this_url column
 * @method     array findByIsrcNumber(string $isrc_number) Return CcFiles objects filtered by the isrc_number column
 * @method     array findByCatalogNumber(string $catalog_number) Return CcFiles objects filtered by the catalog_number column
 * @method     array findByOriginalArtist(string $original_artist) Return CcFiles objects filtered by the original_artist column
 * @method     array findByCopyright(string $copyright) Return CcFiles objects filtered by the copyright column
 * @method     array findByReportDatetime(string $report_datetime) Return CcFiles objects filtered by the report_datetime column
 * @method     array findByReportLocation(string $report_location) Return CcFiles objects filtered by the report_location column
 * @method     array findByReportOrganization(string $report_organization) Return CcFiles objects filtered by the report_organization column
 * @method     array findBySubject(string $subject) Return CcFiles objects filtered by the subject column
 * @method     array findByContributor(string $contributor) Return CcFiles objects filtered by the contributor column
 * @method     array findByLanguage(string $language) Return CcFiles objects filtered by the language column
 *
 * @package    propel.generator.campcaster.om
 */
abstract class BaseCcFilesQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcFilesQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'campcaster', $modelName = 'CcFiles', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcFilesQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcFilesQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcFilesQuery) {
			return $criteria;
		}
		$query = new CcFilesQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CcFiles|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcFilesPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$criteria = $this->isKeepQuery() ? clone $this : $this;
			$stmt = $criteria
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
		}
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{	
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcFilesPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcFilesPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcFilesPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the gunid column
	 * 
	 * @param     string $gunid The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByGunid($gunid = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($gunid)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $gunid)) {
				$gunid = str_replace('*', '%', $gunid);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::GUNID, $gunid, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $name The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::NAME, $name, $comparison);
	}

	/**
	 * Filter the query on the mime column
	 * 
	 * @param     string $mime The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::MIME, $mime, $comparison);
	}

	/**
	 * Filter the query on the ftype column
	 * 
	 * @param     string $ftype The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByFtype($ftype = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($ftype)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $ftype)) {
				$ftype = str_replace('*', '%', $ftype);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::FTYPE, $ftype, $comparison);
	}

	/**
	 * Filter the query on the filepath column
	 * 
	 * @param     string $filepath The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByfilepath($filepath = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($filepath)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $filepath)) {
				$filepath = str_replace('*', '%', $filepath);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::FILEPATH, $filepath, $comparison);
	}

	/**
	 * Filter the query on the state column
	 * 
	 * @param     string $state The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByState($state = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($state)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $state)) {
				$state = str_replace('*', '%', $state);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::STATE, $state, $comparison);
	}

	/**
	 * Filter the query on the currentlyaccessing column
	 * 
	 * @param     int|array $currentlyaccessing The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCurrentlyaccessing($currentlyaccessing = null, $comparison = null)
	{
		if (is_array($currentlyaccessing)) {
			$useMinMax = false;
			if (isset($currentlyaccessing['min'])) {
				$this->addUsingAlias(CcFilesPeer::CURRENTLYACCESSING, $currentlyaccessing['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($currentlyaccessing['max'])) {
				$this->addUsingAlias(CcFilesPeer::CURRENTLYACCESSING, $currentlyaccessing['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CURRENTLYACCESSING, $currentlyaccessing, $comparison);
	}

	/**
	 * Filter the query on the editedby column
	 * 
	 * @param     int|array $editedby The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByEditedby($editedby = null, $comparison = null)
	{
		if (is_array($editedby)) {
			$useMinMax = false;
			if (isset($editedby['min'])) {
				$this->addUsingAlias(CcFilesPeer::EDITEDBY, $editedby['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($editedby['max'])) {
				$this->addUsingAlias(CcFilesPeer::EDITEDBY, $editedby['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::EDITEDBY, $editedby, $comparison);
	}

	/**
	 * Filter the query on the mtime column
	 * 
	 * @param     string|array $mtime The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByMtime($mtime = null, $comparison = null)
	{
		if (is_array($mtime)) {
			$useMinMax = false;
			if (isset($mtime['min'])) {
				$this->addUsingAlias(CcFilesPeer::MTIME, $mtime['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($mtime['max'])) {
				$this->addUsingAlias(CcFilesPeer::MTIME, $mtime['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::MTIME, $mtime, $comparison);
	}

	/**
	 * Filter the query on the md5 column
	 * 
	 * @param     string $md5 The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::MD5, $md5, $comparison);
	}

	/**
	 * Filter the query on the track_title column
	 * 
	 * @param     string $trackTitle The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::TRACK_TITLE, $trackTitle, $comparison);
	}

	/**
	 * Filter the query on the artist_name column
	 * 
	 * @param     string $artistName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::ARTIST_NAME, $artistName, $comparison);
	}

	/**
	 * Filter the query on the bit_rate column
	 * 
	 * @param     string $bitRate The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByBitRate($bitRate = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($bitRate)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $bitRate)) {
				$bitRate = str_replace('*', '%', $bitRate);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::BIT_RATE, $bitRate, $comparison);
	}

	/**
	 * Filter the query on the sample_rate column
	 * 
	 * @param     string $sampleRate The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterBySampleRate($sampleRate = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($sampleRate)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $sampleRate)) {
				$sampleRate = str_replace('*', '%', $sampleRate);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SAMPLE_RATE, $sampleRate, $comparison);
	}

	/**
	 * Filter the query on the format column
	 * 
	 * @param     string $format The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByFormat($format = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($format)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $format)) {
				$format = str_replace('*', '%', $format);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::FORMAT, $format, $comparison);
	}

	/**
	 * Filter the query on the length column
	 * 
	 * @param     string|array $dbLength The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDbLength($dbLength = null, $comparison = null)
	{
		if (is_array($dbLength)) {
			$useMinMax = false;
			if (isset($dbLength['min'])) {
				$this->addUsingAlias(CcFilesPeer::LENGTH, $dbLength['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLength['max'])) {
				$this->addUsingAlias(CcFilesPeer::LENGTH, $dbLength['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LENGTH, $dbLength, $comparison);
	}

	/**
	 * Filter the query on the album_title column
	 * 
	 * @param     string $albumTitle The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::ALBUM_TITLE, $albumTitle, $comparison);
	}

	/**
	 * Filter the query on the genre column
	 * 
	 * @param     string $genre The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::GENRE, $genre, $comparison);
	}

	/**
	 * Filter the query on the comments column
	 * 
	 * @param     string $comments The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::COMMENTS, $comments, $comparison);
	}

	/**
	 * Filter the query on the year column
	 * 
	 * @param     string $year The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::YEAR, $year, $comparison);
	}

	/**
	 * Filter the query on the track_number column
	 * 
	 * @param     int|array $trackNumber The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByTrackNumber($trackNumber = null, $comparison = null)
	{
		if (is_array($trackNumber)) {
			$useMinMax = false;
			if (isset($trackNumber['min'])) {
				$this->addUsingAlias(CcFilesPeer::TRACK_NUMBER, $trackNumber['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($trackNumber['max'])) {
				$this->addUsingAlias(CcFilesPeer::TRACK_NUMBER, $trackNumber['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::TRACK_NUMBER, $trackNumber, $comparison);
	}

	/**
	 * Filter the query on the channels column
	 * 
	 * @param     int|array $channels The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByChannels($channels = null, $comparison = null)
	{
		if (is_array($channels)) {
			$useMinMax = false;
			if (isset($channels['min'])) {
				$this->addUsingAlias(CcFilesPeer::CHANNELS, $channels['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($channels['max'])) {
				$this->addUsingAlias(CcFilesPeer::CHANNELS, $channels['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CHANNELS, $channels, $comparison);
	}

	/**
	 * Filter the query on the url column
	 * 
	 * @param     string $url The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByUrl($url = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($url)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $url)) {
				$url = str_replace('*', '%', $url);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::URL, $url, $comparison);
	}

	/**
	 * Filter the query on the bpm column
	 * 
	 * @param     string $bpm The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByBpm($bpm = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($bpm)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $bpm)) {
				$bpm = str_replace('*', '%', $bpm);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::BPM, $bpm, $comparison);
	}

	/**
	 * Filter the query on the rating column
	 * 
	 * @param     string $rating The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByRating($rating = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($rating)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $rating)) {
				$rating = str_replace('*', '%', $rating);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::RATING, $rating, $comparison);
	}

	/**
	 * Filter the query on the encoded_by column
	 * 
	 * @param     string $encodedBy The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::ENCODED_BY, $encodedBy, $comparison);
	}

	/**
	 * Filter the query on the disc_number column
	 * 
	 * @param     string $discNumber The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByDiscNumber($discNumber = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($discNumber)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $discNumber)) {
				$discNumber = str_replace('*', '%', $discNumber);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::DISC_NUMBER, $discNumber, $comparison);
	}

	/**
	 * Filter the query on the mood column
	 * 
	 * @param     string $mood The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::MOOD, $mood, $comparison);
	}

	/**
	 * Filter the query on the label column
	 * 
	 * @param     string $label The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::LABEL, $label, $comparison);
	}

	/**
	 * Filter the query on the composer column
	 * 
	 * @param     string $composer The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::COMPOSER, $composer, $comparison);
	}

	/**
	 * Filter the query on the encoder column
	 * 
	 * @param     string $encoder The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByEncoder($encoder = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($encoder)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $encoder)) {
				$encoder = str_replace('*', '%', $encoder);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ENCODER, $encoder, $comparison);
	}

	/**
	 * Filter the query on the checksum column
	 * 
	 * @param     string $checksum The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByChecksum($checksum = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($checksum)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $checksum)) {
				$checksum = str_replace('*', '%', $checksum);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CHECKSUM, $checksum, $comparison);
	}

	/**
	 * Filter the query on the lyrics column
	 * 
	 * @param     string $lyrics The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByLyrics($lyrics = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($lyrics)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $lyrics)) {
				$lyrics = str_replace('*', '%', $lyrics);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LYRICS, $lyrics, $comparison);
	}

	/**
	 * Filter the query on the orchestra column
	 * 
	 * @param     string $orchestra The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByOrchestra($orchestra = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($orchestra)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $orchestra)) {
				$orchestra = str_replace('*', '%', $orchestra);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ORCHESTRA, $orchestra, $comparison);
	}

	/**
	 * Filter the query on the conductor column
	 * 
	 * @param     string $conductor The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::CONDUCTOR, $conductor, $comparison);
	}

	/**
	 * Filter the query on the lyricist column
	 * 
	 * @param     string $lyricist The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByLyricist($lyricist = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($lyricist)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $lyricist)) {
				$lyricist = str_replace('*', '%', $lyricist);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::LYRICIST, $lyricist, $comparison);
	}

	/**
	 * Filter the query on the original_lyricist column
	 * 
	 * @param     string $originalLyricist The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByOriginalLyricist($originalLyricist = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($originalLyricist)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $originalLyricist)) {
				$originalLyricist = str_replace('*', '%', $originalLyricist);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ORIGINAL_LYRICIST, $originalLyricist, $comparison);
	}

	/**
	 * Filter the query on the radio_station_name column
	 * 
	 * @param     string $radioStationName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByRadioStationName($radioStationName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($radioStationName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $radioStationName)) {
				$radioStationName = str_replace('*', '%', $radioStationName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::RADIO_STATION_NAME, $radioStationName, $comparison);
	}

	/**
	 * Filter the query on the info_url column
	 * 
	 * @param     string $infoUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::INFO_URL, $infoUrl, $comparison);
	}

	/**
	 * Filter the query on the artist_url column
	 * 
	 * @param     string $artistUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByArtistUrl($artistUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($artistUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $artistUrl)) {
				$artistUrl = str_replace('*', '%', $artistUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ARTIST_URL, $artistUrl, $comparison);
	}

	/**
	 * Filter the query on the audio_source_url column
	 * 
	 * @param     string $audioSourceUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByAudioSourceUrl($audioSourceUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($audioSourceUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $audioSourceUrl)) {
				$audioSourceUrl = str_replace('*', '%', $audioSourceUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::AUDIO_SOURCE_URL, $audioSourceUrl, $comparison);
	}

	/**
	 * Filter the query on the radio_station_url column
	 * 
	 * @param     string $radioStationUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByRadioStationUrl($radioStationUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($radioStationUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $radioStationUrl)) {
				$radioStationUrl = str_replace('*', '%', $radioStationUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::RADIO_STATION_URL, $radioStationUrl, $comparison);
	}

	/**
	 * Filter the query on the buy_this_url column
	 * 
	 * @param     string $buyThisUrl The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByBuyThisUrl($buyThisUrl = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($buyThisUrl)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $buyThisUrl)) {
				$buyThisUrl = str_replace('*', '%', $buyThisUrl);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::BUY_THIS_URL, $buyThisUrl, $comparison);
	}

	/**
	 * Filter the query on the isrc_number column
	 * 
	 * @param     string $isrcNumber The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::ISRC_NUMBER, $isrcNumber, $comparison);
	}

	/**
	 * Filter the query on the catalog_number column
	 * 
	 * @param     string $catalogNumber The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCatalogNumber($catalogNumber = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($catalogNumber)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $catalogNumber)) {
				$catalogNumber = str_replace('*', '%', $catalogNumber);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CATALOG_NUMBER, $catalogNumber, $comparison);
	}

	/**
	 * Filter the query on the original_artist column
	 * 
	 * @param     string $originalArtist The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByOriginalArtist($originalArtist = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($originalArtist)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $originalArtist)) {
				$originalArtist = str_replace('*', '%', $originalArtist);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::ORIGINAL_ARTIST, $originalArtist, $comparison);
	}

	/**
	 * Filter the query on the copyright column
	 * 
	 * @param     string $copyright The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::COPYRIGHT, $copyright, $comparison);
	}

	/**
	 * Filter the query on the report_datetime column
	 * 
	 * @param     string $reportDatetime The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByReportDatetime($reportDatetime = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($reportDatetime)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $reportDatetime)) {
				$reportDatetime = str_replace('*', '%', $reportDatetime);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::REPORT_DATETIME, $reportDatetime, $comparison);
	}

	/**
	 * Filter the query on the report_location column
	 * 
	 * @param     string $reportLocation The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByReportLocation($reportLocation = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($reportLocation)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $reportLocation)) {
				$reportLocation = str_replace('*', '%', $reportLocation);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::REPORT_LOCATION, $reportLocation, $comparison);
	}

	/**
	 * Filter the query on the report_organization column
	 * 
	 * @param     string $reportOrganization The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByReportOrganization($reportOrganization = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($reportOrganization)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $reportOrganization)) {
				$reportOrganization = str_replace('*', '%', $reportOrganization);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::REPORT_ORGANIZATION, $reportOrganization, $comparison);
	}

	/**
	 * Filter the query on the subject column
	 * 
	 * @param     string $subject The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterBySubject($subject = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($subject)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $subject)) {
				$subject = str_replace('*', '%', $subject);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::SUBJECT, $subject, $comparison);
	}

	/**
	 * Filter the query on the contributor column
	 * 
	 * @param     string $contributor The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByContributor($contributor = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($contributor)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $contributor)) {
				$contributor = str_replace('*', '%', $contributor);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcFilesPeer::CONTRIBUTOR, $contributor, $comparison);
	}

	/**
	 * Filter the query on the language column
	 * 
	 * @param     string $language The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcFilesPeer::LANGUAGE, $language, $comparison);
	}

	/**
	 * Filter the query by a related CcSubjs object
	 *
	 * @param     CcSubjs $ccSubjs  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcSubjs($ccSubjs, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::EDITEDBY, $ccSubjs->getId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjs relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcSubjs($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
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
		if($relationAlias) {
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
	 * @return    CcSubjsQuery A secondary query class using the current class as primary query
	 */
	public function useCcSubjsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcSubjs($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSubjs', 'CcSubjsQuery');
	}

	/**
	 * Filter the query by a related CcPlaylistcontents object
	 *
	 * @param     CcPlaylistcontents $ccPlaylistcontents  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylistcontents($ccPlaylistcontents, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFilesPeer::ID, $ccPlaylistcontents->getDbFileId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylistcontents relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function joinCcPlaylistcontents($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlaylistcontents');
		
		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}
		
		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcPlaylistcontents');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlaylistcontents relation CcPlaylistcontents object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistcontentsQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlaylistcontentsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPlaylistcontents($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlaylistcontents', 'CcPlaylistcontentsQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcFiles $ccFiles Object to remove from the list of results
	 *
	 * @return    CcFilesQuery The current query, for fluid interface
	 */
	public function prune($ccFiles = null)
	{
		if ($ccFiles) {
			$this->addUsingAlias(CcFilesPeer::ID, $ccFiles->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcFilesQuery
