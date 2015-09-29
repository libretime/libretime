<?php


/**
 * Base class that represents a query for the 'imported_podcast' table.
 *
 *
 *
 * @method ImportedPodcastQuery orderByDbUrl($order = Criteria::ASC) Order by the url column
 * @method ImportedPodcastQuery orderByDbAutoIngest($order = Criteria::ASC) Order by the auto_ingest column
 * @method ImportedPodcastQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method ImportedPodcastQuery orderByDbTitle($order = Criteria::ASC) Order by the title column
 * @method ImportedPodcastQuery orderByDbCreator($order = Criteria::ASC) Order by the creator column
 * @method ImportedPodcastQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method ImportedPodcastQuery orderByDbLanguage($order = Criteria::ASC) Order by the language column
 * @method ImportedPodcastQuery orderByDbCopyright($order = Criteria::ASC) Order by the copyright column
 * @method ImportedPodcastQuery orderByDbLink($order = Criteria::ASC) Order by the link column
 * @method ImportedPodcastQuery orderByDbItunesAuthor($order = Criteria::ASC) Order by the itunes_author column
 * @method ImportedPodcastQuery orderByDbItunesKeywords($order = Criteria::ASC) Order by the itunes_keywords column
 * @method ImportedPodcastQuery orderByDbItunesSummary($order = Criteria::ASC) Order by the itunes_summary column
 * @method ImportedPodcastQuery orderByDbItunesSubtitle($order = Criteria::ASC) Order by the itunes_subtitle column
 * @method ImportedPodcastQuery orderByDbItunesCategory($order = Criteria::ASC) Order by the itunes_category column
 * @method ImportedPodcastQuery orderByDbItunesExplicit($order = Criteria::ASC) Order by the itunes_explicit column
 * @method ImportedPodcastQuery orderByDbOwner($order = Criteria::ASC) Order by the owner column
 *
 * @method ImportedPodcastQuery groupByDbUrl() Group by the url column
 * @method ImportedPodcastQuery groupByDbAutoIngest() Group by the auto_ingest column
 * @method ImportedPodcastQuery groupByDbId() Group by the id column
 * @method ImportedPodcastQuery groupByDbTitle() Group by the title column
 * @method ImportedPodcastQuery groupByDbCreator() Group by the creator column
 * @method ImportedPodcastQuery groupByDbDescription() Group by the description column
 * @method ImportedPodcastQuery groupByDbLanguage() Group by the language column
 * @method ImportedPodcastQuery groupByDbCopyright() Group by the copyright column
 * @method ImportedPodcastQuery groupByDbLink() Group by the link column
 * @method ImportedPodcastQuery groupByDbItunesAuthor() Group by the itunes_author column
 * @method ImportedPodcastQuery groupByDbItunesKeywords() Group by the itunes_keywords column
 * @method ImportedPodcastQuery groupByDbItunesSummary() Group by the itunes_summary column
 * @method ImportedPodcastQuery groupByDbItunesSubtitle() Group by the itunes_subtitle column
 * @method ImportedPodcastQuery groupByDbItunesCategory() Group by the itunes_category column
 * @method ImportedPodcastQuery groupByDbItunesExplicit() Group by the itunes_explicit column
 * @method ImportedPodcastQuery groupByDbOwner() Group by the owner column
 *
 * @method ImportedPodcastQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ImportedPodcastQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ImportedPodcastQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ImportedPodcastQuery leftJoinPodcast($relationAlias = null) Adds a LEFT JOIN clause to the query using the Podcast relation
 * @method ImportedPodcastQuery rightJoinPodcast($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Podcast relation
 * @method ImportedPodcastQuery innerJoinPodcast($relationAlias = null) Adds a INNER JOIN clause to the query using the Podcast relation
 *
 * @method ImportedPodcastQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method ImportedPodcastQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method ImportedPodcastQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method ImportedPodcast findOne(PropelPDO $con = null) Return the first ImportedPodcast matching the query
 * @method ImportedPodcast findOneOrCreate(PropelPDO $con = null) Return the first ImportedPodcast matching the query, or a new ImportedPodcast object populated from the query conditions when no match is found
 *
 * @method ImportedPodcast findOneByDbUrl(string $url) Return the first ImportedPodcast filtered by the url column
 * @method ImportedPodcast findOneByDbAutoIngest(boolean $auto_ingest) Return the first ImportedPodcast filtered by the auto_ingest column
 * @method ImportedPodcast findOneByDbTitle(string $title) Return the first ImportedPodcast filtered by the title column
 * @method ImportedPodcast findOneByDbCreator(string $creator) Return the first ImportedPodcast filtered by the creator column
 * @method ImportedPodcast findOneByDbDescription(string $description) Return the first ImportedPodcast filtered by the description column
 * @method ImportedPodcast findOneByDbLanguage(string $language) Return the first ImportedPodcast filtered by the language column
 * @method ImportedPodcast findOneByDbCopyright(string $copyright) Return the first ImportedPodcast filtered by the copyright column
 * @method ImportedPodcast findOneByDbLink(string $link) Return the first ImportedPodcast filtered by the link column
 * @method ImportedPodcast findOneByDbItunesAuthor(string $itunes_author) Return the first ImportedPodcast filtered by the itunes_author column
 * @method ImportedPodcast findOneByDbItunesKeywords(string $itunes_keywords) Return the first ImportedPodcast filtered by the itunes_keywords column
 * @method ImportedPodcast findOneByDbItunesSummary(string $itunes_summary) Return the first ImportedPodcast filtered by the itunes_summary column
 * @method ImportedPodcast findOneByDbItunesSubtitle(string $itunes_subtitle) Return the first ImportedPodcast filtered by the itunes_subtitle column
 * @method ImportedPodcast findOneByDbItunesCategory(string $itunes_category) Return the first ImportedPodcast filtered by the itunes_category column
 * @method ImportedPodcast findOneByDbItunesExplicit(string $itunes_explicit) Return the first ImportedPodcast filtered by the itunes_explicit column
 * @method ImportedPodcast findOneByDbOwner(int $owner) Return the first ImportedPodcast filtered by the owner column
 *
 * @method array findByDbUrl(string $url) Return ImportedPodcast objects filtered by the url column
 * @method array findByDbAutoIngest(boolean $auto_ingest) Return ImportedPodcast objects filtered by the auto_ingest column
 * @method array findByDbId(int $id) Return ImportedPodcast objects filtered by the id column
 * @method array findByDbTitle(string $title) Return ImportedPodcast objects filtered by the title column
 * @method array findByDbCreator(string $creator) Return ImportedPodcast objects filtered by the creator column
 * @method array findByDbDescription(string $description) Return ImportedPodcast objects filtered by the description column
 * @method array findByDbLanguage(string $language) Return ImportedPodcast objects filtered by the language column
 * @method array findByDbCopyright(string $copyright) Return ImportedPodcast objects filtered by the copyright column
 * @method array findByDbLink(string $link) Return ImportedPodcast objects filtered by the link column
 * @method array findByDbItunesAuthor(string $itunes_author) Return ImportedPodcast objects filtered by the itunes_author column
 * @method array findByDbItunesKeywords(string $itunes_keywords) Return ImportedPodcast objects filtered by the itunes_keywords column
 * @method array findByDbItunesSummary(string $itunes_summary) Return ImportedPodcast objects filtered by the itunes_summary column
 * @method array findByDbItunesSubtitle(string $itunes_subtitle) Return ImportedPodcast objects filtered by the itunes_subtitle column
 * @method array findByDbItunesCategory(string $itunes_category) Return ImportedPodcast objects filtered by the itunes_category column
 * @method array findByDbItunesExplicit(string $itunes_explicit) Return ImportedPodcast objects filtered by the itunes_explicit column
 * @method array findByDbOwner(int $owner) Return ImportedPodcast objects filtered by the owner column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseImportedPodcastQuery extends PodcastQuery
{
    /**
     * Initializes internal state of BaseImportedPodcastQuery object.
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
            $modelName = 'ImportedPodcast';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ImportedPodcastQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ImportedPodcastQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ImportedPodcastQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ImportedPodcastQuery) {
            return $criteria;
        }
        $query = new ImportedPodcastQuery(null, null, $modelAlias);

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
     * @return   ImportedPodcast|ImportedPodcast[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ImportedPodcastPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ImportedPodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ImportedPodcast A model object, or null if the key is not found
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
     * @return                 ImportedPodcast A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "url", "auto_ingest", "id", "title", "creator", "description", "language", "copyright", "link", "itunes_author", "itunes_keywords", "itunes_summary", "itunes_subtitle", "itunes_category", "itunes_explicit", "owner" FROM "imported_podcast" WHERE "id" = :p0';
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
            $obj = new ImportedPodcast();
            $obj->hydrate($row);
            ImportedPodcastPeer::addInstanceToPool($obj, (string) $key);
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
     * @return ImportedPodcast|ImportedPodcast[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ImportedPodcast[]|mixed the list of results, formatted by the current formatter
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
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ImportedPodcastPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ImportedPodcastPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByDbUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByDbUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbUrl($dbUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbUrl)) {
                $dbUrl = str_replace('*', '%', $dbUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ImportedPodcastPeer::URL, $dbUrl, $comparison);
    }

    /**
     * Filter the query on the auto_ingest column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAutoIngest(true); // WHERE auto_ingest = true
     * $query->filterByDbAutoIngest('yes'); // WHERE auto_ingest = true
     * </code>
     *
     * @param     boolean|string $dbAutoIngest The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbAutoIngest($dbAutoIngest = null, $comparison = null)
    {
        if (is_string($dbAutoIngest)) {
            $dbAutoIngest = in_array(strtolower($dbAutoIngest), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ImportedPodcastPeer::AUTO_INGEST, $dbAutoIngest, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(ImportedPodcastPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(ImportedPodcastPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImportedPodcastPeer::ID, $dbId, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::TITLE, $dbTitle, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::CREATOR, $dbCreator, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::DESCRIPTION, $dbDescription, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::LANGUAGE, $dbLanguage, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::COPYRIGHT, $dbCopyright, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::LINK, $dbLink, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::ITUNES_AUTHOR, $dbItunesAuthor, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::ITUNES_KEYWORDS, $dbItunesKeywords, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::ITUNES_SUMMARY, $dbItunesSummary, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::ITUNES_SUBTITLE, $dbItunesSubtitle, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::ITUNES_CATEGORY, $dbItunesCategory, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ImportedPodcastPeer::ITUNES_EXPLICIT, $dbItunesExplicit, $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbOwner($dbOwner = null, $comparison = null)
    {
        if (is_array($dbOwner)) {
            $useMinMax = false;
            if (isset($dbOwner['min'])) {
                $this->addUsingAlias(ImportedPodcastPeer::OWNER, $dbOwner['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbOwner['max'])) {
                $this->addUsingAlias(ImportedPodcastPeer::OWNER, $dbOwner['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImportedPodcastPeer::OWNER, $dbOwner, $comparison);
    }

    /**
     * Filter the query by a related Podcast object
     *
     * @param   Podcast|PropelObjectCollection $podcast The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ImportedPodcastQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPodcast($podcast, $comparison = null)
    {
        if ($podcast instanceof Podcast) {
            return $this
                ->addUsingAlias(ImportedPodcastPeer::ID, $podcast->getDbId(), $comparison);
        } elseif ($podcast instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ImportedPodcastPeer::ID, $podcast->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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
     * @return                 ImportedPodcastQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(ImportedPodcastPeer::OWNER, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ImportedPodcastPeer::OWNER, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return ImportedPodcastQuery The current query, for fluid interface
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
     * @param   ImportedPodcast $importedPodcast Object to remove from the list of results
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function prune($importedPodcast = null)
    {
        if ($importedPodcast) {
            $this->addUsingAlias(ImportedPodcastPeer::ID, $importedPodcast->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
