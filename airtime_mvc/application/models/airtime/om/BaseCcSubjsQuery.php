<?php


/**
 * Base class that represents a query for the 'cc_subjs' table.
 *
 * 
 *
 * @method     CcSubjsQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcSubjsQuery orderByDbLogin($order = Criteria::ASC) Order by the login column
 * @method     CcSubjsQuery orderByDbPass($order = Criteria::ASC) Order by the pass column
 * @method     CcSubjsQuery orderByDbType($order = Criteria::ASC) Order by the type column
 * @method     CcSubjsQuery orderByDbFirstName($order = Criteria::ASC) Order by the first_name column
 * @method     CcSubjsQuery orderByDbLastName($order = Criteria::ASC) Order by the last_name column
 * @method     CcSubjsQuery orderByDbLastlogin($order = Criteria::ASC) Order by the lastlogin column
 * @method     CcSubjsQuery orderByDbLastfail($order = Criteria::ASC) Order by the lastfail column
 * @method     CcSubjsQuery orderByDbSkypeContact($order = Criteria::ASC) Order by the skype_contact column
 * @method     CcSubjsQuery orderByDbJabberContact($order = Criteria::ASC) Order by the jabber_contact column
 * @method     CcSubjsQuery orderByDbEmail($order = Criteria::ASC) Order by the email column
 * @method     CcSubjsQuery orderByDbCellPhone($order = Criteria::ASC) Order by the cell_phone column
 * @method     CcSubjsQuery orderByDbLoginAttempts($order = Criteria::ASC) Order by the login_attempts column
 *
 * @method     CcSubjsQuery groupByDbId() Group by the id column
 * @method     CcSubjsQuery groupByDbLogin() Group by the login column
 * @method     CcSubjsQuery groupByDbPass() Group by the pass column
 * @method     CcSubjsQuery groupByDbType() Group by the type column
 * @method     CcSubjsQuery groupByDbFirstName() Group by the first_name column
 * @method     CcSubjsQuery groupByDbLastName() Group by the last_name column
 * @method     CcSubjsQuery groupByDbLastlogin() Group by the lastlogin column
 * @method     CcSubjsQuery groupByDbLastfail() Group by the lastfail column
 * @method     CcSubjsQuery groupByDbSkypeContact() Group by the skype_contact column
 * @method     CcSubjsQuery groupByDbJabberContact() Group by the jabber_contact column
 * @method     CcSubjsQuery groupByDbEmail() Group by the email column
 * @method     CcSubjsQuery groupByDbCellPhone() Group by the cell_phone column
 * @method     CcSubjsQuery groupByDbLoginAttempts() Group by the login_attempts column
 *
 * @method     CcSubjsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcSubjsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcSubjsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcSubjsQuery leftJoinCcFilesRelatedByDbOwnerId($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFilesRelatedByDbOwnerId relation
 * @method     CcSubjsQuery rightJoinCcFilesRelatedByDbOwnerId($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFilesRelatedByDbOwnerId relation
 * @method     CcSubjsQuery innerJoinCcFilesRelatedByDbOwnerId($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFilesRelatedByDbOwnerId relation
 *
 * @method     CcSubjsQuery leftJoinCcFilesRelatedByDbEditedby($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFilesRelatedByDbEditedby relation
 * @method     CcSubjsQuery rightJoinCcFilesRelatedByDbEditedby($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFilesRelatedByDbEditedby relation
 * @method     CcSubjsQuery innerJoinCcFilesRelatedByDbEditedby($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFilesRelatedByDbEditedby relation
 *
 * @method     CcSubjsQuery leftJoinCcPerms($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPerms relation
 * @method     CcSubjsQuery rightJoinCcPerms($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPerms relation
 * @method     CcSubjsQuery innerJoinCcPerms($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPerms relation
 *
 * @method     CcSubjsQuery leftJoinCcShowHosts($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcShowHosts relation
 * @method     CcSubjsQuery rightJoinCcShowHosts($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcShowHosts relation
 * @method     CcSubjsQuery innerJoinCcShowHosts($relationAlias = '') Adds a INNER JOIN clause to the query using the CcShowHosts relation
 *
 * @method     CcSubjsQuery leftJoinCcPlaylist($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlaylist relation
 * @method     CcSubjsQuery rightJoinCcPlaylist($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlaylist relation
 * @method     CcSubjsQuery innerJoinCcPlaylist($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlaylist relation
 *
 * @method     CcSubjsQuery leftJoinCcBlock($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcBlock relation
 * @method     CcSubjsQuery rightJoinCcBlock($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcBlock relation
 * @method     CcSubjsQuery innerJoinCcBlock($relationAlias = '') Adds a INNER JOIN clause to the query using the CcBlock relation
 *
 * @method     CcSubjsQuery leftJoinCcPref($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPref relation
 * @method     CcSubjsQuery rightJoinCcPref($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPref relation
 * @method     CcSubjsQuery innerJoinCcPref($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPref relation
 *
 * @method     CcSubjsQuery leftJoinCcSess($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSess relation
 * @method     CcSubjsQuery rightJoinCcSess($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSess relation
 * @method     CcSubjsQuery innerJoinCcSess($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSess relation
 *
 * @method     CcSubjsQuery leftJoinCcSubjsToken($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcSubjsToken relation
 * @method     CcSubjsQuery rightJoinCcSubjsToken($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcSubjsToken relation
 * @method     CcSubjsQuery innerJoinCcSubjsToken($relationAlias = '') Adds a INNER JOIN clause to the query using the CcSubjsToken relation
 *
 * @method     CcSubjs findOne(PropelPDO $con = null) Return the first CcSubjs matching the query
 * @method     CcSubjs findOneOrCreate(PropelPDO $con = null) Return the first CcSubjs matching the query, or a new CcSubjs object populated from the query conditions when no match is found
 *
 * @method     CcSubjs findOneByDbId(int $id) Return the first CcSubjs filtered by the id column
 * @method     CcSubjs findOneByDbLogin(string $login) Return the first CcSubjs filtered by the login column
 * @method     CcSubjs findOneByDbPass(string $pass) Return the first CcSubjs filtered by the pass column
 * @method     CcSubjs findOneByDbType(string $type) Return the first CcSubjs filtered by the type column
 * @method     CcSubjs findOneByDbFirstName(string $first_name) Return the first CcSubjs filtered by the first_name column
 * @method     CcSubjs findOneByDbLastName(string $last_name) Return the first CcSubjs filtered by the last_name column
 * @method     CcSubjs findOneByDbLastlogin(string $lastlogin) Return the first CcSubjs filtered by the lastlogin column
 * @method     CcSubjs findOneByDbLastfail(string $lastfail) Return the first CcSubjs filtered by the lastfail column
 * @method     CcSubjs findOneByDbSkypeContact(string $skype_contact) Return the first CcSubjs filtered by the skype_contact column
 * @method     CcSubjs findOneByDbJabberContact(string $jabber_contact) Return the first CcSubjs filtered by the jabber_contact column
 * @method     CcSubjs findOneByDbEmail(string $email) Return the first CcSubjs filtered by the email column
 * @method     CcSubjs findOneByDbCellPhone(string $cell_phone) Return the first CcSubjs filtered by the cell_phone column
 * @method     CcSubjs findOneByDbLoginAttempts(int $login_attempts) Return the first CcSubjs filtered by the login_attempts column
 *
 * @method     array findByDbId(int $id) Return CcSubjs objects filtered by the id column
 * @method     array findByDbLogin(string $login) Return CcSubjs objects filtered by the login column
 * @method     array findByDbPass(string $pass) Return CcSubjs objects filtered by the pass column
 * @method     array findByDbType(string $type) Return CcSubjs objects filtered by the type column
 * @method     array findByDbFirstName(string $first_name) Return CcSubjs objects filtered by the first_name column
 * @method     array findByDbLastName(string $last_name) Return CcSubjs objects filtered by the last_name column
 * @method     array findByDbLastlogin(string $lastlogin) Return CcSubjs objects filtered by the lastlogin column
 * @method     array findByDbLastfail(string $lastfail) Return CcSubjs objects filtered by the lastfail column
 * @method     array findByDbSkypeContact(string $skype_contact) Return CcSubjs objects filtered by the skype_contact column
 * @method     array findByDbJabberContact(string $jabber_contact) Return CcSubjs objects filtered by the jabber_contact column
 * @method     array findByDbEmail(string $email) Return CcSubjs objects filtered by the email column
 * @method     array findByDbCellPhone(string $cell_phone) Return CcSubjs objects filtered by the cell_phone column
 * @method     array findByDbLoginAttempts(int $login_attempts) Return CcSubjs objects filtered by the login_attempts column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSubjsQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcSubjsQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcSubjs', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcSubjsQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcSubjsQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcSubjsQuery) {
			return $criteria;
		}
		$query = new CcSubjsQuery();
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
	 * @return    CcSubjs|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcSubjsPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcSubjsPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcSubjsPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 * 
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcSubjsPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the login column
	 * 
	 * @param     string $dbLogin The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbLogin($dbLogin = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLogin)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLogin)) {
				$dbLogin = str_replace('*', '%', $dbLogin);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::LOGIN, $dbLogin, $comparison);
	}

	/**
	 * Filter the query on the pass column
	 * 
	 * @param     string $dbPass The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbPass($dbPass = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbPass)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbPass)) {
				$dbPass = str_replace('*', '%', $dbPass);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::PASS, $dbPass, $comparison);
	}

	/**
	 * Filter the query on the type column
	 * 
	 * @param     string $dbType The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcSubjsPeer::TYPE, $dbType, $comparison);
	}

	/**
	 * Filter the query on the first_name column
	 * 
	 * @param     string $dbFirstName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbFirstName($dbFirstName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbFirstName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbFirstName)) {
				$dbFirstName = str_replace('*', '%', $dbFirstName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::FIRST_NAME, $dbFirstName, $comparison);
	}

	/**
	 * Filter the query on the last_name column
	 * 
	 * @param     string $dbLastName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbLastName($dbLastName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbLastName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbLastName)) {
				$dbLastName = str_replace('*', '%', $dbLastName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::LAST_NAME, $dbLastName, $comparison);
	}

	/**
	 * Filter the query on the lastlogin column
	 * 
	 * @param     string|array $dbLastlogin The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbLastlogin($dbLastlogin = null, $comparison = null)
	{
		if (is_array($dbLastlogin)) {
			$useMinMax = false;
			if (isset($dbLastlogin['min'])) {
				$this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $dbLastlogin['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLastlogin['max'])) {
				$this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $dbLastlogin['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::LASTLOGIN, $dbLastlogin, $comparison);
	}

	/**
	 * Filter the query on the lastfail column
	 * 
	 * @param     string|array $dbLastfail The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbLastfail($dbLastfail = null, $comparison = null)
	{
		if (is_array($dbLastfail)) {
			$useMinMax = false;
			if (isset($dbLastfail['min'])) {
				$this->addUsingAlias(CcSubjsPeer::LASTFAIL, $dbLastfail['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLastfail['max'])) {
				$this->addUsingAlias(CcSubjsPeer::LASTFAIL, $dbLastfail['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::LASTFAIL, $dbLastfail, $comparison);
	}

	/**
	 * Filter the query on the skype_contact column
	 * 
	 * @param     string $dbSkypeContact The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbSkypeContact($dbSkypeContact = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbSkypeContact)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbSkypeContact)) {
				$dbSkypeContact = str_replace('*', '%', $dbSkypeContact);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::SKYPE_CONTACT, $dbSkypeContact, $comparison);
	}

	/**
	 * Filter the query on the jabber_contact column
	 * 
	 * @param     string $dbJabberContact The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbJabberContact($dbJabberContact = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbJabberContact)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbJabberContact)) {
				$dbJabberContact = str_replace('*', '%', $dbJabberContact);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::JABBER_CONTACT, $dbJabberContact, $comparison);
	}

	/**
	 * Filter the query on the email column
	 * 
	 * @param     string $dbEmail The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbEmail($dbEmail = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbEmail)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbEmail)) {
				$dbEmail = str_replace('*', '%', $dbEmail);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::EMAIL, $dbEmail, $comparison);
	}

	/**
	 * Filter the query on the cell_phone column
	 * 
	 * @param     string $dbCellPhone The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbCellPhone($dbCellPhone = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbCellPhone)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbCellPhone)) {
				$dbCellPhone = str_replace('*', '%', $dbCellPhone);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::CELL_PHONE, $dbCellPhone, $comparison);
	}

	/**
	 * Filter the query on the login_attempts column
	 * 
	 * @param     int|array $dbLoginAttempts The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByDbLoginAttempts($dbLoginAttempts = null, $comparison = null)
	{
		if (is_array($dbLoginAttempts)) {
			$useMinMax = false;
			if (isset($dbLoginAttempts['min'])) {
				$this->addUsingAlias(CcSubjsPeer::LOGIN_ATTEMPTS, $dbLoginAttempts['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbLoginAttempts['max'])) {
				$this->addUsingAlias(CcSubjsPeer::LOGIN_ATTEMPTS, $dbLoginAttempts['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcSubjsPeer::LOGIN_ATTEMPTS, $dbLoginAttempts, $comparison);
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcFilesRelatedByDbOwnerId($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccFiles->getDbOwnerId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFilesRelatedByDbOwnerId relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcFilesRelatedByDbOwnerId($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcFilesRelatedByDbOwnerId');
		
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
			$this->addJoinObject($join, 'CcFilesRelatedByDbOwnerId');
		}
		
		return $this;
	}

	/**
	 * Use the CcFilesRelatedByDbOwnerId relation CcFiles object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery A secondary query class using the current class as primary query
	 */
	public function useCcFilesRelatedByDbOwnerIdQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcFilesRelatedByDbOwnerId($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcFilesRelatedByDbOwnerId', 'CcFilesQuery');
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcFilesRelatedByDbEditedby($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccFiles->getDbEditedby(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFilesRelatedByDbEditedby relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcFilesRelatedByDbEditedby($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcFilesRelatedByDbEditedby');
		
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
			$this->addJoinObject($join, 'CcFilesRelatedByDbEditedby');
		}
		
		return $this;
	}

	/**
	 * Use the CcFilesRelatedByDbEditedby relation CcFiles object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFilesQuery A secondary query class using the current class as primary query
	 */
	public function useCcFilesRelatedByDbEditedbyQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcFilesRelatedByDbEditedby($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcFilesRelatedByDbEditedby', 'CcFilesQuery');
	}

	/**
	 * Filter the query by a related CcPerms object
	 *
	 * @param     CcPerms $ccPerms  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcPerms($ccPerms, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccPerms->getSubj(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPerms relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcPerms($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPerms');
		
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
			$this->addJoinObject($join, 'CcPerms');
		}
		
		return $this;
	}

	/**
	 * Use the CcPerms relation CcPerms object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPermsQuery A secondary query class using the current class as primary query
	 */
	public function useCcPermsQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPerms($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPerms', 'CcPermsQuery');
	}

	/**
	 * Filter the query by a related CcShowHosts object
	 *
	 * @param     CcShowHosts $ccShowHosts  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcShowHosts($ccShowHosts, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccShowHosts->getDbHost(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcShowHosts relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcShowHosts($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcShowHosts');
		
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
			$this->addJoinObject($join, 'CcShowHosts');
		}
		
		return $this;
	}

	/**
	 * Use the CcShowHosts relation CcShowHosts object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcShowHostsQuery A secondary query class using the current class as primary query
	 */
	public function useCcShowHostsQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcShowHosts($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcShowHosts', 'CcShowHostsQuery');
	}

	/**
	 * Filter the query by a related CcPlaylist object
	 *
	 * @param     CcPlaylist $ccPlaylist  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcPlaylist($ccPlaylist, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccPlaylist->getDbCreatorId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlaylist relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcPlaylist($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlaylist');
		
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
			$this->addJoinObject($join, 'CcPlaylist');
		}
		
		return $this;
	}

	/**
	 * Use the CcPlaylist relation CcPlaylist object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlaylistQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlaylistQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPlaylist($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlaylist', 'CcPlaylistQuery');
	}

	/**
	 * Filter the query by a related CcBlock object
	 *
	 * @param     CcBlock $ccBlock  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcBlock($ccBlock, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccBlock->getDbCreatorId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcBlock relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcBlock($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcBlock');
		
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
			$this->addJoinObject($join, 'CcBlock');
		}
		
		return $this;
	}

	/**
	 * Use the CcBlock relation CcBlock object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcBlockQuery A secondary query class using the current class as primary query
	 */
	public function useCcBlockQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcBlock($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcBlock', 'CcBlockQuery');
	}

	/**
	 * Filter the query by a related CcPref object
	 *
	 * @param     CcPref $ccPref  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcPref($ccPref, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccPref->getSubjid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPref relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcPref($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPref');
		
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
			$this->addJoinObject($join, 'CcPref');
		}
		
		return $this;
	}

	/**
	 * Use the CcPref relation CcPref object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPrefQuery A secondary query class using the current class as primary query
	 */
	public function useCcPrefQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcPref($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPref', 'CcPrefQuery');
	}

	/**
	 * Filter the query by a related CcSess object
	 *
	 * @param     CcSess $ccSess  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcSess($ccSess, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccSess->getUserid(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSess relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcSess($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcSess');
		
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
			$this->addJoinObject($join, 'CcSess');
		}
		
		return $this;
	}

	/**
	 * Use the CcSess relation CcSess object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSessQuery A secondary query class using the current class as primary query
	 */
	public function useCcSessQuery($relationAlias = '', $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinCcSess($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSess', 'CcSessQuery');
	}

	/**
	 * Filter the query by a related CcSubjsToken object
	 *
	 * @param     CcSubjsToken $ccSubjsToken  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function filterByCcSubjsToken($ccSubjsToken, $comparison = null)
	{
		return $this
			->addUsingAlias(CcSubjsPeer::ID, $ccSubjsToken->getDbUserId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcSubjsToken relation
	 * 
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function joinCcSubjsToken($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcSubjsToken');
		
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
			$this->addJoinObject($join, 'CcSubjsToken');
		}
		
		return $this;
	}

	/**
	 * Use the CcSubjsToken relation CcSubjsToken object
	 *
	 * @see       useQuery()
	 * 
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcSubjsTokenQuery A secondary query class using the current class as primary query
	 */
	public function useCcSubjsTokenQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcSubjsToken($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcSubjsToken', 'CcSubjsTokenQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcSubjs $ccSubjs Object to remove from the list of results
	 *
	 * @return    CcSubjsQuery The current query, for fluid interface
	 */
	public function prune($ccSubjs = null)
	{
		if ($ccSubjs) {
			$this->addUsingAlias(CcSubjsPeer::ID, $ccSubjs->getDbId(), Criteria::NOT_EQUAL);
	  }
	  
		return $this;
	}

} // BaseCcSubjsQuery
