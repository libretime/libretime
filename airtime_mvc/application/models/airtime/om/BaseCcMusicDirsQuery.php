<?php

namespace Airtime\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Airtime\CcFiles;
use Airtime\CcMusicDirs;
use Airtime\CcMusicDirsPeer;
use Airtime\CcMusicDirsQuery;
use Airtime\MediaItem\AudioFile;

/**
 * Base class that represents a query for the 'cc_music_dirs' table.
 *
 *
 *
 * @method CcMusicDirsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CcMusicDirsQuery orderByDirectory($order = Criteria::ASC) Order by the directory column
 * @method CcMusicDirsQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method CcMusicDirsQuery orderByExists($order = Criteria::ASC) Order by the exists column
 * @method CcMusicDirsQuery orderByWatched($order = Criteria::ASC) Order by the watched column
 *
 * @method CcMusicDirsQuery groupById() Group by the id column
 * @method CcMusicDirsQuery groupByDirectory() Group by the directory column
 * @method CcMusicDirsQuery groupByType() Group by the type column
 * @method CcMusicDirsQuery groupByExists() Group by the exists column
 * @method CcMusicDirsQuery groupByWatched() Group by the watched column
 *
 * @method CcMusicDirsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcMusicDirsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcMusicDirsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcMusicDirsQuery leftJoinCcFiles($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method CcMusicDirsQuery rightJoinCcFiles($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method CcMusicDirsQuery innerJoinCcFiles($relationAlias = null) Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method CcMusicDirsQuery leftJoinAudioFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the AudioFile relation
 * @method CcMusicDirsQuery rightJoinAudioFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AudioFile relation
 * @method CcMusicDirsQuery innerJoinAudioFile($relationAlias = null) Adds a INNER JOIN clause to the query using the AudioFile relation
 *
 * @method CcMusicDirs findOne(PropelPDO $con = null) Return the first CcMusicDirs matching the query
 * @method CcMusicDirs findOneOrCreate(PropelPDO $con = null) Return the first CcMusicDirs matching the query, or a new CcMusicDirs object populated from the query conditions when no match is found
 *
 * @method CcMusicDirs findOneByDirectory(string $directory) Return the first CcMusicDirs filtered by the directory column
 * @method CcMusicDirs findOneByType(string $type) Return the first CcMusicDirs filtered by the type column
 * @method CcMusicDirs findOneByExists(boolean $exists) Return the first CcMusicDirs filtered by the exists column
 * @method CcMusicDirs findOneByWatched(boolean $watched) Return the first CcMusicDirs filtered by the watched column
 *
 * @method array findById(int $id) Return CcMusicDirs objects filtered by the id column
 * @method array findByDirectory(string $directory) Return CcMusicDirs objects filtered by the directory column
 * @method array findByType(string $type) Return CcMusicDirs objects filtered by the type column
 * @method array findByExists(boolean $exists) Return CcMusicDirs objects filtered by the exists column
 * @method array findByWatched(boolean $watched) Return CcMusicDirs objects filtered by the watched column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcMusicDirsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcMusicDirsQuery object.
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
            $modelName = 'Airtime\\CcMusicDirs';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcMusicDirsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcMusicDirsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcMusicDirsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcMusicDirsQuery) {
            return $criteria;
        }
        $query = new CcMusicDirsQuery(null, null, $modelAlias);

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
     * @return   CcMusicDirs|CcMusicDirs[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcMusicDirsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcMusicDirsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcMusicDirs A model object, or null if the key is not found
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
     * @return                 CcMusicDirs A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "directory", "type", "exists", "watched" FROM "cc_music_dirs" WHERE "id" = :p0';
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
            $obj = new CcMusicDirs();
            $obj->hydrate($row);
            CcMusicDirsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcMusicDirs|CcMusicDirs[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcMusicDirs[]|mixed the list of results, formatted by the current formatter
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
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcMusicDirsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcMusicDirsPeer::ID, $keys, Criteria::IN);
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
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CcMusicDirsPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CcMusicDirsPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcMusicDirsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the directory column
     *
     * Example usage:
     * <code>
     * $query->filterByDirectory('fooValue');   // WHERE directory = 'fooValue'
     * $query->filterByDirectory('%fooValue%'); // WHERE directory LIKE '%fooValue%'
     * </code>
     *
     * @param     string $directory The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function filterByDirectory($directory = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($directory)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $directory)) {
                $directory = str_replace('*', '%', $directory);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcMusicDirsPeer::DIRECTORY, $directory, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcMusicDirsPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the exists column
     *
     * Example usage:
     * <code>
     * $query->filterByExists(true); // WHERE exists = true
     * $query->filterByExists('yes'); // WHERE exists = true
     * </code>
     *
     * @param     boolean|string $exists The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function filterByExists($exists = null, $comparison = null)
    {
        if (is_string($exists)) {
            $exists = in_array(strtolower($exists), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcMusicDirsPeer::EXISTS, $exists, $comparison);
    }

    /**
     * Filter the query on the watched column
     *
     * Example usage:
     * <code>
     * $query->filterByWatched(true); // WHERE watched = true
     * $query->filterByWatched('yes'); // WHERE watched = true
     * </code>
     *
     * @param     boolean|string $watched The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function filterByWatched($watched = null, $comparison = null)
    {
        if (is_string($watched)) {
            $watched = in_array(strtolower($watched), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcMusicDirsPeer::WATCHED, $watched, $comparison);
    }

    /**
     * Filter the query by a related CcFiles object
     *
     * @param   CcFiles|PropelObjectCollection $ccFiles  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcMusicDirsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcFiles($ccFiles, $comparison = null)
    {
        if ($ccFiles instanceof CcFiles) {
            return $this
                ->addUsingAlias(CcMusicDirsPeer::ID, $ccFiles->getDbDirectory(), $comparison);
        } elseif ($ccFiles instanceof PropelObjectCollection) {
            return $this
                ->useCcFilesQuery()
                ->filterByPrimaryKeys($ccFiles->getPrimaryKeys())
                ->endUse();
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
     * @return CcMusicDirsQuery The current query, for fluid interface
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
     * @return   \Airtime\CcFilesQuery A secondary query class using the current class as primary query
     */
    public function useCcFilesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcFiles($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcFiles', '\Airtime\CcFilesQuery');
    }

    /**
     * Filter the query by a related AudioFile object
     *
     * @param   AudioFile|PropelObjectCollection $audioFile  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcMusicDirsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAudioFile($audioFile, $comparison = null)
    {
        if ($audioFile instanceof AudioFile) {
            return $this
                ->addUsingAlias(CcMusicDirsPeer::ID, $audioFile->getDirectory(), $comparison);
        } elseif ($audioFile instanceof PropelObjectCollection) {
            return $this
                ->useAudioFileQuery()
                ->filterByPrimaryKeys($audioFile->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAudioFile() only accepts arguments of type AudioFile or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AudioFile relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function joinAudioFile($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AudioFile');

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
            $this->addJoinObject($join, 'AudioFile');
        }

        return $this;
    }

    /**
     * Use the AudioFile relation AudioFile object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\MediaItem\AudioFileQuery A secondary query class using the current class as primary query
     */
    public function useAudioFileQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAudioFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AudioFile', '\Airtime\MediaItem\AudioFileQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcMusicDirs $ccMusicDirs Object to remove from the list of results
     *
     * @return CcMusicDirsQuery The current query, for fluid interface
     */
    public function prune($ccMusicDirs = null)
    {
        if ($ccMusicDirs) {
            $this->addUsingAlias(CcMusicDirsPeer::ID, $ccMusicDirs->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
