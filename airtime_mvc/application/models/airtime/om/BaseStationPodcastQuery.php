<?php


/**
 * Base class that represents a query for the 'station_podcast' table.
 *
 *
 *
 * @method StationPodcastQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method StationPodcastQuery orderByDbTitle($order = Criteria::ASC) Order by the title column
 * @method StationPodcastQuery orderByDbCreator($order = Criteria::ASC) Order by the creator column
 * @method StationPodcastQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method StationPodcastQuery orderByDbLanguage($order = Criteria::ASC) Order by the language column
 * @method StationPodcastQuery orderByDbCopyright($order = Criteria::ASC) Order by the copyright column
 * @method StationPodcastQuery orderByDbLink($order = Criteria::ASC) Order by the link column
 * @method StationPodcastQuery orderByDbItunesAuthor($order = Criteria::ASC) Order by the itunes_author column
 * @method StationPodcastQuery orderByDbItunesKeywords($order = Criteria::ASC) Order by the itunes_keywords column
 * @method StationPodcastQuery orderByDbItunesSummary($order = Criteria::ASC) Order by the itunes_summary column
 * @method StationPodcastQuery orderByDbItunesSubtitle($order = Criteria::ASC) Order by the itunes_subtitle column
 * @method StationPodcastQuery orderByDbItunesCategory($order = Criteria::ASC) Order by the itunes_category column
 * @method StationPodcastQuery orderByDbItunesExplicit($order = Criteria::ASC) Order by the itunes_explicit column
 * @method StationPodcastQuery orderByDbOwner($order = Criteria::ASC) Order by the owner column
 *
 * @method StationPodcastQuery groupByDbId() Group by the id column
 * @method StationPodcastQuery groupByDbTitle() Group by the title column
 * @method StationPodcastQuery groupByDbCreator() Group by the creator column
 * @method StationPodcastQuery groupByDbDescription() Group by the description column
 * @method StationPodcastQuery groupByDbLanguage() Group by the language column
 * @method StationPodcastQuery groupByDbCopyright() Group by the copyright column
 * @method StationPodcastQuery groupByDbLink() Group by the link column
 * @method StationPodcastQuery groupByDbItunesAuthor() Group by the itunes_author column
 * @method StationPodcastQuery groupByDbItunesKeywords() Group by the itunes_keywords column
 * @method StationPodcastQuery groupByDbItunesSummary() Group by the itunes_summary column
 * @method StationPodcastQuery groupByDbItunesSubtitle() Group by the itunes_subtitle column
 * @method StationPodcastQuery groupByDbItunesCategory() Group by the itunes_category column
 * @method StationPodcastQuery groupByDbItunesExplicit() Group by the itunes_explicit column
 * @method StationPodcastQuery groupByDbOwner() Group by the owner column
 *
 * @method StationPodcastQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method StationPodcastQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method StationPodcastQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method StationPodcastQuery leftJoinPodcast($relationAlias = null) Adds a LEFT JOIN clause to the query using the Podcast relation
 * @method StationPodcastQuery rightJoinPodcast($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Podcast relation
 * @method StationPodcastQuery innerJoinPodcast($relationAlias = null) Adds a INNER JOIN clause to the query using the Podcast relation
 *
 * @method StationPodcastQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method StationPodcastQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method StationPodcastQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method StationPodcast findOne(PropelPDO $con = null) Return the first StationPodcast matching the query
 * @method StationPodcast findOneOrCreate(PropelPDO $con = null) Return the first StationPodcast matching the query, or a new StationPodcast object populated from the query conditions when no match is found
 *
 * @method StationPodcast findOneByDbTitle(string $title) Return the first StationPodcast filtered by the title column
 * @method StationPodcast findOneByDbCreator(string $creator) Return the first StationPodcast filtered by the creator column
 * @method StationPodcast findOneByDbDescription(string $description) Return the first StationPodcast filtered by the description column
 * @method StationPodcast findOneByDbLanguage(string $language) Return the first StationPodcast filtered by the language column
 * @method StationPodcast findOneByDbCopyright(string $copyright) Return the first StationPodcast filtered by the copyright column
 * @method StationPodcast findOneByDbLink(string $link) Return the first StationPodcast filtered by the link column
 * @method StationPodcast findOneByDbItunesAuthor(string $itunes_author) Return the first StationPodcast filtered by the itunes_author column
 * @method StationPodcast findOneByDbItunesKeywords(string $itunes_keywords) Return the first StationPodcast filtered by the itunes_keywords column
 * @method StationPodcast findOneByDbItunesSummary(string $itunes_summary) Return the first StationPodcast filtered by the itunes_summary column
 * @method StationPodcast findOneByDbItunesSubtitle(string $itunes_subtitle) Return the first StationPodcast filtered by the itunes_subtitle column
 * @method StationPodcast findOneByDbItunesCategory(string $itunes_category) Return the first StationPodcast filtered by the itunes_category column
 * @method StationPodcast findOneByDbItunesExplicit(string $itunes_explicit) Return the first StationPodcast filtered by the itunes_explicit column
 * @method StationPodcast findOneByDbOwner(int $owner) Return the first StationPodcast filtered by the owner column
 *
 * @method array findByDbId(int $id) Return StationPodcast objects filtered by the id column
 * @method array findByDbTitle(string $title) Return StationPodcast objects filtered by the title column
 * @method array findByDbCreator(string $creator) Return StationPodcast objects filtered by the creator column
 * @method array findByDbDescription(string $description) Return StationPodcast objects filtered by the description column
 * @method array findByDbLanguage(string $language) Return StationPodcast objects filtered by the language column
 * @method array findByDbCopyright(string $copyright) Return StationPodcast objects filtered by the copyright column
 * @method array findByDbLink(string $link) Return StationPodcast objects filtered by the link column
 * @method array findByDbItunesAuthor(string $itunes_author) Return StationPodcast objects filtered by the itunes_author column
 * @method array findByDbItunesKeywords(string $itunes_keywords) Return StationPodcast objects filtered by the itunes_keywords column
 * @method array findByDbItunesSummary(string $itunes_summary) Return StationPodcast objects filtered by the itunes_summary column
 * @method array findByDbItunesSubtitle(string $itunes_subtitle) Return StationPodcast objects filtered by the itunes_subtitle column
 * @method array findByDbItunesCategory(string $itunes_category) Return StationPodcast objects filtered by the itunes_category column
 * @method array findByDbItunesExplicit(string $itunes_explicit) Return StationPodcast objects filtered by the itunes_explicit column
 * @method array findByDbOwner(int $owner) Return StationPodcast objects filtered by the owner column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseStationPodcastQuery extends PodcastQuery
{
    /**
     * Initializes internal state of BaseStationPodcastQuery object.
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
            $modelName = 'StationPodcast';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new StationPodcastQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   StationPodcastQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return StationPodcastQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof StationPodcastQuery) {
            return $criteria;
        }
        $query = new StationPodcastQuery(null, null, $modelAlias);

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
     * @return   StationPodcast|StationPodcast[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = StationPodcastPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(StationPodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 StationPodcast A model object, or null if the key is not found
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
     * @return                 StationPodcast A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "title", "creator", "description", "language", "copyright", "link", "itunes_author", "itunes_keywords", "itunes_summary", "itunes_subtitle", "itunes_category", "itunes_explicit", "owner" FROM "station_podcast" WHERE "id" = :p0';
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
            $obj = new StationPodcast();
            $obj->hydrate($row);
            StationPodcastPeer::addInstanceToPool($obj, (string) $key);
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
     * @return StationPodcast|StationPodcast[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|StationPodcast[]|mixed the list of results, formatted by the current formatter
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
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StationPodcastPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StationPodcastPeer::ID, $keys, Criteria::IN);
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
     * @see       filterByPodcast()
     *
     * @param     mixed $dbId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(StationPodcastPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(StationPodcastPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByDbTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbTitle($dbTitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbTitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbTitle)) {
                $dbTitle = str_replace('*', '%', $dbTitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::TITLE, $dbTitle, $comparison);
    }

    /**
     * Filter the query on the creator column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCreator('fooValue');   // WHERE creator = 'fooValue'
     * $query->filterByDbCreator('%fooValue%'); // WHERE creator LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCreator The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbCreator($dbCreator = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbCreator)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbCreator)) {
                $dbCreator = str_replace('*', '%', $dbCreator);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::CREATOR, $dbCreator, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDbDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDbDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbDescription($dbDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbDescription)) {
                $dbDescription = str_replace('*', '%', $dbDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::DESCRIPTION, $dbDescription, $comparison);
    }

    /**
     * Filter the query on the language column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLanguage('fooValue');   // WHERE language = 'fooValue'
     * $query->filterByDbLanguage('%fooValue%'); // WHERE language LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLanguage The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbLanguage($dbLanguage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLanguage)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLanguage)) {
                $dbLanguage = str_replace('*', '%', $dbLanguage);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::LANGUAGE, $dbLanguage, $comparison);
    }

    /**
     * Filter the query on the copyright column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCopyright('fooValue');   // WHERE copyright = 'fooValue'
     * $query->filterByDbCopyright('%fooValue%'); // WHERE copyright LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCopyright The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbCopyright($dbCopyright = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbCopyright)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbCopyright)) {
                $dbCopyright = str_replace('*', '%', $dbCopyright);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::COPYRIGHT, $dbCopyright, $comparison);
    }

    /**
     * Filter the query on the link column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLink('fooValue');   // WHERE link = 'fooValue'
     * $query->filterByDbLink('%fooValue%'); // WHERE link LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLink The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbLink($dbLink = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLink)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLink)) {
                $dbLink = str_replace('*', '%', $dbLink);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::LINK, $dbLink, $comparison);
    }

    /**
     * Filter the query on the itunes_author column
     *
     * Example usage:
     * <code>
     * $query->filterByDbItunesAuthor('fooValue');   // WHERE itunes_author = 'fooValue'
     * $query->filterByDbItunesAuthor('%fooValue%'); // WHERE itunes_author LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbItunesAuthor The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbItunesAuthor($dbItunesAuthor = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbItunesAuthor)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbItunesAuthor)) {
                $dbItunesAuthor = str_replace('*', '%', $dbItunesAuthor);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::ITUNES_AUTHOR, $dbItunesAuthor, $comparison);
    }

    /**
     * Filter the query on the itunes_keywords column
     *
     * Example usage:
     * <code>
     * $query->filterByDbItunesKeywords('fooValue');   // WHERE itunes_keywords = 'fooValue'
     * $query->filterByDbItunesKeywords('%fooValue%'); // WHERE itunes_keywords LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbItunesKeywords The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbItunesKeywords($dbItunesKeywords = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbItunesKeywords)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbItunesKeywords)) {
                $dbItunesKeywords = str_replace('*', '%', $dbItunesKeywords);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::ITUNES_KEYWORDS, $dbItunesKeywords, $comparison);
    }

    /**
     * Filter the query on the itunes_summary column
     *
     * Example usage:
     * <code>
     * $query->filterByDbItunesSummary('fooValue');   // WHERE itunes_summary = 'fooValue'
     * $query->filterByDbItunesSummary('%fooValue%'); // WHERE itunes_summary LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbItunesSummary The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbItunesSummary($dbItunesSummary = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbItunesSummary)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbItunesSummary)) {
                $dbItunesSummary = str_replace('*', '%', $dbItunesSummary);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::ITUNES_SUMMARY, $dbItunesSummary, $comparison);
    }

    /**
     * Filter the query on the itunes_subtitle column
     *
     * Example usage:
     * <code>
     * $query->filterByDbItunesSubtitle('fooValue');   // WHERE itunes_subtitle = 'fooValue'
     * $query->filterByDbItunesSubtitle('%fooValue%'); // WHERE itunes_subtitle LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbItunesSubtitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbItunesSubtitle($dbItunesSubtitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbItunesSubtitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbItunesSubtitle)) {
                $dbItunesSubtitle = str_replace('*', '%', $dbItunesSubtitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::ITUNES_SUBTITLE, $dbItunesSubtitle, $comparison);
    }

    /**
     * Filter the query on the itunes_category column
     *
     * Example usage:
     * <code>
     * $query->filterByDbItunesCategory('fooValue');   // WHERE itunes_category = 'fooValue'
     * $query->filterByDbItunesCategory('%fooValue%'); // WHERE itunes_category LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbItunesCategory The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbItunesCategory($dbItunesCategory = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbItunesCategory)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbItunesCategory)) {
                $dbItunesCategory = str_replace('*', '%', $dbItunesCategory);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::ITUNES_CATEGORY, $dbItunesCategory, $comparison);
    }

    /**
     * Filter the query on the itunes_explicit column
     *
     * Example usage:
     * <code>
     * $query->filterByDbItunesExplicit('fooValue');   // WHERE itunes_explicit = 'fooValue'
     * $query->filterByDbItunesExplicit('%fooValue%'); // WHERE itunes_explicit LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbItunesExplicit The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbItunesExplicit($dbItunesExplicit = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbItunesExplicit)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbItunesExplicit)) {
                $dbItunesExplicit = str_replace('*', '%', $dbItunesExplicit);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::ITUNES_EXPLICIT, $dbItunesExplicit, $comparison);
    }

    /**
     * Filter the query on the owner column
     *
     * Example usage:
     * <code>
     * $query->filterByDbOwner(1234); // WHERE owner = 1234
     * $query->filterByDbOwner(array(12, 34)); // WHERE owner IN (12, 34)
     * $query->filterByDbOwner(array('min' => 12)); // WHERE owner >= 12
     * $query->filterByDbOwner(array('max' => 12)); // WHERE owner <= 12
     * </code>
     *
     * @see       filterByCcSubjs()
     *
     * @param     mixed $dbOwner The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbOwner($dbOwner = null, $comparison = null)
    {
        if (is_array($dbOwner)) {
            $useMinMax = false;
            if (isset($dbOwner['min'])) {
                $this->addUsingAlias(StationPodcastPeer::OWNER, $dbOwner['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbOwner['max'])) {
                $this->addUsingAlias(StationPodcastPeer::OWNER, $dbOwner['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::OWNER, $dbOwner, $comparison);
    }

    /**
     * Filter the query by a related Podcast object
     *
     * @param   Podcast|PropelObjectCollection $podcast The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 StationPodcastQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPodcast($podcast, $comparison = null)
    {
        if ($podcast instanceof Podcast) {
            return $this
                ->addUsingAlias(StationPodcastPeer::ID, $podcast->getDbId(), $comparison);
        } elseif ($podcast instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StationPodcastPeer::ID, $podcast->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return StationPodcastQuery The current query, for fluid interface
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
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 StationPodcastQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(StationPodcastPeer::OWNER, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StationPodcastPeer::OWNER, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return StationPodcastQuery The current query, for fluid interface
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
     * @return   CcSubjsQuery A secondary query class using the current class as primary query
     */
    public function useCcSubjsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcSubjs($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSubjs', 'CcSubjsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   StationPodcast $stationPodcast Object to remove from the list of results
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function prune($stationPodcast = null)
    {
        if ($stationPodcast) {
            $this->addUsingAlias(StationPodcastPeer::ID, $stationPodcast->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
