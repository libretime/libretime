<?php


/**
 * Base class that represents a query for the 'podcast_episodes' table.
 *
 *
 *
 * @method PodcastEpisodesQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method PodcastEpisodesQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method PodcastEpisodesQuery orderByDbPodcastId($order = Criteria::ASC) Order by the podcast_id column
 * @method PodcastEpisodesQuery orderByDbPublicationDate($order = Criteria::ASC) Order by the publication_date column
 * @method PodcastEpisodesQuery orderByDbDownloadUrl($order = Criteria::ASC) Order by the download_url column
 * @method PodcastEpisodesQuery orderByDbEpisodeGuid($order = Criteria::ASC) Order by the episode_guid column
 * @method PodcastEpisodesQuery orderByDbEpisodeTitle($order = Criteria::ASC) Order by the episode_title column
 * @method PodcastEpisodesQuery orderByDbEpisodeDescription($order = Criteria::ASC) Order by the episode_description column
 *
 * @method PodcastEpisodesQuery groupByDbId() Group by the id column
 * @method PodcastEpisodesQuery groupByDbFileId() Group by the file_id column
 * @method PodcastEpisodesQuery groupByDbPodcastId() Group by the podcast_id column
 * @method PodcastEpisodesQuery groupByDbPublicationDate() Group by the publication_date column
 * @method PodcastEpisodesQuery groupByDbDownloadUrl() Group by the download_url column
 * @method PodcastEpisodesQuery groupByDbEpisodeGuid() Group by the episode_guid column
 * @method PodcastEpisodesQuery groupByDbEpisodeTitle() Group by the episode_title column
 * @method PodcastEpisodesQuery groupByDbEpisodeDescription() Group by the episode_description column
 *
 * @method PodcastEpisodesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PodcastEpisodesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PodcastEpisodesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PodcastEpisodesQuery leftJoinCcFiles($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method PodcastEpisodesQuery rightJoinCcFiles($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method PodcastEpisodesQuery innerJoinCcFiles($relationAlias = null) Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method PodcastEpisodesQuery leftJoinPodcast($relationAlias = null) Adds a LEFT JOIN clause to the query using the Podcast relation
 * @method PodcastEpisodesQuery rightJoinPodcast($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Podcast relation
 * @method PodcastEpisodesQuery innerJoinPodcast($relationAlias = null) Adds a INNER JOIN clause to the query using the Podcast relation
 *
 * @method PodcastEpisodes findOne(PropelPDO $con = null) Return the first PodcastEpisodes matching the query
 * @method PodcastEpisodes findOneOrCreate(PropelPDO $con = null) Return the first PodcastEpisodes matching the query, or a new PodcastEpisodes object populated from the query conditions when no match is found
 *
 * @method PodcastEpisodes findOneByDbFileId(int $file_id) Return the first PodcastEpisodes filtered by the file_id column
 * @method PodcastEpisodes findOneByDbPodcastId(int $podcast_id) Return the first PodcastEpisodes filtered by the podcast_id column
 * @method PodcastEpisodes findOneByDbPublicationDate(string $publication_date) Return the first PodcastEpisodes filtered by the publication_date column
 * @method PodcastEpisodes findOneByDbDownloadUrl(string $download_url) Return the first PodcastEpisodes filtered by the download_url column
 * @method PodcastEpisodes findOneByDbEpisodeGuid(string $episode_guid) Return the first PodcastEpisodes filtered by the episode_guid column
 * @method PodcastEpisodes findOneByDbEpisodeTitle(string $episode_title) Return the first PodcastEpisodes filtered by the episode_title column
 * @method PodcastEpisodes findOneByDbEpisodeDescription(string $episode_description) Return the first PodcastEpisodes filtered by the episode_description column
 *
 * @method array findByDbId(int $id) Return PodcastEpisodes objects filtered by the id column
 * @method array findByDbFileId(int $file_id) Return PodcastEpisodes objects filtered by the file_id column
 * @method array findByDbPodcastId(int $podcast_id) Return PodcastEpisodes objects filtered by the podcast_id column
 * @method array findByDbPublicationDate(string $publication_date) Return PodcastEpisodes objects filtered by the publication_date column
 * @method array findByDbDownloadUrl(string $download_url) Return PodcastEpisodes objects filtered by the download_url column
 * @method array findByDbEpisodeGuid(string $episode_guid) Return PodcastEpisodes objects filtered by the episode_guid column
 * @method array findByDbEpisodeTitle(string $episode_title) Return PodcastEpisodes objects filtered by the episode_title column
 * @method array findByDbEpisodeDescription(string $episode_description) Return PodcastEpisodes objects filtered by the episode_description column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BasePodcastEpisodesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePodcastEpisodesQuery object.
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
            $modelName = 'PodcastEpisodes';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PodcastEpisodesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PodcastEpisodesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PodcastEpisodesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PodcastEpisodesQuery) {
            return $criteria;
        }
        $query = new PodcastEpisodesQuery(null, null, $modelAlias);

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
     * @return   PodcastEpisodes|PodcastEpisodes[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PodcastEpisodesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PodcastEpisodesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 PodcastEpisodes A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByDbId($key, $con = null)
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
     * @return                 PodcastEpisodes A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "file_id", "podcast_id", "publication_date", "download_url", "episode_guid", "episode_title", "episode_description" FROM "podcast_episodes" WHERE "id" = :p0';
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
            $obj = new PodcastEpisodes();
            $obj->hydrate($row);
            PodcastEpisodesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return PodcastEpisodes|PodcastEpisodes[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|PodcastEpisodes[]|mixed the list of results, formatted by the current formatter
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
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PodcastEpisodesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PodcastEpisodesPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbId(1234); // WHERE id = 1234
     * $query->filterByDbId(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterByDbId(array('min' => 12)); // WHERE id >= 12
     * $query->filterByDbId(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $dbId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(PodcastEpisodesPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(PodcastEpisodesPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PodcastEpisodesPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the file_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFileId(1234); // WHERE file_id = 1234
     * $query->filterByDbFileId(array(12, 34)); // WHERE file_id IN (12, 34)
     * $query->filterByDbFileId(array('min' => 12)); // WHERE file_id >= 12
     * $query->filterByDbFileId(array('max' => 12)); // WHERE file_id <= 12
     * </code>
     *
     * @see       filterByCcFiles()
     *
     * @param     mixed $dbFileId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByDbFileId($dbFileId = null, $comparison = null)
    {
        if (is_array($dbFileId)) {
            $useMinMax = false;
            if (isset($dbFileId['min'])) {
                $this->addUsingAlias(PodcastEpisodesPeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbFileId['max'])) {
                $this->addUsingAlias(PodcastEpisodesPeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PodcastEpisodesPeer::FILE_ID, $dbFileId, $comparison);
    }

    /**
     * Filter the query on the podcast_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbPodcastId(1234); // WHERE podcast_id = 1234
     * $query->filterByDbPodcastId(array(12, 34)); // WHERE podcast_id IN (12, 34)
     * $query->filterByDbPodcastId(array('min' => 12)); // WHERE podcast_id >= 12
     * $query->filterByDbPodcastId(array('max' => 12)); // WHERE podcast_id <= 12
     * </code>
     *
     * @see       filterByPodcast()
     *
     * @param     mixed $dbPodcastId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByDbPodcastId($dbPodcastId = null, $comparison = null)
    {
        if (is_array($dbPodcastId)) {
            $useMinMax = false;
            if (isset($dbPodcastId['min'])) {
                $this->addUsingAlias(PodcastEpisodesPeer::PODCAST_ID, $dbPodcastId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbPodcastId['max'])) {
                $this->addUsingAlias(PodcastEpisodesPeer::PODCAST_ID, $dbPodcastId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PodcastEpisodesPeer::PODCAST_ID, $dbPodcastId, $comparison);
    }

    /**
     * Filter the query on the publication_date column
     *
     * Example usage:
     * <code>
     * $query->filterByDbPublicationDate('2011-03-14'); // WHERE publication_date = '2011-03-14'
     * $query->filterByDbPublicationDate('now'); // WHERE publication_date = '2011-03-14'
     * $query->filterByDbPublicationDate(array('max' => 'yesterday')); // WHERE publication_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbPublicationDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByDbPublicationDate($dbPublicationDate = null, $comparison = null)
    {
        if (is_array($dbPublicationDate)) {
            $useMinMax = false;
            if (isset($dbPublicationDate['min'])) {
                $this->addUsingAlias(PodcastEpisodesPeer::PUBLICATION_DATE, $dbPublicationDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbPublicationDate['max'])) {
                $this->addUsingAlias(PodcastEpisodesPeer::PUBLICATION_DATE, $dbPublicationDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PodcastEpisodesPeer::PUBLICATION_DATE, $dbPublicationDate, $comparison);
    }

    /**
     * Filter the query on the download_url column
     *
     * Example usage:
     * <code>
     * $query->filterByDbDownloadUrl('fooValue');   // WHERE download_url = 'fooValue'
     * $query->filterByDbDownloadUrl('%fooValue%'); // WHERE download_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbDownloadUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByDbDownloadUrl($dbDownloadUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbDownloadUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbDownloadUrl)) {
                $dbDownloadUrl = str_replace('*', '%', $dbDownloadUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PodcastEpisodesPeer::DOWNLOAD_URL, $dbDownloadUrl, $comparison);
    }

    /**
     * Filter the query on the episode_guid column
     *
     * Example usage:
     * <code>
     * $query->filterByDbEpisodeGuid('fooValue');   // WHERE episode_guid = 'fooValue'
     * $query->filterByDbEpisodeGuid('%fooValue%'); // WHERE episode_guid LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbEpisodeGuid The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByDbEpisodeGuid($dbEpisodeGuid = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbEpisodeGuid)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbEpisodeGuid)) {
                $dbEpisodeGuid = str_replace('*', '%', $dbEpisodeGuid);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PodcastEpisodesPeer::EPISODE_GUID, $dbEpisodeGuid, $comparison);
    }

    /**
     * Filter the query on the episode_title column
     *
     * Example usage:
     * <code>
     * $query->filterByDbEpisodeTitle('fooValue');   // WHERE episode_title = 'fooValue'
     * $query->filterByDbEpisodeTitle('%fooValue%'); // WHERE episode_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbEpisodeTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByDbEpisodeTitle($dbEpisodeTitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbEpisodeTitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbEpisodeTitle)) {
                $dbEpisodeTitle = str_replace('*', '%', $dbEpisodeTitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PodcastEpisodesPeer::EPISODE_TITLE, $dbEpisodeTitle, $comparison);
    }

    /**
     * Filter the query on the episode_description column
     *
     * Example usage:
     * <code>
     * $query->filterByDbEpisodeDescription('fooValue');   // WHERE episode_description = 'fooValue'
     * $query->filterByDbEpisodeDescription('%fooValue%'); // WHERE episode_description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbEpisodeDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function filterByDbEpisodeDescription($dbEpisodeDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbEpisodeDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbEpisodeDescription)) {
                $dbEpisodeDescription = str_replace('*', '%', $dbEpisodeDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PodcastEpisodesPeer::EPISODE_DESCRIPTION, $dbEpisodeDescription, $comparison);
    }

    /**
     * Filter the query by a related CcFiles object
     *
     * @param   CcFiles|PropelObjectCollection $ccFiles The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PodcastEpisodesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcFiles($ccFiles, $comparison = null)
    {
        if ($ccFiles instanceof CcFiles) {
            return $this
                ->addUsingAlias(PodcastEpisodesPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
        } elseif ($ccFiles instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PodcastEpisodesPeer::FILE_ID, $ccFiles->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcFiles() only accepts arguments of type CcFiles or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcFiles relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function joinCcFiles($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcFiles');

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
            $this->addJoinObject($join, 'CcFiles');
        }

        return $this;
    }

    /**
     * Use the CcFiles relation CcFiles object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcFilesQuery A secondary query class using the current class as primary query
     */
    public function useCcFilesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcFiles($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcFiles', 'CcFilesQuery');
    }

    /**
     * Filter the query by a related Podcast object
     *
     * @param   Podcast|PropelObjectCollection $podcast The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PodcastEpisodesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPodcast($podcast, $comparison = null)
    {
        if ($podcast instanceof Podcast) {
            return $this
                ->addUsingAlias(PodcastEpisodesPeer::PODCAST_ID, $podcast->getDbId(), $comparison);
        } elseif ($podcast instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PodcastEpisodesPeer::PODCAST_ID, $podcast->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByPodcast() only accepts arguments of type Podcast or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Podcast relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function joinPodcast($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Podcast');

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
            $this->addJoinObject($join, 'Podcast');
        }

        return $this;
    }

    /**
     * Use the Podcast relation Podcast object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   PodcastQuery A secondary query class using the current class as primary query
     */
    public function usePodcastQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPodcast($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Podcast', 'PodcastQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   PodcastEpisodes $podcastEpisodes Object to remove from the list of results
     *
     * @return PodcastEpisodesQuery The current query, for fluid interface
     */
    public function prune($podcastEpisodes = null)
    {
        if ($podcastEpisodes) {
            $this->addUsingAlias(PodcastEpisodesPeer::ID, $podcastEpisodes->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
