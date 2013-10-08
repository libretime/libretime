<?php


/**
 * Base class that represents a query for the 'cc_playout_history_template_field' table.
 *
 * 
 *
 * @method     CcPlayoutHistoryTemplateFieldQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcPlayoutHistoryTemplateFieldQuery orderByDbTemplateId($order = Criteria::ASC) Order by the template_id column
 * @method     CcPlayoutHistoryTemplateFieldQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcPlayoutHistoryTemplateFieldQuery orderByDbLabel($order = Criteria::ASC) Order by the label column
 * @method     CcPlayoutHistoryTemplateFieldQuery orderByDbType($order = Criteria::ASC) Order by the type column
 * @method     CcPlayoutHistoryTemplateFieldQuery orderByDbIsFileMD($order = Criteria::ASC) Order by the is_file_md column
 * @method     CcPlayoutHistoryTemplateFieldQuery orderByDbPosition($order = Criteria::ASC) Order by the position column
 *
 * @method     CcPlayoutHistoryTemplateFieldQuery groupByDbId() Group by the id column
 * @method     CcPlayoutHistoryTemplateFieldQuery groupByDbTemplateId() Group by the template_id column
 * @method     CcPlayoutHistoryTemplateFieldQuery groupByDbName() Group by the name column
 * @method     CcPlayoutHistoryTemplateFieldQuery groupByDbLabel() Group by the label column
 * @method     CcPlayoutHistoryTemplateFieldQuery groupByDbType() Group by the type column
 * @method     CcPlayoutHistoryTemplateFieldQuery groupByDbIsFileMD() Group by the is_file_md column
 * @method     CcPlayoutHistoryTemplateFieldQuery groupByDbPosition() Group by the position column
 *
 * @method     CcPlayoutHistoryTemplateFieldQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPlayoutHistoryTemplateFieldQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPlayoutHistoryTemplateFieldQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPlayoutHistoryTemplateFieldQuery leftJoinCcPlayoutHistoryTemplate($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 * @method     CcPlayoutHistoryTemplateFieldQuery rightJoinCcPlayoutHistoryTemplate($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 * @method     CcPlayoutHistoryTemplateFieldQuery innerJoinCcPlayoutHistoryTemplate($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 *
 * @method     CcPlayoutHistoryTemplateField findOne(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplateField matching the query
 * @method     CcPlayoutHistoryTemplateField findOneOrCreate(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplateField matching the query, or a new CcPlayoutHistoryTemplateField object populated from the query conditions when no match is found
 *
 * @method     CcPlayoutHistoryTemplateField findOneByDbId(int $id) Return the first CcPlayoutHistoryTemplateField filtered by the id column
 * @method     CcPlayoutHistoryTemplateField findOneByDbTemplateId(int $template_id) Return the first CcPlayoutHistoryTemplateField filtered by the template_id column
 * @method     CcPlayoutHistoryTemplateField findOneByDbName(string $name) Return the first CcPlayoutHistoryTemplateField filtered by the name column
 * @method     CcPlayoutHistoryTemplateField findOneByDbLabel(string $label) Return the first CcPlayoutHistoryTemplateField filtered by the label column
 * @method     CcPlayoutHistoryTemplateField findOneByDbType(string $type) Return the first CcPlayoutHistoryTemplateField filtered by the type column
 * @method     CcPlayoutHistoryTemplateField findOneByDbIsFileMD(boolean $is_file_md) Return the first CcPlayoutHistoryTemplateField filtered by the is_file_md column
 * @method     CcPlayoutHistoryTemplateField findOneByDbPosition(int $position) Return the first CcPlayoutHistoryTemplateField filtered by the position column
 *
 * @method     array findByDbId(int $id) Return CcPlayoutHistoryTemplateField objects filtered by the id column
 * @method     array findByDbTemplateId(int $template_id) Return CcPlayoutHistoryTemplateField objects filtered by the template_id column
 * @method     array findByDbName(string $name) Return CcPlayoutHistoryTemplateField objects filtered by the name column
 * @method     array findByDbLabel(string $label) Return CcPlayoutHistoryTemplateField objects filtered by the label column
 * @method     array findByDbType(string $type) Return CcPlayoutHistoryTemplateField objects filtered by the type column
 * @method     array findByDbIsFileMD(boolean $is_file_md) Return CcPlayoutHistoryTemplateField objects filtered by the is_file_md column
 * @method     array findByDbPosition(int $position) Return CcPlayoutHistoryTemplateField objects filtered by the position column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlayoutHistoryTemplateFieldQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPlayoutHistoryTemplateFieldQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcPlayoutHistoryTemplateField', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPlayoutHistoryTemplateFieldQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPlayoutHistoryTemplateFieldQuery) {
			return $criteria;
		}
		$query = new CcPlayoutHistoryTemplateFieldQuery();
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
	 * @return    CcPlayoutHistoryTemplateField|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPlayoutHistoryTemplateFieldPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the template_id column
	 * 
	 * @param     int|array $dbTemplateId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByDbTemplateId($dbTemplateId = null, $comparison = null)
	{
		if (is_array($dbTemplateId)) {
			$useMinMax = false;
			if (isset($dbTemplateId['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $dbTemplateId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbTemplateId['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $dbTemplateId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $dbTemplateId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 * 
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByDbName($dbName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbName)) {
				$dbName = str_replace('*', '%', $dbName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the label column
	 * 
	 * @param     string $dbLabel The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByDbLabel($dbLabel = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLabel)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLabel)) {
				$dbLabel = str_replace('*', '%', $dbLabel);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::LABEL, $dbLabel, $comparison);
	}

	/**
	 * Filter the query on the type column
	 * 
	 * @param     string $dbType The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByDbType($dbType = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbType)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbType)) {
				$dbType = str_replace('*', '%', $dbType);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TYPE, $dbType, $comparison);
	}

	/**
	 * Filter the query on the is_file_md column
	 * 
	 * @param     boolean|string $dbIsFileMD The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByDbIsFileMD($dbIsFileMD = null, $comparison = null)
	{
		if (is_string($dbIsFileMD)) {
			$is_file_md = in_array(strtolower($dbIsFileMD), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::IS_FILE_MD, $dbIsFileMD, $comparison);
	}

	/**
	 * Filter the query on the position column
	 * 
	 * @param     int|array $dbPosition The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByDbPosition($dbPosition = null, $comparison = null)
	{
		if (is_array($dbPosition)) {
			$useMinMax = false;
			if (isset($dbPosition['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::POSITION, $dbPosition['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbPosition['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::POSITION, $dbPosition['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::POSITION, $dbPosition, $comparison);
	}

	/**
	 * Filter the query by a related CcPlayoutHistoryTemplate object
	 *
	 * @param     CcPlayoutHistoryTemplate $ccPlayoutHistoryTemplate  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistoryTemplate($ccPlayoutHistoryTemplate, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::TEMPLATE_ID, $ccPlayoutHistoryTemplate->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistoryTemplate relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistoryTemplate($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlayoutHistoryTemplate');
		
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
			$this->addJoinObject($join, 'CcPlayoutHistoryTemplate');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlayoutHistoryTemplate relation CcPlayoutHistoryTemplate object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryTemplateQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryTemplateQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcPlayoutHistoryTemplate($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryTemplate', 'CcPlayoutHistoryTemplateQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcPlayoutHistoryTemplateField $ccPlayoutHistoryTemplateField Object to remove from the list of results
	 *
	 * @return    CcPlayoutHistoryTemplateFieldQuery The current query, for fluid interface
	 */
	public function prune($ccPlayoutHistoryTemplateField = null)
	{
		if ($ccPlayoutHistoryTemplateField) {
			$this->addUsingAlias(CcPlayoutHistoryTemplateFieldPeer::ID, $ccPlayoutHistoryTemplateField->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcPlayoutHistoryTemplateFieldQuery
